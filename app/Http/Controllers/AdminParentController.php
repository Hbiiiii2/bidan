<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminParentController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        $parents = User::role('parent')
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('name', 'like', '%' . $query . '%')
                        ->orWhere('email', 'like', '%' . $query . '%');
                });
            })
            ->withCount('children')
            ->orderBy('name')
            ->get();

        return view('pages.admin.parents.index', [
            'parents' => $parents,
            'query' => $query,
        ]);
    }

    public function resetPassword(Request $request, User $parent)
    {
        if (! $parent->hasRole('parent')) {
            abort(404);
        }

        $newPassword = Str::password(10, true, true, false, false);

        $parent->update([
            'password' => $newPassword,
        ]);

        return back()->with('success', 'Password user ' . $parent->name . ' berhasil direset. Password sementara: ' . $newPassword);
    }

    public function destroy(User $parent)
    {
        if (! $parent->hasRole('parent')) {
            abort(404);
        }

        $parent->delete();

        return back()->with('success', 'User parent berhasil dihapus.');
    }
}
