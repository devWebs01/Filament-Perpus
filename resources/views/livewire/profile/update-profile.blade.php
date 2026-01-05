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

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Jenis Kelamin</legend>
                    <select class="select w-full" wire:model="gender">
                        <option disabled selected value="">Pilih jenis kelamin</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                    @error('gender')
                        <span class="label text-red-500">{{ $message }}</span>
                    @enderror
                </fieldset>
            </div>

            <!-- KOLOM KANAN -->
            <div class="space-y-6">

                <x-input label="NIK" type="number" wire:model="nik" />

                <x-input label="NIS (Nomor Induk Siswa)" type="number" wire:model="nis" />

                <x-input label="NISN" wire:model="nisn" />

                <x-input label="Kelas" wire:model="class" placeholder="Contoh: XII IPA 2" />

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Agama</legend>
                    <select class="select w-full" wire:model="religion">
                        <option disabled selected value="">Pilih agama</option>
                        <option value="islam">Islam</option>
                        <option value="christian">Kristen Protestan</option>
                        <option value="catholic">Katolik</option>
                        <option value="hindu">Hindu</option>
                        <option value="buddhist">Buddha</option>
                        <option value="confucianism">Konghucu</option>
                        <option value="other">Lainnya</option>
                    </select>
                    @error('religion')
                        <span class="label text-red-500">{{ $message }}</span>
                    @enderror
                </fieldset>
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
