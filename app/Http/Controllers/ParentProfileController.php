<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ParentProfileController extends Controller
{
    public function show()
    {
        $parent = Auth::user();

        return view('pages.parent.profile', compact('parent'));
    }

    public function update(Request $request)
    {
        $parent = Auth::user();

        $validated = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('profile_photo')) {
            $storedName = ltrim((string) $parent->profile_photo, '/');
            $publicPhotoPath = str_starts_with($storedName, 'profile_photos/') ? $storedName : 'profile_photos/' . $storedName;
            $legacyPrivatePath = 'public/' . $publicPhotoPath;

            if ($parent->profile_photo && Storage::disk('public')->exists($publicPhotoPath)) {
                Storage::disk('public')->delete($publicPhotoPath);
            } elseif ($parent->profile_photo && Storage::disk('local')->exists($legacyPrivatePath)) {
                Storage::disk('local')->delete($legacyPrivatePath);
            }

            $fileName = time() . '_' . $parent->id . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->storeAs('profile_photos', $fileName, 'public');
            $parent->profile_photo = $fileName;
        }

        $parent->phone_number = $validated['phone_number'] ?? null;
        $parent->address = $validated['address'] ?? null;
        $parent->save();

        return back()->with('success', 'Profil parent berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $parent = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        if (! Hash::check($validated['current_password'], (string) $parent->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $parent->password = $validated['new_password'];
        $parent->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
