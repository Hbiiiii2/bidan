@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <section class="relative overflow-hidden rounded-3xl border border-rose-200 bg-gradient-to-br from-rose-600 via-fuchsia-600 to-amber-500 p-6 text-white shadow-xl sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/20 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-20 left-8 h-52 w-52 rounded-full bg-amber-200/30 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="inline-flex rounded-full border border-white/25 bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]">Admin Dashboard</p>
                <h1 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Panel Monitoring Kebidan-Yuk</h1>
                <p class="mt-2 max-w-2xl text-sm text-white/90 sm:text-base">Pantau performa operasional, aktivitas booking, dan progres imunisasi dalam satu panel terpadu.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="/admin/services" class="inline-flex items-center rounded-full border border-white/40 bg-white/20 px-4 py-2 text-sm font-bold text-white backdrop-blur transition hover:bg-white/30">Kelola Layanan</a>
                <a href="/admin/midwives" class="inline-flex items-center rounded-full border border-white/40 bg-white px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-50">Kelola Bidan</a>
                <a href="/admin/midwives/schedules" class="inline-flex items-center rounded-full border border-white/40 bg-white/20 px-4 py-2 text-sm font-bold text-white backdrop-blur transition hover:bg-white/30">Lihat Jadwal Bidan</a>
                <a href="/admin/parents" class="inline-flex items-center rounded-full border border-white/40 bg-white px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-50">Kelola Parent</a>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">Total Parent</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $summary['total_parents'] }}</p>
            <p class="mt-1 text-sm text-slate-500">Akun orang tua terdaftar.</p>
        </article>

        <article class="rounded-2xl border border-indigo-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Total Bidan</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $summary['total_midwives'] }}</p>
            <p class="mt-1 text-sm text-slate-500">Tenaga bidan aktif di sistem.</p>
        </article>

        <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Anak Terdaftar</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $summary['total_children'] }}</p>
            <p class="mt-1 text-sm text-slate-500">Data anak yang sudah masuk.</p>
        </article>

        <article class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Total Layanan</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $summary['total_services'] }}</p>
            <p class="mt-1 text-sm text-slate-500">Layanan yang tersedia.</p>
        </article>
    </section>

    <section class="mt-4 grid gap-4 lg:grid-cols-3">
        <article class="rounded-2xl border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Booking Pending</p>
                    <p class="mt-2 text-3xl font-extrabold text-amber-700">{{ $summary['pending_bookings'] }}</p>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">Perlu tindak lanjut</span>
            </div>
        </article>

        <article class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Booking Paid</p>
                    <p class="mt-2 text-3xl font-extrabold text-emerald-700">{{ $summary['paid_bookings'] }}</p>
                </div>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Siap diproses</span>
            </div>
        </article>

        <article class="rounded-2xl border border-fuchsia-100 bg-gradient-to-br from-fuchsia-50 to-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-fuchsia-700">Total Imunisasi</p>
                    <p class="mt-2 text-3xl font-extrabold text-fuchsia-700">{{ $summary['total_immunizations'] }}</p>
                </div>
                <span class="rounded-full bg-fuchsia-100 px-3 py-1 text-xs font-bold text-fuchsia-700">Data tercatat</span>
            </div>
        </article>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900 sm:text-lg">Booking Terbaru</h2>
                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">{{ $recentBookings->count() }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 sm:px-5">Parent</th>
                            <th class="px-4 py-3 sm:px-5">Anak</th>
                            <th class="px-4 py-3 sm:px-5">Layanan</th>
                            <th class="px-4 py-3 sm:px-5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($recentBookings as $booking)
                            @php
                                $statusClass = match($booking->status) {
                                    'paid' => 'bg-emerald-100 text-emerald-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'confirmed' => 'bg-blue-100 text-blue-700',
                                    'declined' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-4 py-3 sm:px-5">{{ $booking->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900 sm:px-5">{{ $booking->child->name ?? '-' }}</td>
                                <td class="px-4 py-3 sm:px-5">{{ $booking->service->name ?? '-' }}</td>
                                <td class="px-4 py-3 sm:px-5">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center sm:px-5">
                                    <p class="text-sm font-medium text-slate-600">Belum ada data booking.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900 sm:text-lg">Imunisasi Terbaru</h2>
                <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600">{{ $recentImmunizations->count() }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 sm:px-5">Tanggal</th>
                            <th class="px-4 py-3 sm:px-5">Anak</th>
                            <th class="px-4 py-3 sm:px-5">Vaksin</th>
                            <th class="px-4 py-3 sm:px-5">Bidan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($recentImmunizations as $item)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-4 py-3 whitespace-nowrap sm:px-5">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900 sm:px-5">{{ $item->child->name ?? '-' }}</td>
                                <td class="px-4 py-3 sm:px-5">{{ $item->vaccine->name ?? '-' }}</td>
                                <td class="px-4 py-3 sm:px-5">{{ $item->midwife->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center sm:px-5">
                                    <p class="text-sm font-medium text-slate-600">Belum ada data imunisasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@endsection
