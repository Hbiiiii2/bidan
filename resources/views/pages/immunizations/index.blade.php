@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
	@php
		$totalImmunizations = $immunizations->count();
		$todayImmunizations = $immunizations->filter(fn ($item) => optional($item->date)->isToday())->count();
		$monthImmunizations = $immunizations->filter(fn ($item) => optional($item->date)->isCurrentMonth())->count();
	@endphp

	<div class="mb-6 overflow-hidden rounded-3xl border border-rose-100 bg-gradient-to-r from-rose-50 via-white to-amber-50 shadow-sm">
		<div class="flex flex-col gap-4 px-5 py-6 sm:px-8 md:flex-row md:items-center md:justify-between">
			<div>
				<p class="mb-1 inline-flex items-center rounded-full border border-rose-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-rose-600">Data Imunisasi</p>
				<h2 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Riwayat Imunisasi Anak</h2>
				<p class="mt-2 max-w-2xl text-sm text-slate-600 sm:text-base">Pantau riwayat tindakan imunisasi beserta jam pelaksanaan dan catatan bidan.</p>
			</div>

			<a href="/immunization/create" class="inline-flex items-center rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-rose-700">Input Imunisasi</a>
		</div>
	</div>

	<div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
		<div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
			<p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Hari Ini</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $todayImmunizations }}</p>
			<p class="mt-1 text-sm text-slate-500">Jumlah tindakan pada tanggal hari ini.</p>
		</div>
		<div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
			<p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Bulan Ini</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $monthImmunizations }}</p>
			<p class="mt-1 text-sm text-slate-500">Total tindakan pada bulan berjalan.</p>
		</div>
		<div class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm sm:col-span-2 lg:col-span-1">
			<p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Total Data</p>
			<p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalImmunizations }}</p>
			<p class="mt-1 text-sm text-slate-500">Seluruh riwayat imunisasi terinput.</p>
		</div>
	</div>

	<form method="GET" action="/immunization" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
		<div class="grid gap-3 md:grid-cols-2 lg:grid-cols-5">
			<div class="lg:col-span-2">
				<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Cari Data</label>
				<input
					type="text"
					name="q"
					value="{{ $filters['q'] ?? '' }}"
					placeholder="Cari anak, parent, vaksin, atau catatan"
					class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-rose-400 focus:outline-none"
				>
			</div>

			<div>
				<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Vaksin</label>
				<select name="vaccine_id" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-rose-400 focus:outline-none">
					<option value="">Semua Vaksin</option>
					@foreach($vaccines as $vaccine)
						<option value="{{ $vaccine->id }}" @selected((string) ($filters['vaccine_id'] ?? '') === (string) $vaccine->id)>{{ $vaccine->name }}</option>
					@endforeach
				</select>
			</div>

			<div>
				<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Dari Tanggal</label>
				<input
					type="date"
					name="date_from"
					value="{{ $filters['date_from'] ?? '' }}"
					class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-rose-400 focus:outline-none"
				>
			</div>

			<div>
				<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Sampai Tanggal</label>
				<input
					type="date"
					name="date_to"
					value="{{ $filters['date_to'] ?? '' }}"
					class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-rose-400 focus:outline-none"
				>
			</div>
		</div>

		<div class="mt-4 flex flex-wrap items-center gap-2">
			<button type="submit" class="inline-flex rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Terapkan Filter</button>
			<a href="/immunization" class="inline-flex rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
		</div>
	</form>

	<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
		<div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-6">
			<div>
				<h3 class="text-lg font-bold text-slate-900">Daftar Imunisasi</h3>
				<p class="text-sm text-slate-500">Detail anak, parent, vaksin, waktu tindakan, dan catatan bidan.</p>
			</div>
			<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $totalImmunizations }} data</span>
		</div>

		<div class="overflow-x-auto">
			<table class="min-w-full text-left">
				<thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
					<tr>
						<th class="px-4 py-3 sm:px-6">Anak</th>
						<th class="px-4 py-3 sm:px-6">Parent</th>
						<th class="px-4 py-3 sm:px-6">Vaksin</th>
						<th class="px-4 py-3 sm:px-6">Tanggal</th>
						<th class="px-4 py-3 sm:px-6">Jam</th>
						<th class="px-4 py-3 sm:px-6">Bidan</th>
						<th class="px-4 py-3 sm:px-6">Catatan</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-100 text-sm text-slate-700">
					@forelse($immunizations as $immunization)
						<tr class="transition hover:bg-slate-50/70">
							<td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-900 sm:px-6">{{ $immunization->child->name ?? 'Anak tidak ditemukan' }}</td>
							<td class="whitespace-nowrap px-4 py-3 sm:px-6">{{ $immunization->child->user->name ?? '-' }}</td>
							<td class="px-4 py-3 sm:px-6">{{ $immunization->vaccine->name ?? 'Vaksin tidak ditemukan' }}</td>
							<td class="whitespace-nowrap px-4 py-3 sm:px-6">{{ $immunization->date ? $immunization->date->format('d/m/Y') : '-' }}</td>
							<td class="whitespace-nowrap px-4 py-3 sm:px-6">
								@if($immunization->immunized_at)
									<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ $immunization->immunized_at->format('H:i') }}</span>
								@else
									-
								@endif
							</td>
							<td class="whitespace-nowrap px-4 py-3 sm:px-6">{{ $immunization->midwife->name ?? '-' }}</td>
							<td class="px-4 py-3 text-slate-600 sm:px-6">{{ $immunization->notes ?? '-' }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="7" class="px-4 py-12 text-center sm:px-6">
								<p class="text-sm font-medium text-slate-600">Belum ada data imunisasi.</p>
								<p class="mt-1 text-xs text-slate-500">Silakan input imunisasi baru dari tombol Input Imunisasi.</p>
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>

@endsection