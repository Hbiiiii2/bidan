@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex flex-col gap-4 mb-8 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-3xl font-black text-gray-900">Data Anak Anda</h1>
            <p class="text-gray-600 mt-2">Kelola profil anak dan pantau imunisasi mereka.</p>
        </div>
        <a href="/children/create" class="bg-pink-600 text-white px-5 py-2.5 rounded-full font-bold inline-flex items-center w-fit">
            + Tambah Data Anak
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($children as $child)
            @php
                $age = $child->birth_date->age;
                $months = $child->birth_date->diff(now())->m;
                if ($age > 0) {
                    $ageDisplay = $age . ' tahun';
                } else {
                    $ageDisplay = $months . ' bulan';
                }
                $immunizationsCount = $child->immunizations_count ?? 0;
            @endphp
            
            <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $child->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $child->gender === 'male' ? '👦 Laki-laki' : '👧 Perempuan' }}</p>
                    </div>
                    <span class="inline-flex rounded-full bg-pink-100 px-3 py-1 text-sm font-bold text-pink-700">{{ $ageDisplay }}</span>
                </div>

                <div class="space-y-3 mb-5 pb-5 border-b">
                    <div>
                        <p class="text-xs uppercase tracking-[0.1em] text-gray-400 font-semibold">Tanggal Lahir</p>
                        <p class="text-gray-700 font-semibold">{{ $child->birth_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.1em] text-gray-400 font-semibold">Imunisasi Tercatat</p>
                        <p class="text-2xl font-black text-pink-600">{{ $immunizationsCount }}</p>
                    </div>
                </div>

                <div class="flex gap-2 flex-wrap">
                    <a href="/child/{{ $child->id }}/status" class="flex-1 text-center bg-pink-100 text-pink-700 px-3 py-2 rounded-xl font-semibold text-sm hover:bg-pink-200 transition">
                        Status Imunisasi
                    </a>
                    <a href="/children/{{ $child->id }}/edit" class="flex-1 text-center bg-gray-100 text-gray-700 px-3 py-2 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        Edit
                    </a>
                    <form method="POST" action="/children/{{ $child->id }}" class="flex-1" onsubmit="return confirm('Hapus data anak ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center bg-red-100 text-red-700 px-3 py-2 rounded-xl font-semibold text-sm hover:bg-red-200 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="md:col-span-2 lg:col-span-3 rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-100">
                <p class="text-xl font-bold text-gray-700">Belum ada data anak</p>
                <p class="text-gray-500 mt-2">Mulai dengan menambahkan data anak Anda untuk memantau imunisasi.</p>
                <a href="/children/create" class="mt-6 inline-flex items-center rounded-full bg-pink-600 px-6 py-3 text-white font-bold hover:bg-pink-700 transition">
                    Tambah Anak Pertama
                </a>
            </div>
        @endforelse
    </div>
</div>

@endsection
