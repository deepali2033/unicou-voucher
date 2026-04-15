<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyHelper
{
    public static function convert($amount, $from, $to = 'USD')
    {
        // Cache for 1 hour
        $rates = Cache::remember("currency_rates_{$from}", 3600, function () use ($from) {
            $response = Http::get("https://api.exchangerate-api.com/v4/latest/{$from}");

            if ($response->failed()) {
                throw new \Exception('Currency API failed');
            }

            return $response->json();
        });

        if (!isset($rates['rates'][$to])) {
            throw new \Exception("Conversion rate not available");
        }

        $rate = $rates['rates'][$to];

        return $amount * $rate;
    }

}