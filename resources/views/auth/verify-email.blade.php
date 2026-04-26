<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#fff8f8]">
    <div class="mx-auto flex min-h-screen max-w-xl items-center px-4 py-8">
        <div class="w-full rounded-2xl border border-pink-100 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-2xl font-black text-[#b80049]">Verifikasi Email Anda</h1>
            <p class="mt-2 text-sm text-gray-600">
                Sebelum akun parent aktif, silakan klik link verifikasi yang sudah dikirim ke email Anda.
            </p>

            @if (session('success'))
                <div class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-full bg-[#b80049] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#e2165f]">
                        Kirim Ulang Link Verifikasi
                    </button>
                </form>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-full rounded-full border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
