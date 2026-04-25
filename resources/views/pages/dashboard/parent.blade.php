@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <p class="text-sm uppercase tracking-[0.2em] font-bold text-pink-600 opacity-80">Welcome Back</p>
        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mt-2">Halo, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-gray-600 mt-2">Pantau kesehatan anak dan kelola booking layanan bidan.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid gap-6 md:grid-cols-4 mb-8">
        <div class="rounded-2xl bg-gradient-to-br from-pink-50 to-pink-100 p-6 ring-1 ring-pink-200">
            <p class="text-sm font-semibold text-pink-600 uppercase tracking-[0.1em]\">Anak</p>
            <p class="text-3xl font-black text-pink-700 mt-2">{{ auth()->user()->children_count ?? 0 }}</p>
            <p class="text-sm text-pink-600 mt-2">Terdaftar</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 p-6 ring-1 ring-blue-200">
            <p class="text-sm font-semibold text-blue-600 uppercase tracking-[0.1em]\">Booking Aktif</p>
            <p class="text-3xl font-black text-blue-700 mt-2">{{ $activeBookings ?? 0 }}</p>
            <p class="text-sm text-blue-600 mt-2">Menunggu pembayaran</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-green-50 to-green-100 p-6 ring-1 ring-green-200">
            <p class="text-sm font-semibold text-green-600 uppercase tracking-[0.1em]\">Terkonfirmasi</p>
            <p class="text-3xl font-black text-green-700 mt-2">{{ $paidBookings ?? 0 }}</p>
            <p class="text-sm text-green-600 mt-2\">Sudah dibayar</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 p-6 ring-1 ring-purple-200">
            <p class="text-sm font-semibold text-purple-600 uppercase tracking-[0.1em]\">Imunisasi</p>
            <p class="text-3xl font-black text-purple-700 mt-2">{{ $totalImmunizations ?? 0 }}</p>
            <p class="text-sm text-purple-600 mt-2\">Tercatat</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 md:grid-cols-3 mb-8">
        <a href="/children/create" class="rounded-2xl bg-pink-600 text-white p-6 hover:bg-pink-700 transition ring-1 ring-pink-700/50">
            <p class="text-xl font-bold">+ Tambah Data Anak</p>
            <p class="text-sm text-pink-100 mt-1">Daftarkan anak Anda</p>
        </a>
        <a href="/services" class="rounded-2xl bg-blue-600 text-white p-6 hover:bg-blue-700 transition ring-1 ring-blue-700/50">
            <p class="text-xl font-bold">Booking Layanan</p>
            <p class="text-sm text-blue-100 mt-1">Lihat layanan yang tersedia</p>
        </a>
        <a href="/bookings" class="rounded-2xl bg-purple-600 text-white p-6 hover:bg-purple-700 transition ring-1 ring-purple-700/50">
            <p class="text-xl font-bold">Riwayat Booking</p>
            <p class="text-sm text-purple-100 mt-1">Kelola booking Anda</p>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Recent Bookings -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Booking Terbaru</h2>
            <div class="space-y-3">
                @forelse($recentBookings ?? [] as $booking)
                    <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100 hover:shadow-lg transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">{{ $booking->service->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $booking->child->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d M Y') }} • {{ substr($booking->schedule->start_time, 0, 5) }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold
                                @if($booking->status === 'paid') bg-green-100 text-green-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                @if($booking->status === 'paid') ✓ Dibayar @else ⏱ Pending @endif
                            </span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-100">
                        <p class="text-gray-600 font-semibold">Belum ada booking</p>
                        <a href="/services" class="text-pink-600 font-semibold mt-2 inline-block">Buat booking sekarang →</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Data Anak Overview -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Data Anak</h2>
            <div class="space-y-3">
                @forelse($children ?? [] as $child)
                    <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100 hover:shadow-lg transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">{{ $child->name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $child->gender === 'male' ? '👦 Laki-laki' : '👧 Perempuan' }} • {{ $child->birth_date->age }} tahun</p>
                                <p class="text-xs text-gray-500 mt-1\">{{ $child->immunizations_count }} imunisasi tercatat</p>
                            </div>
                            <a href="/child/{{ $child->id }}/status" class="text-pink-600 font-semibold text-sm hover:text-pink-700">Cek →</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-100">
                        <p class="text-gray-600 font-semibold">Belum ada data anak</p>
                        <a href="/children/create" class="text-pink-600 font-semibold mt-2 inline-block">Tambah anak sekarang →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
