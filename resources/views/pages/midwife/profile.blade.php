@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <h1 class="text-2xl font-black text-gray-900">Profil Bidan</h1>
        <p class="mt-1 text-gray-600">Kelola informasi profil Anda.</p>

        @if(session('success'))
            <div class="mt-6 rounded-2xl bg-green-50 border border-green-200 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Foto Profil</h2>
                <p class="text-sm text-gray-500 mb-4">Upload foto profil Anda untuk tampilan yang lebih personal.</p>

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                        @if(auth()->user()->profile_photo_url)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile Photo" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl text-gray-400">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <form method="POST" action="/profile" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Foto Baru</label>
                        <input type="file" name="profile_photo" accept="image/*" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                    </div>
                    <button type="submit" class="rounded-full bg-[#b80049] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#e2165f]">Upload Foto</button>
                </form>
            </div>

            <div>
                <h2 class="text-lg font-bold text-gray-900">Informasi Profil</h2>
                <p class="text-sm text-gray-500 mb-4">Informasi ini diatur oleh admin.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Nama</label>
                        <p class="text-gray-900">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Email</label>
                        <p class="text-gray-900">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">NIP</label>
                        <p class="text-gray-900">{{ auth()->user()->nip ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Instansi Rumah Sakit</label>
                        <p class="text-gray-900">{{ auth()->user()->hospital_institution ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Alamat</label>
                        <p class="text-gray-900">{{ auth()->user()->address ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Pengalaman</label>
                        <p class="text-gray-900">{{ auth()->user()->experience ? auth()->user()->experience . ' tahun' : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Jadwal Tersedia</label>
                        <p class="text-gray-900">
                            @if(auth()->user()->available_days)
                                {{ collect(auth()->user()->available_days)->map(function($day) {
                                    return ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'][$day] ?? $day;
                                })->join(', ') }}
                                @if(auth()->user()->available_start_time && auth()->user()->available_end_time)
                                    ({{ substr(auth()->user()->available_start_time, 0, 5) }} - {{ substr(auth()->user()->available_end_time, 0, 5) }})
                                @endif
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection