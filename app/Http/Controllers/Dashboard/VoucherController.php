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

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = VoucherPriceRule::with('inventoryVoucher')
            ->where('is_stopped', 0)
            ->where('is_brand_stopped', 0)
            ->where('is_country_stopped', 0);

        // Filter by Voucher Name (Brand Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('inventoryVoucher', function ($q) use ($search) {
                $q->where('brand_name', 'like', "%$search%");
            });
        }

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by Price Range
        if ($request->filled('min_price')) {
            $query->where('sale_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('sale_price', '<=', $request->max_price);
        }

        $vouchers = $query->latest()->paginate(10)->withQueryString();

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
        $rule = VoucherPriceRule::with('inventoryVoucher')->findOrFail($id);

        $user = auth()->user();
        $banks = BankAccountModel::where('user_id', $user->id)->get();
        $adminBanks = AdminPaymentMethod::where('status', true)->get();

        // Mock data for points and store credit (you may want to fetch these from actual user models)
        $userPoints = [
            'quarterly' => 0,
            'yearly' => 0,
            'store_credit' => $user->wallet_balance ?? 0,
        ];

        return view('dashboard.voucher.order-now', compact('rule', 'userPoints', 'banks', 'adminBanks'));
    }

    public function placeOrder(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'payment_type' => 'required|in:my_bank,admin_bank',
            'bank_id' => 'required_if:payment_type,my_bank|exists:bank_accounts,id',
            'admin_bank_id' => 'required_if:payment_type,admin_bank|exists:admin_payment_methods,id',
            'payment_receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $rule = VoucherPriceRule::with('inventoryVoucher')->findOrFail($id);
        $voucher = $rule->inventoryVoucher;
        $user = auth()->user();

        if ($voucher->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient stock available.'], 400);
        }

        $totalAmount = $rule->final_price * $request->quantity;
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

            DB::commit();

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
