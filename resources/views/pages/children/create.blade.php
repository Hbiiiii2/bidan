@extends('layouts.app')

@section('content')

<div class="max-w-lg mx-auto px-6 py-6">

    <h2 class="text-2xl font-bold mb-6">Tambah Data Anak</h2>

    <form method="POST" action="/children/store">
        @csrf

        @if($errors->any())
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4">
            <label>Nama Anak</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label>Tanggal Lahir</label>
            <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full border p-2 rounded">
            <p class="mt-1 text-xs text-gray-500">Format tampilan tanggal: dd/mm/yy.</p>
        </div>

        <div class="mb-4">
            <label>Jenis Kelamin</label>
            <select name="gender" class="w-full border p-2 rounded">
                <option value="male" @selected(old('gender') === 'male')>Laki-laki</option>
                <option value="female" @selected(old('gender') === 'female')>Perempuan</option>
            </select>
        </div>

        @if($vaccines->isNotEmpty())
            <div class="mb-6 rounded-2xl bg-white border border-gray-200 p-4">
                <p class="font-semibold text-gray-900 mb-3">Vaksinasi luar aplikasi</p>
                <p class="text-sm text-gray-500 mb-4">Jika anak sudah divaksin di luar aplikasi, centang vaksin terakhir yang telah dilakukan agar jadwal imunisasi bisa dilanjutkan.</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach($vaccines as $vaccine)
                        <label class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 cursor-pointer hover:bg-gray-50 transition">
                            <input type="checkbox" name="vaccine_ids[]" value="{{ $vaccine->id }}" @checked(is_array(old('vaccine_ids')) && in_array($vaccine->id, old('vaccine_ids')) ) class="h-4 w-4 text-pink-600 border-gray-300 rounded">
                            <span class="text-gray-700">{{ $vaccine->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <button class="bg-pink-600 text-white px-4 py-2 rounded-full">
            Simpan
        </button>

        <a href="/children" class="ml-3 text-gray-600 font-semibold">
            Kembali
        </a>
    </form>

</div>

@endsection
