@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
	<div class="mb-6 overflow-hidden rounded-3xl border border-rose-100 bg-gradient-to-r from-rose-50 via-white to-amber-50 shadow-sm">
		<div class="flex flex-col gap-4 px-5 py-6 sm:px-8 md:flex-row md:items-center md:justify-between">
			<div>
				<p class="mb-1 inline-flex items-center rounded-full border border-rose-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-rose-600">Area Bidan</p>
				<h2 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Dashboard Bidan</h2>
				<p class="mt-2 max-w-2xl text-sm text-slate-600 sm:text-base">Selamat datang, {{ auth()->user()->name }}. Pantau jadwal kontrol terbaru dan kelola data imunisasi dengan lebih cepat.</p>
			</div>

			<div class="flex flex-wrap items-center gap-2">
				<a href="/profile" class="inline-flex items-center rounded-full border border-blue-100 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40">Profil</a>
				<a href="/immunization" class="inline-flex items-center rounded-full border border-slate-200 bg-slate-700 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400/40">Data Imunisasi</a>
			</div>
		</div>
	</div>

	@if(session('success'))
		<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
			{{ session('success') }}
		</div>
	@endif

	<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
		<div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
			<p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Jadwal Hari Ini</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $todayCount }}</p>
			<p class="mt-1 text-sm text-slate-500">Kunjungan yang berlangsung hari ini.</p>
		</div>
		<div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
			<p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Jadwal Minggu Ini</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $weekCount }}</p>
			<p class="mt-1 text-sm text-slate-500">Total kontrol pada pekan berjalan.</p>
		</div>
		<div class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm sm:col-span-2 lg:col-span-1">
			<p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Total Jadwal Mendatang</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalCount }}</p>
			<p class="mt-1 text-sm text-slate-500">Semua jadwal terdekat yang aktif.</p>
		</div>
	</div>

	<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
		<div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-6">
			<div>
				<h3 class="text-lg font-bold text-slate-900">Jadwal Kontrol Mendatang</h3>
				<p class="text-sm text-slate-500">Daftar 10 jadwal terdekat yang perlu Anda pantau.</p>
			</div>
			<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $bookings->count() }} data</span>
		</div>

		<div class="overflow-x-auto">
			<table class="min-w-full text-left">
				<thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
					<tr>
						<th class="px-4 py-3 sm:px-6">Tanggal</th>
						<th class="px-4 py-3 sm:px-6">Waktu</th>
						<th class="px-4 py-3 sm:px-6">Layanan</th>
						<th class="px-4 py-3 sm:px-6">Anak</th>
						<th class="px-4 py-3 sm:px-6">Status</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-100 text-sm text-slate-700">
					@forelse($bookings as $booking)
						@php
							$statusClass = match($booking->status) {
								'confirmed' => 'bg-emerald-100 text-emerald-700',
								'pending' => 'bg-amber-100 text-amber-700',
								'declined' => 'bg-rose-100 text-rose-700',
								'paid' => 'bg-blue-100 text-blue-700',
								default => 'bg-slate-100 text-slate-700',
							};
						@endphp
						<tr class="transition hover:bg-slate-50/70">
							<td class="whitespace-nowrap px-4 py-3 font-medium text-slate-800 sm:px-6">{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d/m/Y') }}</td>
							<td class="whitespace-nowrap px-4 py-3 sm:px-6">{{ substr($booking->schedule->start_time, 0, 5) }} - {{ substr($booking->schedule->end_time, 0, 5) }}</td>
							<td class="px-4 py-3 sm:px-6">{{ $booking->service->name }}</td>
							<td class="px-4 py-3 sm:px-6">{{ $booking->child->name }}</td>
							<td class="px-4 py-3 sm:px-6">
								<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5" class="px-4 py-12 text-center sm:px-6">
								<p class="text-sm font-medium text-slate-600">Belum ada jadwal kontrol mendatang.</p>
								<p class="mt-1 text-xs text-slate-500">Jadwal baru akan muncul otomatis setelah ada booking aktif.</p>
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>

@endsection