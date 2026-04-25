<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MidwifeProfileController extends Controller
{
    public function show()
    {
        $midwife = Auth::user();
        return view('pages.midwife.profile', compact('midwife'));
    }

    public function update(Request $request)
    {
        $midwife = Auth::user();

        $validated = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $storedName = ltrim((string) $midwife->profile_photo, '/');
            $publicPhotoPath = str_starts_with($storedName, 'profile_photos/') ? $storedName : 'profile_photos/' . $storedName;
            $legacyPrivatePath = 'public/' . $publicPhotoPath;

            // Delete old photo if exists
            if ($midwife->profile_photo && Storage::disk('public')->exists($publicPhotoPath)) {
                Storage::disk('public')->delete($publicPhotoPath);
            } elseif ($midwife->profile_photo && Storage::disk('local')->exists($legacyPrivatePath)) {
                Storage::disk('local')->delete($legacyPrivatePath);
            }

            // Store new photo
            $fileName = time() . '_' . $midwife->id . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->storeAs('profile_photos', $fileName, 'public');
            $midwife->profile_photo = $fileName;
        }

        $midwife->save();

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui.');
    }
}
