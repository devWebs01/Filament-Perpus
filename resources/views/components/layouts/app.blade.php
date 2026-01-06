<!DOCTYPE html>
<html lang="id" dir="ltr" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--    Document Title-->
    <title>{{ ($title ?? '') }}{{ ($title ?? '') ? ' - ' : '' }}{{ ($setting->name ?? config('app.name', 'Sistem Informasi Perpustakaan')) }}</title>

    <!-- Include SEO partial -->
    {{-- <x-partials.seo :setting="$setting" :title="$title ?? null" /> --}}

    @if (file_exists(public_path('hot')))
        {{-- Mode HMR (npm run dev) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @elseif (file_exists(public_path('build/manifest.json')))
        {{-- Mode Production (npm run build) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Fallback jika Vite tidak berjalan --}}
        <link rel="stylesheet" href="/css/app.css">
        <script src="/js/app.js" defer></script>
    @endif

    <!--    Favicons & PWA -->
    <script src="https://kit.fontawesome.com/49d7584956.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/iconoir-icons/iconoir@main/css/iconoir.css" />

    <!--    Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
        *,
        html {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-style: normal;
        }
    </style>

    @stack('styles')

    @livewireStyles

</head>

<body class="font-inter transition-colors duration-300">
    <section id="section"
        class="bg-gradient-to-b px-3 sm:px-10 overflow-hidden from-neutral-50 via-neutral-100/50 to-neutral-50 pt-6 h-full transition-colors duration-300">

        @isset($setting)
            <x-guest-navbar :setting="$setting" />
        @endisset

        <main>
            {{ $slot }}

        </main>

        <div class="divider mt-20"></div>

        <footer
            class="footer footer-horizontal footer-center text-neutral-900 rounded-lg p-10 transition-colors duration-300">
            <nav class="grid grid-flow-col gap-4">
                <a href="{{ route('welcome') }}"
                    class="link link-hover text-neutral-700 hover:text-primary-600 transition-colors">Beranda</a>
                <a href="{{ route('catalog') }}"
                    class="link link-hover text-neutral-700 hover:text-primary-600 transition-colors">Katalog Buku</a>
                <a href="#about"
                    class="link link-hover text-neutral-700 hover:text-primary-600 transition-colors">Tentang Kami</a>
                <a href="#contact"
                    class="link link-hover text-neutral-700 hover:text-primary-600 transition-colors">Kontak</a>
            </nav>

            <aside>
                <p class="text-neutral-800 font-medium">Copyright © {{ date('Y') }} -
                    {{ $setting->name ?? config('app.name', 'Sistem Informasi Perpustakaan') }}
                </p>
                @isset($setting)
                    @if ($setting->address)
                        <p class="text-sm mt-1 text-neutral-600">{{ $setting->address }}</p>
                    @endif
                    @if ($setting->phone)
                        <p class="text-sm mt-1 text-neutral-600">{{ $setting->phone }}</p>
                    @endif
                @endisset
            </aside>
        </footer>
    </section>

    <!-- Include scripts partial -->
    <x-partials.scripts />

    @stack('scripts')

    @livewireScripts
</body>

</html>
