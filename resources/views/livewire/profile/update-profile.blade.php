<x-form wire:submit="updateProfile">
    <div class="px-6 py-12 space-y-6">

        <!-- FOTO PROFIL -->
        <div class="flex items-center gap-4">

            {{-- PREVIEW --}}
            @if ($profile_photo)
                <img src="{{ $profile_photo->temporaryUrl() }}" class="w-24 h-24 rounded-lg object-cover border">
            @elseif ($userDetail?->profile_photo)
                <img src="{{ Storage::url($userDetail->profile_photo) }}"
                    class="w-24 h-24 rounded-lg object-cover border">
            @else
                <div class="w-24 h-24 rounded-lg border bg-base-200"></div>
            @endif

            {{-- FILE INPUT --}}
            <div class="flex-1">
                <x-file label="Foto Profil" wire:model="profile_photo" accept="image/*" />
            </div>
        </div>

        <!-- NAMA -->
        <x-input label="Nama Lengkap" wire:model="name" placeholder="Masukkan nama lengkap" icon="o-user" />

        <!-- GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- KOLOM KIRI -->
            <div class="space-y-6">
                <x-input label="Email" type="email" wire:model="email" placeholder="Masukkan email"
                    icon="o-envelope" />

                <x-input label="Nomor Telepon" wire:model="phone_number" placeholder="Contoh: 08123456789"
                    icon="o-phone" />

                <x-input label="Tanggal Lahir" type="date" wire:model="birth_date" />

                <x-input label="Tempat Lahir" wire:model="birth_place" placeholder="Masukkan tempat lahir" />

                <x-select label="Jenis Kelamin" wire:model="gender" :options="[
                    '' => 'Pilih jenis kelamin',
                    'male' => 'Laki-laki',
                    'female' => 'Perempuan',
                ]" />
            </div>

            <!-- KOLOM KANAN -->
            <div class="space-y-6">
                <x-select label="Agama" wire:model="religion" :options="[
                    '' => 'Pilih agama',
                    'islam' => 'Islam',
                    'christian' => 'Kristen Protestan',
                    'catholic' => 'Katolik',
                    'hindu' => 'Hindu',
                    'buddhist' => 'Buddha',
                    'confucianism' => 'Konghucu',
                    'other' => 'Lainnya',
                ]" />

                <x-input label="NIK" type="number" wire:model="nik" />

                <x-input label="NIS (Nomor Induk Siswa)" type="number" wire:model="nis" />

                <x-input label="NISN" wire:model="nisn" />

                <x-input label="Kelas" wire:model="class" placeholder="Contoh: XII IPA 2" />
            </div>
        </div>

        <!-- ALAMAT -->
        <x-textarea label="Alamat Lengkap" wire:model="address" rows="4" placeholder="Masukkan alamat lengkap" />

        <!-- BUTTON -->
        <div class="flex justify-end">
            <x-button label="Update Data"
                class="w-full lg:w-auto flex items-center justify-center gap-2 px-4 py-2
                   bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium
                   rounded-lg transition-all shadow-sm"
                type="submit" spinner="updateProfile" />
        </div>
    </div>
</x-form>
