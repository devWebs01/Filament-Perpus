<div data-aos="fade-up" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

    {{-- Sedang Dipinjam --}}
    <div class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900/40 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-700 dark:text-gray-300">
                    {{ $stats['active'] }}
                </div>
                <div class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Sedang Dipinjam
                </div>
                <div class="text-sm text-gray-700/80 dark:text-gray-300/70">
                    Buku aktif saat ini
                </div>
            </div>

            <div class="bg-primary-200 dark:bg-primary-800 p-4 rounded-lg">
                <i class="iconoir-book text-primary-700 dark:text-primary-200 text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- Selesai Dibaca --}}
    <div class="border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900/40 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-700 dark:text-gray-300">
                    {{ $stats['returned'] }}
                </div>
                <div class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Selesai Dibaca
                </div>
                <div class="text-sm text-gray-700/80 dark:text-gray-300/70">
                    Sudah dikembalikan
                </div>
            </div>

            <div class="bg-success-200 dark:bg-success-800 p-4 rounded-lg">
                <i class="iconoir-check-circle text-success-700 dark:text-success-200 text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- Terlambat --}}
    <div class="rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900/40 p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold text-gray-700 dark:text-gray-300">
                    {{ $stats['overdue'] }}
                </div>

                <div class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Terlambat
                </div>

                <div class="text-sm text-gray-700/80 dark:text-gray-300/70">
                    {{ $stats['overdue'] > 0 ? 'Segera kembalikan!' : 'Tidak ada keterlambatan' }}
                </div>
            </div>

            <div class="bg-red-200 dark:bg-red-800 p-4 rounded-lg">
                <i class="iconoir-warning-circle text-red-700 dark:text-red-200 text-3xl"></i>
            </div>
        </div>
    </div>

</div>
