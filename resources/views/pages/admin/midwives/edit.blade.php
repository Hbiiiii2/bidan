@extends('layouts.app')

@section('title', 'Edit Bidan')

@section('content')

<div class="max-w-3xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-black text-gray-900">Edit Bidan</h1>
    <p class="text-gray-600 mt-1">Perbarui informasi akun bidan.</p>

    <form method="POST" action="/admin/midwives/{{ $midwife->id }}" class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Bidan</label>
            <input type="text" name="name" value="{{ old('name', $midwife->name) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $midwife->email) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">NIP</label>
            <input type="text" name="nip" value="{{ old('nip', $midwife->nip) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" placeholder="Nomor Induk Pegawai (opsional)">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Instansi Rumah Sakit</label>
            <input type="text" name="hospital_institution" value="{{ old('hospital_institution', $midwife->hospital_institution) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" placeholder="Contoh: RSUD Jakarta Pusat">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Dokter</label>
            <textarea name="address" rows="3" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" placeholder="Alamat lengkap dokter">{{ old('address', $midwife->address) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Mulai Karir</label>
            <input type="number" name="career_start_year" value="{{ old('career_start_year', $midwife->career_start_year) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5" placeholder="Contoh: 2015" min="1900" max="{{ date('Y') }}">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Jadwal Tersedia</label>
            <div class="grid gap-2">
                <div class="flex flex-wrap gap-2">
                    @foreach(['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'] as $day => $label)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="available_days[]" value="{{ $day }}" @checked(in_array($day, old('available_days', $midwife->available_days ?? []))) class="rounded border-gray-300 text-pink-600">
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jam Mulai</label>
                        <input type="time" name="available_start_time" value="{{ old('available_start_time', $midwife->available_start_time) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jam Selesai</label>
                        <input type="time" name="available_end_time" value="{{ old('available_end_time', $midwife->available_end_time) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5">
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru</label>
                <input type="password" name="password" class="w-full rounded-xl border border-gray-200 px-3 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-xl border border-gray-200 px-3 py-2.5">
            </div>
        </div>

        <p class="text-sm text-gray-500">Kosongkan password jika tidak ingin mengubah kata sandi.</p>

        <div class="flex items-center gap-3">
            <button class="rounded-full bg-[#b80049] px-5 py-2.5 text-sm font-bold text-white">Simpan Perubahan</button>
            <a href="/admin/midwives" class="text-gray-600 font-semibold">Batal</a>
        </div>
    </form>
</div>

@endsection