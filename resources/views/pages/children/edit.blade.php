@extends('layouts.app')

@section('content')

<div class="max-w-lg mx-auto px-6 py-6">

    <h2 class="text-2xl font-bold mb-6">Edit Data Anak</h2>

    <form method="POST" action="/children/{{ $child->id }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label>Nama Anak</label>
            <input type="text" name="name" value="{{ old('name', $child->name) }}" class="w-full border p-2 rounded">
        </div>

        <div class="mb-4">
            <label>Tanggal Lahir</label>
            <input type="date" name="birth_date" value="{{ old('birth_date', $child->birth_date->format('Y-m-d')) }}" class="w-full border p-2 rounded">
            <p class="mt-1 text-xs text-gray-500">Format tampilan tanggal: dd/mm/yy.</p>
        </div>

        <div class="mb-4">
            <label>Jenis Kelamin</label>
            <select name="gender" class="w-full border p-2 rounded">
                <option value="male" @selected(old('gender', $child->gender) === 'male')>Laki-laki</option>
                <option value="female" @selected(old('gender', $child->gender) === 'female')>Perempuan</option>
            </select>
        </div>

        <button class="bg-pink-600 text-white px-4 py-2 rounded-full">
            Update
        </button>

        <a href="/children" class="ml-3 text-gray-600 font-semibold">
            Kembali
        </a>
    </form>

</div>

@endsection
