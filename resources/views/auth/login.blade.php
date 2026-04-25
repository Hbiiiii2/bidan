<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff8f8] flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow w-full max-w-md">

        <h2 class="text-2xl font-bold mb-6 text-center text-[#b80049]">
            Login
        </h2>

        <form method="POST" action="/login">
            @csrf

            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="email"
                    class="w-full border p-2 rounded mt-1"
                    required>
            </div>

            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password"
                    class="w-full border p-2 rounded mt-1"
                    required>
            </div>

            @error('email')
                <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
            @enderror

            <button class="w-full bg-[#b80049] text-white py-2 rounded-full">
                Login
            </button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-5">
            Belum punya akun?
            <a href="/register" class="text-[#b80049] font-bold">Register</a>
        </p>

    </div>

</body>

</html>
