@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
	<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
		<div>
			<h2 class="text-2xl font-bold text-slate-900">Input Imunisasi</h2>
			<p class="text-slate-600">Bidan cukup isi jam imunisasi dan catatan. Data anak, vaksin, dan tanggal mengikuti booking.</p>
		</div>
		<a href="/immunization" class="inline-flex items-center rounded-full bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Lihat Riwayat Imunisasi</a>
	</div>

	@if(session('success'))
		<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
			{{ session('success') }}
		</div>
	@endif

	@if($errors->any())
		<div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
			<ul class="list-disc space-y-1 pl-5">
				@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="space-y-4">
		@forelse($bookings as $booking)
			@php
				$currentSchedule = $booking->schedule;
				$parentName = $booking->user->name ?? optional($booking->child->user)->name ?? '-';
				$statusClass = match($booking->status) {
					'paid' => 'bg-blue-100 text-blue-700',
					'confirmed' => 'bg-emerald-100 text-emerald-700',
					default => 'bg-amber-100 text-amber-700',
				};
			@endphp

			<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
				<div class="border-b border-slate-200 bg-slate-50 px-4 py-3 sm:px-6">
					<div class="flex flex-wrap items-center justify-between gap-3">
						<div>
							<p class="text-lg font-bold text-slate-900">{{ $booking->child->name }}</p>
							<p class="text-sm text-slate-600">Parent: {{ $parentName }}</p>
						</div>
						<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
					</div>
				</div>

				<div class="grid gap-4 p-4 sm:p-6 lg:grid-cols-2">
					<div class="space-y-2 rounded-xl border border-slate-200 bg-white p-4">
						<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Detail Booking</p>
						<p class="text-sm text-slate-700"><span class="font-semibold">Layanan:</span> {{ $booking->service->name }}</p>
						<p class="text-sm text-slate-700"><span class="font-semibold">Vaksin:</span> {{ $booking->vaccine->name ?? '-' }}</p>
						<p class="text-sm text-slate-700"><span class="font-semibold">Tanggal:</span> {{ $currentSchedule->date->format('d/m/Y') }}</p>
						<p class="text-sm text-slate-700"><span class="font-semibold">Jam Slot:</span> {{ substr($currentSchedule->start_time, 0, 5) }} - {{ substr($currentSchedule->end_time, 0, 5) }}</p>
					</div>

					<form method="POST" action="/immunization/store" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
						@csrf
						<input type="hidden" name="booking_id" value="{{ $booking->id }}">
						<p class="mb-3 text-sm font-bold text-emerald-800">Tandai Imunisasi Jadi</p>
						<div class="mb-3">
							<label class="mb-1 block text-sm font-semibold text-emerald-900">Jam Imunisasi</label>
							<input type="time" name="immunization_time" required class="w-full rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" value="{{ old('booking_id') == $booking->id ? old('immunization_time') : '' }}">
						</div>
						<div class="mb-4">
							<label class="mb-1 block text-sm font-semibold text-emerald-900">Catatan</label>
							<textarea name="notes" rows="3" class="w-full rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none" placeholder="Contoh: Anak dalam kondisi baik, observasi 15 menit.">{{ old('booking_id') == $booking->id ? old('notes') : '' }}</textarea>
						</div>
						<button type="submit" class="inline-flex rounded-full bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Simpan Imunisasi</button>
					</form>
				</div>

				<form method="POST" action="/immunization/bookings/{{ $booking->id }}/reschedule" class="border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-6">
					@csrf
					<p class="mb-3 text-sm font-bold text-slate-800">Reschedule Jadwal (jika dokter tidak bisa di hari ini)</p>
					<div class="grid gap-3 md:grid-cols-3">
						<div class="md:col-span-2">
							<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pindahkan ke Jadwal</label>
							<select name="new_schedule_id" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
								<option value="">Pilih jadwal baru</option>
								@foreach($availableSchedules as $schedule)
									@php
										$remaining = max(0, (int) $schedule->quota - (int) $schedule->active_bookings_count);
										$isCurrent = (int) $schedule->id === (int) $booking->schedule_id;
										$isDisabled = !$isCurrent && $remaining <= 0;
									@endphp
									<option value="{{ $schedule->id }}" @if($isCurrent) selected @endif @if($isDisabled) disabled @endif>
										{{ $schedule->date->format('d/m/Y') }} | {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
										@if($isCurrent)
											(aktif)
										@elseif($isDisabled)
											(penuh)
										@else
											(sisa {{ $remaining }})
										@endif
									</option>
								@endforeach
							</select>
						</div>
						<div>
							<label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Reschedule</label>
							<input type="text" name="reschedule_note" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" placeholder="Contoh: Dokter berhalangan.">
						</div>
					</div>
					<button type="submit" class="mt-3 inline-flex rounded-full bg-amber-500 px-4 py-2 text-sm font-bold text-white hover:bg-amber-600">Reschedule</button>
				</form>
			</div>
		@empty
			<div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
				<p class="text-base font-semibold text-slate-700">Tidak ada booking imunisasi yang perlu diproses.</p>
				<p class="mt-1 text-sm text-slate-500">Booking baru akan muncul di sini saat sudah siap diinput oleh bidan.</p>
			</div>
		@endforelse
	</div>
</div>

@endsection
