<?php

use function Livewire\Volt\{state, usesFileUploads};
use App\Models\{UserDetail};
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

usesFileUploads();

state(['user', 'userDetail']);

state([
    'name' => fn() => $this->user->name ?? '',
    'email' => fn() => $this->user->email ?? '',
    'nik' => fn() => $this->userDetail?->nik ?? '',
    'nis' => fn() => $this->userDetail?->nis ?? '',
    'nisn' => fn() => $this->userDetail?->nisn ?? '',
    'class' => fn() => $this->userDetail?->class ?? '',
    'address' => fn() => $this->userDetail?->address ?? '',
    'phone_number' => fn() => $this->userDetail?->phone_number ?? '',
    'birth_date' => fn() => $this->userDetail?->birth_date?->format('Y-m-d') ?? '',
    'birth_place' => fn() => $this->userDetail?->birth_place ?? '',
    'gender' => fn() => $this->userDetail?->gender ?? '',
    'religion' => fn() => $this->userDetail?->religion ?? '',
    'profile_photo',
]);

$updateProfile = function () {
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,

        'nik' => 'nullable|string|max:20', // Nomor Induk Kependudukan
        'nis' => 'nullable|string|max:20', // Nomor Induk Siswa
        'nisn' => 'nullable|string|max:20', // Nomor Siswa Nasional
        'class' => 'nullable|string|max:50',
        'address' => 'nullable|string|max:500',
        'phone_number' => 'nullable|string|max:20',
        'birth_date' => 'nullable|date|before:today',
        'birth_place' => 'nullable|string|max:100',
        'gender' => 'nullable|in:male,female',
        'religion' => 'nullable|string|in:islam,christian,catholic,hindu,buddhist,confucianism,other|max:50',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    $this->validate($rules);

    // Update user
    $this->user->update([
        'name' => $this->name,
        'email' => $this->email,
    ]);

    // Handle profile photo upload
    $profilePhotoPath = null;
    if ($this->profile_photo) {
        $profilePhotoPath = $this->profile_photo->store('profile-photos', 'public');
    }

    // Update or create user detail
    // Convert empty strings to null for nullable fields
    $userDetailData = [
        'nik' => $this->nik ?: null,
        'nis' => $this->nis ?: null,
        'nisn' => $this->nisn ?: null,
        'class' => $this->class ?: null,
        'address' => $this->address ?: null,
        'phone_number' => $this->phone_number ?: null,
        'birth_date' => $this->birth_date ?: null,
        'birth_place' => $this->birth_place ?: null,
        'gender' => $this->gender ?: null,
        'religion' => $this->religion ?: null,
        'profile_photo' => $profilePhotoPath ?? ($this->userDetail?->profile_photo ?? null),
    ];

    if ($this->userDetail) {
        $this->userDetail->update($userDetailData);
    } else {
        $userDetailData['user_id'] = $this->user->id;
        UserDetail::create($userDetailData);
    }

    LivewireAlert::title('Berhasil')->text('Update Data pengguna berhasil!')->success()->show();

    $this->redirectRoute('profile');
};

?>

@volt
<x-form wire:submit="updateProfile">
    <div class="px-6 py-12 space-y-6">

        <!-- FOTO PROFIL -->
        <div class="flex items-center gap-4">

            {{-- PREVIEW --}}
            @if ($profile_photo)
                <img
                    src="{{ $profile_photo->temporaryUrl() }}"
                    class="w-24 h-24 rounded-lg object-cover border"
                >
            @elseif ($userDetail?->profile_photo)
                <img
                    src="{{ Storage::url($userDetail->profile_photo) }}"
                    class="w-24 h-24 rounded-lg object-cover border"
                >
            @else
                <div class="w-24 h-24 rounded-lg border bg-base-200"></div>
            @endif

            {{-- FILE INPUT --}}
            <div class="flex-1">
                <x-file
                    label="Foto Profil"
                    wire:model="profile_photo"
                    accept="image/*"
                />
            </div>
        </div>

        <!-- NAMA -->
        <x-input
            label="Nama Lengkap"
            wire:model="name"
            placeholder="Masukkan nama lengkap"
            icon="o-user"
        />

        <!-- GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- KOLOM KIRI -->
            <div class="space-y-6">
                <x-input
                    label="Email"
                    type="email"
                    wire:model="email"
                    placeholder="Masukkan email"
                    icon="o-envelope"
                />

                <x-input
                    label="Nomor Telepon"
                    wire:model="phone_number"
                    placeholder="Contoh: 08123456789"
                    icon="o-phone"
                />

                <x-input
                    label="Tanggal Lahir"
                    type="date"
                    wire:model="birth_date"
                />

                <x-input
                    label="Tempat Lahir"
                    wire:model="birth_place"
                    placeholder="Masukkan tempat lahir"
                />

                <x-select
                    label="Jenis Kelamin"
                    wire:model="gender"
                    :options="[
        '' => 'Pilih jenis kelamin',
        'male' => 'Laki-laki',
        'female' => 'Perempuan'
    ]"
                />
            </div>

            <!-- KOLOM KANAN -->
            <div class="space-y-6">
                <x-select
                    label="Agama"
                    wire:model="religion"
                    :options="[
        '' => 'Pilih agama',
        'islam' => 'Islam',
        'christian' => 'Kristen Protestan',
        'catholic' => 'Katolik',
        'hindu' => 'Hindu',
        'buddhist' => 'Buddha',
        'confucianism' => 'Konghucu',
        'other' => 'Lainnya',
    ]"
                />

                <x-input
                    label="NIK"
                    type="number"
                    wire:model="nik"
                />

                <x-input
                    label="NIS (Nomor Induk Siswa)"
                    type="number"
                    wire:model="nis"
                />

                <x-input
                    label="NISN"
                    wire:model="nisn"
                />

                <x-input
                    label="Kelas"
                    wire:model="class"
                    placeholder="Contoh: XII IPA 2"
                />
            </div>
        </div>

        <!-- ALAMAT -->
        <x-textarea
            label="Alamat Lengkap"
            wire:model="address"
            rows="4"
            placeholder="Masukkan alamat lengkap"
        />

        <!-- BUTTON -->
        <x-slot:actions>
            <div class="flex justify-end">
                <x-button
                    label="Update Data"
                    class="btn-primary px-6"
                    type="submit"
                    spinner="updateProfile"
                />
            </div>
        </x-slot:actions>

    </div>
</x-form>
@endvolt
