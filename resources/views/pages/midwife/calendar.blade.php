@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
	<div class="mb-6 overflow-hidden rounded-3xl border border-rose-100 bg-gradient-to-r from-rose-50 via-white to-amber-50 shadow-sm">
		<div class="flex flex-col gap-4 px-5 py-6 sm:px-8 md:flex-row md:items-center md:justify-between">
			<div>
				<p class="mb-1 inline-flex items-center rounded-full border border-rose-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-rose-600">Kalender Bidan</p>
				<h2 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Jadwal Layanan Imunisasi</h2>
				<p class="mt-2 max-w-2xl text-sm text-slate-600 sm:text-base">Pilih tanggal pada kalender untuk melihat daftar anak dan parent yang akan menggunakan layanan.</p>
			</div>

			<div class="flex flex-wrap items-center gap-2">
				<a href="/dashboard" class="inline-flex items-center rounded-full border border-blue-100 bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-blue-700">Dashboard</a>
				<a href="/immunization" class="inline-flex items-center rounded-full border border-slate-200 bg-slate-700 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-slate-800">Data Imunisasi</a>
			</div>
		</div>
	</div>

	<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
		<div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
			<p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Jadwal Hari Ini</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $todayCount }}</p>
		</div>
		<div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
			<p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Jadwal Minggu Ini</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $weekCount }}</p>
		</div>
		<div class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm sm:col-span-2 lg:col-span-1">
			<p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Total Jadwal Mendatang</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalCount }}</p>
		</div>
	</div>

	@php
		$monthLabel = $activeMonth->isoFormat('MMMM Y');
		$prevMonth = $activeMonth->copy()->subMonth()->format('Y-m');
		$nextMonth = $activeMonth->copy()->addMonth()->format('Y-m');
		$weekdayLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
	@endphp

	<div class="grid gap-6 lg:grid-cols-5">
		<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-3">
			<div class="flex items-center justify-between border-b border-slate-200 px-4 py-4 sm:px-6">
				<a href="{{ url('/calendar') }}?month={{ $prevMonth }}&date={{ $activeMonth->copy()->subMonth()->startOfMonth()->toDateString() }}" class="rounded-full border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Sebelumnya</a>
				<h3 class="text-lg font-bold capitalize text-slate-900">{{ $monthLabel }}</h3>
				<a href="{{ url('/calendar') }}?month={{ $nextMonth }}&date={{ $activeMonth->copy()->addMonth()->startOfMonth()->toDateString() }}" class="rounded-full border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Berikutnya</a>
			</div>

			<div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
				@foreach($weekdayLabels as $label)
					<div class="px-2 py-2">{{ $label }}</div>
				@endforeach
			</div>

			<div class="grid grid-cols-7">
				@foreach($calendarDays as $day)
					@php
						$baseClass = $day['inMonth'] ? 'bg-white text-slate-800' : 'bg-slate-50 text-slate-400';
						$selectedClass = $day['isSelected'] ? 'ring-2 ring-rose-400 bg-rose-50' : 'hover:bg-slate-50';
						$todayClass = $day['isToday'] ? 'border border-blue-300' : 'border border-transparent';
					@endphp
					<a href="{{ url('/calendar') }}?month={{ $activeMonth->format('Y-m') }}&date={{ $day['dateKey'] }}" class="relative min-h-[92px] border-b border-r border-slate-100 p-2 transition {{ $baseClass }} {{ $selectedClass }} {{ $todayClass }}">
						<p class="text-sm font-semibold">{{ $day['date']->day }}</p>
						@if($day['bookingCount'] > 0)
							<span class="mt-2 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">{{ $day['bookingCount'] }} jadwal</span>
						@endif
					</a>
				@endforeach
			</div>
		</div>

		<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
			<div class="border-b border-slate-200 px-4 py-4 sm:px-6">
				<h3 class="text-lg font-bold text-slate-900">Detail {{ $selectedDate->format('d/m/Y') }}</h3>
				<p class="text-sm text-slate-500">Daftar anak dan parent pada tanggal terpilih.</p>
			</div>

			<div class="max-h-[560px] overflow-y-auto px-4 py-4 sm:px-6">
				@forelse($selectedBookings as $booking)
					@php
						$statusClass = match($booking->status) {
							'confirmed' => 'bg-emerald-100 text-emerald-700',
							'pending' => 'bg-amber-100 text-amber-700',
							'declined' => 'bg-rose-100 text-rose-700',
							'paid' => 'bg-blue-100 text-blue-700',
							default => 'bg-slate-100 text-slate-700',
						};
						$parentName = $booking->user->name ?? optional($booking->child->user)->name ?? '-';
					@endphp

					<div class="mb-3 rounded-xl border border-slate-200 bg-slate-50 p-3 last:mb-0">
						<div class="mb-2 flex items-center justify-between gap-2">
							<p class="text-sm font-bold text-slate-900">{{ $booking->child->name }}</p>
							<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
						</div>
						<div class="space-y-1 text-sm text-slate-600">
							<p><span class="font-semibold text-slate-700">Parent:</span> {{ $parentName }}</p>
							<p><span class="font-semibold text-slate-700">Layanan:</span> {{ $booking->service->name }}</p>
							<p><span class="font-semibold text-slate-700">Waktu:</span> {{ substr($booking->schedule->start_time, 0, 5) }} - {{ substr($booking->schedule->end_time, 0, 5) }}</p>
							@if($booking->midwife_notes)
								<p><span class="font-semibold text-slate-700">Catatan:</span> {{ $booking->midwife_notes }}</p>
							@endif
						</div>
					</div>
				@empty
					<div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
						<p class="text-sm font-medium text-slate-600">Tidak ada jadwal di tanggal ini.</p>
						<p class="mt-1 text-xs text-slate-500">Silakan pilih tanggal lain pada kalender.</p>
					</div>
				@endforelse
			</div>
		</div>
	</div>
</div>

@endsection