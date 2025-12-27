<!-- SEO Meta Tags -->
<meta name="description"
    content="{{ $setting?->description ?? 'Sistem Informasi Perpustakaan modern dengan koleksi buku digital lengkap, kemudahan peminjaman, dan pengalaman membaca yang nyaman.' }}">
<meta name="keywords"
    content="perpustakaan, buku, digital, peminjaman, katalog, e-book, {{ $setting?->name ?? 'perpustakaan' }}, literasi, pendidikan">
<meta name="author" content="{{ $setting?->name ?? 'Sistem Informasi Perpustakaan' }}">
<meta name="robots" content="index, follow">
<meta name="language" content="id">

<!-- Favicon -->
@if ($setting?->logo)
    <link rel="icon" type="image/x-icon" href="{{ Storage::url($setting->logo) }}">
    <link rel="apple-touch-icon" href="{{ Storage::url($setting->logo) }}">
@else
    <link rel="icon" type="image/x-icon"
        href="https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png">
    <link rel="apple-touch-icon"
        href="https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png">
@endif

<!-- Open Graph Meta Tags for Social Sharing -->
<meta property="og:title"
    content="{{ $title ? $title . ' - ' : '' }}{{ $setting?->name ?? 'Sistem Informasi Perpustakaan' }}">
<meta property="og:description"
    content="{{ $setting?->description ?? 'Sistem Informasi Perpustakaan modern dengan koleksi buku digital lengkap.' }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@if ($setting?->logo)
    <meta property="og:image" content="{{ Storage::url($setting->logo) }}">
@else
    <meta property="og:image"
        content="https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png">
@endif
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="{{ $setting?->name ?? 'Sistem Informasi Perpustakaan' }}">
<meta property="og:locale" content="id_ID">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title"
    content="{{ $title ? $title . ' - ' : '' }}{{ $setting?->name ?? 'Sistem Informasi Perpustakaan' }}">
<meta name="twitter:description"
    content="{{ $setting?->description ?? 'Sistem Informasi Perpustakaan modern dengan koleksi buku digital lengkap.' }}">
@if ($setting?->logo)
    <meta name="twitter:image" content="{{ Storage::url($setting->logo) }}">
@else
    <meta name="twitter:image"
        content="https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png">
@endif

<!-- Additional SEO Tags -->
<meta name="theme-color" content="#3B82F6">
<meta name="msapplication-TileColor" content="#3B82F6">
<link rel="canonical" href="{{ url()->current() }}">

<!-- Structured Data (JSON-LD) for SEO -->
@if ($setting)
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $setting->name ?? 'Sistem Informasi Perpustakaan' }}",
        "description": "{{ $setting->description ?? 'Sistem Informasi Perpustakaan modern dengan koleksi buku digital lengkap.' }}",
        "url": "{{ url('/') }}",
        "logo": "{{ $setting->logo ? Storage::url($setting->logo) : 'https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png' }}",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ $setting->phone ?? '' }}",
            "contactType": "customer service",
            "availableLanguage": ["Indonesian"]
        },
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $setting->address ?? '' }}",
            "addressCountry": "ID"
        },
        "sameAs": [
            "{{ $setting->facebook ?? '#' }}",
            "{{ $setting->twitter ?? '#' }}",
            "{{ $setting->instagram ?? '#' }}"
        ]
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Library",
        "name": "{{ $setting->name ?? 'Sistem Informasi Perpustakaan' }}",
        "description": "{{ $setting->description ?? 'Sistem Informasi Perpustakaan modern dengan koleksi buku digital lengkap.' }}",
        "url": "{{ url('/') }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $setting->address ?? '' }}",
            "addressCountry": "ID"
        },
        "telephone": "{{ $setting->phone ?? '' }}",
        "openingHours": "Mo-Su 08:00-17:00",
        "keywords": "perpustakaan, buku, digital, peminjaman, katalog, e-book, literasi, pendidikan"
    }
    </script>
@endif