@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto px-6 py-10 text-center">
	<div class="bg-white rounded-xl shadow p-8">
		@if(session('success'))
			<div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
				{{ session('success') }}
			</div>
		@endif

		@if(session('error'))
			<div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
				{{ session('error') }}
			</div>
		@endif

		@if(session('info'))
			<div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
				{{ session('info') }}
			</div>
		@endif

		@if($isPaid)
			<h2 class="text-3xl font-bold text-green-600">Pembayaran Berhasil</h2>
			<p class="text-gray-600 mt-3">Booking layanan kamu sudah terkonfirmasi.</p>
		@else
			<h2 class="text-3xl font-bold text-amber-600">Pembayaran Diproses</h2>
			<p class="text-gray-600 mt-3">Status transaksi kamu: <span class="font-bold uppercase">{{ $transaction->status }}</span>. Silakan cek kembali beberapa saat lagi.</p>
			<form method="POST" action="{{ route('payment.recheck', $transaction->id) }}" class="mt-5">
				@csrf
				<button type="submit" class="inline-flex rounded-full bg-amber-500 px-5 py-2.5 text-sm font-bold text-white hover:bg-amber-600">Cek Status Sekarang</button>
			</form>
		@endif

		@if($transaction->reference_id)
			<p class="mt-3 text-xs text-gray-500">Order ID: {{ $transaction->reference_id }}</p>
		@endif

		<div class="mt-6 flex justify-center gap-3">
			<a href="/bookings" class="bg-pink-600 text-white px-4 py-2 rounded-full font-semibold">Lihat Riwayat Booking</a>
			<a href="/children" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full font-semibold">Kembali ke Data Anak</a>
		</div>
	</div>
</div>

@endsection

