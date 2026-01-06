<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LibraryStatsWidget;
use App\Filament\Widgets\OverdueBooksWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Http\Middleware\RedirectIfNotFilamentAdmin;
use App\Models\Setting;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentDeveloperLogins\FilamentDeveloperLoginsPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->emailChangeVerification()
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                LibraryStatsWidget::class,
                RecentTransactionsWidget::class,
                OverdueBooksWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                // Authenticate::class,
                RedirectIfNotFilamentAdmin::class,
            ])
            ->brandName(Setting::first()->name ?? 'Perpustakaan')
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationLabel('Hak Akses')
                    ->navigationGroup('Manajemen Pengguna')
                    ->modelLabel('Hak Akses')
                    ->pluralModelLabel('Hak Akses'),
                FilamentEditProfilePlugin::make()
                    ->setTitle('Profil Akun')
                    ->setNavigationLabel('Profil Akun')
                    ->setNavigationGroup('Manajemen Pengguna')
                    ->setIcon('heroicon-o-user'),
                FilamentDeveloperLoginsPlugin::make()
                    ->enabled(app()->environment('local'))
                    ->users(fn () => \App\Models\User::query()->whereIn('email', [
                        'kepala@testing.com',
                        'siswa@testing.com',
                        'petugas1@testing.com',
                    ])->get()->pluck('email', 'name')->toArray()),
            ])
            ->unsavedChangesAlerts();
    }
}
