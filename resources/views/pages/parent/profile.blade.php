@extends('layouts.app')

@section('title', 'Profil Parent')

@section('content')

<div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6 overflow-hidden rounded-3xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-white to-rose-50 shadow-sm">
        <div class="flex flex-col gap-4 px-5 py-6 sm:px-8 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-1 inline-flex items-center rounded-full border border-cyan-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-700">Profil Parent</p>
                <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Pengaturan Profil</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600 sm:text-base">Lengkapi data pribadi, unggah foto profil, dan atur keamanan akun.</p>
            </div>
            <a href="/parent/dashboard" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Dashboard</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold text-slate-900">Detail Profil</h2>
            <p class="mt-1 text-sm text-slate-500">Perbarui foto profil, nomor telepon, dan alamat tempat tinggal.</p>

            <div class="mt-5 flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="h-20 w-20 overflow-hidden rounded-full bg-slate-200">
                    @if($parent->profile_photo_url)
                        <img src="{{ $parent->profile_photo_url }}" alt="Foto Profil" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-slate-400">{{ strtoupper(substr($parent->name, 0, 1)) }}</div>
                    @endif
                </div>
                <div>
                    <p class="font-bold text-slate-900">{{ $parent->name }}</p>
                    <p class="text-sm text-slate-500">{{ $parent->email }}</p>
                </div>
            </div>

            <form method="POST" action="/parent/profile" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Foto Profil</label>
                    <input type="file" name="profile_photo" accept="image/*" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-rose-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-rose-700 hover:file:bg-rose-100">
                    <p class="mt-1 text-xs text-slate-500">Format JPG, PNG, GIF. Maksimal 2MB.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Nomor Telepon</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $parent->phone_number) }}" placeholder="Contoh: 081234567890" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Alamat Tempat Tinggal</label>
                    <textarea name="address" rows="4" placeholder="Masukkan alamat lengkap" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none">{{ old('address', $parent->address) }}</textarea>
                </div>

                <button type="submit" class="inline-flex rounded-full bg-rose-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-rose-700">Simpan Profil</button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-bold text-slate-900">Ganti Password</h2>
            <p class="mt-1 text-sm text-slate-500">Gunakan password kuat agar akun tetap aman.</p>

            <form method="POST" action="/parent/profile/password" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Password Saat Ini</label>
                    <input type="password" name="current_password" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none" required>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Password Baru</label>
                    <input type="password" name="new_password" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none" required>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-cyan-500 focus:outline-none" required>
                </div>

                <ul class="rounded-lg bg-slate-50 px-4 py-3 text-xs text-slate-500">
                    <li>Minimal 8 karakter.</li>
                    <li>Harus berbeda dari password saat ini.</li>
                </ul>

                <button type="submit" class="inline-flex rounded-full bg-slate-800 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-900">Perbarui Password</button>
            </form>
        </section>
    </div>
</div>

@endsection
