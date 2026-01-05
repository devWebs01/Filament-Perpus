<div class="px-6 py-12">

    <x-form wire:submit="updatePassword">
        <!-- PASSWORD SAAT INI -->
        <x-input label="Password Saat Ini" type="password" wire:model="current_password"
            placeholder="Masukkan password saat ini" icon="o-key" />

        <!-- PASSWORD BARU -->
        <x-input label="Password Baru" type="password" wire:model="password"
            placeholder="Masukkan password baru (min. 8 karakter)" icon="o-lock-closed" />

        <!-- KONFIRMASI PASSWORD -->
        <x-input label="Konfirmasi Password Baru" type="password" wire:model="password_confirmation"
            placeholder="Ulangi password baru" icon="o-lock-closed" />

        <div class="w-full flex justify-end">
            <x-button label="Update Password" type="submit"
                class="w-full lg:w-auto flex items-center justify-center gap-2 px-4 py-2
                   bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium
                   rounded-lg transition-all shadow-sm"
                spinner="updatePassword" />
        </div>
    </x-form>

</div>
