<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LibraryStatsWidget;
use App\Filament\Widgets\OverdueBooksWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Http\Middleware\RedirectIfNotFilamentAdmin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentDeveloperLogins\FilamentDeveloperLoginsPlugin;
use Filament\Actions\Action;
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
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;

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
            ->navigationGroups([
                'Manajemen Pengguna',
                'Manajemen Perpustakaan',
            ])
            ->brandName(fn () => \Illuminate\Support\Facades\Schema::hasTable('settings') ? (\App\Models\Setting::first()?->name ?? 'Perpustakaan') : 'Perpustakaan')
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationLabel('Hak Akses')
                    ->navigationGroup('Manajemen Pengguna')
                    ->modelLabel('Hak Akses')
                    ->pluralModelLabel('Hak Akses'),
                FilamentDeveloperLoginsPlugin::make()
                    ->enabled(app()->environment('local'))
                    ->users(function () {
                        // Mengambil satu user pertama untuk setiap role yang diinginkan
                        $roles = ['super_admin', 'petugas', 'siswa'];
                        $devUsers = [];

                        foreach ($roles as $role) {
                            $user = \App\Models\User::role($role)->first();
                            if ($user) {
                                // Format: 'Nama Role (Nama User)' => 'email@user.com'
                                $devUsers[ucfirst($role)] = $user->email;
                            }
                        }

                        return $devUsers;
                    }),
                FilamentEditProfilePlugin::make()
                    ->shouldRegisterNavigation(false),
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(
                        MyImages::make()
                            ->directory('images/backgrounds')
                    ),

            ])
            ->userMenuItems([
                    'profile' => Action::make('profile')
                        ->label(fn () => auth()->user()->name)
                        ->url(fn (): string => EditProfilePage::getUrl())
                        ->icon('heroicon-m-user-circle'),
            ])
            ->unsavedChangesAlerts();
    }
}
