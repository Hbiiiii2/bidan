@extends('layouts.app')

@section('title', 'Jadwal Bidan')

@section('content')

@php
    $dayLabels = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu',
    ];
@endphp

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6 overflow-hidden rounded-3xl border border-indigo-100 bg-gradient-to-r from-indigo-50 via-white to-cyan-50 shadow-sm">
        <div class="flex flex-col gap-4 px-5 py-6 sm:px-8 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="mb-1 inline-flex items-center rounded-full border border-indigo-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600">Panel Admin</p>
                <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Jadwal Bidan</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600 sm:text-base">Lihat hari aktif, jam praktik, dan jadwal mendatang setiap bidan.</p>
            </div>
            <a href="/admin/midwives" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Kelola Bidan</a>
        </div>
    </div>

    <form method="GET" action="/admin/midwives/schedules" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari Bidan</label>
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari nama, email, atau NIP" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-indigo-400 focus:outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Cari</button>
                <a href="/admin/midwives/schedules" class="inline-flex rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </div>
    </form>

    <div class="space-y-5">
        @forelse($midwives as $midwife)
            @php
                $availableDays = collect($midwife->available_days ?? [])->map(fn($day) => $dayLabels[$day] ?? ucfirst($day));
                $workTime = $midwife->available_start_time && $midwife->available_end_time
                    ? substr($midwife->available_start_time, 0, 5) . ' - ' . substr($midwife->available_end_time, 0, 5)
                    : null;
            @endphp

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $midwife->name }}</h2>
                            <p class="text-sm text-slate-600">{{ $midwife->email }} @if($midwife->nip) • NIP: {{ $midwife->nip }} @endif</p>
                        </div>
                        <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $midwife->upcoming_schedules_count }} jadwal mendatang</span>
                    </div>
                </div>

                <div class="grid gap-4 p-4 sm:p-6 lg:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 lg:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hari Praktik</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @if($availableDays->isNotEmpty())
                                @foreach($availableDays as $label)
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $label }}</span>
                                @endforeach
                            @else
                                <span class="text-sm text-slate-500">Belum diatur</span>
                            @endif
                        </div>

                        <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Jam Praktik</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ $workTime ?? 'Belum diatur' }}</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-0 lg:col-span-2 overflow-hidden">
                        <div class="border-b border-slate-200 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-800">Jadwal Mendatang</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left">
                                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-4 py-2.5">Hari</th>
                                        <th class="px-4 py-2.5">Tanggal</th>
                                        <th class="px-4 py-2.5">Jam</th>
                                        <th class="px-4 py-2.5">Kuota</th>
                                        <th class="px-4 py-2.5">Terisi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                                    @forelse($midwife->schedules as $schedule)
                                        @php
                                            $dayKey = strtolower($schedule->date->englishDayOfWeek);
                                            $dayName = $dayLabels[$dayKey] ?? $schedule->date->translatedFormat('l');
                                        @endphp
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="px-4 py-2.5">{{ $dayName }}</td>
                                            <td class="px-4 py-2.5 whitespace-nowrap">{{ $schedule->date->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2.5 whitespace-nowrap">{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td>
                                            <td class="px-4 py-2.5">{{ $schedule->quota }}</td>
                                            <td class="px-4 py-2.5">{{ $schedule->active_bookings_count }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada jadwal mendatang.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
                <p class="text-base font-semibold text-slate-700">Data bidan tidak ditemukan.</p>
                <p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci pencarian atau tambahkan bidan baru.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
