<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    // private function getLocationData()
    // {

    //     $response = Http::get('https://ipapi.co/json/');


    //     return [
    //         'countryCode' => $countryCode,
    //         'timezone'    => $response['timezone'] ?? 'UTC',
    //         'currency'    => $response['currency'] ?? 'USD',
    //         'flag'        => $flagUrl,
    //     ];
    // }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
