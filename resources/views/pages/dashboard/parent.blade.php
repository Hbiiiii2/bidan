@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <section class="mb-6 overflow-hidden rounded-3xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-white to-rose-50 shadow-sm">
        <div class="flex flex-col gap-4 px-5 py-6 sm:px-8 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="mb-1 inline-flex rounded-full border border-cyan-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-700">Dashboard Parent</p>
                <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Halo, {{ auth()->user()->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600 sm:text-base">Pantau jadwal layanan anak, progres imunisasi, dan kelengkapan profil keluarga dalam satu halaman.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/services" class="inline-flex rounded-full bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Booking Layanan</a>
                <a href="/parent/profile" class="inline-flex rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Profil Parent</a>
            </div>
        </div>
    </section>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Jumlah Anak</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $childrenCount }}</p>
            <p class="mt-1 text-sm text-slate-500">Anak terdaftar di akun ini.</p>
        </article>
        <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Booking Pending</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $activeBookings }}</p>
            <p class="mt-1 text-sm text-slate-500">Menunggu pembayaran.</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Booking Dibayar</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $paidBookings }}</p>
            <p class="mt-1 text-sm text-slate-500">Siap diproses bidan.</p>
        </article>
        <article class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Total Imunisasi</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalImmunizations }}</p>
            <p class="mt-1 text-sm text-slate-500">Tindakan imunisasi tercatat.</p>
        </article>
    </section>

    <section class="mb-6 grid gap-4 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-bold text-slate-900">Jadwal Terdekat</h2>
            @if($nextBooking)
                <div class="mt-4 rounded-xl border border-cyan-100 bg-cyan-50 p-4">
                    <p class="text-sm font-semibold text-cyan-700">{{ $nextBooking->service->name }}</p>
                    <p class="mt-1 text-lg font-bold text-slate-900">{{ $nextBooking->child->name }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ \Carbon\Carbon::parse($nextBooking->schedule->date)->format('d/m/Y') }} • {{ substr($nextBooking->schedule->start_time, 0, 5) }} - {{ substr($nextBooking->schedule->end_time, 0, 5) }}</p>
                    <p class="mt-1 text-sm text-slate-600">Bidan: {{ $nextBooking->schedule->midwife->name ?? '-' }}</p>
                </div>
            @else
                <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada jadwal terdekat. Silakan booking layanan terlebih dahulu.
                </div>
            @endif

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jadwal Mendatang</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $upcomingBookings }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Booking Selesai</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $completedBookings }}</p>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Status Keluarga</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700">Cakupan Imunisasi Anak</span>
                        <span class="font-bold text-cyan-700">{{ $immunizationCoverage }}%</span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-cyan-500" style="width: {{ $immunizationCoverage }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $childrenWithImmunization }} dari {{ $childrenCount }} anak sudah punya catatan imunisasi.</p>
                </div>

                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700">Kelengkapan Profil Parent</span>
                        <span class="font-bold text-rose-700">{{ $profileCompletion }}%</span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-rose-500" style="width: {{ $profileCompletion }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Lengkapi foto, telepon, dan alamat untuk hasil terbaik.</p>
                </div>

                <a href="/parent/profile" class="inline-flex rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Lengkapi Profil</a>
            </div>
        </article>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Booking Terbaru</h2>
            </div>
            <div class="space-y-3 p-4 sm:p-5">
                @forelse($recentBookings as $booking)
                    @php
                        $statusClass = match($booking->status) {
                            'paid' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-slate-200 text-slate-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <article class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-900">{{ $booking->service->name ?? '-' }}</p>
                                <p class="text-sm text-slate-600">{{ $booking->child->name ?? '-' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d/m/Y') }} • {{ substr($booking->schedule->start_time, 0, 5) }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada booking terbaru.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Data Anak</h2>
            </div>
            <div class="space-y-3 p-4 sm:p-5">
                @forelse($children as $child)
                    <article class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-900">{{ $child->name }}</p>
                                <p class="text-sm text-slate-600">
                                    {{ $child->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                    @if($child->birth_date)
                                        • {{ $child->birth_date->age }} tahun
                                    @endif
                                </p>
                                <p class="mt-1 text-xs text-slate-500">{{ $child->immunizations_count }} catatan imunisasi</p>
                            </div>
                            <a href="/child/{{ $child->id }}/status" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">Lihat</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        Belum ada data anak. Tambahkan anak untuk mulai tracking imunisasi.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

@endsection
