<!doctype html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Kebidan Yuk</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-primary {
            background: #b80049;
            color: white;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: #e2165f;
        }

        .btn-secondary {
            background: #f4dce4;
            padding: 12px 24px;
            border-radius: 999px;
        }
    </style>
</head>

<body class="bg-[#fff8f8] text-gray-800">

    <!-- NAVBAR -->
    <nav class="fixed top-0 w-full bg-white shadow z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">

            <div class="text-2xl font-black text-[#b80049]">
                kebidan-yuk
            </div>

            <div class="hidden md:flex gap-8">
                <a href="/" class="font-semibold text-[#b80049]">Home</a>
                <a href="/children" class="font-semibold text-gray-600">Data Anak</a>
                <a href="/services" class="font-semibold text-gray-600">Layanan</a>
                <a href="/dashboard" class="font-semibold text-gray-600">Dashboard</a>
            </div>

            <a href="/login" class="btn-primary">Login</a>
        </div>
    </nav>

    <main class="pt-24">

        <!-- HERO -->
        <section class="max-w-7xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
                    Pantau Imunisasi Anak Anda
                    <span class="text-[#b80049]">Dengan Mudah</span>
                </h1>

                <p class="mt-4 text-lg text-gray-600">
                    Cek status imunisasi, atur jadwal, dan booking layanan bidan dalam satu platform.
                </p>

                <div class="flex gap-4 mt-6">
                    <a href="/children" class="btn-primary">Kelola Data Anak</a>
                    <a href="/services" class="btn-secondary">Booking Layanan</a>
                </div>
            </div>
        </section>

        <!-- SERVICES (DINAMIS) -->
        <section class="max-w-7xl mx-auto px-6 py-16">

            <h2 class="text-2xl font-bold mb-8 text-center">Layanan Tersedia</h2>

            <div class="grid md:grid-cols-3 gap-6">

                @forelse($services as $service)
                    <div class="bg-white p-6 rounded-xl shadow">
                        <h3 class="font-bold text-lg mb-2">{{ $service->name }}</h3>

                        <p class="text-gray-600 mb-2">
                            Rp {{ number_format($service->price, 0, ',', '.') }}
                        </p>

                        <a href="/booking/{{ $service->id }}" class="text-[#b80049] font-semibold">
                            Booking →
                        </a>
                    </div>
                @empty
                    <p class="text-center col-span-3 text-gray-500">
                        Belum ada layanan tersedia
                    </p>
                @endforelse

            </div>

        </section>

        <!-- VALUE -->
        <section class="max-w-7xl mx-auto px-6 py-16">
            <div class="bg-[#b80049] text-white p-10 rounded-xl text-center">
                <h2 class="text-2xl font-bold">
                    Jangan sampai imunisasi anak terlewat
                </h2>

                <p class="mt-2">
                    Sistem akan membantu Anda memantau dan mengingatkan jadwal imunisasi anak.
                </p>

                <p class="mt-4 text-sm opacity-90">
                    Mendukung lebih dari {{ $totalVaccines }} jenis imunisasi
                </p>
            </div>
        </section>

        <!-- CTA -->
        <section class="max-w-7xl mx-auto px-6 py-16 text-center">

            <h2 class="text-2xl font-bold">
                Sudah cek imunisasi anak Anda?
            </h2>

            <a href="/children" class="btn-primary mt-6 inline-block">
                Cek Sekarang
            </a>

        </section>

    </main>

</body>

</html>
