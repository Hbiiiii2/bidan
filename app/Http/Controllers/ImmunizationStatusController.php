<?php
namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImmunizationStatusController extends Controller
{
    public function show($childId)
    {
        $child = Child::with('immunizations.vaccine')
            ->where('user_id', Auth::id())
            ->findOrFail($childId);

        $allVaccines = Vaccine::all();

        $doneVaccineIds = $child->immunizations->pluck('vaccine_id')->toArray();

        $status = $allVaccines->map(function ($vaccine) use ($doneVaccineIds) {
            return [
                'vaccine' => $vaccine->name,
                'status'  => in_array($vaccine->id, $doneVaccineIds)
                    ? 'done'
                    : 'pending',
            ];
        });

        $pendingVaccines = $allVaccines->whereNotIn('id', $doneVaccineIds);

        return view('pages.immunizations.status', compact('child', 'status', 'pendingVaccines'));
    }

    public function store(Request $request, $childId)
    {
        $child = Child::where('user_id', Auth::id())
            ->findOrFail($childId);

        $validated = $request->validate([
            'vaccine_ids' => 'required|array|min:1',
            'vaccine_ids.*' => 'exists:vaccines,id',
        ]);

        $doneVaccineIds = $child->immunizations->pluck('vaccine_id')->toArray();
        $newVaccineIds = array_diff($validated['vaccine_ids'], $doneVaccineIds);

        foreach ($newVaccineIds as $vaccineId) {
            $child->immunizations()->create([
                'vaccine_id' => $vaccineId,
                'midwife_id' => null,
                'date' => now(),
                'notes' => 'Vaksinasi sebelumnya dilakukan di luar aplikasi.',
            ]);
        }

        return back()->with('success', 'Riwayat vaksinasi luar berhasil disimpan.');
    }
}
