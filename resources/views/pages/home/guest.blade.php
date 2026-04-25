<!doctype html>
<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Kebidan Yuk - Pelayanan Kesehatan Ibu & Anak</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(180deg, #fff8f8 0%, #fdf0f4 100%);
        }

        .btn-primary {
            background: #b80049;
            color: #fff;
            padding: 14px 28px;
            border-radius: 999px;
            font-weight: 800;
            transition: background 0.25s ease;
        }

        .btn-primary:hover {
            background: #e2165f;
        }

        .btn-secondary {
            background: #f4dce4;
            color: #5b3f43;
            padding: 14px 28px;
            border-radius: 999px;
            font-weight: 700;
            transition: transform 0.25s ease, background 0.25s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-1px);
            background: #fde3ec;
        }

        .hero-pill {
            background: rgba(232, 76, 131, 0.1);
            color: #b80049;
            padding: 10px 18px;
            border-radius: 999px;
            display: inline-flex;
            font-weight: 700;
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="text-gray-800">

    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <a href="/" class="text-2xl font-black text-[#b80049]">kebidan-yuk</a>

            <div class="hidden md:flex gap-10 items-center">
                <a href="#layanan" class="font-semibold text-gray-600 hover:text-[#b80049]">Layanan</a>
                <a href="#fitur" class="font-semibold text-gray-600 hover:text-[#b80049]">Fitur</a>
                <a href="#alur" class="font-semibold text-gray-600 hover:text-[#b80049]">Alur</a>
                <a href="#testimoni" class="font-semibold text-gray-600 hover:text-[#b80049]">Testimoni</a>
            </div>

            @auth
                @php
                    $homeUrl = '/children';
                    if (auth()->user()->hasRole('midwife')) {
                        $homeUrl = '/calendar';
                    }
                    if (auth()->user()->hasRole('admin')) {
                        $homeUrl = '/admin';
                    }
                @endphp

                <div class="flex items-center gap-3">
                    <div class="hidden lg:block text-right">
                        <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</p>
                    </div>
                    <a href="{{ $homeUrl }}" class="btn-secondary">Dashboard</a>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="btn-primary">Logout</button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <a href="/register" class="btn-secondary">Daftar</a>
                    <a href="/login" class="btn-primary">Masuk</a>
                </div>
            @endauth
        </div>
    </nav>

    <main class="pt-28">
        <section class="max-w-7xl mx-auto px-6 py-20 grid gap-12 lg:grid-cols-2 items-center">
            <div class="space-y-8">
                <span class="hero-pill">Solusi digital untuk keluarga dan bidan</span>
                <h1 class="text-4xl md:text-5xl xl:text-6xl font-black leading-tight">
                    Imunisasi Anak dan Layanan Bidan
                    <span class="text-[#b80049]">lebih cepat dan terorganisir.</span>
                </h1>
                <p class="max-w-xl text-lg text-gray-600">
                    Kebidan-Yuk membantu orang tua memonitor imunisasi anak, booking layanan bidan, dan menyimpan data kesehatan dengan cara yang mudah dipahami.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="/login" class="btn-primary">Mulai Sekarang</a>
                    <a href="#layanan" class="btn-secondary">Lihat Layanan</a>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-white/90 p-4 shadow-sm ring-1 ring-gray-200">
                        <p class="text-3xl font-black text-[#b80049]">99%</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mt-2">Keandalan</p>
                    </div>
                    <div class="rounded-3xl bg-white/90 p-4 shadow-sm ring-1 ring-gray-200">
                        <p class="text-3xl font-black text-[#b80049]">3x</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mt-2">Lebih cepat booking</p>
                    </div>
                    <div class="rounded-3xl bg-white/90 p-4 shadow-sm ring-1 ring-gray-200">
                        <p class="text-3xl font-black text-[#b80049]">24/7</p>
                        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mt-2">Akses kapan saja</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-8 top-10 w-40 h-40 rounded-full bg-[#ffe4ec] blur-3xl"></div>
                <div class="absolute -right-6 bottom-14 w-32 h-32 rounded-full bg-[#f7d0df] blur-3xl"></div>
                <div class="rounded-[40px] bg-white p-8 shadow-2xl ring-1 ring-gray-100">
                    <div class="rounded-[32px] overflow-hidden bg-gradient-to-br from-[#ffdee7] to-[#ffd1e9] p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="text-sm text-gray-700">Jadwal Kontrol</p>
                                <p class="text-2xl font-black">16 Mei 2026</p>
                            </div>
                            <span class="inline-flex rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-[#b80049]">Booked</span>
                        </div>
                        <div class="space-y-4">
                            <div class="rounded-3xl bg-white p-4 shadow-sm">
                                <p class="text-xs text-gray-500">Anak</p>
                                <p class="font-bold">Alya</p>
                            </div>
                            <div class="rounded-3xl bg-white p-4 shadow-sm">
                                <p class="text-xs text-gray-500">Bidan</p>
                                <p class="font-bold">Bidan Putri</p>
                            </div>
                            <div class="rounded-3xl bg-white p-4 shadow-sm">
                                <p class="text-xs text-gray-500">Jenis Layanan</p>
                                <p class="font-bold">Imunisasi Anak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="layanan" class="max-w-7xl mx-auto px-6 py-20">
            <div class="text-center mx-auto max-w-2xl">
                <p class="text-sm uppercase tracking-[0.3em] text-[#b80049] font-bold">Layanan</p>
                <h2 class="text-3xl font-black mt-4">Fitur yang mendukung keluarga dan bidan</h2>
                <p class="mt-4 text-gray-600">Semua kebutuhan imunisasi dan layanan kebidanan dikelola dalam satu aplikasi yang ringkas.</p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                <div class="rounded-[32px] bg-white p-8 shadow-lg ring-1 ring-gray-100">
                    <p class="text-3xl">👶</p>
                    <h3 class="mt-5 font-bold text-xl">Profil Anak</h3>
                    <p class="mt-3 text-gray-600">Simpan data anak dengan lengkap untuk memantau imunisasi dan perkembangan.</p>
                </div>
                <div class="rounded-[32px] bg-white p-8 shadow-lg ring-1 ring-gray-100">
                    <p class="text-3xl">💉</p>
                    <h3 class="mt-5 font-bold text-xl">Tracking Imunisasi</h3>
                    <p class="mt-3 text-gray-600">Cek riwayat vaksin dan jadwal selanjutnya dengan status jelas.</p>
                </div>
                <div class="rounded-[32px] bg-white p-8 shadow-lg ring-1 ring-gray-100">
                    <p class="text-3xl">📅</p>
                    <h3 class="mt-5 font-bold text-xl">Booking Bidan</h3>
                    <p class="mt-3 text-gray-600">Pesan layanan bidan dan jadwal kontrol tanpa repot.</p>
                </div>
            </div>
        </section>

        <section id="fitur" class="max-w-7xl mx-auto px-6 py-20 bg-white/80 rounded-[40px] shadow-2xl ring-1 ring-gray-100">
            <div class="grid gap-12 lg:grid-cols-2 items-center">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-[#b80049] font-bold">Cara kerja</p>
                    <h2 class="text-3xl font-black mt-4">Dari daftar anak sampai booking layanan</h2>
                    <p class="mt-4 text-gray-600">Sistem dirancang agar orang tua dan bidan bisa melakukan semua proses dengan langkah yang sederhana dan jelas.</p>

                    <div class="mt-10 space-y-6">
                        <div class="flex gap-4 items-start">
                            <div class="min-w-[48px] h-12 rounded-full bg-[#ffe4ec] text-[#b80049] flex items-center justify-center font-black">1</div>
                            <div>
                                <h3 class="font-semibold">Daftar dan masuk</h3>
                                <p class="text-gray-600">Buat akun orang tua atau bidan, lalu langsung akses dashboard.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="min-w-[48px] h-12 rounded-full bg-[#ffe4ec] text-[#b80049] flex items-center justify-center font-black">2</div>
                            <div>
                                <h3 class="font-semibold">Kelola data anak dan imunisasi</h3>
                                <p class="text-gray-600">Input profil anak dan pantau riwayat vaksin secara digital.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="min-w-[48px] h-12 rounded-full bg-[#ffe4ec] text-[#b80049] flex items-center justify-center font-black">3</div>
                            <div>
                                <h3 class="font-semibold">Booking bidan dan kontrol</h3>
                                <p class="text-gray-600">Pilih layanan, jadwal, dan bidan terbaik dengan satu klik.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[32px] bg-[#fdf0f5] p-8 shadow-inner">
                    <div class="rounded-[28px] bg-white p-6 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm uppercase tracking-[0.3em] text-[#b80049] font-bold">Contoh tampilan aplikasi</p>
                        <div class="mt-6 space-y-5">
                            <div class="rounded-3xl bg-[#fef0f5] p-5">
                                <p class="text-sm text-gray-500">Status Imunisasi</p>
                                <p class="mt-2 font-bold text-lg">Alya • 2 tahun</p>
                                <p class="mt-3 text-gray-600">Vaksin DTP dijadwalkan 12 Mei 2026.</p>
                            </div>
                            <div class="rounded-3xl bg-[#fff1f6] p-5">
                                <p class="text-sm text-gray-500">Jadwal Kontrol</p>
                                <p class="mt-2 font-bold text-lg">Bidan Putri</p>
                                <p class="mt-3 text-gray-600">Senin, 16 Mei • 10:00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimoni" class="max-w-7xl mx-auto px-6 py-20">
            <div class="text-center mx-auto max-w-2xl">
                <p class="text-sm uppercase tracking-[0.3em] text-[#b80049] font-bold">Apa kata pengguna</p>
                <h2 class="text-3xl font-black mt-4">Trusted oleh keluarga dan bidan</h2>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-3">
                <div class="rounded-[32px] bg-white p-8 shadow-lg ring-1 ring-gray-100">
                    <p class="text-gray-600">"Sangat membantu untuk ingat jadwal vaksin anak dan langsung pesan jadwal kontrol bidan."</p>
                    <p class="mt-6 font-semibold">- Ibu Dinda</p>
                </div>
                <div class="rounded-[32px] bg-white p-8 shadow-lg ring-1 ring-gray-100">
                    <p class="text-gray-600">"Aplikasinya mudah digunakan dan membuat saya lebih cepat tangani jadwal pasien."</p>
                    <p class="mt-6 font-semibold">- Bidan Putri</p>
                </div>
                <div class="rounded-[32px] bg-white p-8 shadow-lg ring-1 ring-gray-100">
                    <p class="text-gray-600">"Kelola data anak jadi lebih rapi, dan orang tua juga merasa aman."</p>
                    <p class="mt-6 font-semibold">- Ibu Riana</p>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-6 py-20 text-center rounded-[40px] bg-[#b80049] text-white shadow-2xl ring-1 ring-white/20">
            <h2 class="text-3xl font-black">Siap bawa kesehatan anak ke level lebih baik?</h2>
            <p class="mt-4 text-gray-100 max-w-2xl mx-auto">Daftar sekarang dan nikmati kemudahan booking bidan, tracking imunisasi, serta manajemen data anak dalam satu platform.</p>
            <a href="/register" class="btn-primary mt-8 inline-block">Mulai Sekarang</a>
        </section>
    </main>

</body>

</html>
