<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AdminPaymentMethod;
use App\Models\BankAccountModel;
use App\Models\WalletLedger;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\FinancialConnections\Session;
use Stripe\PaymentIntent;


class BankController extends Controller
{


    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('bank.link')->with('error', 'Invalid payment');
        }


        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        // dd($session->payment_status);

        if ($session->payment_status === 'paid') {

            $amount = $session->amount_total / 100;

            $user = auth()->user();

            // ✅ Duplicate check using MODEL
            $exists = WalletLedger::where('transaction_id', $sessionId)->exists();

            if (!$exists) {

                // ✅ Wallet update
                $user->wallet_balance += $amount;
                $user->save();

                // ✅ Ledger entry using MODEL
                WalletLedger::create([
                    'transaction_id' => $sessionId,
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'source' => 'stripe',
                    'description' => 'Wallet Top-up via Stripe',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                ]);
            }

            return redirect()->route('bank.link')->with('success', 'Amount added successfully!');
        }

        return redirect()->route('bank.link')->with('error', 'Payment failed');
    }

    public function cancel()
    {
        return view('wallet.cancel');
    }
    public function createSession(Request $request)
    {
        try {

            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $amount = $request->input('amount');

            if (!$amount) {
                return back()->with('error', 'Amount missing');
            }

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card', 'us_bank_account'],
                'payment_method_options' => [
                    'us_bank_account' => [
                        'verification_method' => 'automatic',
                    ],
                ],

                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Wallet Top-up',
                        ],
                        'unit_amount' => $amount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('wallet.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('wallet.cancel'),
            ]);

            return redirect($session->url); // ✅ IMPORTANT

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }
    public function finalizeStripeLink(Request $request)
    {
        try {
            $secretKey = config('services.stripe.secret');
            Stripe::setApiKey($secretKey);

            $session = Session::retrieve($request->session_id, [
                'expand' => ['accounts'],
            ]);

            foreach ($session->accounts as $account) {
                // Check if account already exists
                $exists = BankAccountModel::where('user_id', auth()->id())
                    ->where('account_number', 'stripe_' . $account->id)
                    ->exists();

                if (!$exists) {
                    BankAccountModel::create([
                        'user_id' => auth()->id(),
                        'bank_name' => $account->institution_name ?? 'Linked Bank',
                        'account_holder_name' => auth()->user()->name,
                        'account_number' => 'stripe_' . $account->id, // Use stripe id for uniqueness
                        'ifsc_code' => 'STRIPE_LINKED', // Providing a default value
                        'account_type' => $account->category ?? 'Savings',
                        'balance' => 1000, // Dummy initial balance for testing
                        'is_verified' => true,
                    ]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Finalize Stripe Link Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $methods = AdminPaymentMethod::all();
        return view('dashboard.banks.manage', compact('methods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'method_type' => 'required|in:bank,upi,qr',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'bank_code_value' => 'nullable|string',
            'bank_code_type' => 'nullable|string',
            'upi_id' => 'nullable|string',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('payment_methods', 'public');
        }

        AdminPaymentMethod::create($data);

        return redirect()->back()->with('success', 'Payment method added successfully.');
    }

    public function update(Request $request, $id)
    {
        $method = AdminPaymentMethod::findOrFail($id);

        $request->validate([
            'method_type' => 'required|in:bank,upi,qr',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'upi_id' => 'nullable|string',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('qr_code')) {
            if ($method->qr_code) {
                Storage::disk('public')->delete($method->qr_code);
            }
            $data['qr_code'] = $request->file('qr_code')->store('payment_methods', 'public');
        }

        $method->update($data);

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }

    public function destroy($id)
    {
        $method = AdminPaymentMethod::findOrFail($id);
        if ($method->qr_code) {
            Storage::disk('public')->delete($method->qr_code);
        }
        $method->delete();

        return redirect()->back()->with('success', 'Payment method deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $method = AdminPaymentMethod::findOrFail($id);
        $method->status = !$method->status;
        $method->save();

        return response()->json(['success' => true, 'status' => $method->status]);
    }

    public function bankLink()
    {
        $user = auth()->user();
        $banks = BankAccountModel::where('user_id', $user->id)->get();
        $totalBalance = $banks->sum('balance');
        $transactions = WalletLedger::where('user_id', $user->id)->latest()->paginate(10);

        return view('dashboard.banks.link', compact('banks', 'totalBalance', 'transactions'));
    }

    public function linkBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_holder_name' => 'required|string',
            'account_number' => 'required|string',
            'ifsc_code' => 'nullable|string',
            'account_type' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
        ]);
        BankAccountModel::create([
            'user_id' => auth()->id(),
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,

            // UI ke liye (last 4)
            'account_number' => substr($request->account_number, -4),

            // actual Stripe ID
            'stripe_account_id' => $request->account_number,

            'ifsc_code' => $request->ifsc_code ?? 'STRIPE',
            'account_type' => $request->account_type ?? 'stripe',
            'balance' => $request->balance ?? 0,
            'is_verified' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Bank account linked successfully.']);
    }


    public function createBankPayment(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $bank = BankAccountModel::where('id', $request->bank_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // 🔥 CREATE PAYMENT INTENT (BANK)
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',

                'payment_method_types' => ['us_bank_account'],

                'payment_method_data' => [
                    'type' => 'us_bank_account',
                    'us_bank_account' => [
                        // 👇 YE SABSE IMPORTANT
                        'financial_connections_account' => $bank->stripe_account_id,
                    ],
                ],

                'confirm' => true,

                'metadata' => [
                    'user_id' => auth()->id(),
                    'amount' => $request->amount,
                ]
            ]);

            return response()->json([
                'success' => true,
                'status' => $paymentIntent->status,
                'message' => 'Bank payment started (processing)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function exportBankReport()
    {
        $reportData = $this->getReportData();
        $filename = "bank_report_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sr. No.',
            'Bank ID',
            'Currency',
            'Country/Region',
            'Vouchers Sold',
            'Sale Value in Local Currency',
            'Refunds',
            'Disputes',
            'Currency Conversion @',
            'Sale Value in Buying Currency',
            'FX Gain/Loss',
            'Created At'
        ];

        $callback = function () use ($reportData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $index => $data) {
                fputcsv($file, [
                    $index + 1,
                    $data['bank_name'],
                    $data['currency'],
                    $data['country'],
                    $data['vouchers_sold'],
                    number_format($data['sale_value'], 2, '.', ''),
                    number_format($data['refunds'], 2, '.', ''),
                    $data['disputes'],
                    $data['conversion_rate'],
                    number_format($data['sale_value'], 2, '.', ''),
                    '0.00',
                    is_string($data['created_at']) ? $data['created_at'] : $data['created_at']->format('Y-m-d')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData()
    {
        $methods = AdminPaymentMethod::all();
        $reportData = collect();

        foreach ($methods as $method) {
            $sold = \App\Models\Order::where('admin_payment_method_id', $method->id)->where('status', 'delivered')->sum('quantity');
            $value = \App\Models\Order::where('admin_payment_method_id', $method->id)->where('status', 'delivered')->sum('amount');
            $refunds = \App\Models\RefundRequest::whereHas('order', function ($q) use ($method) {
                $q->where('admin_payment_method_id', $method->id);
            })->where('status', 'approved')->sum('amount');

            $disputesCount = \App\Models\Dispute::whereHas('user.orders', function ($q) use ($method) {
                $q->where('admin_payment_method_id', $method->id);
            })->count();

            $reportData->push([
                'id' => $method->id,
                'bank_name' => $method->bank_name,
                'currency' => 'PKR',
                'country' => $method->country ?? 'N/A',
                'vouchers_sold' => $sold,
                'sale_value' => $value,
                'refunds' => $refunds,
                'disputes' => $disputesCount,
                'conversion_rate' => '1.00',
                'created_at' => $method->created_at
            ]);
        }

        $stripeSold = \App\Models\Order::where('payment_method', 'stripe')->where('status', 'delivered')->sum('quantity');
        $stripeValue = \App\Models\Order::where('payment_method', 'stripe')->where('status', 'delivered')->sum('amount');
        $stripeRefunds = \App\Models\RefundRequest::whereHas('order', function ($q) {
            $q->where('payment_method', 'stripe');
        })->where('status', 'approved')->sum('amount');
        $stripeDisputes = \App\Models\Dispute::whereHas('user.orders', function ($q) {
            $q->where('payment_method', 'stripe');
        })->count();

        $reportData->push([
            'id' => 'stripe',
            'bank_name' => 'Stripe (Global)',
            'currency' => 'USD/PKR',
            'country' => 'GLB',
            'vouchers_sold' => $stripeSold,
            'sale_value' => $stripeValue,
            'refunds' => $stripeRefunds,
            'disputes' => $stripeDisputes,
            'conversion_rate' => 'Dynamic',
            'created_at' => now()
        ]);

        return $reportData;
    }

    public function bankreport()
    {
        $methods = AdminPaymentMethod::all();
        $reportData = $this->getReportData();

        // Fetch recent stripe transactions (credits)
        $stripeTransactions = \App\Models\Order::where('payment_method', 'stripe')
            ->whereIn('status', ['completed', 'delivered'])
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('dashboard.banks.bank-table', compact('methods', 'reportData', 'stripeTransactions'));
    }
}
