<!DOCTYPE html>
<html lang="id" dir="ltr" data-theme="light">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--    Document Title-->
        <title>{{ $title ? $title . ' - ' : '' }}{{ $setting->name ?? 'Sistem Informasi Perpustakaan' }}</title>

        <!-- Include SEO partial -->
        {{-- <x-partials.seo :setting="$setting" :title="$title ?? null" /> --}}

        <!-----------------------------------------------------------
        -- animate.min.css by Daniel Eden (https://animate.style)
        -- is required for the animation of notifications and slide out panels
        -- you can ignore this step if you already have this file in your project
        --------------------------------------------------------------------------->

        <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
        <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>

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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">

        @stack('styles')

        @livewireStyles

    </head>

    <body class="font-inter transition-colors duration-300">
        <section id="section"
            class="bg-gradient-to-b px-3 sm:px-10 overflow-hidden from-[#F5F7FF] via-[#fffbee] to-[#E6EFFF] pt-6 h-full transition-colors duration-300">

            <x-guest-navbar :setting="$setting" />

            <main>
                {{ $slot }}

            </main>

            <div class="divider class-namemt-20"></div>

            <footer
                class="footer footer-horizontal footer-center text-gray-900 rounded p-10 transition-colors duration-300">
                <nav class="grid grid-flow-col gap-4">
                    <a href="{{ route('welcome') }}" class="link link-hover">Beranda</a>
                    <a href="{{ route('catalog') }}" class="link link-hover">Katalog Buku</a>
                    <a href="#about" class="link link-hover">Tentang Kami</a>
                    <a href="#contact" class="link link-hover">Kontak</a>
                </nav>

                <aside>
                    <p>Copyright © {{ date('Y') }} - {{ $setting->name ?? 'Sistem Informasi Perpustakaan' }}</p>
                    @if ($setting->address)
                        <p class="text-sm mt-1">{{ $setting->address }}</p>
                    @endif
                    @if ($setting->phone)
                        <p class="text-sm mt-1">{{ $setting->phone }}</p>
                    @endif
                </aside>
            </footer>
        </section>

        <!-- Include scripts partial -->
        <x-partials.scripts />

        @stack('scripts')

        @livewireScripts
    </body>

</html>
