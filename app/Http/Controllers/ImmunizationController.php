<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Immunization;
use App\Models\Schedule;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImmunizationController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'vaccine_id' => $request->query('vaccine_id'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        $immunizationsQuery = Immunization::with(['child.user', 'vaccine', 'midwife', 'booking.schedule', 'booking.service'])
            ->where('midwife_id', Auth::id())
            ->whereHas('child')
            ->whereHas('vaccine');

        if ($filters['q'] !== '') {
            $keyword = $filters['q'];
            $immunizationsQuery->where(function ($query) use ($keyword) {
                $query->where('notes', 'like', '%' . $keyword . '%')
                    ->orWhereHas('child', function ($childQuery) use ($keyword) {
                        $childQuery->where('name', 'like', '%' . $keyword . '%')
                            ->orWhereHas('user', function ($userQuery) use ($keyword) {
                                $userQuery->where('name', 'like', '%' . $keyword . '%');
                            });
                    })
                    ->orWhereHas('vaccine', function ($vaccineQuery) use ($keyword) {
                        $vaccineQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if (!empty($filters['vaccine_id'])) {
            $immunizationsQuery->where('vaccine_id', $filters['vaccine_id']);
        }

        if (!empty($filters['date_from'])) {
            $immunizationsQuery->whereDate('date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $immunizationsQuery->whereDate('date', '<=', $filters['date_to']);
        }

        $immunizations = $immunizationsQuery
            ->latest('immunized_at')
            ->latest('date')
            ->get();

        $vaccines = Vaccine::query()
            ->orderBy('name')
            ->get();

        return view('pages.immunizations.index', compact('immunizations', 'vaccines', 'filters'));
    }

    public function create()
    {
        $bookings = Booking::with(['child.user', 'service', 'schedule', 'vaccine'])
            ->whereHas('schedule', function ($query) {
                $query->where('midwife_id', Auth::id())
                    ->whereDate('date', '>=', now()->toDateString());
            })
            ->whereIn('status', ['pending', 'paid', 'confirmed'])
            ->whereNotNull('vaccine_id')
            ->whereHas('service', function ($query) {
                $query->where('type', 'immunization')
                    ->orWhere('tag', 'like', '%imunisasi%');
            })
            ->whereDoesntHave('immunization')
            ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->orderBy('schedules.date')
            ->orderBy('schedules.start_time')
            ->select('bookings.*')
            ->get();

        $availableSchedules = Schedule::query()
            ->withCount([
                'bookings as active_bookings_count' => function ($query) {
                    $query->where('status', '!=', 'canceled');
                },
            ])
            ->where('midwife_id', Auth::id())
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('pages.immunizations.create', compact('bookings', 'availableSchedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'immunization_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::with(['schedule'])
            ->where('id', $validated['booking_id'])
            ->whereHas('schedule', function ($query) {
                $query->where('midwife_id', Auth::id());
            })
            ->whereIn('status', ['pending', 'paid', 'confirmed'])
            ->whereNotNull('vaccine_id')
            ->whereDoesntHave('immunization')
            ->firstOrFail();

        $immunizedAt = Carbon::parse($booking->schedule->date->toDateString() . ' ' . $validated['immunization_time']);

        Immunization::create([
            'booking_id' => $booking->id,
            'child_id'   => $booking->child_id,
            'vaccine_id' => $booking->vaccine_id,
            'midwife_id' => Auth::id(),
            'date'       => $booking->schedule->date,
            'immunized_at' => $immunizedAt,
            'notes'      => $validated['notes'] ?? null,
        ]);

        $booking->update([
            'status' => 'completed',
        ]);

        return redirect('/immunization')->with('success', 'Data imunisasi berhasil disimpan.');
    }

    public function reschedule(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'new_schedule_id' => 'required|exists:schedules,id',
            'reschedule_note' => 'nullable|string|max:500',
        ]);

        $booking->load('schedule');

        abort_unless($booking->schedule && (int) $booking->schedule->midwife_id === (int) Auth::id(), 403);

        if (in_array($booking->status, ['completed', 'canceled', 'declined'], true)) {
            return back()->withErrors([
                'reschedule' => 'Booking dengan status ini tidak dapat dijadwal ulang.',
            ]);
        }

        if ($booking->immunization()->exists()) {
            return back()->withErrors([
                'reschedule' => 'Booking yang sudah diinput imunisasi tidak dapat dijadwal ulang.',
            ]);
        }

        $newSchedule = Schedule::query()
            ->withCount([
                'bookings as active_bookings_count' => function ($query) {
                    $query->where('status', '!=', 'canceled');
                },
            ])
            ->where('id', $validated['new_schedule_id'])
            ->where('midwife_id', Auth::id())
            ->whereDate('date', '>=', now()->toDateString())
            ->firstOrFail();

        $activeBookingCount = (int) $newSchedule->active_bookings_count;
        $isMovingToSameSchedule = (int) $booking->schedule_id === (int) $newSchedule->id;
        $effectiveCount = $isMovingToSameSchedule ? max(0, $activeBookingCount - 1) : $activeBookingCount;

        if ($effectiveCount >= (int) $newSchedule->quota) {
            return back()->withErrors([
                'reschedule' => 'Jadwal baru sudah penuh. Silakan pilih jadwal lain.',
            ]);
        }

        $systemNote = '[Reschedule] ' . now()->format('d/m/Y H:i') . ' - ' . ($validated['reschedule_note'] ?: 'Jadwal dipindahkan oleh bidan.');

        $existingNote = trim((string) $booking->midwife_notes);
        $combinedNote = $existingNote !== ''
            ? $existingNote . PHP_EOL . $systemNote
            : $systemNote;

        $booking->update([
            'schedule_id' => $newSchedule->id,
            'midwife_notes' => $combinedNote,
            'status' => in_array($booking->status, ['paid', 'confirmed'], true) ? $booking->status : 'confirmed',
        ]);

        return back()->with('success', 'Jadwal booking berhasil diubah.');
    }
}
