<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\VoucherPriceRule;
use App\Models\InventoryVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\BankAccountModel;
use App\Models\AdminPaymentMethod;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\Notification;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = InventoryVoucher::where('is_expired', false)
            ->where('quantity', '>', 0);

        // Country filtering: match user's country with voucher's country_region
        if (!in_array($user->account_type, ['admin', 'manager'])) {
            $query->where('country_region', $user->country);
        }

        // Filter by Voucher Name (Brand Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('brand_name', 'like', "%$search%");
        }

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by Price Range
        if ($request->filled('min_price')) {
            if ($user->isStudent()) {
                $query->where('student_sale_price', '>=', $request->min_price);
            } else {
                $query->where('agent_sale_price', '>=', $request->min_price);
            }
        }
        if ($request->filled('max_price')) {
            if ($user->isStudent()) {
                $query->where('student_sale_price', '<=', $request->max_price);
            } else {
                $query->where('agent_sale_price', '<=', $request->max_price);
            }
        }

        $vouchers = $query->latest()->paginate(10)->withQueryString();

        // Calculate limits and pricing for each voucher
        $userTotalLimit = $user->voucher_limit;
        $boughtTotalLast24h = Order::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('quantity');

        foreach ($vouchers as $voucher) {
            // Set final price based on user role
            if ($user->isStudent()) {
                $voucher->final_price = $voucher->student_sale_price;
            } else {
                $voucher->final_price = $voucher->agent_sale_price;
            }

            // Check 24h limit
            $boughtLast24h = Order::where('user_id', $user->id)
                ->where('voucher_id', $voucher->sku_id)
                ->where('created_at', '>=', now()->subHours(24))
                ->sum('quantity');

            $voucher->is_limited = ($boughtLast24h > 0 || $boughtTotalLast24h >= $userTotalLimit);
            $voucher->remaining_limit = max(0, $userTotalLimit - $boughtTotalLast24h);
            $voucher->quantity_bought_today = $boughtLast24h;
        }

        $stats = [
            'total_vouchers' => InventoryVoucher::count(),
            'total_stock' => InventoryVoucher::sum('quantity'),
            'total_valuation' => InventoryVoucher::sum(DB::raw('agent_sale_price * quantity')),
            'active_brands' => InventoryVoucher::distinct('brand_name')->count('brand_name'),
        ];

        return view('dashboard.voucher.index', compact('vouchers', 'stats'));
    }

    public function showOrder($id)
    {
        $voucher = InventoryVoucher::findOrFail($id);
        $user = auth()->user();

        // Check if voucher is available in user's country
        if (!in_array($user->account_type, ['admin', 'manager'])) {
            if ($voucher->country_region !== $user->country) {
                return redirect()->route('vouchers')->with('error', 'This voucher is not available in your country.');
            }
        }

        // Calculate correct price based on user role
        if ($user->isStudent()) {
            $voucher->final_price = $voucher->student_sale_price;
        } else {
            $voucher->final_price = $voucher->agent_sale_price;
        }

        // Check user total 24h limit
        $userTotalLimit = $user->voucher_limit;
        $boughtTotalLast24h = Order::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('quantity');

        if ($boughtTotalLast24h >= $userTotalLimit) {
            return redirect()->route('vouchers')->with('error', 'Aapne pichle 24 ghanto me apni voucher kharidne ki limit puri kar li he.');
        }

        $maxAllowed = $userTotalLimit - $boughtTotalLast24h;

        // Check quantity availability
        if ($voucher->quantity <= 0) {
            return redirect()->route('vouchers')->with('error', 'This voucher is out of stock.');
        }

        $banks = BankAccountModel::where('user_id', $user->id)->get();
        $adminBanks = AdminPaymentMethod::where('status', true)->get();

        // Mock data for points and store credit
        $userPoints = [
            'quarterly' => 0,
            'yearly' => 0,
            'store_credit' => $user->wallet_balance ?? 0,
            'max_allowed' => $maxAllowed
        ];

        $rule = $voucher;

        return view('dashboard.voucher.order-now', compact('voucher', 'rule', 'userPoints', 'banks', 'adminBanks'));
    }

    public function placeOrder(Request $request, $id)
    {
        $rule = VoucherPriceRule::with('inventoryVoucher')->findOrFail($id);
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'payment_type' => 'required|in:my_bank,admin_bank',
            'bank_id' => 'required_if:payment_type,my_bank|exists:bank_accounts,id',
            'admin_bank_id' => 'required_if:payment_type,admin_bank|exists:admin_payment_methods,id',
            'payment_receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $voucher = $rule->inventoryVoucher;
        $user = auth()->user();

        // Calculate correct price based on user role
        if ($user->isStudent()) {
            $totalAmount = $voucher->student_sale_price * $request->quantity;
        } else {
            // Agent and Reseller see same price
            $totalAmount = $voucher->agent_sale_price * $request->quantity;
        }

        // Check 24h limit
        $ruleLimit = 0;
        if ($user->isStudent()) {
            $ruleLimit = $rule->student_daily_limit;
        } elseif ($user->isResellerAgent()) {
            $ruleLimit = $rule->reseller_daily_limit;
        } elseif ($user->isRegularAgent()) {
            $ruleLimit = $rule->agent_daily_limit;
        }

        // Check user total 24h limit
        $userTotalLimit = $user->voucher_limit;
        $boughtTotalLast24h = Order::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->sum('quantity');

        if ($boughtTotalLast24h + $request->quantity > $userTotalLimit) {
            return response()->json(['message' => 'Aapki pichle 24 ghanto ki limit puri ho gayi he. Remaining: ' . max(0, $userTotalLimit - $boughtTotalLast24h)], 400);
        }

        if ($ruleLimit > 0) {
            $boughtLast24h = Order::where('user_id', $user->id)
                ->where('voucher_id', $voucher->sku_id)
                ->where('created_at', '>=', now()->subHours(24))
                ->sum('quantity');

            if ($boughtLast24h + $request->quantity > $ruleLimit) {
                return response()->json(['message' => 'This order exceeds your 24-hour purchase limit for this specific voucher. Remaining: ' . max(0, $ruleLimit - $boughtLast24h)], 400);
            }
        }

        if ($voucher->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient stock available.'], 400);
        }

        $status = 'pending';
        $payment_receipt = null;
        $bank_details = [];

        if ($request->payment_type == 'my_bank') {
            $bank = BankAccountModel::where('id', $request->bank_id)->where('user_id', $user->id)->firstOrFail();
            if ($bank->balance < $totalAmount) {
                return response()->json(['message' => 'Your bank account does not have enough balance.'], 400);
            }
            $bank_details = [
                'bank_name' => $bank->bank_name,
                'account_number' => $bank->account_number,
                'ifsc_code' => $bank->ifsc_code,
            ];
            $status = 'completed';
        } else {
            $adminBank = AdminPaymentMethod::findOrFail($request->admin_bank_id);
            if ($request->hasFile('payment_receipt')) {
                $payment_receipt = $request->file('payment_receipt')->store('receipts', 'public');
            }
            $bank_details = [
                'admin_payment_method_id' => $adminBank->id,
                'bank_name' => $adminBank->bank_name,
                'account_number' => $adminBank->account_number,
                'ifsc_code' => $adminBank->ifsc_code,
                'transaction_id' => $request->transaction_id,
                'account_holder_name' => $request->account_holder_name,
                'amount_transferred' => $request->transfer_amount,
                'captured_details' => $request->captured_details, // Storing JSON from OCR
            ];
            $status = 'pending';
        }

        $referralPointsPerUnit = 0;
        $bonusPointsPerUnit = 0;
        if ($user->isStudent()) {
            $referralPointsPerUnit = $voucher->student_referral_points_per_unit;
            $bonusPointsPerUnit = $voucher->student_bonus_points_per_unit;
        } elseif ($user->isResellerAgent()) {
            $referralPointsPerUnit = $voucher->referral_points_reseller;
            $bonusPointsPerUnit = 0;
        } elseif ($user->isRegularAgent()) {
            $referralPointsPerUnit = $voucher->agent_referral_points_per_unit;
            $bonusPointsPerUnit = $voucher->agent_bonus_points_per_unit;
        }

        $totalReferralPoints = $referralPointsPerUnit * $request->quantity;
        $totalBonusPoints = $bonusPointsPerUnit * $request->quantity;

        DB::beginTransaction();
        try {
            // Create Order
            $orderData = [
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'sub_agent_id' => $user->sub_agent_id, // Store parent ID for referral points
                'user_role' => $user->account_type,
                'voucher_type' => $voucher->voucher_type,
                'voucher_id' => $voucher->sku_id,
                'quantity' => $request->quantity,
                'amount' => $totalAmount,
                'status' => $status,
                'referral_points' => $totalReferralPoints,
                'bonus_amount' => $totalBonusPoints,
                'payment_method' => $request->payment_type,
                'payment_receipt' => $payment_receipt,
            ];
            $orderData = array_merge($orderData, $bank_details);

            $order = Order::create($orderData);

            if ($request->payment_type == 'my_bank') {
                $bank->decrement('balance', $totalAmount);
                $voucher->decrement('quantity', $request->quantity);
            }

            // Notify Admins and Managers
            $adminsAndManagers = User::whereIn('account_type', ['admin', 'manager'])->get();
            $adminMsg = "Voucher order kiya he by " . $user->name . " (Order ID: " . $order->order_id . ")";
            Notification::send($adminsAndManagers, new OrderPlacedNotification($order, $adminMsg, 'order_placed'));

            // Notify the User
            $userMsg = "Aapne jo order kiya uska order ho gaya he. Voucher: " . $order->voucher_type . ", Amount: " . $order->amount . ", Order ID: " . $order->order_id;
            $user->notify(new OrderPlacedNotification($order, $userMsg, 'order_placed'));

            DB::commit();

            session()->flash('success', $status == 'completed' ? 'Order placed successfully.' : 'Order submitted. Waiting for admin approval.');

            return response()->json([
                'success' => true,
                'message' => $status == 'completed' ? 'Order placed successfully.' : 'Order submitted. Waiting for admin approval.',
                'order_id' => $order->order_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to place order: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        return view('dashboard.voucher.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'yearly_points' => 'nullable|integer|min:0',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Vouchar::create([
            'voucher_id' => 'VCH-' . strtoupper(Str::random(6)),
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'original_price' => $request->original_price,
            'stock' => $request->stock,
            'yearly_points' => $request->yearly_points ?? 0,
            'logo' => $request->logo,
            'description' => $request->description,
            'status' => 'active',
        ]);

        return back()->with('success', 'Voucher added successfully.');
    }

    public function edit($id)
    {
        $voucher = Vouchar::findOrFail($id);
        return view('dashboard.voucher.edit', compact('voucher'));
    }

    public function update(Request $request, $id)
    {
        Vouchar::where('id', $id)->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'original_price' => $request->original_price,
            'stock' => $request->stock,
            'yearly_points' => $request->yearly_points ?? 0,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Voucher updated successfully.');
    }

    public function destroy($id)
    {
        Vouchar::where('id', $id)->delete();
        return back()->with('success', 'Voucher deleted successfully.');
    }

    public function export()
    {
        $vouchers = Vouchar::all();
        $filename = "vouchers_" . date('Ymd') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Voucher ID', 'Name', 'Category', 'Price', 'Stock', 'Status']);

        foreach ($vouchers as $v) {
            fputcsv($handle, [$v->voucher_id, $v->name, $v->category, $v->price, $v->stock, $v->status]);
        }

        fclose($handle);
        exit;
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => 'required|mimes:csv,txt']);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Skip header

        while (($data = fgetcsv($handle)) !== FALSE) {
            Vouchar::updateOrInsert(
                ['voucher_id' => $data[0]],
                [
                    'name' => $data[1],
                    'category' => $data[2],
                    'price' => $data[3],
                    'stock' => $data[4],
                    'status' => $data[5] ?? 'active',
                    'updated_at' => now(),
                ]
            );
        }

        fclose($handle);
        return back()->with('success', 'Vouchers imported successfully.');
    }
}
