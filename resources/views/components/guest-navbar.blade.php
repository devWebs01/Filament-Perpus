<?php

use App\Models\{Setting, Transaction, Bookmark};

use function Livewire\Volt\{state, computed};

state([
    'setting' => Setting::first(),
]);

// Badge counters
$profileIncomplete = computed(function () {
    if (!auth()->check()) {
        return false;
    }
    $userDetail = auth()->user()->userDetail;
    return !$userDetail || !$userDetail->nis || !$userDetail->phone_number;
});

$overdueCount = computed(function () {
    if (!auth()->check()) {
        return 0;
    }
    return Transaction::where('user_id', auth()->id())
        ->whereHas('status', function ($query) {
            $query->where('name', 'Dipinjam');
        })
        ->where('due_date', '<', now())
        ->count();
});

$bookmarkCount = computed(function () {
    if (!auth()->check()) {
        return 0;
    }
    return Bookmark::where('user_id', auth()->id())->count();
});
  
  ?>

@volt
<div
    class="flex items-center justify-between px-6 py-3 md:py-4 shadow-sm w-full max-w-7xl mx-auto rounded-full bg-white transition-colors duration-300">
    <a href="{{ route('welcome') }}">
        <img src="{{ $setting?->logo ? Storage::url($setting->logo) : 'https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png' }}"
            style="width: 30px;" />
    </a>
    <nav id="menu"
        class="max-md:absolute max-md:top-0 max-md:left-0 max-md:overflow-hidden max-md:inset-y-0 items-center justify-center max-md:h-full max-md:w-0 transition-[width] bg-white/95 backdrop-blur flex-col md:flex-row flex gap-6 md:gap-8 text-gray-900 text-sm font-normal z-10">

        <!-- Navigation Links -->
        <div class="flex flex-col md:flex-row gap-4 md:gap-8 w-full md:w-auto">
            <a class="hover:text-warning-600 transition-colors py-2 text-center" href="{{ route('welcome') }}">
                Beranda
            </a>
            <a class="hover:text-warning-600 transition-colors py-2 text-center" href="{{ route('catalog') }}">
                Katalog Buku
            </a>
        </div>
        @guest
            <div class="flex flex-col md:hidden gap-3 w-full px-6 border-t border-gray-200 pt-4 mt-4">
                <a class="bg-warning-600 text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-warning-700 transition text-center"
                    href="/admin/login">
                    Sign In
                </a>
                <a class="border border-warning-600 text-warning-600 px-5 py-2 rounded-full text-sm font-medium hover:bg-warning-50 transition text-center"
                    href="/admin/register">
                    Sign Up
                </a>
            </div>
        @else
            <div class="flex flex-col md:hidden w-full px-6 border-t border-gray-200 pt-4 mt-4">
                <div class="flex items-center justify-center mb-4">
                    <span class="ml-3 font-medium text-gray-900">{{ auth()->user()->name }}</span>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('profile') }}"
                        class="flex items-center justify-center gap-2 text-gray-700 hover:text-warning-600 py-2 relative">
                        <i class="iconoir-user"></i>
                        Profil
                        @if ($this->profileIncomplete)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-4 w-4 bg-red-500 items-center justify-center text-xs text-white font-bold">!</span>
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('my-books') }}"
                        class="flex items-center justify-center gap-2 text-gray-700 hover:text-warning-600 py-2 relative">
                        <i class="iconoir-book"></i>
                        Buku Saya
                        @if ($this->overdueCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-4 w-4 bg-red-500 items-center justify-center text-xs text-white font-bold">!</span>
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('my-bookmarks') }}"
                        class="flex items-center justify-center gap-2 text-gray-700 hover:text-warning-600 py-2 relative">
                        <i class="iconoir-bookmark-book"></i>
                        Buku Tersimpan
                        @if ($this->bookmarkCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span
                                    class="relative inline-flex rounded-full h-4 w-4 bg-red-500 items-center justify-center text-xs text-white font-bold">!</span>
                            </span>
                        @endif
                    </a>
                    <div class="border-t border-gray-200 pt-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center justify-center gap-2 text-error hover:bg-red-50 rounded-lg p-2 w-full transition-colors">
                                <i class="iconoir-log-out"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endguest

        <!-- Close Menu Button -->
        <button id="closeMenu"
            class="md:hidden absolute top-4 right-4 text-gray-600 hover:text-gray-800 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </nav>

    <div class="flex items-center space-x-4">
        <!-- Desktop Authentication Buttons -->
        @guest
            <a class="hidden md:flex bg-warning-600 text-white px-5 py-2 rounded-full text-sm font-medium hover:bg-warning-700 transition"
                href="/admin/login">
                Sign In
            </a>
        @else
            <!-- User dropdown menu -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-sm btn-circle avatar hidden md:flex bg-gray-50">
                    <i class="iconoir-user" style="transition: opacity 0.2s;"></i>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li class="menu-title">
                        <span>{{ Str::limit(auth()->user()->name, '15', '...') }}</span>
                    </li>
                    <li>
                        <a href="{{ route('profile') }}" class="flex items-center text-black gap-2 justify-between">
                            <span class="flex items-center gap-2">
                                <i class="iconoir-user"></i>
                                Profil
                            </span>
                            @if ($this->profileIncomplete)
                                <span class="flex h-5 w-5">
                                    <span
                                        class="animate-ping absolute inline-flex h-5 w-5 rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-5 w-5 bg-red-500 items-center justify-center text-xs text-white font-bold">!</span>
                                </span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('my-books') }}" class="flex items-center text-black gap-2 justify-between">
                            <span class="flex items-center gap-2">
                                <i class="iconoir-book"></i>
                                Buku Saya
                            </span>
                            @if ($this->overdueCount > 0)
                                <span class="flex h-5 w-5">
                                    <span
                                        class="animate-ping absolute inline-flex h-5 w-5 rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-5 w-5 bg-red-500 items-center justify-center text-xs text-white font-bold">!</span>
                                </span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('my-bookmarks') }}" class="flex items-center text-black gap-2 justify-between">
                            <span class="flex items-center gap-2">
                                <i class="iconoir-bookmark-book"></i>
                                Buku Tersimpan
                            </span>
                            @if ($this->bookmarkCount > 0)
                                <span class="flex h-5 w-5">
                                    <span
                                        class="animate-ping absolute inline-flex h-5 w-5 rounded-full bg-red-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-5 w-5 bg-red-500 items-center justify-center text-xs text-white font-bold">!</span>
                                </span>
                            @endif
                        </a>
                    </li>
                    <div class="divider my-1"></div>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 text-error rounded-lg p-2 w-full text-left">
                                <i class="iconoir-log-out"></i>
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endguest

        <button id="openMenu" class="md:hidden text-gray-600 hover:text-gray-800 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openMenuBtn = document.getElementById('openMenu');
        const closeMenuBtn = document.getElementById('closeMenu');
        const menu = document.getElementById('menu');

        // Open menu
        openMenuBtn?.addEventListener('click', function () {
            menu?.classList.remove('max-md:w-0');
            menu?.classList.add('max-md:w-screen');
            menu?.classList.add('max-md:px-4');
            menu?.classList.remove('max-md:overflow-hidden');
            menu?.classList.add('max-md:overflow-auto');
        });

        // Close menu
        closeMenuBtn?.addEventListener('click', function () {
            menu?.classList.add('max-md:w-0');
            menu?.classList.remove('max-md:w-screen');
            menu?.classList.remove('max-md:px-4');
            menu?.classList.add('max-md:overflow-hidden');
            menu?.classList.remove('max-md:overflow-auto');
        });

        // Close menu when clicking on links (mobile)
        const menuLinks = menu?.querySelectorAll('a');
        menuLinks?.forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth < 768) { // md breakpoint
                    menu?.classList.add('max-md:w-0');
                    menu?.classList.remove('max-md:w-screen');
                    menu?.classList.remove('max-md:px-4');
                    menu?.classList.add('max-md:overflow-hidden');
                    menu?.classList.remove('max-md:overflow-auto');
                }
            });
        });
    });
</script>
@endvolt