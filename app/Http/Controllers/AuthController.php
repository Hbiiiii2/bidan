<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Role::firstOrCreate(['name' => 'parent']);
        $user->assignRole('parent');

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice')->with('success', 'Registrasi berhasil. Silakan verifikasi email Anda terlebih dahulu.');
    }

    // PROSES LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            // 🔥 REDIRECT BERDASARKAN ROLE (SPATIE)
            if ($user instanceof User && $user->hasRole('admin')) {
                return redirect('/admin');
            }

            if ($user instanceof User && $user->hasRole('midwife')) {
                return redirect('/dashboard');
            }

            if ($user instanceof User && $user->hasRole('parent') && ! $user->hasVerifiedEmail()) {
                $intendedUrl = $request->session()->get('url.intended');

                if (is_string($intendedUrl) && str_contains($intendedUrl, '/email/verify/')) {
                    return redirect()->to($intendedUrl);
                }

                $user->sendEmailVerificationNotification();

                return redirect()->route('verification.notice')->with('success', 'Akun Anda belum aktif. Link verifikasi sudah dikirim ulang ke email Anda.');
            }

            return redirect('/parent/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
