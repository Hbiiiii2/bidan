<?php
namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $children = Child::withCount('immunizations')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pages.children.index', compact('children'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vaccines = Vaccine::all();

        return view('pages.children.create', compact('vaccines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required',
            'birth_date'  => 'required|date',
            'gender'      => 'required',
            'vaccine_ids' => 'nullable|array',
            'vaccine_ids.*' => 'exists:vaccines,id',
        ]);

        $child = Child::create([
            'user_id'    => Auth::id(),
            'name'       => $validated['name'],
            'birth_date' => $validated['birth_date'],
            'gender'     => $validated['gender'],
        ]);

        if (!empty($validated['vaccine_ids'])) {
            foreach ($validated['vaccine_ids'] as $vaccineId) {
                $child->immunizations()->create([
                    'vaccine_id' => $vaccineId,
                    'midwife_id' => null,
                    'date'       => now(),
                    'notes'      => 'Vaksinasi sebelumnya dilakukan di luar aplikasi.',
                ]);
            }
        }

        return redirect('/children')->with('success', 'Data anak berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $child = Child::where('user_id', Auth::id())->findOrFail($id);

        return view('pages.children.edit', compact('child'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $child = Child::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required',
            'birth_date' => 'required|date',
            'gender'     => 'required|in:male,female',
        ]);

        $child->update($validated);

        return redirect('/children')->with('success', 'Data anak berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $child = Child::where('user_id', Auth::id())->findOrFail($id);

        $child->delete();

        return redirect('/children')->with('success', 'Data anak berhasil dihapus');
    }
}
