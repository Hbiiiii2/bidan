@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto px-6 py-8">
	<section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
		<h1 class="text-2xl font-black text-gray-900">Checkout Booking</h1>
		<p class="mt-1 text-gray-600">Periksa detail sebelum melakukan pembayaran.</p>

		@if(session('error'))
			<div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
				{{ session('error') }}
			</div>
		@endif

		<div class="mt-6 grid gap-4 sm:grid-cols-2">
			<div class="rounded-xl bg-gray-50 p-4">
				<p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Anak</p>
				<p class="mt-1 text-lg font-bold text-gray-900">{{ $booking->child->name }}</p>
			</div>
			<div class="rounded-xl bg-gray-50 p-4">
				<p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Layanan</p>
				<p class="mt-1 text-lg font-bold text-gray-900">{{ $booking->service->name }}</p>
				@if($booking->vaccine)
					<p class="mt-2 text-sm text-gray-600">Imunisasi: {{ $booking->vaccine->name }}</p>
				@endif
			</div>
			<div class="rounded-xl bg-gray-50 p-4 sm:col-span-2">
				<p class="text-xs uppercase tracking-[0.2em] text-gray-400 font-bold">Jadwal</p>
				<p class="mt-1 text-lg font-bold text-gray-900">
					{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d/m/y') }}
					<span class="text-gray-500 font-semibold">({{ substr($booking->schedule->start_time, 0, 5) }} - {{ substr($booking->schedule->end_time, 0, 5) }})</span>
				</p>
			</div>
		</div>

		<div class="mt-6 rounded-xl border border-pink-200 bg-pink-50 p-4 flex items-center justify-between">
			<p class="font-semibold text-pink-700">Total Pembayaran</p>
			<p class="text-2xl font-black text-[#b80049]">Rp {{ number_format($booking->service->price, 0, ',', '.') }}</p>
		</div>

		@if(($transaction?->status ?? null) === 'settlement')
			<div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
				Pembayaran untuk booking ini sudah berhasil.
			</div>
			<div class="mt-4">
				<a href="{{ route('payment.success', $transaction->id) }}" class="inline-flex rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Lihat Status Pembayaran</a>
			</div>
		@else
			<div class="mt-6 flex flex-wrap items-center gap-3">
				<form method="POST" action="/pay/{{ $booking->id }}">
					@csrf
					<button type="submit" class="rounded-full bg-[#b80049] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#e2165f]">Bayar dengan Midtrans</button>
				</form>
				<form method="POST" action="{{ route('booking.cancel', $booking->id) }}" onsubmit="return confirm('Yakin ingin membatalkan booking ini?');">
					@csrf
					<button type="submit" class="rounded-full bg-gray-100 px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-200">Batalkan Booking</button>
				</form>
				<a href="/bookings" class="font-semibold text-gray-600">Nanti Saja</a>
			</div>
		@endif
	</section>
</div>

@if(!empty($snapToken) && !empty($snapClientKey))
	<script src="https://app{{ config('services.midtrans.is_production') ? '' : '.sandbox' }}.midtrans.com/snap/snap.js" data-client-key="{{ $snapClientKey }}"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			window.snap.pay(@json($snapToken), {
				onSuccess: function (result) {
					var orderId = result.order_id || @json($transaction?->reference_id);
					window.location.href = @json(route('payment.finish')) + '?order_id=' + encodeURIComponent(orderId);
				},
				onPending: function (result) {
					var orderId = result.order_id || @json($transaction?->reference_id);
					window.location.href = @json(route('payment.finish')) + '?order_id=' + encodeURIComponent(orderId);
				},
				onError: function (result) {
					var orderId = result && result.order_id ? result.order_id : @json($transaction?->reference_id);
					window.location.href = @json(route('payment.finish')) + '?order_id=' + encodeURIComponent(orderId);
				},
				onClose: function () {
					window.location.href = @json(route('checkout', $booking->id));
				}
			});
		});
	</script>
@endif

@endsection

