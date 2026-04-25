<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminController extends Controller
{
    public function index()
    {
        $summary = [
            'total_parents'       => User::whereHas('roles', function (Builder $query) {
                $query->where('name', 'parent');
            })->count(),
            'total_midwives'      => User::whereHas('roles', function (Builder $query) {
                $query->where('name', 'midwife');
            })->count(),
            'total_children'      => Child::count(),
            'total_services'      => Service::count(),
            'pending_bookings'    => Booking::where('status', 'pending')->count(),
            'paid_bookings'       => Booking::where('status', 'paid')->count(),
            'total_immunizations' => Immunization::count(),
        ];

        $recentBookings = Booking::with(['user', 'child', 'service'])
            ->latest()
            ->take(8)
            ->get();

        $recentImmunizations = Immunization::with(['child', 'vaccine', 'midwife'])
            ->latest('date')
            ->take(8)
            ->get();

        return view('pages.admin.dashboard', compact('summary', 'recentBookings', 'recentImmunizations'));
    }
}
