@extends('layouts.app')

@section('content')

<div class="relative overflow-hidden bg-[#f7f2f4]">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-64 bg-[radial-gradient(circle_at_15%_20%,rgba(184,0,73,0.16),transparent_45%),radial-gradient(circle_at_85%_0%,rgba(226,22,95,0.14),transparent_40%)]"></div>

    <div class="relative mx-auto max-w-7xl px-6 py-8 md:py-10">
        <section class="overflow-hidden rounded-[30px] border border-white/40 bg-gradient-to-br from-[#a50042] via-[#b80049] to-[#ea2c72] p-7 text-white shadow-[0_24px_60px_-30px_rgba(184,0,73,0.9)] md:p-10">
            <div class="grid gap-8 lg:grid-cols-[1.35fr_0.95fr] lg:items-end">
                <div>
                    <p class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em]">Parent Area</p>
                    <h1 class="mt-4 text-3xl font-black leading-tight md:text-5xl">Layanan Kesehatan Anak, Lebih Cepat Dipilih</h1>
                    <p class="mt-4 max-w-2xl text-sm text-white/90 md:text-base">
                        Pilih layanan, lihat profil bidan, lalu lanjut booking dalam satu alur yang ringkas dan jelas.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="/bookings" class="inline-flex items-center rounded-full bg-white px-5 py-2.5 text-[#b80049] font-bold shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-50">
                            📋 Riwayat Booking
                        </a>
                        <a href="/children" class="inline-flex items-center rounded-full border border-white/70 px-5 py-2.5 font-bold text-white transition hover:bg-white/15">
                            👨‍👩‍👦 Kelola Data Anak
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/25 bg-white/12 p-5 backdrop-blur-sm md:p-6">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-white/75">Ringkasan</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="rounded-2xl bg-white/15 p-4">
                            <p class="text-[11px] uppercase tracking-[0.14em] text-white/75">Total Layanan</p>
                            <p class="mt-1 text-2xl font-black">{{ $services->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 p-4">
                            <p class="text-[11px] uppercase tracking-[0.14em] text-white/75">Bidan Aktif</p>
                            <p class="mt-1 text-2xl font-black">{{ $services->flatMap(fn ($service) => $service->midwives)->unique('id')->count() }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/15 p-4">
                            <p class="text-[11px] uppercase tracking-[0.14em] text-white/75">Mulai Dari</p>
                            <p class="mt-1 text-2xl font-black">Rp {{ number_format((int) $services->min('price'), 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-9 space-y-10">
        @forelse($services as $service)
            @php
                $typeLabel = $service->tag ?: ($service->type === 'immunization' ? 'Imunisasi' : 'Konsultasi');
                $typeEmoji = $service->type === 'immunization' ? '💉' : '👨‍⚕️';
                $typeColor = $service->type === 'immunization' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700';
            @endphp

            <div class="rounded-[26px] border border-rose-100/70 bg-white/85 p-5 shadow-[0_16px_40px_-30px_rgba(0,0,0,0.35)] backdrop-blur-sm md:p-7">
                <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $typeColor }}">{{ $typeEmoji }} {{ $typeLabel }}</span>
                        <h2 class="mt-3 text-2xl font-black text-gray-900 md:text-3xl">{{ $service->name }}</h2>
                        <p class="mt-2 max-w-3xl text-sm text-gray-600 md:text-base">{{ $service->description ?: 'Layanan profesional untuk mendukung kesehatan ibu dan anak.' }}</p>
                    </div>

                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-rose-500">Biaya Layanan</p>
                        <p class="mt-1 text-2xl font-black text-[#b80049]">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if($service->midwives->count() > 0)
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($service->midwives as $midwife)
                            <article class="group rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-100/40">
                                <div class="flex flex-col h-full">
                                    <div class="mb-4 flex items-start gap-3">
                                        @if($midwife->profile_photo_url)
                                            <div class="h-16 w-16 overflow-hidden rounded-full bg-gray-100 ring-2 ring-rose-100">
                                                <img src="{{ $midwife->profile_photo_url }}" alt="{{ $midwife->name }}" class="h-full w-full object-cover">
                                            </div>
                                        @else
                                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-rose-100 ring-2 ring-rose-200">
                                                <span class="text-xl font-black text-rose-600">{{ substr($midwife->name, 0, 1) }}</span>
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <h3 class="truncate text-lg font-black text-gray-900">{{ $midwife->name }}</h3>
                                            <p class="mt-1 truncate text-sm text-gray-500">{{ $midwife->hospital_institution ?? 'Bidan Profesional' }}</p>
                                        </div>
                                    </div>

                                    <div class="space-y-2 text-sm text-gray-600">
                                        @if($midwife->career_start_year)
                                            <div class="flex items-center gap-2">
                                                <span>📚</span>
                                                <span>Pengalaman {{ $midwife->experience }} tahun</span>
                                            </div>
                                        @endif
                                        @if($midwife->available_start_time && $midwife->available_end_time)
                                            <div class="flex items-center gap-2">
                                                <span>🕒</span>
                                                <span>{{ substr($midwife->available_start_time, 0, 5) }} - {{ substr($midwife->available_end_time, 0, 5) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 rounded-xl bg-rose-50 px-3 py-2">
                                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-rose-500">Biaya</p>
                                        <p class="mt-1 text-xl font-black text-[#b80049]">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                    </div>

                                    <a
                                        href="/booking/{{ $service->id }}"
                                        class="mt-4 block w-full rounded-xl bg-[#b80049] px-4 py-3 text-center text-sm font-bold text-white transition duration-200 hover:bg-[#e2165f]"
                                    >
                                        Pilih & Jadwalkan
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center">
                        <p class="font-semibold text-amber-700">Layanan ini belum memiliki bidan yang ditugaskan.</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-3xl border border-rose-100 bg-white p-12 text-center shadow-sm">
                <p class="text-xl font-black text-gray-800">Belum ada layanan tersedia</p>
                <p class="mt-2 text-gray-500">Silakan hubungi admin untuk menambahkan layanan baru.</p>
            </div>
        @endforelse
        </section>
    </div>
</div>

@endsection
