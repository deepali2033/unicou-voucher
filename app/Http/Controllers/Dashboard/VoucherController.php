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

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = VoucherPriceRule::with('inventoryVoucher');

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

        $vouchers = $query->latest()->get();

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

        // Mock data for points and store credit (you may want to fetch these from actual user models)
        $userPoints = [
            'quarterly' => 0,
            'yearly' => 0,
            'store_credit' => $user->wallet_balance ?? 0,
        ];

        return view('dashboard.voucher.order-now', compact('rule', 'userPoints', 'banks'));
    }

    public function placeOrder(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'bank_id' => 'required|exists:bank_accounts,id',
        ]);

        $rule = VoucherPriceRule::with('inventoryVoucher')->findOrFail($id);
        $voucher = $rule->inventoryVoucher;
        $user = auth()->user();

        $bank = BankAccountModel::where('id', $request->bank_id)->where('user_id', $user->id)->firstOrFail();

        if ($voucher->quantity < $request->quantity) {
            return response()->json(['message' => 'Insufficient stock available.'], 400);
        }

        $totalAmount = $rule->final_price * $request->quantity;

        if ($bank->balance < $totalAmount) {
            return response()->json(['message' => 'Your bank account does not have enough balance. Please add funds.'], 400);
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
            $order = Order::create([
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'user_role' => $user->account_type,
                'voucher_type' => $voucher->voucher_type,
                'voucher_id' => $voucher->sku_id,
                'quantity' => $request->quantity,
                'amount' => $totalAmount,
                'status' => 'completed',
                'referral_points' => $totalReferralPoints,
                'bonus_amount' => $totalBonusPoints, // Using bonus_amount to store bonus points
                'payment_method' => 'bank',
                'bank_name' => $bank->bank_name,
                'account_number' => $bank->account_number,
                'ifsc_code' => $bank->ifsc_code,
            ]);

            // Deduct Bank Balance
            $bank->decrement('balance', $totalAmount);

            // Deduct Inventory
            $voucher->decrement('quantity', $request->quantity);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully using ' . $bank->bank_name,
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
