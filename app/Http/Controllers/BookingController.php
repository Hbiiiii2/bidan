<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Child;
use App\Mail\BookingCreatedMail;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Vaccine;
use App\Services\ServiceScheduleSynchronizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['child', 'service', 'schedule', 'transaction'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pages.booking.index', compact('bookings'));
    }

    public function create($serviceId, ServiceScheduleSynchronizer $scheduleSynchronizer)
    {
        $service = $this->findAvailableServiceOrFail($serviceId);
        $service->load('midwives');
        $scheduleSynchronizer->sync($service);

        $children = Child::where('user_id', Auth::id())->orderBy('name')->get();
        $isImmunization = $service->type === 'immunization' || str_contains(strtolower($service->tag), 'imunisasi');

        $serviceMidwifeIds = $service->midwives->pluck('id')->toArray();
        $midwifeLimitMap = $service->midwives
            ->mapWithKeys(fn ($midwife) => [
                $midwife->id => max(1, (int) ($midwife->pivot->max_daily_quota ?? ServiceScheduleSynchronizer::DEFAULT_MIDWIFE_DAILY_QUOTA)),
            ])
            ->all();

        $schedules = collect();
        if (!empty($serviceMidwifeIds)) {
            $schedules = Schedule::with('midwife')
                ->withCount([
                    'bookings as bookings_count' => function ($query) {
                        $query->where('status', '!=', 'canceled');
                    },
                ])
                ->whereIn('midwife_id', $serviceMidwifeIds)
                ->whereDate('date', '>=', now())
                ->when($service->available_from_date, function ($query) use ($service) {
                    $query->whereDate('date', '>=', $service->available_from_date);
                })
                ->when($service->available_until_date, function ($query) use ($service) {
                    $query->whereDate('date', '<=', $service->available_until_date);
                })
                ->when($service->available_start_time, function ($query) use ($service) {
                    $query->whereTime('start_time', '>=', $service->available_start_time);
                })
                ->when($service->available_end_time, function ($query) use ($service) {
                    $query->whereTime('end_time', '<=', $service->available_end_time);
                })
                ->orderBy('date')
                ->orderBy('start_time')
                ->get()
                ->map(function ($schedule) use ($isImmunization, $midwifeLimitMap) {
                $remaining = max(0, $schedule->quota - $schedule->bookings_count);
                $schedule->remaining = $remaining;

                if ($isImmunization) {
                    $dailyLimit = $midwifeLimitMap[$schedule->midwife_id] ?? ServiceScheduleSynchronizer::DEFAULT_MIDWIFE_DAILY_QUOTA;
                    $dailyCount = $this->midwifeDailyImmunizationCount($schedule->midwife_id, $schedule->date->toDateString());
                    $schedule->daily_limit = $dailyLimit;
                    $schedule->daily_immunization_count = $dailyCount;
                    $schedule->daily_remaining = max(0, $dailyLimit - $dailyCount);
                    $schedule->available_slots = min($remaining, $schedule->daily_remaining);
                } else {
                    $schedule->daily_limit = $remaining;
                    $schedule->daily_immunization_count = 0;
                    $schedule->daily_remaining = $remaining;
                    $schedule->available_slots = $remaining;
                }

                return $schedule;
            });
        }

        $vaccines = $isImmunization ? Vaccine::all() : collect();

        return view('pages.booking.create', compact('service', 'children', 'schedules', 'vaccines'));
    }

    public function store(Request $request, ServiceScheduleSynchronizer $scheduleSynchronizer)
    {
        $rules = [
            'child_id'    => 'required|exists:children,id',
            'service_id'  => 'required|exists:services,id',
            'booking_date' => 'required|date',
            'notes'       => 'nullable|string|max:500',
        ];

        $validated = $request->validate($rules, [
            'booking_date.required' => 'Silakan pilih tanggal booking terlebih dahulu.',
            'booking_date.date' => 'Format tanggal booking tidak valid.',
        ]);

        $child = Child::where('id', $validated['child_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $service = $this->findAvailableServiceOrFail($validated['service_id']);
        $service->load('midwives');
        $scheduleSynchronizer->sync($service);
        $isImmunization = $service->type === 'immunization' || str_contains(strtolower($service->tag), 'imunisasi');

        if ($isImmunization) {
            $validated = array_merge($validated, $request->validate([
                'vaccine_id' => 'required|exists:vaccines,id',
            ]));
        }

        $bookingDate = Carbon::parse($validated['booking_date'])->toDateString();
        if ($bookingDate < now()->toDateString()) {
            return back()->withErrors([
                'booking_date' => 'Tanggal booking tidak boleh di masa lalu.',
            ])->withInput();
        }

        $serviceFromDate = $service->available_from_date ? Carbon::parse($service->available_from_date)->toDateString() : null;
        $serviceUntilDate = $service->available_until_date ? Carbon::parse($service->available_until_date)->toDateString() : null;

        if ($serviceFromDate && $bookingDate < $serviceFromDate) {
            return back()->withErrors([
                'booking_date' => 'Tanggal di luar rentang layanan.',
            ])->withInput();
        }

        if ($serviceUntilDate && $bookingDate > $serviceUntilDate) {
            return back()->withErrors([
                'booking_date' => 'Tanggal di luar rentang layanan.',
            ])->withInput();
        }

        $schedule = $this->pickScheduleForDate($service, $bookingDate, $isImmunization, $scheduleSynchronizer);

        if (!$schedule) {
            return back()->withErrors([
                'booking_date' => 'Tidak ada kuota tersedia pada tanggal yang dipilih. Silakan pilih tanggal lain.',
            ])->withInput();
        }

        if ($service->available_start_time && $schedule->start_time < $service->available_start_time) {
            return back()->withErrors([
                'booking_date' => 'Jadwal bidan tidak berada dalam jam layanan.',
            ])->withInput();
        }

        if ($service->available_end_time && $schedule->end_time > $service->available_end_time) {
            return back()->withErrors([
                'booking_date' => 'Jadwal bidan tidak berada dalam jam layanan.',
            ])->withInput();
        }

        if ($schedule->bookings_count >= $schedule->quota) {
            return back()->withErrors([
                'booking_date' => 'Kuota pada tanggal yang dipilih sudah penuh. Silakan pilih tanggal lain.',
            ])->withInput();
        }

        if ($isImmunization) {
            $dailyImmunizationCount = $this->midwifeDailyImmunizationCount($schedule->midwife_id, $schedule->date->toDateString());
            $dailyLimit = $scheduleSynchronizer->resolveMidwifeDailyQuota($service, (int) $schedule->midwife_id);

            if ($dailyImmunizationCount >= $dailyLimit) {
                return back()->withErrors([
                    'booking_date' => 'Kuota imunisasi harian sudah penuh pada tanggal ini. Silakan pilih tanggal lain.',
                ])->withInput();
            }
        }

        $booking = Booking::create([
            'user_id'     => Auth::id(),
            'child_id'    => $child->id,
            'service_id'  => $validated['service_id'],
            'schedule_id' => $schedule->id,
            'vaccine_id'  => $validated['vaccine_id'] ?? null,
            'status'      => 'pending',
            'notes'       => $validated['notes'] ?? null,
        ]);

        $booking->load(['user', 'child', 'service', 'schedule.midwife', 'transaction']);

        if ($booking->user?->email) {
            Mail::to($booking->user->email)->send(new BookingCreatedMail($booking));
        }

        return redirect('/bookings')->with('success', 'Booking berhasil dibuat. Silakan lanjutkan ke pembayaran.');
    }

    public function cancel($bookingId)
    {
        $booking = Booking::with('transaction')
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if (in_array($booking->status, ['paid', 'completed', 'canceled', 'declined'], true)) {
            return redirect('/bookings')->with('error', 'Booking ini tidak bisa dibatalkan.');
        }

        if ($booking->transaction && $booking->transaction->status === 'settlement') {
            return redirect('/bookings')->with('error', 'Pembayaran sudah berhasil, booking tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'canceled']);

        if ($booking->transaction && $booking->transaction->status !== 'settlement') {
            $booking->transaction->update([
                'status' => 'failed',
                'paid_at' => null,
            ]);
        }

        return redirect('/bookings')->with('success', 'Booking berhasil dibatalkan.');
    }

    private function midwifeDailyImmunizationCount(int $midwifeId, string $date): int
    {
        return Booking::whereHas('schedule', function ($query) use ($midwifeId, $date) {
                $query->where('midwife_id', $midwifeId)
                    ->whereDate('date', $date);
            })
            ->whereHas('service', function ($query) {
                $query->where('type', 'immunization')
                    ->orWhere('tag', 'like', '%imunisasi%');
            })
            ->where('status', '!=', 'canceled')
            ->count();
    }

    private function pickScheduleForDate(Service $service, string $bookingDate, bool $isImmunization, ServiceScheduleSynchronizer $scheduleSynchronizer): ?Schedule
    {
        $serviceMidwifeIds = $service->midwives->pluck('id')->toArray();
        if (empty($serviceMidwifeIds)) {
            return null;
        }

        $midwifeLimitMap = $service->midwives
            ->mapWithKeys(fn ($midwife) => [
                $midwife->id => max(1, (int) ($midwife->pivot->max_daily_quota ?? ServiceScheduleSynchronizer::DEFAULT_MIDWIFE_DAILY_QUOTA)),
            ])
            ->all();

        $candidates = Schedule::query()
            ->withCount([
                'bookings as bookings_count' => function ($query) {
                    $query->where('status', '!=', 'canceled');
                },
            ])
            ->whereIn('midwife_id', $serviceMidwifeIds)
            ->whereDate('date', $bookingDate)
            ->when($service->available_start_time, function ($query) use ($service) {
                $query->whereTime('start_time', '>=', $service->available_start_time);
            })
            ->when($service->available_end_time, function ($query) use ($service) {
                $query->whereTime('end_time', '<=', $service->available_end_time);
            })
            ->orderBy('start_time')
            ->get()
            ->map(function ($schedule) use ($isImmunization, $midwifeLimitMap) {
                $remaining = max(0, $schedule->quota - $schedule->bookings_count);

                if ($isImmunization) {
                    $dailyLimit = $midwifeLimitMap[$schedule->midwife_id] ?? ServiceScheduleSynchronizer::DEFAULT_MIDWIFE_DAILY_QUOTA;
                    $dailyCount = $this->midwifeDailyImmunizationCount((int) $schedule->midwife_id, $schedule->date->toDateString());
                    $schedule->available_slots = min($remaining, max(0, $dailyLimit - $dailyCount));
                } else {
                    $schedule->available_slots = $remaining;
                }

                return $schedule;
            })
            ->filter(fn ($schedule) => $schedule->available_slots > 0)
            ->sort(function ($a, $b) {
                if ($a->available_slots !== $b->available_slots) {
                    return $b->available_slots <=> $a->available_slots;
                }

                if ($a->bookings_count !== $b->bookings_count) {
                    return $a->bookings_count <=> $b->bookings_count;
                }

                return strcmp((string) $a->start_time, (string) $b->start_time);
            })
            ->values();

        return $candidates->first();
    }

    private function findAvailableServiceOrFail(int|string $serviceId): Service
    {
        $today = now()->toDateString();

        return Service::query()
            ->whereKey($serviceId)
            ->where(function ($query) use ($today) {
                $query->where(function ($rangeQuery) use ($today) {
                    $rangeQuery->whereNotNull('available_from_date')
                        ->whereNotNull('available_until_date')
                        ->whereDate('available_from_date', '<=', $today)
                        ->whereDate('available_until_date', '>=', $today);
                })->orWhere(function ($legacyQuery) use ($today) {
                    $legacyQuery->whereNull('available_from_date')
                        ->whereNull('available_until_date')
                        ->where(function ($dateQuery) use ($today) {
                            $dateQuery->whereNull('available_date')
                                ->orWhereDate('available_date', '<=', $today);
                        });
                });
            })
            ->firstOrFail();
    }
}
