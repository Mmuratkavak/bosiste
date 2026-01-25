<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MediaFile silindiğinde dosyaları da sil
        \App\Models\MediaFile::observe(\App\Observers\MediaFileObserver::class);
    }
}
