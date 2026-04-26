@extends('layouts.app')

@section('title', 'Kelola User Parent')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6 overflow-hidden rounded-3xl border border-indigo-100 bg-gradient-to-r from-indigo-50 via-white to-sky-50 shadow-sm">
        <div class="flex flex-col gap-4 px-5 py-6 sm:px-8 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-1 inline-flex items-center rounded-full border border-indigo-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600">Panel Admin</p>
                <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Kelola User Parent</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600 sm:text-base">Admin dapat mencari user parent, reset password, atau menghapus akun yang sudah tidak digunakan.</p>
            </div>
            <a href="/admin" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Dashboard</a>
        </div>
    </div>

    <form method="GET" action="/admin/parents" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari User Parent</label>
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari nama atau email" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Cari</button>
                <a href="/admin/parents" class="inline-flex rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
            <h2 class="text-base font-bold text-slate-900 sm:text-lg">Daftar User Parent</h2>
            <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">{{ $parents->count() }} user</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 sm:px-6">Nama</th>
                        <th class="px-4 py-3 sm:px-6">Email</th>
                        <th class="px-4 py-3 sm:px-6">Data Anak</th>
                        <th class="px-4 py-3 sm:px-6">Terdaftar</th>
                        <th class="px-4 py-3 sm:px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($parents as $parent)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-semibold text-slate-900 sm:px-6">{{ $parent->name }}</td>
                            <td class="px-4 py-3 sm:px-6">{{ $parent->email }}</td>
                            <td class="px-4 py-3 sm:px-6">{{ $parent->children_count }}</td>
                            <td class="px-4 py-3 whitespace-nowrap sm:px-6">{{ $parent->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 sm:px-6">
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="/admin/parents/{{ $parent->id }}/reset-password" onsubmit="return confirm('Reset password untuk user ini?');">
                                        @csrf
                                        <button type="submit" class="inline-flex rounded-full bg-amber-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-600">Reset Password</button>
                                    </form>

                                    <form method="POST" action="/admin/parents/{{ $parent->id }}" onsubmit="return confirm('Yakin ingin menghapus user parent ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex rounded-full bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-700">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center sm:px-6">
                                <p class="text-sm font-medium text-slate-600">Data user parent tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
