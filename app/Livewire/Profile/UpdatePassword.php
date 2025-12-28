<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class UpdatePassword extends Component
{
    public $user;

    public $userDetail;

    public $password;

    public $current_password;

    public $password_confirmation;

    public function mount($user, $userDetail): void
    {
        $this->user = $user;
        $this->userDetail = $userDetail;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($this->current_password, $this->user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');

            return;
        }

        $this->user->update([
            'password' => Hash::make($this->password),
        ]);

        LivewireAlert::title('Berhasil')->text('Update password pengguna berhasil!')->success()->show();

        $this->reset('password', 'current_password', 'password_confirmation');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.profile.update-password');
    }
}
