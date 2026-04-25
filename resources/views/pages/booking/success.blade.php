@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto px-6 py-10 text-center">
	<div class="bg-white rounded-xl shadow p-8">
		<h2 class="text-3xl font-bold text-green-600">Pembayaran Berhasil</h2>
		<p class="text-gray-600 mt-3">Booking layanan kamu sudah terkonfirmasi.</p>

		<div class="mt-6 flex justify-center gap-3">
			<a href="/bookings" class="bg-pink-600 text-white px-4 py-2 rounded-full font-semibold">Lihat Riwayat Booking</a>
			<a href="/children" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-full font-semibold">Kembali ke Data Anak</a>
		</div>
	</div>
</div>

@endsection

