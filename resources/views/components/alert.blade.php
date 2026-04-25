@if (session('success'))
	<div class="max-w-7xl mx-auto px-6 pt-6">
		<div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
			{{ session('success') }}
		</div>
	</div>
@endif

@if ($errors->any())
	<div class="max-w-7xl mx-auto px-6 pt-6">
		<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
			<ul class="list-disc pl-5 space-y-1">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	</div>
@endif
