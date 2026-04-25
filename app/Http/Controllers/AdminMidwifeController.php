<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminMidwifeController extends Controller
{
    public function index()
    {
        $midwives = User::role('midwife')->orderBy('name')->get();

        return view('pages.admin.midwives.index', compact('midwives'));
    }

    public function create()
    {
        Role::firstOrCreate(['name' => 'midwife']);

        return view('pages.admin.midwives.create');
    }

    public function store(Request $request)
    {
        Role::firstOrCreate(['name' => 'midwife']);

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'nip'                   => 'nullable|string|max:50|unique:users,nip',
            'password'              => 'required|string|min:8|confirmed',
            'hospital_institution'  => 'nullable|string|max:255',
            'address'               => 'nullable|string|max:500',
            'career_start_year'     => 'nullable|integer|min:1900|max:' . now()->year,
            'available_days'        => 'nullable|array',
            'available_days.*'      => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'available_start_time'  => 'nullable|date_format:H:i',
            'available_end_time'    => 'nullable|date_format:H:i|after:available_start_time',
        ]);

        $midwife = User::create([
            'name'                  => $validated['name'],
            'email'                 => $validated['email'],
            'nip'                   => $validated['nip'] ?? null,
            'password'              => $validated['password'],
            'role'                  => 'midwife',
            'hospital_institution'  => $validated['hospital_institution'] ?? null,
            'address'               => $validated['address'] ?? null,
            'career_start_year'     => $validated['career_start_year'] ?? null,
            'available_days'        => $validated['available_days'] ?? null,
            'available_start_time'  => $validated['available_start_time'] ?? null,
            'available_end_time'    => $validated['available_end_time'] ?? null,
        ]);

        $midwife->assignRole('midwife');

        return redirect('/admin/midwives')->with('success', 'Bidan berhasil ditambahkan.');
    }

    public function edit(User $midwife)
    {
        if (! $midwife->hasRole('midwife')) {
            abort(404);
        }

        return view('pages.admin.midwives.edit', compact('midwife'));
    }

    public function update(Request $request, User $midwife)
    {
        if (! $midwife->hasRole('midwife')) {
            abort(404);
        }

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $midwife->id,
            'nip'                   => 'nullable|string|max:50|unique:users,nip,' . $midwife->id,
            'password'              => 'nullable|string|min:8|confirmed',
            'hospital_institution'  => 'nullable|string|max:255',
            'address'               => 'nullable|string|max:500',
            'career_start_year'     => 'nullable|integer|min:1900|max:' . now()->year,
            'available_days'        => 'nullable|array',
            'available_days.*'      => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'available_start_time'  => 'nullable|date_format:H:i',
            'available_end_time'    => 'nullable|date_format:H:i|after:available_start_time',
        ]);

        $midwife->name = $validated['name'];
        $midwife->email = $validated['email'];
        $midwife->nip = $validated['nip'] ?? null;
        $midwife->role = 'midwife';
        $midwife->hospital_institution = $validated['hospital_institution'] ?? null;
        $midwife->address = $validated['address'] ?? null;
        $midwife->career_start_year = $validated['career_start_year'] ?? null;
        $midwife->available_days = $validated['available_days'] ?? null;
        $midwife->available_start_time = $validated['available_start_time'] ?? null;
        $midwife->available_end_time = $validated['available_end_time'] ?? null;

        if (! empty($validated['password'])) {
            $midwife->password = $validated['password'];
        }

        $midwife->save();
        $midwife->assignRole('midwife');

        return redirect('/admin/midwives')->with('success', 'Data bidan berhasil diperbarui.');
    }

    public function destroy(User $midwife)
    {
        if (! $midwife->hasRole('midwife')) {
            abort(404);
        }

        $midwife->delete();

        return redirect('/admin/midwives')->with('success', 'Bidan berhasil dihapus.');
    }
}
