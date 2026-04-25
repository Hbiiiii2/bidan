<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MidwifeController extends Controller
{
    public function dashboard()
    {
        // Ambil bookings yang schedule nya midwife_id adalah user login
        $bookings = Booking::with(['child', 'service', 'schedule'])
            ->whereHas('schedule', function ($query) {
                $query->where('midwife_id', Auth::id())
                      ->whereDate('date', '>=', now());
            })
            ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->orderBy('schedules.date')
            ->orderBy('schedules.start_time')
            ->select('bookings.*')
            ->limit(10) // Limit untuk dashboard
            ->get();

        // Statistik
        $todayCount = $bookings->filter(function ($booking) {
            return $booking->schedule->date->isToday();
        })->count();

        $weekCount = $bookings->filter(function ($booking) {
            return $booking->schedule->date->isCurrentWeek();
        })->count();

        $totalCount = $bookings->count();

        return view('pages.dashboard.midwife', compact('bookings', 'todayCount', 'weekCount', 'totalCount'));
    }

    public function calendar()
    {
        $monthParam = request('month');

        try {
            $activeMonth = $monthParam
                ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth()
                : now()->startOfMonth();
        } catch (\Throwable $e) {
            $activeMonth = now()->startOfMonth();
        }

        $monthStart = $activeMonth->copy()->startOfMonth();
        $monthEnd = $activeMonth->copy()->endOfMonth();

        $bookings = Booking::with(['child.user', 'user', 'service', 'schedule'])
            ->whereHas('schedule', function ($query) use ($monthStart, $monthEnd) {
                $query->where('midwife_id', Auth::id())
                    ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
            })
            ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->orderBy('schedules.date')
            ->orderBy('schedules.start_time')
            ->select('bookings.*')
            ->get();

        $bookingsByDate = $bookings->groupBy(function ($booking) {
            return $booking->schedule->date->toDateString();
        });

        $selectedDateParam = request('date');
        try {
            $selectedDate = $selectedDateParam
                ? Carbon::parse($selectedDateParam)
                : ($monthStart->isSameMonth(now()) ? now()->startOfDay() : $monthStart->copy());
        } catch (\Throwable $e) {
            $selectedDate = $monthStart->isSameMonth(now()) ? now()->startOfDay() : $monthStart->copy();
        }

        if (!$selectedDate->isSameMonth($activeMonth)) {
            $selectedDate = $monthStart->copy();
        }

        $selectedBookings = $bookingsByDate->get($selectedDate->toDateString(), collect());

        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $calendarDays = [];

        for ($date = $calendarStart->copy(); $date->lte($calendarEnd); $date->addDay()) {
            $dateKey = $date->toDateString();
            $calendarDays[] = [
                'date' => $date->copy(),
                'dateKey' => $dateKey,
                'inMonth' => $date->isSameMonth($activeMonth),
                'isToday' => $date->isToday(),
                'isSelected' => $date->isSameDay($selectedDate),
                'bookingCount' => $bookingsByDate->get($dateKey, collect())->count(),
            ];
        }

        $statsBookings = Booking::with('schedule')
            ->whereHas('schedule', function ($query) {
                $query->where('midwife_id', Auth::id())
                    ->whereDate('date', '>=', now());
            })
            ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->orderBy('schedules.date')
            ->orderBy('schedules.start_time')
            ->select('bookings.*')
            ->get();

        $todayCount = $statsBookings->filter(function ($booking) {
            return $booking->schedule->date->isToday();
        })->count();

        $weekCount = $statsBookings->filter(function ($booking) {
            return $booking->schedule->date->isCurrentWeek();
        })->count();

        $totalCount = $statsBookings->count();

        return view('pages.midwife.calendar', compact(
            'calendarDays',
            'selectedDate',
            'selectedBookings',
            'activeMonth',
            'todayCount',
            'weekCount',
            'totalCount'
        ));
    }

    public function respond(Request $request, $bookingId)
    {
        $booking = Booking::with('schedule')->whereHas('schedule', function ($query) {
            $query->where('midwife_id', Auth::id());
        })->findOrFail($bookingId);

        $validated = $request->validate([
            'action' => 'required|in:approve,decline',
            'midwife_notes' => 'nullable|string|max:500',
        ]);

        $status = $validated['action'] === 'approve' ? 'confirmed' : 'declined';

        $booking->update([
            'status' => $status,
            'midwife_notes' => $validated['midwife_notes'] ?? null,
        ]);

        return back()->with('success', 'Request booking telah ' . ($status === 'confirmed' ? 'diterima' : 'ditolak') . '.');
    }
}
