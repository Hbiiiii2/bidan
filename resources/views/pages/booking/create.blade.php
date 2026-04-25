@extends('layouts.app')

@section('content')

@php
	$hasSelectableSchedule = $schedules->contains(function ($schedule) {
		return $schedule->available_slots > 0;
	});
@endphp

<div class="max-w-6xl mx-auto px-6 py-8">
	<div class="grid gap-6 lg:grid-cols-[1.4fr,1fr]">
		<section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
			<h1 class="text-2xl font-black text-gray-900">Booking Layanan</h1>
			<p class="mt-1 text-gray-600">Lengkapi data berikut untuk melanjutkan ke checkout.</p>

			@if($children->isEmpty())
				<div class="mt-5 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-yellow-800">
					Kamu belum punya data anak. Tambahkan dulu sebelum booking layanan.
					<a href="/children/create" class="ml-1 font-bold underline">Tambah data anak</a>
				</div>
			@endif

			<form method="POST" action="/booking/store" class="mt-6 space-y-5">
				@csrf

				@if($errors->any())
					<div class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">
						<ul class="list-disc list-inside">
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<input type="hidden" name="service_id" value="{{ $service->id }}">

				<div>
					<div class="flex items-center justify-between gap-4 mb-2">
						<label class="block text-sm font-bold text-gray-700">Pilih Anak</label>
						<a href="/children/create" class="text-pink-600 font-semibold text-sm hover:text-pink-700">+ Tambah Data Anak</a>
					</div>
					<select
						name="child_id"
						class="w-full rounded-xl border border-gray-200 px-3 py-2.5 focus:border-pink-500 focus:outline-none"
					>
						<option value="">Pilih nama anak</option>
						@foreach($children as $child)
							<option value="{{ $child->id }}" @selected(old('child_id') == $child->id)>{{ $child->name }}</option>
						@endforeach
					</select>
				</div>

				<div>
					<label class="mb-2 block text-sm font-bold text-gray-700">Pilih Tanggal Booking</label>
					<p class="mb-3 text-xs text-gray-500">Cukup pilih tanggal kedatangan. Jam kunjungan fleksibel selama jam layanan.</p>

					@php
						$minDate = $schedules->min('date');
						$maxDate = $schedules->max('date');
						
						if ($minDate && $maxDate) {
							$start = \Carbon\Carbon::parse($minDate)->startOfMonth();
							$end = \Carbon\Carbon::parse($maxDate)->endOfMonth();
							$schedulesByDate = $schedules->groupBy(function ($schedule) {
								return \Carbon\Carbon::parse($schedule->date)->format('Y-m-d');
							});
						}
					@endphp

					@if($service->midwives->isEmpty())
						<div class="rounded-2xl border border-dashed border-gray-300 bg-yellow-50 p-5 text-yellow-700">
							Layanan ini belum memiliki bidan terdaftar. Silakan hubungi admin atau pilih layanan lain.
						</div>
					@elseif(!$minDate || !$maxDate)
						<div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5 text-gray-500">
							Tidak ada jadwal tersedia untuk layanan ini. Silakan pilih layanan atau tanggal lain.
						</div>
					@else
						<div class="space-y-4">
							<div class="md:hidden space-y-2">
								<p class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">Pilih Tanggal (Mobile)</p>
								<div class="space-y-2">
									@foreach($schedulesByDate->sortKeys() as $dateStr => $daySchedules)
										@php
											$mobileDate = \Carbon\Carbon::parse($dateStr);
											$totalSlots = $daySchedules->sum('available_slots');
											$activeMidwifeCount = $daySchedules->where('available_slots', '>', 0)->count();
											$isToday = $mobileDate->isToday();
											$isPast = $mobileDate->isPast() && !$isToday;
											$isFull = $totalSlots <= 0;
											$canSelectDate = !$isPast && !$isFull;
											$isSelectedDate = old('booking_date') === $dateStr;
										@endphp

										<div class="rounded-xl border p-3 {{ $isSelectedDate ? 'border-pink-500 bg-pink-50' : 'border-gray-200 bg-white' }}">
											<div class="flex items-start justify-between gap-3">
												<div>
													<p class="text-sm font-bold text-gray-900">{{ $mobileDate->translatedFormat('D, d M Y') }}</p>
													<p class="mt-1 text-xs {{ $canSelectDate ? 'text-pink-700' : 'text-gray-500' }}">
														{{ $totalSlots }} slot tersedia • {{ $activeMidwifeCount }} bidan siap
													</p>
												</div>

												@if($canSelectDate)
													<label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-pink-200 bg-white px-3 py-1.5 text-xs font-semibold text-pink-700">
														<input
															type="radio"
															name="booking_date"
															value="{{ $dateStr }}"
															@checked($isSelectedDate)
															class="h-4 w-4"
														>
														<span>Pilih</span>
													</label>
												@elseif($isPast)
													<span class="inline-flex rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500">Lewat</span>
												@else
													<span class="inline-flex rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500">Penuh</span>
												@endif
											</div>
										</div>
									@endforeach
								</div>
							</div>

							<!-- Calendar Grid -->
							<div class="hidden md:block rounded-2xl border border-gray-200 bg-white p-5">
								<div class="grid grid-cols-7 gap-2 mb-4">
									@foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
										<div class="py-2 text-center text-xs font-bold uppercase tracking-[0.08em] text-gray-500">{{ $day }}</div>
									@endforeach

									@php
										$current = $start->copy();
										$daysShown = 0;
										// Offset for first day of month
										$firstDayOffset = $start->dayOfWeek;
										for ($i = 0; $i < $firstDayOffset; $i++) {
											echo '<div class="aspect-square"></div>';
											$daysShown++;
										}
									@endphp

									@while($current <= $end)
										@php
											$dateStr = $current->format('Y-m-d');
											$daySchedules = $schedulesByDate->get($dateStr, collect());
											$totalSlots = $daySchedules->sum('available_slots');
											$activeMidwifeCount = $daySchedules->where('available_slots', '>', 0)->count();
											$isFull = $totalSlots <= 0;
											$isToday = $current->isToday();
											$isPast = $current->isPast() && !$isToday;
											$canSelectDate = !$isPast && !$isFull;
											$isSelectedDate = old('booking_date') === $dateStr;
										@endphp

										<div class="flex min-h-[148px] flex-col rounded-xl border p-3 @if($isPast) border-gray-200 bg-gray-50 @elseif($isFull) border-gray-200 bg-gray-50 @elseif($isSelectedDate) border-pink-500 bg-pink-50 @elseif($isToday) border-pink-400 bg-pink-50 @else border-gray-200 bg-white @endif">
											<div class="flex items-start justify-between gap-2">
												<div class="text-base font-black text-gray-900">{{ $current->day }}</div>
												@if($daySchedules->isNotEmpty())
													<span class="inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold @if($isFull || $isPast) bg-gray-100 text-gray-500 @else bg-pink-100 text-pink-700 @endif">
														{{ $totalSlots }}/{{ $daySchedules->sum('quota') }} slot
													</span>
												@endif
											</div>

											<div class="mt-2 flex-1">
												@if($daySchedules->isEmpty())
													<p class="text-xs text-gray-400">Tidak ada jadwal</p>
												@elseif($isPast)
													<p class="text-xs text-gray-400">Tanggal sudah lewat</p>
												@elseif($isFull)
													<p class="text-xs text-gray-500">Kuota penuh</p>
												@else
													<p class="text-[11px] text-gray-500">{{ $activeMidwifeCount }} bidan siap</p>
												@endif
											</div>

											@if($daySchedules->isNotEmpty() && $canSelectDate)
												<label class="mt-2 inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg border px-2 py-2 text-xs font-bold @if($isSelectedDate) border-pink-600 bg-pink-600 text-white @else border-pink-200 bg-white text-pink-700 hover:bg-pink-50 @endif">
													<input
														type="radio"
														name="booking_date"
														value="{{ $dateStr }}"
														@checked($isSelectedDate)
														class="h-3.5 w-3.5"
													>
													<span>Pilih</span>
												</label>
											@endif
										</div>

										@php $current->addDay(); $daysShown++; @endphp
									@endwhile

									@php
										// Fill remaining cells
										while ($daysShown % 7 != 0) {
											echo '<div class="aspect-square"></div>';
											$daysShown++;
										}
									@endphp
								</div>
							</div>

						</div>
					@endif
				</div>

				@if(($service->type === 'immunization' || str_contains(strtolower($service->tag), 'imunisasi')) && $vaccines->isNotEmpty())
					<div>
						<p class="mb-2 text-sm font-bold text-gray-700">Pilih Imunisasi Wajib</p>
						<div class="grid gap-3">
							@foreach($vaccines as $vaccine)
								<label class="flex items-center gap-3 rounded-xl border border-gray-200 p-4 cursor-pointer hover:bg-gray-50 transition">
									<input
										type="radio"
										name="vaccine_id"
										value="{{ $vaccine->id }}"
										@checked(old('vaccine_id') == $vaccine->id)
										class="h-4 w-4 text-pink-600 border-gray-300 rounded"
									>
									<div>
										<p class="font-semibold text-gray-900">{{ $vaccine->name }}</p>
										@if($vaccine->description)
											<p class="text-sm text-gray-500">{{ $vaccine->description }}</p>
										@endif
									</div>
								</label>
							@endforeach
						</div>
						<p class="mt-2 text-sm text-gray-500">Pilih salah satu imunisasi wajib dari layanan ini sebelum melanjutkan ke checkout.</p>
					</div>
				@endif

				<div>
					<label class="mb-2 block text-sm font-bold text-gray-700">Catatan (Opsional)</label>
					<textarea
						name="notes"
						rows="4"
						class="w-full rounded-xl border border-gray-200 px-3 py-2.5 focus:border-pink-500 focus:outline-none"
						placeholder="Contoh: Anak sedang demam ringan"
					>{{ old('notes') }}</textarea>
				</div>

				<div class="flex flex-wrap items-center gap-3 pt-1">
					<button type="submit" class="rounded-full bg-[#b80049] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#e2165f] disabled:cursor-not-allowed disabled:opacity-50" @disabled($children->isEmpty() || !$hasSelectableSchedule)>
						Lanjut Checkout
					</button>
					<a href="/services" class="font-semibold text-gray-600">Kembali</a>
				</div>

				@if(!$hasSelectableSchedule)
					<p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-xl px-3 py-2">
						Belum ada jadwal tersedia untuk layanan ini. Silakan pilih layanan lain atau hubungi bidan/admin untuk membuka jadwal imunisasi.
					</p>
				@endif
			</form>
		</section>

		<aside class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 h-fit">
			<p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Ringkasan Layanan</p>
			<h2 class="mt-2 text-xl font-black text-gray-900">{{ $service->name }}</h2>
			<p class="mt-2 text-sm text-gray-500">Jenis: {{ $service->type === 'immunization' ? 'Imunisasi' : 'Konsultasi' }}</p>
			<p class="mt-2 text-sm text-gray-500">
				Periode: {{ optional($service->available_from_date ?? $service->available_date)->format('d/m/y') ?? '-' }} - {{ optional($service->available_until_date ?? $service->available_date)->format('d/m/y') ?? '-' }}
			</p>
			<p class="mt-1 text-sm text-gray-500">
				Jam layanan: {{ $service->available_start_time ? substr($service->available_start_time, 0, 5) : '-' }} - {{ $service->available_end_time ? substr($service->available_end_time, 0, 5) : '-' }} WIB
			</p>
			<p class="mt-5 text-3xl font-black text-[#b80049]">Rp {{ number_format($service->price, 0, ',', '.') }}</p>

			<div class="mt-6 rounded-xl bg-pink-50 p-4 text-sm text-pink-700">
				Setelah submit form, kamu akan diarahkan ke halaman checkout untuk konfirmasi dan pembayaran.
			</div>
		</aside>
	</div>
</div>

@endsection
