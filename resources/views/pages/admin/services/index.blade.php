@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900">Kelola Layanan</h1>
            <p class="text-gray-600 mt-1">Admin dapat menambah, mengubah, dan menghapus layanan yang bisa dibeli user.</p>
        </div>
        <a href="/admin/services/create" class="rounded-full bg-[#b80049] px-5 py-2.5 text-white font-bold">+ Tambah Layanan</a>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-sm text-gray-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Tag</th>
                    <th class="px-4 py-3">Bidan</th>
                    <th class="px-4 py-3">Periode & Jam</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr class="border-t align-top">
                        <td class="px-4 py-3">
                            <p class="font-bold text-gray-900">{{ $service->name }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $service->description ?: '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-pink-100 px-3 py-1 text-xs font-bold text-pink-700">{{ $service->tag }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            @if($service->midwives->count() > 0)
                                <div class="space-y-1">
                                    @foreach($service->midwives as $midwife)
                                        <p>
                                            {{ $midwife->name }}
                                            <span class="text-xs text-gray-500">(kuota: {{ $midwife->pivot->max_daily_quota ?? 10 }}/hari)</span>
                                        </p>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <p>
                                {{ optional($service->available_from_date ?? $service->available_date)->format('d/m/y') ?? '-' }}
                                -
                                {{ optional($service->available_until_date ?? $service->available_date)->format('d/m/y') ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $service->available_start_time ? substr($service->available_start_time, 0, 5) : '-' }}
                                -
                                {{ $service->available_end_time ? substr($service->available_end_time, 0, 5) : '-' }}
                            </p>
                        </td>
                        <td class="px-4 py-3 font-bold text-[#b80049]">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="/admin/services/{{ $service->id }}/edit" class="text-indigo-600 font-semibold">Edit</a>
                                <form method="POST" action="/admin/services/{{ $service->id }}" onsubmit="return confirm('Hapus layanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 font-semibold">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada layanan. Tambahkan layanan baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
