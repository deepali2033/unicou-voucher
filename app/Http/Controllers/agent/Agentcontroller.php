<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use App\Models\BankAccountModel;
use App\Helpers\CountryHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class Agentcontroller extends Controller
{



    public function getLocationData()
    {
        // Allow forcing a refresh via query param ?refresh_location=1
        if (request()->has('refresh_location')) {
            session()->forget('user_country_code');
        }

        if (session()->has('user_country_code')) {
            return [
                'countryCode' => session('user_country_code'),
                'timezone'    => session('user_timezone'),
                'currency'    => session('user_currency'),
                'flag'        => session('user_flag'),
            ];
        }

        try {
            $response = Http::timeout(5)->get('https://ipapi.co/json/');
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

            session()->forget('api_error');

            return [
                'countryCode' => $countryCode,
                'timezone'    => $data['timezone'] ?? 'UTC',
                'currency'    => $data['currency'] ?? 'USD',
                'flag'        => $flagUrl,
            ];
        } catch (\Exception $e) {
            session(['api_error' => 'Location API failed. Defaulting to US.']);

            $defaults = [
                'countryCode' => 'US',
                'timezone'    => 'UTC',
                'currency'    => 'USD',
                'flag'        => 'https://flagcdn.com/w40/us.png',
            ];

            session([
                'user_country_code' => $defaults['countryCode'],
                'user_country_name' => 'United States',
                'user_timezone'     => $defaults['timezone'],
                'user_currency'     => $defaults['currency'],
                'user_flag'         => $defaults['flag'],
            ]);

            return $defaults;
        }
    }



    // public function index()
    // {
    //     $response = Http::get('https://ipapi.co/json/');
    //     dd($response->json());
    // }
    public function dashboard()
    {
        $location = $this->getLocationData();
        $currentTime = now()->timezone($location['timezone'])->format('l d F Y, h:i A');
        return view('agent.dashboard', compact('currentTime'));
    }

    public function orderHistory()
    {
        $this->getLocationData();
        return view('agent.order_history');
    }

    public function vouchers()
    {
        $this->getLocationData();
        return view('agent.vouchers');
    }

    public function banks()
    {
        $this->getLocationData();
        $banks = BankAccountModel::where('user_id', Auth::id())->get();

        return view('agent.bank_link', compact('banks'));
    }

    public function deposit()
    {
        $location = $this->getLocationData();
        $countryData = CountryHelper::getCountryData($location['countryCode']);
        $banks = BankAccountModel::where('user_id', Auth::id())->get();

        return view('agent.deposit', compact('banks', 'countryData'));
    }

    public function bankLink()
    {
        $location = $this->getLocationData();
        $countryData = CountryHelper::getCountryData($location['countryCode']);
        $banks =   $banks = BankAccountModel::where('user_id', Auth::id())->get();
        return view('agent.bank_link', compact('countryData', 'banks', 'location'));
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required',
            'account_number' => 'required',
            'id_number' => 'required',
        ]);

        BankAccountModel::create([
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'cnic' => $request->id_number, // Mapping generic ID to existing cnic column
        ]);

        return redirect()->route('agent.deposit.store.credit')->with('success', 'Bank linked successfully');
    }
}
