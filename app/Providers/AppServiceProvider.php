<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\UserDetail;
use App\Observers\BookObserver;
use App\Observers\UserDetailObserver;
use App\Services\BarcodeScannerService;
use App\Services\BarcodeService;
use App\Services\TransactionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services sebagai singleton
        $this->app->singleton(BarcodeService::class);
        $this->app->singleton(BarcodeScannerService::class);
        $this->app->singleton(TransactionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        UserDetail::observe(UserDetailObserver::class);
        Book::observe(BookObserver::class);
    }
}
