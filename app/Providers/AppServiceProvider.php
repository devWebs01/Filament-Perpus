<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\UserDetail;
use App\Observers\BookObserver;
use App\Observers\UserDetailObserver;
use App\Services\BarcodeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register BarcodeService sebagai singleton
        $this->app->singleton(BarcodeService::class);
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
