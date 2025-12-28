<div class="px-6 py-12">

    <x-form wire:submit="updatePassword">
        <!-- PASSWORD SAAT INI -->
        <x-input label="Password Saat Ini" type="password" wire:model="current_password"
            placeholder="Masukkan password saat ini" icon="o-key" />

        <!-- PASSWORD BARU -->
        <x-input class="mt-6" label="Password Baru" type="password" wire:model="password"
            placeholder="Masukkan password baru (min. 8 karakter)" icon="o-lock-closed" />

        <!-- KONFIRMASI PASSWORD -->
        <x-input class="mt-6" label="Konfirmasi Password Baru" type="password" wire:model="password_confirmation"
            placeholder="Ulangi password baru" icon="o-lock-closed" />

        <x-slot:actions>
            <div class="w-full flex justify-end">
                <x-button label="Update Password" type="submit" class="btn-primary px-4" spinner="updatePassword" />
            </div>
        </x-slot:actions>
    </x-form>

</div>
