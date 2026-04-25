<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vaccine;

class HomeController extends Controller
{
    public function index()
    {
        // 🔹 Ambil beberapa layanan (buat ditampilkan di home)
        $services = Service::limit(3)->get();

        // 🔹 Hitung total vaksin (buat value display)
        $totalVaccines = Vaccine::count();

        return view('pages.home.index', [
            'services'      => $services,
            'totalVaccines' => $totalVaccines,
        ]);
    }
}
