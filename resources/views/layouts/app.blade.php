<!doctype html>
<html lang="id" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', config('app.name', 'Kebidan Yuk'))</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">

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
    @include('components.navbar')

    <main class="pt-24">
        @include('components.alert')
        @yield('content')
    </main>

    @include('components.footer')
</body>

</html>
