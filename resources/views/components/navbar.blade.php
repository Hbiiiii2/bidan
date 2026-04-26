@php
    $homeUrl = '/';
    $homeIsActive = request()->path() === '/';

    if (auth()->check()) {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $homeUrl = '/admin';
            $homeIsActive = request()->is('admin') || request()->is('admin/*');
        } elseif ($user->hasRole('parent')) {
            $homeUrl = '/parent/dashboard';
            $homeIsActive = request()->is('parent/dashboard') || request()->is('parent/dashboard/*');
        } elseif ($user->hasRole('midwife')) {
            $homeUrl = '/dashboard';
            $homeIsActive = request()->is('dashboard') || request()->is('dashboard/*');
        }
    }
@endphp

<nav class="fixed top-0 w-full bg-white shadow z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
        <a href="/" class="text-2xl font-black text-[#b80049]">kebidan-yuk</a>

        <div class="hidden md:flex gap-8">
            <a href="{{ $homeUrl }}" class="font-semibold {{ $homeIsActive ? 'text-[#b80049]' : 'text-gray-600' }}">Home</a>
            @auth
                @if(auth()->user()->hasRole('parent'))
                    {{-- <a href="/dashboard" class="font-semibold {{ request()->is('dashboard') ? 'text-[#b80049]' : 'text-gray-600' }}">Dashboard</a> --}}
                    <a href="/children" class="font-semibold {{ request()->is('children*') || request()->is('child/*') ? 'text-[#b80049]' : 'text-gray-600' }}">Data Anak</a>
                    <a href="/services" class="font-semibold {{ request()->is('services*') ? 'text-[#b80049]' : 'text-gray-600' }}">Layanan</a>
                    <a href="/bookings" class="font-semibold {{ request()->is('bookings*') || request()->is('booking*') || request()->is('checkout*') ? 'text-[#b80049]' : 'text-gray-600' }}">Booking</a>
                    <a href="/parent/profile" class="font-semibold {{ request()->is('parent/profile') ? 'text-[#b80049]' : 'text-gray-600' }}">Profil</a>
                @endif

                @if(auth()->user()->hasRole('midwife'))
                    <a href="/calendar" class="font-semibold {{ request()->is('calendar') ? 'text-[#b80049]' : 'text-gray-600' }}">Kalender</a>
                    <a href="/immunization" class="font-semibold {{ request()->is('immunization') && !request()->is('immunization/create') ? 'text-[#b80049]' : 'text-gray-600' }}">Data Imunisasi</a>
                    <a href="/immunization/create" class="font-semibold {{ request()->is('immunization/create') ? 'text-[#b80049]' : 'text-gray-600' }}">Input Imunisasi</a>
                    <a href="/profile" class="font-semibold {{ request()->is('profile') ? 'text-[#b80049]' : 'text-gray-600' }}">Profil</a>
                @endif

                @if(auth()->user()->hasRole('admin'))
                    <a href="/admin" class="font-semibold {{ request()->is('admin*') ? 'text-[#b80049]' : 'text-gray-600' }}">Admin</a>
                    <a href="/admin/parents" class="font-semibold {{ request()->is('admin/parents*') ? 'text-[#b80049]' : 'text-gray-600' }}">User Parent</a>
                @endif
            @endauth
        </div>

        @auth
            <div class="hidden md:flex items-center gap-3">
                @if(auth()->user()->hasRole('midwife'))
                    <a href="/profile" class="rounded-full border border-pink-200 px-3 py-1.5 text-xs font-bold text-[#b80049] hover:bg-pink-50 {{ request()->is('profile') ? 'bg-pink-50' : '' }}">Profil</a>
                @endif

                <div class="hidden sm:block text-right">
                    <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</p>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn-primary">Logout</button>
                </form>
            </div>
        @else
            <a href="/login" class="hidden md:inline-flex btn-primary">Login</a>
        @endauth

        <button
            id="mobile-menu-button"
            type="button"
            class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-lg border border-pink-100 text-[#b80049]"
            aria-controls="mobile-menu"
            aria-expanded="false"
            aria-label="Toggle navigation menu"
        >
            <svg data-open-icon class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg data-close-icon class="hidden h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div id="mobile-menu" class="hidden border-t border-pink-100 bg-white md:hidden">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex flex-col gap-2">
                <a href="{{ $homeUrl }}" class="rounded-lg px-3 py-2 font-semibold {{ $homeIsActive ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Home</a>

                @auth
                    @if(auth()->user()->hasRole('parent'))
                        <a href="/children" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('children*') || request()->is('child/*') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Data Anak</a>
                        <a href="/services" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('services*') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Layanan</a>
                        <a href="/bookings" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('bookings*') || request()->is('booking*') || request()->is('checkout*') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Booking</a>
                        <a href="/parent/profile" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('parent/profile') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Profil</a>
                    @endif

                    @if(auth()->user()->hasRole('midwife'))
                        <a href="/calendar" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('calendar') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Kalender</a>
                        <a href="/immunization" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('immunization') && !request()->is('immunization/create') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Data Imunisasi</a>
                        <a href="/immunization/create" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('immunization/create') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Input Imunisasi</a>
                        <a href="/profile" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('profile') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Profil</a>
                    @endif

                    @if(auth()->user()->hasRole('admin'))
                        <a href="/admin" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('admin*') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">Admin</a>
                        <a href="/admin/parents" class="rounded-lg px-3 py-2 font-semibold {{ request()->is('admin/parents*') ? 'bg-pink-50 text-[#b80049]' : 'text-gray-700 hover:bg-gray-50' }}">User Parent</a>
                    @endif

                    <div class="mt-3 border-t border-gray-100 pt-3">
                        <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</p>
                        <form method="POST" action="/logout" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full btn-primary">Logout</button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="mt-2 inline-flex w-full items-center justify-center rounded-full bg-[#b80049] px-4 py-2.5 text-sm font-bold text-white">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');

        if (!button || !menu) {
            return;
        }

        const openIcon = button.querySelector('[data-open-icon]');
        const closeIcon = button.querySelector('[data-close-icon]');

        const setOpenState = (isOpen) => {
            menu.classList.toggle('hidden', !isOpen);
            button.setAttribute('aria-expanded', String(isOpen));
            openIcon?.classList.toggle('hidden', isOpen);
            closeIcon?.classList.toggle('hidden', !isOpen);
        };

        button.addEventListener('click', function () {
            const isOpen = menu.classList.contains('hidden');
            setOpenState(isOpen);
        });

        document.addEventListener('click', function (event) {
            if (menu.classList.contains('hidden')) {
                return;
            }

            if (!menu.contains(event.target) && !button.contains(event.target)) {
                setOpenState(false);
            }
        });
    });
</script>
