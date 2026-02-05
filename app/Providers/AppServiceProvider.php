<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

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
        // Veritabanı anahtar uzunluğu
        Schema::defaultStringLength(191);

        // Tarih ve Saat için Türkçe Ayarı
        setlocale(LC_TIME, 'tr_TR.utf8', 'tr_TR', 'turkish');
        Carbon::setLocale('tr');
    }
}
