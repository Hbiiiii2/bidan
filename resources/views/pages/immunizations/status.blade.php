@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-8">
	<!-- Header -->
	<div class="flex flex-col gap-4 mb-8 md:flex-row md:items-end md:justify-between">
		<div>
			<h1 class="text-3xl font-black text-gray-900">Status Imunisasi</h1>
			<p class="text-gray-600 mt-2">{{ $child->name }} • {{ $child->birth_date->format('d M Y') }}</p>
		</div>
		<a href="/children" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-full font-bold inline-flex items-center w-fit transition">
			← Kembali
		</a>
	</div>

	@if(session('success'))
		<div class="mb-6 rounded-2xl bg-green-50 border border-green-200 p-4 text-green-700">
			{{ session('success') }}
		</div>
	@endif
	@if(session('error'))
		<div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">
			{{ session('error') }}
		</div>
	@endif
	@if($errors->any())
		<div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">
			<ul class="list-disc list-inside">
				@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<!-- Progress Overview -->
	<div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 mb-8">
		<div class="flex items-center justify-between mb-4">
			<h2 class="text-lg font-bold text-gray-900">Progres Imunisasi</h2>
			<p class="text-2xl font-black text-pink-600">{{ $status->where('status', 'done')->count() }}/{{ $status->count() }}</p>
		</div>
		@php
			$done = $status->where('status', 'done')->count();
			$total = $status->count();
			$percentage = $total > 0 ? ($done / $total) * 100 : 0;
		@endphp
		<div class="w-full bg-gray-200 rounded-full h-3">
			<div class="bg-gradient-to-r from-pink-500 to-pink-600 h-3 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
		</div>
		<p class="text-sm text-gray-600 mt-3">{{ $done }} dari {{ $total }} imunisasi telah dilakukan</p>
	</div>

	<!-- Stats Cards -->
	<div class="grid gap-4 md:grid-cols-3 mb-8">
		<div class="rounded-2xl bg-gradient-to-br from-green-50 to-green-100 p-6 ring-1 ring-green-200">
			<p class="text-sm font-semibold text-green-600 uppercase tracking-[0.1em]">Sudah Dilakukan</p>
			<p class="text-3xl font-black text-green-700 mt-2">{{ $status->where('status', 'done')->count() }}</p>
			<p class="text-xs text-green-600 mt-2">Vaksin terpenuhi</p>
		</div>
		<div class="rounded-2xl bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 ring-1 ring-yellow-200">
			<p class="text-sm font-semibold text-yellow-600 uppercase tracking-[0.1em]">Belum Dilakukan</p>
			<p class="text-3xl font-black text-yellow-700 mt-2">{{ $status->where('status', 'pending')->count() }}</p>
			<p class="text-xs text-yellow-600 mt-2">Vaksin tertunda</p>
		</div>
		<div class="rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 p-6 ring-1 ring-blue-200">
			<p class="text-sm font-semibold text-blue-600 uppercase tracking-[0.1em]">Total Vaksin</p>
			<p class="text-3xl font-black text-blue-700 mt-2">{{ $status->count() }}</p>
			<p class="text-xs text-blue-600 mt-2">Jadwal imunisasi</p>
		</div>
	</div>

	<!-- Vaccine List -->
	<div>
		<h2 class="text-lg font-bold text-gray-900 mb-4">Daftar Vaksin</h2>
		<div class="space-y-3">
			@foreach($status as $item)
				<article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-lg transition">
					<div class="flex items-center justify-between">
						<div class="flex items-start gap-4 flex-1">
							<div class="flex items-center justify-center w-10 h-10 rounded-full flex-shrink-0
								@if($item['status'] === 'done') bg-green-100 @else bg-gray-100 @endif">
								@if($item['status'] === 'done')
									<span class="text-lg">✓</span>
								@else
									<span class="text-lg text-gray-400">○</span>
								@endif
							</div>
							<div class="flex-1">
								<h3 class="font-bold text-gray-900 text-lg">{{ $item['vaccine'] }}</h3>
								<p class="text-sm text-gray-500 mt-1">
									@if($item['status'] === 'done')
										<span class="text-green-600">Sudah dilakukan</span>
									@else
										<span class="text-yellow-600">Belum dilakukan - Segera hubungi bidan</span>
									@endif
								</p>
							</div>
						</div>
						<span class="inline-flex rounded-full px-3 py-1 text-sm font-bold flex-shrink-0
							@if($item['status'] === 'done') bg-green-100 text-green-700
							@else bg-yellow-100 text-yellow-700 @endif">
							@if($item['status'] === 'done') Selesai @else Pending @endif
						</span>
					</div>
				</article>
			@endforeach
		</div>
	</div>

	<!-- CTA -->
	<div class="mt-8 rounded-2xl bg-gradient-to-r from-pink-500 to-pink-600 p-6 text-white text-center">
		<h3 class="text-xl font-bold">Ada imunisasi yang belum dilakukan?</h3>
		<p class="mt-2 text-pink-100">Hubungi bidan untuk mengatur jadwal imunisasi berikutnya.</p>
		<a href="/services" class="mt-4 inline-flex items-center bg-white text-pink-600 px-6 py-2.5 rounded-full font-bold hover:bg-gray-100 transition">
			Pesan Layanan Bidan →
		</a>
	</div>
</div>

@endsection
