@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8">
    <section class="rounded-2xl bg-gradient-to-r from-[#b80049] to-[#e2165f] text-white p-8 shadow-lg">
        <p class="text-sm uppercase tracking-[0.2em] font-bold opacity-80">Admin Dashboard</p>
        <h1 class="text-3xl md:text-4xl font-black mt-2">Monitoring Kebidan-Yuk</h1>
        <p class="mt-3 text-white/90">Pantau statistik utama dan aktivitas terbaru sistem secara ringkas.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="/admin/services" class="inline-flex items-center rounded-full bg-white px-5 py-2.5 font-bold text-[#b80049]">
                Kelola Layanan
            </a>
            <a href="/admin/midwives" class="inline-flex items-center rounded-full bg-white px-5 py-2.5 font-bold text-[#b80049]">
                Kelola Bidan
            </a>
        </div>
    </section>

    <section class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Parent</p>
            <p class="mt-2 text-3xl font-black text-[#b80049]">{{ $summary['total_parents'] }}</p>
        </article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Bidan</p>
            <p class="mt-2 text-3xl font-black text-[#b80049]">{{ $summary['total_midwives'] }}</p>
        </article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Anak Terdaftar</p>
            <p class="mt-2 text-3xl font-black text-[#b80049]">{{ $summary['total_children'] }}</p>
        </article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Total Layanan</p>
            <p class="mt-2 text-3xl font-black text-[#b80049]">{{ $summary['total_services'] }}</p>
        </article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:col-span-2">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Booking Pending</p>
            <p class="mt-2 text-3xl font-black text-amber-600">{{ $summary['pending_bookings'] }}</p>
        </article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:col-span-2">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Booking Paid</p>
            <p class="mt-2 text-3xl font-black text-green-600">{{ $summary['paid_bookings'] }}</p>
        </article>
        <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 sm:col-span-2 lg:col-span-4">
            <p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Total Imunisasi Tercatat</p>
            <p class="mt-2 text-3xl font-black text-[#b80049]">{{ $summary['total_immunizations'] }}</p>
        </article>
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-lg">Booking Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-sm text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Parent</th>
                            <th class="px-4 py-3">Anak</th>
                            <th class="px-4 py-3">Layanan</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ $booking->user->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $booking->child->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $booking->service->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($booking->status === 'paid')
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">Paid</span>
                                    @elseif($booking->status === 'pending')
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">Pending</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada data booking.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-lg">Imunisasi Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-sm text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Anak</th>
                            <th class="px-4 py-3">Vaksin</th>
                            <th class="px-4 py-3">Bidan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentImmunizations as $item)
                            <tr class="border-t">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->date)->format('d/m/y') }}</td>
                                <td class="px-4 py-3">{{ $item->child->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->vaccine->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->midwife->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada data imunisasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@endsection
