<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state};
use App\Models\{Book, Category, Setting};

name('welcome');

state([
    'books_populer' => Book::inRandomOrder()->limit(8)->get(),
    'categories' => Category::select('name')->get(),
    'setting' => Setting::first(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Beranda</x-slot>
    @include('components.partials.aos')
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div
            class="bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg mb-4 mx-auto max-w-7xl mt-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session()->has('verification_required'))
        <div
            class="bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded-lg mb-4 mx-auto max-w-7xl mt-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('verification_required') }}
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="bg-error-50 border border-error-200 text-error-800 px-4 py-3 rounded-lg mb-4 mx-auto max-w-7xl mt-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif
    @volt
        <div>
            <section data-aos="fade-up" class="flex-grow flex flex-col items-center max-w-7xl mx-auto w-full mb-0 pb-0">
                <button
                    class="mt-16 mb-6 flex items-center space-x-2 border border-primary-600 text-primary-600 text-xs rounded-full px-4 pr-1.5 py-1.5 hover:bg-primary-50 transition-all duration-300"
                    type="button">
                    <span>
                        Perpustakaan Digital untuk Siswa SMA
                    </span>
                    <span class="flex items-center justify-center size-6 p-1 rounded-full bg-primary-600">
                        <svg width="14" height="11" viewBox="0 0 16 13" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 6.5h14M9.5 1 15 6.5 9.5 12" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
                <h1 class="text-center text-neutral-900 font-bold text-3xl sm:text-4xl md:text-5xl max-w-2xl leading-tight">
                    Temukan
                    <span class="text-primary-600 fw-bold">
                        Ilmu Tanpa Batas
                    </span>
                    di Perpustakaan Kami
                </h1>
                <p class="mt-4 text-center text-neutral-600 max-w-md text-sm sm:text-base leading-relaxed">
                    Akses koleksi buku pelajaran, literasi, dan referensi yang mendukung
                    pembelajaran siswa-siswi SMA dalam mencapai prestasi akademik terbaik.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <a href="{{ route('catalog') }}"
                        class="bg-primary-600 text-white px-6 py-2.5 rounded-full text-sm font-medium flex items-center justify-center space-x-2 hover:bg-primary-700 hover:shadow-primary-md transition-all duration-300">
                        <span>
                            Jelajahi Koleksi
                        </span>
                        <i class="iconoir-open-book"></i>
                    </a>
                    <a href="/admin/register"
                        class="border border-primary-600 text-primary-600 px-6 py-2.5 rounded-full text-sm font-medium flex items-center justify-center space-x-2 hover:bg-primary-50 transition-all duration-300">
                        <span>
                            Daftar Sebagai Siswa
                        </span>
                        <i class="iconoir-user-scan"></i>
                    </a>
                </div>
                <img class="rounded-2xl mt-16 h-72 w-full object-cover rounded-b-none max-w-5xl shadow-md hover:shadow-lg transition-all duration-300"
                    src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEg5iTZzB6R3NRMdt4ZZb_1DcyFJC9puPEPEIhk5nP4DK_qMgmr0LMHaptK3GTsPEC_wk1P2TP8XRZlvrHOvVB2R94Yc6i8Ds2Wf1qgRoQaNX5Ttr9qi3Kd-arESsSkDJvkz8IkFRZ0RZa6m/s640/Pembelajaran-di-Perpustakaan-892x480.jpg"
                    alt="Perpustakaan modern dengan rak buku">

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 mt-16 w-max mx-auto">
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 border-t-0 border-l-0 transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 border-t-0 border-x-0 md:border-r transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 hidden md:flex md:border-t-0 md:border-x-0 lg:border-r transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 hidden lg:block lg:border-x-0 lg:border-t-0 transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 hidden xl:block xl:border-t-0 xl:border-r-0 transition-colors duration-200">
                    </div>

                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 hidden xl:block xl:border-y-0 xl:border-l-0 transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 hidden lg:block lg:border-y-0 lg:border-l-0 transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 hidden md:block md:border-l-0 md:border-y-0 transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 border-x-0 border-y-0 border-r xl:border-r-0 transition-colors duration-200">
                    </div>
                    <div
                        class="hover:bg-neutral-50 h-16 w-44 sm:w-60 sm:h-20 border border-neutral-200 border-x-0 border-y-0 xl:border-l transition-colors duration-200">
                    </div>
                </div>
            </section>

            <section class="w-full items-center max-w-6xl mx-auto px-4">
                <!-- Card buku populer -->
                <div data-aos="fade-up" class="mx-auto lg:max-w-7xl md:max-w-4xl sm:max-w-xl max-sm:max-w-sm">
                    <div class="flex items-center justify-between w-full mb-8">
                        <h1 class="text-2xl font-semibold text-neutral-900">
                            Buku Populer
                        </h1>

                        <a href="{{ route('catalog') }}"
                            class="text-lg font-semibold text-primary-600 hover:text-primary-700 underline underline-offset-4 transition-colors duration-200">
                            Lainnya
                        </a>
                    </div>
                    @include('pages.catalog-book.book_recommendations')

                </div>

            </section>

            <section data-aos="fade-up" class="w-full items-center max-w-6xl mx-auto px-4">
                <div class="mt-20">
                    <div class="flex flex-col items-center text-center">
                        <img src="{{ $setting?->logo ? Storage::url($setting->logo) : 'https://sman1singgahan.sch.id/wp-content/uploads/2023/04/Logo-Tut-Wuri-Handayani-PNG-Warna.png' }}"
                            alt="logo" class="w-10 mb-4">

                        <h2 class="text-2xl sm:text-3xl text-neutral-700 font-semibold px-4">
                            Jelajahi Kategori Favorit
                        </h2>
                        <div
                            class="flex items-center justify-center max-w-full sm:max-w-md mx-auto px-4 sm:px-6 gap-2 border border-neutral-200 text-neutral-500 rounded-full py-2 mt-6 text-xs sm:text-sm text-center leading-relaxed">
                            <span class="break-words">Temukan buku sesuai mata pelajaran dan kebutuhan belajarmu.</span>
                        </div>
                    </div>

                    <div class="mt-4 relative w-full overflow-hidden whitespace-nowrap">
                        <div class="animate-marquee-horizontal inline-block" style="animation-duration: 15s;">
                            @foreach ($categories as $category)
                                <span class="inline-block px-4 font-bold text-lg text-base-content">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="w-full items-center max-w-6xl mx-auto px-4">
                <div id="about" data-aos="fade-up"
                    class="flex flex-col gap-5 max-md:gap-20 md:flex-row pb-20 items-center justify-between mt-20">

                    <div class="flex flex-col items-center md:items-start">

                        <div
                            class="flex flex-wrap items-center justify-center p-1.5 rounded-full border border-neutral-600 bg-white text-neutral-900 text-xs shadow-sm">

                            <div class="flex items-center">

                                <img class="size-7 rounded-full border-2 border-white"
                                    src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?q=80&w=50"
                                    alt="userImage1">

                                <img class="size-7 rounded-full border-2 border-white -translate-x-2"
                                    src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=50"
                                    alt="userImage2">

                                <img class="size-7 rounded-full border-2 border-white -translate-x-4"
                                    src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=50&h=50&auto=format&fit=crop"
                                    alt="userImage3">

                            </div>

                            <p class="-translate-x-2 text-neutral-900 font-medium">Digunakan oleh siswa-siswi SMA kami
                            </p>

                        </div>

                        <h1
                            class="text-center md:text-left text-5xl leading-[68px] md:text-6xl md:leading-[84px] font-semibold max-w-xl text-neutral-900">

                            Sistem Perpustakaan Modern

                        </h1>

                        <p class="text-center md:text-left text-base text-neutral-600 max-w-lg mt-3 leading-relaxed">

                            Sistem perpustakaan digital yang memudahkan siswa dalam mencari buku pelajaran,
                            melakukan peminjaman, dan mengakses materi pembelajaran secara online.

                        </p>

                    </div>

                    <img src="https://darul-muttaqien.com/wp-content/uploads/2025/02/perpus-2.jpg"
                        alt="Perpustakaan digital"
                        class="max-w-xs sm:max-w-sm lg:max-w-md transition-all duration-300 object-cover rounded-2xl shadow-lg hover:shadow-xl"
                        style="height: 400px;">

                </div>
            </section>
            <section class="w-full items-center max-w-6xl mx-auto px-4">
                <div id="contact" data-aos="fade-up-left"
                    class="max-w-4xl mx-auto flex flex-col md:flex-row items-start justify-center gap-8 px-4 md:px-0 py-10">

                    <img class="max-w-sm w-full rounded-lg object-cover shadow-md"
                        src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjjbpz-EWu36azT2Ms3hsz3K75ZzS6-dU6iaQY17VTSM1nxO1nTb1VKYfEdIYwJPITrvqGDmjcx-Lv5lM_pqlkVpprB75eBBQY7i_gqCER_wIlDv0sQpCv144yjohmWbu7JwhHfzcRU98aZ/s1600/IMG_20160824_100106.jpg"
                        alt="FAQ" style="height: 400px;" />

                    <div>

                        <p class="text-primary-600 text-sm font-medium">Pertanyaan yang Sering Diajukan</p>

                        <h1 class="text-3xl text-neutral-900 font-semibold">Butuh Bantuan?</h1>

                        <p class="text-sm text-neutral-500 mt-2 pb-4">

                            Temukan jawaban atas pertanyaan yang sering diajukan siswa tentang
                            perpustakaan sekolah dan cara penggunaannya.

                        </p>

                        <!-- FAQ Items -->

                        <div tabindex="0"
                            class="collapse collapse-arrow bg-base-100 border border-neutral-200 text-neutral-900 mb-3">
                            <div class="collapse-title font-semibold">Bagaimana cara meminjam buku di perpustakaan?</div>
                            <div class="collapse-content text-sm text-neutral-600">
                                Siswa dapat mencari buku melalui katalog online, kemudian datang ke perpustakaan
                                dengan membawa kartu pelajar untuk meminjam buku yang diinginkan.
                            </div>
                        </div>
                        <div tabindex="0"
                            class="collapse collapse-arrow bg-base-100 border border-neutral-200 text-neutral-900 mb-3">
                            <div class="collapse-title font-semibold">Berapa lama masa peminjaman buku?</div>
                            <div class="collapse-content text-sm text-neutral-600">
                                Masa peminjaman buku untuk siswa adalah 7 hari (1 minggu). Pastikan mengembalikan
                                buku tepat waktu untuk menghindari denda keterlambatan.
                            </div>
                        </div>
                        <div tabindex="0"
                            class="collapse collapse-arrow bg-base-100 border border-neutral-200 text-neutral-900 mb-3">
                            <div class="collapse-title font-semibold">Apakah siswa perlu membayar untuk meminjam buku?
                            </div>
                            <div class="collapse-content text-sm text-neutral-600">
                                Tidak, seluruh siswa SMA dapat meminjam buku secara gratis. Namun akan dikenakan
                                denda jika terlambat mengembalikan buku sesuai ketentuan yang berlaku.
                            </div>
                        </div>
                        <div tabindex="0"
                            class="collapse collapse-arrow bg-base-100 border border-neutral-200 text-neutral-900">
                            <div class="collapse-title font-semibold">Bagaimana cara mengembalikan buku yang dipinjam?
                            </div>
                            <div class="collapse-content text-sm text-neutral-600">
                                Siswa dapat mengembalikan buku dengan datang langsung ke perpustakaan dan
                                menyerahkan buku kepada petugas perpustakaan untuk dicatat pengembaliannya.
                            </div>
                        </div>

                    </div>

                </div>
            </section>

            <section data-aos="fade-up">
                <h1 class="text-3xl font-semibold text-center text-neutral-900 mx-auto">Gallery Kegiatan
                    Perpustakaan</h1>
                <p class="text-sm text-neutral-500 text-center mt-2 max-w-lg mx-auto">Dokumentasi kegiatan literasi dan
                    pembelajaran siswa di perpustakaan - menangkap momen semangat belajar dan membaca siswa-siswi SMA.</p>
                <div class="flex items-center gap-2 h-[400px] w-full max-w-4xl mt-10 mx-auto">
                    <div
                        class="relative group flex-grow transition-all w-56 rounded-lg overflow-hidden h-[400px] duration-500 hover:w-full">
                        <img class="h-full w-full object-cover object-center"
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRErvpgzF8MCw_rhpHdljrc9zHyFq6mmUl0RDZiuYz6CL8SeAQ0lUc7deasrZjZ3LRnif0&usqp=CAU"
                            alt="image">
                    </div>
                    <div
                        class="relative group flex-grow transition-all w-56 rounded-lg overflow-hidden h-[400px] duration-500 hover:w-full">
                        <img class="h-full w-full object-cover object-center"
                            src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEipB0wh0kI_azDRROQrKZdygt0kzqkJF3HaDzTABWG-PhuFVtuWZoVA73Yivr9h9RlvCuGwvU4SeboO3OgA1ZjW9v6-Luwzwv57qrPNIhEEXW0pXIPY5Q8EsuTyuNjmnBkUneg4hzKZOJi0/s1600/IMG_20160820_101013.jpg"
                            alt="image">
                    </div>
                    <div
                        class="relative group flex-grow transition-all w-56 rounded-lg overflow-hidden h-[400px] duration-500 hover:w-full">
                        <img class="h-full w-full object-cover object-center"
                            src="https://img.harianjogja.com/posts/2018/09/18/940474/perpustakaan.jpg" alt="image">
                    </div>
                    <div
                        class="relative group flex-grow transition-all w-56 rounded-lg overflow-hidden h-[400px] duration-500 hover:w-full">
                        <img class="h-full w-full object-cover object-center"
                            src="https://persisbangil.sch.id/wp-content/uploads/2022/03/20201229_124905-scaled.jpg"
                            alt="image">
                    </div>
                    <div
                        class="relative group flex-grow transition-all w-56 rounded-lg overflow-hidden h-[400px] duration-500 hover:w-full">
                        <img class="h-full w-full object-cover object-center"
                            src="https://tebuireng.online/wp-content/uploads/2020/03/Perpus-Maha.jpeg" alt="image">
                    </div>
                    <div
                        class="relative group flex-grow transition-all w-56 rounded-lg overflow-hidden h-[400px] duration-500 hover:w-full">
                        <img class="h-full w-full object-cover object-center"
                            src="https://www.pesantrenalirsyad6.org/wp-content/uploads/2024/09/PERPUS_5.jpg"
                            alt="image">
                    </div>
                </div>
            </section>

            <!-- CTA Section with Filament Amber Gradient -->
            <section data-aos="fade-up"
                class="flex flex-col items-center justify-center mx-auto max-md:mx-2 max-md:px-2 max-w-5xl w-full text-center rounded-2xl py-20 my-20 md:py-24 bg-cover bg-center bg-no-repeat shadow-2xl"
                style="background: linear-gradient(135deg, oklch(0.644 0.159 61.857) 0%, oklch(0.769 0.145 68.293) 50%, oklch(0.889 0.086 70.969) 100%);">
                <h1 class="text-2xl md:text-3xl font-semibold text-white max-w-2xl drop-shadow-sm">
                    Raih Prestasi Akademik Lebih Baik dengan
                    Perpustakaan Digital SMA
                </h1>
                <div class="h-[3px] w-32 my-3 bg-gradient-to-l from-transparent via-white/80 to-white/40 rounded-full">
                </div>
                <p class="text-sm md:text-base text-white/95 max-w-xl drop-shadow-sm leading-relaxed">
                    Akses koleksi buku pelajaran dan literasi, kelola peminjaman dengan mudah,
                    dan tingkatkan prestasi belajarmu dengan fasilitas perpustakaan modern sekolah.
                </p>
                <a href="{{ route('catalog') }}"
                    class="mt-6 px-10 py-3.5 bg-white text-primary-700 font-semibold rounded-full
                          hover:bg-primary-50 hover:shadow-primary-lg hover:-translate-y-0.5
                          transition-all duration-300 inline-flex items-center gap-2">
                    <span>Mulai Sekarang</span>
                    <i class="iconoir-arrow-right text-lg"></i>
                </a>
            </section>
        </div>
    @endvolt
</x-guest-layout>
