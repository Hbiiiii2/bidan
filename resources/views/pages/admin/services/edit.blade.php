@extends('layouts.app')

@section('title', 'Edit Layanan')

@section('content')

<div class="max-w-3xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-black text-gray-900">Edit Layanan</h1>
    <p class="text-gray-600 mt-1">Perbarui data layanan yang dapat dibeli oleh user.</p>

    <form method="POST" action="/admin/services/{{ $service->id }}" class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Layanan</label>
            <input type="text" name="name" value="{{ old('name', $service->name) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tag</label>
            <input type="text" name="tag" value="{{ old('tag', $service->tag) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
            <textarea name="description" rows="4" class="w-full rounded-xl border border-gray-200 px-3 py-2.5">{{ old('description', $service->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Bidan Penanggung Jawab</label>
            <div class="grid gap-2">
                @foreach($midwives as $midwife)
                    @php
                        $selectedMidwifeIds = old('midwife_ids', $service->midwives->pluck('id')->toArray());
                        $existingQuota = optional($service->midwives->firstWhere('id', $midwife->id)?->pivot)->max_daily_quota;
                    @endphp
                    <div class="flex items-center gap-3 rounded-xl border border-gray-200 px-3 py-2">
                        <label class="flex min-w-0 flex-1 items-center gap-2">
                            <input type="checkbox" name="midwife_ids[]" value="{{ $midwife->id }}" @checked(in_array($midwife->id, $selectedMidwifeIds)) class="rounded border-gray-300 text-pink-600">
                            <span class="text-sm">{{ $midwife->name }}</span>
                        </label>
                        <div class="w-36">
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-gray-500">Kuota Max/Hari</label>
                            <input type="number" name="midwife_quotas[{{ $midwife->id }}]" min="1" max="200" value="{{ old('midwife_quotas.' . $midwife->id, $existingQuota ?: 10) }}" class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm">
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="mt-2 text-xs text-gray-500">Pilih satu atau lebih bidan dan tentukan kuota maksimal pasien per hari untuk layanan ini.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tersedia Dari Tanggal</label>
                <input type="date" name="available_from_date" value="{{ old('available_from_date', optional($service->available_from_date ?? $service->available_date)->format('Y-m-d')) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
                <p class="mt-1 text-xs text-gray-500">Format tampilan: dd/mm/yy.</p>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="available_until_date" value="{{ old('available_until_date', optional($service->available_until_date ?? $service->available_date)->format('Y-m-d')) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
                <p class="mt-1 text-xs text-gray-500">Format tampilan: dd/mm/yy.</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Jam Mulai Layanan</label>
                <input type="time" name="available_start_time" value="{{ old('available_start_time', $service->available_start_time ? substr($service->available_start_time, 0, 5) : '08:00') }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Jam Selesai Layanan</label>
                <input type="time" name="available_end_time" value="{{ old('available_end_time', $service->available_end_time ? substr($service->available_end_time, 0, 5) : '16:00') }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Harga</label>
            <input type="number" name="price" min="0" step="1000" value="{{ old('price', (int) $service->price) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
        </div>

        <div class="flex items-center gap-3">
            <button class="rounded-full bg-[#b80049] px-5 py-2.5 text-sm font-bold text-white">Update Layanan</button>
            <a href="/admin/services" class="text-gray-600 font-semibold">Batal</a>
        </div>
    </form>
</div>

@endsection
