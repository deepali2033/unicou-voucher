<?php

namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use App\Models\BankAccountModel;
use App\Helpers\CountryHelper;
use App\Helpers\LocationHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Order;
use App\Models\User;

class Agentcontroller extends Controller
{

    public function dashboard()
    {
        LocationHelper::storeLocationInSession();
        $currentTime = now()->timezone(session('user_timezone', 'UTC'))->format('l d F Y, h:i A');
        return view('agent.dashboard', compact('currentTime'));
    }

    public function orderHistory(Request $request)
    {
        LocationHelper::storeLocationInSession();
        
        $query = Order::where('user_id', Auth::id());

        // Date Filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Order ID Filter
        if ($request->filled('from_order_id')) {
            $query->where('order_id', '>=', $request->from_order_id);
        }
        if ($request->filled('to_order_id')) {
            $query->where('order_id', '<=', $request->to_order_id);
        }

        // Voucher Type Filter
        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->voucher_type);
        }

        // Bank Wise Filter
        if ($request->filled('bank')) {
            $query->where('bank_name', 'like', '%' . $request->bank . '%');
        }

        // Sub Agent Filter (For Reseller Agents)
        if (Auth::user()->account_type == 'reseller_agent' && $request->filled('sub_agent')) {
            $query->where('sub_agent_id', $request->sub_agent);
        }

        $orders = $query->latest()->paginate(15);

        // Stats for the top cards
        $stats = [
            'points_earned' => Order::where('user_id', Auth::id())->sum('points_earned'),
            'points_redeemed' => Order::where('user_id', Auth::id())->sum('points_redeemed'),
            'current_bonus' => Order::where('user_id', Auth::id())->sum('bonus_amount'),
        ];

        // Fetch sub agents for the filter dropdown
        $subAgents = [];
        if (Auth::user()->account_type == 'reseller_agent') {
            // Assuming sub agents might be linked via some logic, for now just fetch other users or a specific role
            $subAgents = User::where('account_type', 'student')->get(); // Placeholder logic
        }

        return view('agent.order_history', compact('orders', 'stats', 'subAgents'));
    }

    public function vouchers()
    {
        LocationHelper::storeLocationInSession();
        return view('agent.vouchers');
    }

    public function banks()
    {
        LocationHelper::storeLocationInSession();
        $banks = BankAccountModel::where('user_id', Auth::id())->get();

        return view('agent.bank_link', compact('banks'));
    }

    public function deposit()
    {
        LocationHelper::storeLocationInSession();
        $countryData = CountryHelper::getCountryData(session('user_country_code', 'US'));
        $banks = BankAccountModel::where('user_id', Auth::id())->get();

        return view('agent.deposit', compact('banks', 'countryData'));
    }

    public function bankLink()
    {
        LocationHelper::storeLocationInSession();
        $countryData = CountryHelper::getCountryData(session('user_country_code', 'US'));
        $banks =   $banks = BankAccountModel::where('user_id', Auth::id())->get();
        return view('agent.bank_link', compact('countryData', 'banks'));
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
