<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get stats
        $activeBookings = $user->bookings()
            ->where('status', 'pending')
            ->count();
        
        $paidBookings = $user->bookings()
            ->where('status', 'paid')
            ->count();
        
        $totalImmunizations = $user->immunizations()->count();
        
        // Get recent bookings (limit 3)
        $recentBookings = $user->bookings()
            ->with(['child', 'service', 'schedule.midwife'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        // Get children
        $children = $user->children()
            ->withCount('immunizations')
            ->get();
        
        return view('pages.dashboard.parent', [
            'activeBookings' => $activeBookings,
            'paidBookings' => $paidBookings,
            'totalImmunizations' => $totalImmunizations,
            'recentBookings' => $recentBookings,
            'children' => $children,
        ]);
    }
}
