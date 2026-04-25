<?php
namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->where(function ($query) {
                $today = now()->toDateString();

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
            ->orderByRaw('COALESCE(available_from_date, available_date) asc')
            ->orderBy('name')
            ->get();

        return view('pages.services.index', compact('services'));
    }
}
