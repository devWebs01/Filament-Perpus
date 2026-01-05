<?php

use function Laravel\Folio\name;
use function Livewire\Volt\{state, computed};
use App\Models\Setting;

name('profile');

state([
    'user' => auth()->user(),
    'userDetail' => auth()->user()->userDetail ?? null,
    'activeTab' => 'user_detail',
    'setting' => fn() => Setting::first(),
]);

// Badge counters - computed, hanya dihitung saat dibutuhkan
$profileStatus = computed(function () {
    if (!auth()->check()) {
        return [];
    }

    $user = auth()->user();
    $userDetail = $user->userDetail;

    $missing = [];

    if (!$userDetail || !$userDetail->nis || !$userDetail->phone_number) {
        $missing[] = 'profil belum lengkap';
    }

    if (is_null($user->email_verified_at)) {
        $missing[] = 'email belum diverifikasi';
    }

    return $missing;
});

?>

<x-guest-layout>
    <x-slot name="title">Profil Saya</x-slot>

    @volt
        <div x-data="{
            activeTab: 'user_detail',
            switchTab(tab) {
                this.activeTab = tab;
            }
        }">

            <!-- Single Unified Profile Card -->
            <div class="max-w-7xl mx-auto px-4 pt-4">

                @if (count($this->profileStatus))
                    <div role="alert" class="alert alert-warning bg-primary-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.846-.816 1.846-1.857V6.857C20.764 5.816 19.972 5 18.918 5H6.062C5.008 5 4.216 5.816 4.216 6.857v10.286C4.216 17.184 5.008 18 6.062 18z" />
                        </svg>

                        <span>
                            {{-- Jika hanya satu masalah --}}
                            @if (count($this->profileStatus) === 1)
                                @if ($this->profileStatus[0] === 'email belum diverifikasi')
                                    Email Anda belum diverifikasi.
                                    Silakan melakukan verifikasi
                                    <strong>
                                        langsung ke petugas perpustakaan di lokasi
                                        perpustakaan.
                                    </strong>
                                @elseif ($this->profileStatus[0] === 'profil belum lengkap')
                                    Profil Anda belum lengkap. Harap lengkapi data diri terlebih dahulu.
                                @endif

                                {{-- Jika dua-duanya --}}
                            @else
                                Beberapa hal perlu diperbarui: profil belum lengkap dan email belum diverifikasi.
                                Untuk verifikasi email, silakan datang
                                <strong>
                                    langsung ke petugas perpustakaan.
                                </strong>
                                Dan mohon lengkapi data profil Anda terlebih dahulu.
                            @endif
                        </span>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <!-- Profile Header Section -->
                    <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                            <!-- Profile Photo -->
                            <div class="relative flex-shrink-0">
                                @if ($userDetail && $userDetail->profile_photo)
                                    <img src="{{ Storage::url($userDetail->profile_photo) }}"
                                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border border-gray-300 dark:border-gray-600">
                                @else
                                    <div
                                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-600 flex items-center justify-center">
                                        <span class="text-white text-xl font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif

                                <div
                                    class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800">
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="w-full sm:w-auto">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h1 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $user->name }}
                                    </h1>

                                    @if ($userDetail && $userDetail->membership_status)
                                        @php
                                            $statusColors = [
                                                'active' =>
                                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                'suspended' =>
                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-20',
                                                'expired' =>
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'pending' =>
                                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            ];
                                            $statusLabels = [
                                                'active' => 'Aktif',
                                                'suspended' => 'Ditangguhkan',
                                                'expired' => 'Kadaluarsa',
                                                'pending' => 'Menunggu',
                                            ];
                                        @endphp
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium {{ $statusColors[$userDetail->membership_status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }} rounded-full">
                                            {{ $statusLabels[$userDetail->membership_status] ?? 'Tidak Diketahui' }}
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 rounded-full">
                                            Tidak Diketahui
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Terdaftar sejak
                                    {{ $userDetail?->join_date
                                        ? \Carbon\Carbon::parse($userDetail->join_date)->format('d M Y')
                                        : \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}
                                </p>
                            </div>

                            <!-- Kartu Anggota Button -->
                            <div class="flex-1 min-w-0 sm:flex-none sm:ml-auto text-end">
                                <button onclick="openProfileCardModal()"
                                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2
                   bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium
                   rounded-lg transition-all shadow-sm">

                                    <i class="iconoir-hand-card text-base"></i>
                                    <span>Kartu Anggota</span>
                                </button>
                            </div>

                        </div>
                    </div>

                    <div class="w-full mt-6">
                        <ul class="flex">
                            <!-- TAB 1 -->
                            <li @click="switchTab('user_detail')"
                                :class="activeTab === 'user_detail' ? 'bg-gray-400 text-white font-bold' : 'font-medium'"
                                class="relative tab flex-1 btn mx-4 rounded-lg cursor-pointer flex items-center gap-2 text-gray-600 dark:text-gray-100">

                                <i class="iconoir-view-structure-down text-2xl"></i>
                                <span class="hidden lg:block">Informasi Data User</span>
                            </li>

                            <!-- TAB 2 (punya indikator jika profile incomplete) -->
                            <li @click="switchTab('update_data_user')"
                                :class="activeTab === 'update_data_user' ? 'bg-gray-400 text-white font-bold' : 'font-medium'"
                                class="relative tab flex-1 btn mx-4 rounded-lg cursor-pointer flex items-center gap-2 text-gray-600 dark:text-gray-100">

                                <i class="iconoir-user-badge-check text-2xl"></i>
                                <span class="hidden lg:block">Update Data User</span>

                                {{-- INDICATOR --}}
                                @if (in_array('profil belum lengkap', $this->profileStatus))
                                    <span x-show="activeTab !== 'update_data_user'"
                                        class="absolute -top-1 -right-1 h-3 w-3 rounded-full bg-red-500 animate-pulse"></span>
                                @endif
                            </li>

                            <!-- TAB 3 -->
                            <li @click="switchTab('update_password')"
                                :class="activeTab === 'update_password' ? 'bg-gray-400 text-white font-bold' : 'font-medium'"
                                class="relative tab flex-1 btn mx-4 rounded-lg cursor-pointer flex items-center gap-2 text-gray-600 dark:text-gray-100">

                                <i class="iconoir-lock text-2xl"></i>
                                <span class="hidden lg:block">Update Password</span>
                            </li>
                        </ul>

                        {{-- TAB 1: Static content, TANPA Livewire --}}
                        <div x-show="activeTab === 'user_detail'" x-transition:opacity class="p-6">
                            @include('pages.profile.user_detail', [
                                'user' => $user,
                                'userDetail' => $userDetail,
                            ])
                        </div>

                        {{-- TAB 2: Livewire, hanya dirender saat tab aktif --}}
                        <div x-show="activeTab === 'update_data_user'" x-transition:opacity>
                            @livewire('profile.update-profile', [
                                'user' => $user,
                                'userDetail' => $userDetail,
                            ])
                        </div>

                        {{-- TAB 3: Livewire, hanya dirender saat tab aktif --}}
                        <div x-show="activeTab === 'update_password'" x-transition:opacity>
                            @livewire('profile.update-password', [
                                'user' => $user,
                                'userDetail' => $userDetail,
                            ])
                        </div>

                    </div>

                </div>
            </div>

            <!-- Profile Card Modal -->
            <div id="profileCardModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" onclick="closeProfileCardModal()" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>

                    <!-- Modal Content -->
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                        <!-- Modal Header -->
                        <div class="bg-white px-4 py-3 border-b border-gray-200 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Kartu Anggota Perpustakaan
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <button onclick="printProfileCard()"
                                        class="inline-flex items-center px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Cetak
                                    </button>
                                    <button onclick="closeProfileCardModal()"
                                        class="inline-flex items-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Body with Profile Cards -->
                        <div class="bg-white px-4 py-4 sm:px-6 sm:py-6">
                            <div id="printArea">
                                <div id="profileCardContainer" class="flex justify-center">
                                    @include('pages.profile.preview_card')
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal Scripts -->
            <script>
                function openProfileCardModal() {
                    document.getElementById('profileCardModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }

                function closeProfileCardModal() {
                    document.getElementById('profileCardModal').classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }

                function printProfileCard() {
                    // Redirect to dedicated print page
                    window.open('/profile/card-print', '_blank');
                }

                // Close modal on Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeProfileCardModal();
                    }
                });

                // Close modal when clicking outside
                document.getElementById('profileCardModal').addEventListener('click', function(event) {
                    if (event.target === this) {
                        closeProfileCardModal();
                    }
                });
            </script>
        </div>
    @endvolt
</x-guest-layout>
