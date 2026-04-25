@extends('layouts.app')

@section('title', 'Kelola Bidan')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900">Kelola Bidan</h1>
            <p class="text-gray-600 mt-1">Tambahkan, edit, atau hapus bidan yang tersedia di sistem.</p>
        </div>
        <a href="/admin/midwives/create" class="rounded-full bg-[#b80049] px-5 py-2.5 text-white font-bold">+ Tambah Bidan</a>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-sm text-gray-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">Terdaftar</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($midwives as $midwife)
                    <tr class="border-t align-top">
                        <td class="px-4 py-3">
                            <p class="font-bold text-gray-900">{{ $midwife->name }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $midwife->email }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $midwife->nip ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $midwife->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="/admin/midwives/{{ $midwife->id }}/edit" class="text-indigo-600 font-semibold">Edit</a>
                            <form method="POST" action="/admin/midwives/{{ $midwife->id }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus bidan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada bidan terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection