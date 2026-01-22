<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class LocationHelper
{
    public static function storeLocationInSession($request = null)
    {
        if (session()->has('user_country_code') && !request()->has('refresh_location')) {
            return;
        }

        try {
            $ip = $request ? $request->ip() : request()->ip();
            
            // Local IP check
            if ($ip == '127.0.0.1' || $ip == '::1') {
                $ip = ''; // Let ipapi.co detect public IP
            }

            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['error'])) {
                    throw new \Exception($data['reason'] ?? 'IP API Error');
                }

                $countryCode = $data['country_code'] ?? 'US';
                $flagUrl = "https://flagcdn.com/w40/" . strtolower($countryCode) . ".png";

                session([
                    'user_country_code' => $countryCode,
                    'user_country_name' => $data['country_name'] ?? 'United States',
                    'user_timezone'     => $data['timezone'] ?? 'UTC',
                    'user_currency'     => $data['currency'] ?? 'USD',
                    'user_city'         => $data['city'] ?? 'Unknown',
                    'user_flag'         => $flagUrl,
                ]);

                // Map symbols
                $symbols = ['PKR' => 'Rs.', 'INR' => '₹', 'BDT' => '৳', 'USD' => '$', 'AE' => 'DH'];
                if (isset($symbols[$data['currency'] ?? ''])) {
                    session(['user_currency_symbol' => $symbols[$data['currency']]]);
                }

                session()->forget('api_error');
            }
        } catch (\Exception $e) {
            session(['api_error' => 'Location detection failed. Defaulting to US.']);
            session([
                'user_country_code' => 'US',
                'user_country_name' => 'United States',
                'user_timezone'     => 'UTC',
                'user_currency'     => 'USD',
                'user_flag'         => 'https://flagcdn.com/w40/us.png',
                'user_currency_symbol' => '$',
            ]);
        }
    }
}
