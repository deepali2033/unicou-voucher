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



    private function getLocationData()
    {
        // 1️⃣ API call
        $response = Http::get('https://ipapi.co/json/');
        $data = $response->json();

        // 2️⃣ Session SET
        session([
            'user_country_code' => $data['country_code'] ?? 'IN',
            'user_country_name' => $data['country_name'] ?? 'India',
            'user_timezone'     => $data['timezone'] ?? 'Asia/Kolkata',
            'user_currency'     => $data['currency'] ?? 'INR',
        ]);
        dd($response);
        // 3️⃣ Debug output (confirm karne ke liye)
        return response()->json([
            'message' => 'Session set successfully',
            'session_data' => [
                'country_code' => session('user_country_code'),
                'country_name' => session('user_country_name'),
                'timezone'     => session('user_timezone'),
                'currency'     => session('user_currency'),
            ]
        ]);
    }




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

        return view('agent.banks', compact('banks'));
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
