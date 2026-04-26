<?php

namespace App\Http\Controllers;

use App\Models\Immunization;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $children = $user->children()
            ->withCount('immunizations')
            ->orderBy('name')
            ->get();

        $childrenCount = $children->count();
        $childIds = $children->pluck('id');

        $activeBookings = $user->bookings()
            ->where('status', 'pending')
            ->count();

        $paidBookings = $user->bookings()
            ->where('status', 'paid')
            ->count();

        $upcomingBookings = $user->bookings()
            ->whereIn('status', ['pending', 'paid', 'confirmed'])
            ->whereHas('schedule', function ($query) {
                $query->whereDate('date', '>=', now()->toDateString());
            })
            ->count();

        $completedBookings = $user->bookings()
            ->where('status', 'completed')
            ->count();

        $totalImmunizations = $childIds->isEmpty()
            ? 0
            : Immunization::whereIn('child_id', $childIds)->count();

        $childrenWithImmunization = $children->where('immunizations_count', '>', 0)->count();
        $immunizationCoverage = $childrenCount > 0
            ? (int) round(($childrenWithImmunization / $childrenCount) * 100)
            : 0;

        $profileCompletionTotal = 3;
        $profileCompletionFilled = collect([
            $user->profile_photo,
            $user->phone_number,
            $user->address,
        ])->filter(fn ($value) => filled($value))->count();
        $profileCompletion = (int) round(($profileCompletionFilled / $profileCompletionTotal) * 100);

        $recentBookings = $user->bookings()
            ->with(['child', 'service', 'schedule.midwife'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $nextBooking = $user->bookings()
            ->with(['child', 'service', 'schedule.midwife'])
            ->whereIn('status', ['pending', 'paid', 'confirmed'])
            ->whereHas('schedule', function ($query) {
                $query->whereDate('date', '>=', now()->toDateString());
            })
            ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->orderBy('schedules.date')
            ->orderBy('schedules.start_time')
            ->select('bookings.*')
            ->first();
        
        return view('pages.dashboard.parent', [
            'childrenCount' => $childrenCount,
            'activeBookings' => $activeBookings,
            'paidBookings' => $paidBookings,
            'upcomingBookings' => $upcomingBookings,
            'completedBookings' => $completedBookings,
            'totalImmunizations' => $totalImmunizations,
            'childrenWithImmunization' => $childrenWithImmunization,
            'immunizationCoverage' => $immunizationCoverage,
            'profileCompletion' => $profileCompletion,
            'recentBookings' => $recentBookings,
            'nextBooking' => $nextBooking,
            'children' => $children,
        ]);
    }
}
