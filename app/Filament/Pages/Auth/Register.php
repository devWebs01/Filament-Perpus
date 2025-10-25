<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Validation\ValidationException;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nama Lengkap')
            ->required()
            ->maxLength(255)
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Alamat Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Kata Sandi')
            ->password()
            ->required()
            ->revealable()
            ->rule('confirmed')
            ->extraInputAttributes(['tabindex' => 3]);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('password_confirmation')
            ->label('Konfirmasi Kata Sandi')
            ->password()
            ->required()
            ->revealable()
            ->extraInputAttributes(['tabindex' => 4]);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => __('Coba lagi dalam :seconds detik.', [
                    'seconds' => $exception->secondsUntilAvailable,
                ]),
            ]);
        }

        $data = $this->form->getState();

        $user = $this->getUserModel()::create($data);

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }

    public function getHeading(): string
    {
        return 'Buat Akun Baru';
    }

    public function subheading(): string
    {
        return 'Isi formulir di bawah ini untuk membuat akun pengguna baru';
    }
}
