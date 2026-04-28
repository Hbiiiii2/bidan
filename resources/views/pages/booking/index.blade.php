@extends('layouts.app')

@section('content')

<div class="relative overflow-hidden bg-[#f7f2f4]">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-56 bg-[radial-gradient(circle_at_20%_10%,rgba(184,0,73,0.14),transparent_40%),radial-gradient(circle_at_88%_5%,rgba(226,22,95,0.12),transparent_35%)]"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-8 md:py-10">
        <section class="rounded-[28px] border border-white/50 bg-gradient-to-br from-[#a50042] via-[#b80049] to-[#e2165f] p-7 text-white shadow-[0_24px_60px_-30px_rgba(184,0,73,0.9)] md:p-9">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em]">Parent Area</p>
                    <h1 class="mt-4 text-3xl font-black leading-tight md:text-4xl">Riwayat Booking Anda</h1>
                    <p class="mt-2 max-w-2xl text-sm text-white/90 md:text-base">Pantau status booking, cek jadwal, dan lanjutkan pembayaran dari satu halaman.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <div class="rounded-2xl bg-white/15 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-white/75">Total Booking</p>
                        <p class="mt-1 text-2xl font-black">{{ $bookings->count() }}</p>
                    </div>
                    <a href="/services" class="inline-flex items-center rounded-full bg-white px-5 py-2.5 font-bold text-[#b80049] transition hover:-translate-y-0.5 hover:bg-rose-50">
                        + Booking Baru
                    </a>
                </div>
            </div>
        </section>

        <div class="mt-6 space-y-3">
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 p-4 text-green-700">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        @if($bookings->count() > 0)
            <section class="mt-7 space-y-4">
                @foreach($bookings as $booking)
                    @php
                        $statusMeta = match($booking->status) {
                            'paid' => ['label' => 'Dibayar', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'emoji' => '✓'],
                            'pending' => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-700 border-amber-200', 'emoji' => '⏱'],
                            'confirmed' => ['label' => 'Siap Dibayar', 'class' => 'bg-sky-100 text-sky-700 border-sky-200', 'emoji' => '📌'],
                            'declined' => ['label' => 'Ditolak Bidan', 'class' => 'bg-red-100 text-red-700 border-red-200', 'emoji' => '✕'],
                            default => ['label' => ucfirst($booking->status), 'class' => 'bg-gray-100 text-gray-700 border-gray-200', 'emoji' => '•'],
                        };

                        $paidAt = $booking->transaction?->paid_at
                            ? \Carbon\Carbon::parse($booking->transaction->paid_at)
                            : null;
                    @endphp

                    <article class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg md:p-6">
                        <div class="grid gap-4 border-b border-gray-100 pb-5 md:grid-cols-[1.2fr_1fr_auto] md:items-center">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">Anak</p>
                                <p class="mt-1 truncate text-lg font-black text-gray-900">{{ $booking->child->name }}</p>
                                <p class="mt-1 truncate text-sm text-gray-600">{{ $booking->service->name }}</p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">Jadwal</p>
                                <p class="mt-1 text-sm font-bold text-gray-900 md:text-base">{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d M Y') }}</p>
                                <p class="text-sm text-gray-600">{{ substr($booking->schedule->start_time, 0, 5) }} - {{ substr($booking->schedule->end_time, 0, 5) }} WIB</p>
                            </div>

                            <div class="md:text-right">
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-bold {{ $statusMeta['class'] }}">
                                    <span>{{ $statusMeta['emoji'] }}</span>
                                    <span>{{ $statusMeta['label'] }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">Bidan</p>
                                    <p class="mt-1 font-semibold text-gray-900">{{ $booking->schedule->midwife->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">Harga Layanan</p>
                                    <p class="mt-1 text-2xl font-black text-[#b80049]">Rp {{ number_format($booking->service->price, 0, ',', '.') }}</p>
                                </div>
                                @if($booking->status === 'paid')
                                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5">
                                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700">Waktu Pembayaran</p>
                                        @if($paidAt)
                                            <p class="mt-1 text-sm font-bold text-emerald-800">{{ $paidAt->format('d M Y') }} • {{ $paidAt->format('H:i') }} WIB</p>
                                        @else
                                            <p class="mt-1 text-sm font-bold text-emerald-800">Pembayaran berhasil (waktu belum tercatat)</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="w-full md:w-56">
                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <div class="space-y-2">
                                        <a href="/checkout/{{ $booking->id }}" class="block w-full rounded-xl bg-[#b80049] px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-[#e2165f]">
                                            Lanjut Bayar ->
                                        </a>
                                        <form method="POST" action="{{ route('booking.cancel', $booking->id) }}" onsubmit="return confirm('Yakin ingin membatalkan booking ini?');">
                                            @csrf
                                            <button type="submit" class="block w-full rounded-xl bg-gray-100 px-4 py-3 text-center text-sm font-bold text-gray-700 transition hover:bg-gray-200">
                                                Batalkan Booking
                                            </button>
                                        </form>
                                    </div>
                                @elseif($booking->status === 'declined')
                                    <div class="rounded-xl bg-red-100 px-4 py-3 text-center text-sm font-bold text-red-700">
                                        Ditolak Bidan
                                    </div>
                                @else
                                    <div class="rounded-xl bg-emerald-100 px-4 py-3 text-center text-sm font-bold text-emerald-700">
                                        Selesai
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <div class="mt-7 rounded-3xl border border-rose-100 bg-white p-12 text-center shadow-sm">
                <p class="text-xl font-black text-gray-800">Belum ada booking</p>
                <p class="mt-2 text-gray-500">Mulai booking layanan bidan untuk jadwal kontrol anak Anda.</p>
                <a href="/services" class="mt-6 inline-flex items-center rounded-full bg-[#b80049] px-6 py-3 text-white font-bold transition hover:bg-[#e2165f]">
                    Jelajahi Layanan
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
