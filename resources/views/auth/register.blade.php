<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff8f8] min-h-screen flex items-center justify-center px-6">

    <div class="bg-white p-8 rounded-2xl shadow w-full max-w-md">
        <h2 class="text-2xl font-black mb-2 text-center text-[#b80049]">Buat Akun Parent</h2>
        <p class="text-gray-500 text-center mb-6">Daftar untuk mulai kelola data anak dan booking layanan bidan.</p>

        <form method="POST" action="/register" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border p-2 rounded mt-1" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded mt-1" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Password</label>
                <input type="password" name="password" class="w-full border p-2 rounded mt-1" required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="w-full border p-2 rounded mt-1" required>
            </div>

            <button class="w-full bg-[#b80049] text-white py-2.5 rounded-full font-bold">
                Register
            </button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-5">
            Sudah punya akun?
            <a href="/login" class="text-[#b80049] font-bold">Login</a>
        </p>
    </div>

</body>

</html>
