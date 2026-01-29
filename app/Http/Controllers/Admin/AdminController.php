<?php

namespace App\Http\Controllers\Admin;

use App\Models\Vouchar;
use App\Models\Order;
use App\Models\WalletLedger;
use App\Models\PricingRule;
use App\Models\InventoryVoucher;
use App\Models\SystemSetting;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\UserApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::latest()->paginate(10);
        $stats = [
            'total_users' => User::count(),
            'agents' => User::where('account_type', 'reseller_agent')->count(),
            'students' => User::where('account_type', 'student')->count(),
            'pending_approvals' => User::where('profile_verification_status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('users', 'stats'));
    }

    public function users(Request $request)
    {
        $query = User::where('account_type', '!=', 'admin');

        // Filter by role
        if ($request->has('role') && $request->role != 'all') {
            $query->where('account_type', $request->role);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.partials.users-table', compact('users'))->render();
        }

        return view('admin.user-managment', compact('users'));
    }

    public function usersManagemt(Request $request)
    {
        return $this->users($request);
    }

    public function revenue()
    {
        $stats = [
            'total_revenue' => 15250.00,
            'available_balance' => 8420.50,
            'pending_payments' => 1250.00,
            'refund_amount' => 450.00,
        ];
        return view('admin.revenue.index', compact('stats'));
    }

    public function stockAlerts()
    {
        $vouchers = Vouchar::where('stock', '<', 10)->get();
        $alerts = $vouchers->map(function ($v) {
            return [
                'type' => $v->name,
                'remaining' => $v->stock,
                'threshold' => 10
            ];
        });
        return view('admin.stock.alerts', compact('alerts'));
    }

    public function toggleSystem(Request $request)
    {
        return back()->with('success', 'System status updated successfully.');
    }

    public function approvals(Request $request)
    {
        $query = User::where('account_type', '!=', 'admin')
            ->with(['agentDetail', 'studentDetail']);

        if ($request->has('status') && $request->status != 'all' && $request->status != '') {
            $query->where('profile_verification_status', $request->status);
        }

        if ($request->has('role') && $request->role != 'all' && $request->role != '') {
            $query->where('account_type', $request->role);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('user_id', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $pendingUsers = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.partials.kyc-table', compact('pendingUsers'))->render();
        }

        return view('admin.kyc-compliance.index', compact('pendingUsers'));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,rejected,suspended'
        ]);

        $user->update([
            'profile_verification_status' => $request->status,
            'verified_at' => in_array($request->status, ['verified', 'rejected']) ? now() : $user->verified_at,
            'verified_by' => in_array($request->status, ['verified', 'rejected']) ? auth()->id() : $user->verified_by,
        ]);

        if ($request->status === 'verified') {
            try {
                Mail::to($user->email)->send(new UserApproved($user));
            } catch (\Exception $e) {
                // Handle mail error
            }
        }

        return response()->json(['success' => 'User status updated to ' . ucfirst($request->status)]);
    }

    public function rejectUser(User $user)
    {
        $user->update([
            'profile_verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => 'User KYC rejected successfully.']);
        }

        return redirect()->route('admin.approvals.index')->with('success', 'User KYC rejected successfully.');
    }

    public function approveUser(User $user)
    {
        $user->update([
            'profile_verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        // Send Approval Email
        try {
            Mail::to($user->email)->send(new UserApproved($user));
        } catch (\Exception $e) {
            // Log error or handle silently if mail server is not configured
        }

        if (request()->ajax()) {
            return response()->json(['success' => 'User approved successfully and notification email sent.']);
        }

        return redirect()->route('admin.approvals.index')->with('success', 'User approved successfully and notification email sent.');
    }

    public function vouchersControl(Request $request)
    {
        $query = Vouchar::query();

        // Filters
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('voucher_id', 'like', "%$search%");
            });
        }

        $vouchers = $query->latest()->get();

        $stats = [
            'total_vouchers' => Vouchar::count(),
            'total_stock' => Vouchar::sum('stock'),
            'total_valuation' => Vouchar::sum(\Illuminate\Support\Facades\DB::raw('price * stock')),
            'active_brands' => Vouchar::distinct('category')->count('category'),
        ];

        $categories = Vouchar::distinct('category')->pluck('category');

        return view('admin.voucher.voucher-control', compact('vouchers', 'stats', 'categories'));
    }

    public function createVoucher()
    {
        return view('admin.voucher.create');
    }

    public function editVoucher($id)
    {
        $voucher = Vouchar::findOrFail($id);
        return view('admin.voucher.edit', compact('voucher'));
    }


    public function storeVoucher(Request $request)
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


    public function updateVoucher(Request $request, $id)
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

    public function deleteVoucher($id)
    {
        Vouchar::where('id', $id)->delete();
        return back()->with('success', 'Voucher deleted successfully.');
    }

    public function exportVouchers()
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

    public function importVouchers(Request $request)
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
    public function walletManagement(Request $request)
    {
        $users = User::where('account_type', '!=', 'admin')->get();
        $ledger = WalletLedger::with('user')->latest()->limit(20)->get();

        $stats = [
            'total_balance' => User::where('account_type', '!=', 'admin')->sum('wallet_balance'),
            'total_credits' => WalletLedger::where('type', 'credit')->sum('amount'),
            'total_debits' => WalletLedger::where('type', 'debit')->sum('amount'),
        ];

        if ($request->ajax() && $request->has('tab')) {
            $tab = $request->tab;
            if ($tab == 'users') {
                return view('admin.wallet.partials.users-table', compact('users'))->render();
            } elseif ($tab == 'ledger') {
                return view('admin.wallet.partials.ledger-table', compact('ledger'))->render();
            } elseif ($tab == 'webhooks') {
                return view('admin.wallet.partials.webhooks-table')->render();
            }
        }

        return view('admin.wallet.index', compact('users', 'ledger', 'stats'));
    }

    // Orders & Delivery
    public function ordersIndex()
    {
        $orders = Order::with(['user', 'voucher'])->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function exportOrders()
    {
        $orders = Order::with(['user', 'voucher'])->get();
        $filename = "orders_" . date('Ymd') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Order ID', 'Date', 'Customer', 'Email', 'Voucher', 'Amount', 'Status']);

        foreach ($orders as $o) {
            fputcsv($handle, [
                $o->order_id,
                $o->created_at->format('Y-m-d'),
                $o->user->name ?? 'N/A',
                $o->user->email ?? 'N/A',
                $o->voucher->name ?? $o->voucher_type,
                $o->amount,
                $o->status
            ]);
        }

        fclose($handle);
        exit;
    }

    public function deliverOrder(Request $request, $id)
    {
        Order::where('id', $id)->update([
            'status' => 'delivered',
            'delivery_details' => $request->codes
        ]);
        return back()->with('success', 'Order delivered successfully.');
    }

    public function cancelOrder($id)
    {
        Order::where('id', $id)->update(['status' => 'cancelled']);
        return back()->with('success', 'Order cancelled.');
    }

    // Pricing & Discounts
    public function pricingIndex()
    {
        $vouchers = Vouchar::all();
        $rules = PricingRule::all();
        return view('admin.pricing.index', compact('vouchers', 'rules'));
    }

    public function updatePricing(Request $request)
    {
        PricingRule::updateOrInsert(
            ['voucher_id' => $request->voucher_id, 'country_code' => $request->country_code],
            ['base_price' => $request->base_price, 'discount_price' => $request->discount_price]
        );
        return back()->with('success', 'Pricing updated.');
    }

    // Stock & Inventory
    public function inventoryIndex(Request $request)
    {
        $query = InventoryVoucher::query();

        // Filters
        if ($request->has('countries') && !empty($request->countries)) {
            $query->whereIn('country_region', $request->countries);
        }

        if ($request->has('types') && !empty($request->types)) {
            $query->whereIn('voucher_type', $request->types);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->whereIn('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort == 'latest') {
            $query->latest();
        } elseif ($sort == 'oldest') {
            $query->oldest();
        }

        $vouchers = $query->paginate(12)->withQueryString();

        $stats = [
            'total_stock' => InventoryVoucher::sum('quantity'),
            'total_valuation' => InventoryVoucher::sum('purchase_value'),
            'active_brands' => InventoryVoucher::distinct('brand_name')->count('brand_name'),
        ];

        // Unique countries for filters
        $countries = InventoryVoucher::distinct('country_region')->pluck('country_region');

        if ($request->ajax()) {
            return view('admin.inventory.partials.voucher-list', compact('vouchers'))->render();
        }

        return view('admin.inventory.index', compact('vouchers', 'stats', 'countries'));
    }

    public function exportInventory(Request $request)
    {
        $vouchers = InventoryVoucher::all();
        $filename = "inventory_" . date('Ymd') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, [
            'Sr. No.',
            'SKU ID',
            'DateTime',
            'Brand Name',
            'Country/Region',
            'Currency',
            'Voucher Variant',
            'Voucher Type',
            'Purchase Invoice No.',
            'Purchase Date',
            'Quantity',
            'Purchase Value',
            'Purchase Value Per Unit',
            'Taxes',
            'Local currency',
            'Bank',
            'Currency Conversion @',
            'Referral Points to Reseller',
            'Agent Referral Points Per Unit',
            'Agent Bonus Points Per Unit',
            'Agent Sale Price',
            'Student Referral Points Per Unit',
            'Student Bonus Points Per Unit',
            'Student Sale Price'
        ]);

        foreach ($vouchers as $index => $v) {
            fputcsv($handle, [
                $index + 1,
                $v->sku_id,
                $v->created_at->format('Y-m-d H:i:s'),
                $v->brand_name,
                $v->country_region,
                $v->currency,
                $v->voucher_variant,
                $v->voucher_type,
                $v->purchase_invoice_no,
                $v->purchase_date ? $v->purchase_date->format('Y-m-d') : '',
                $v->quantity,
                $v->purchase_value,
                $v->purchase_value_per_unit,
                $v->taxes,
                $v->local_currency,
                $v->bank,
                $v->currency_conversion_rate,
                $v->referral_points_reseller,
                $v->agent_referral_points_per_unit,
                $v->agent_bonus_points_per_unit,
                $v->agent_sale_price,
                $v->student_referral_points_per_unit,
                $v->student_bonus_points_per_unit,
                $v->student_sale_price
            ]);
        }

        fclose($handle);
        exit;
    }

    public function importInventory(Request $request)
    {
        $request->validate(['csv_file' => 'required|mimes:csv,txt']);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle); // Skip header

        while (($data = fgetcsv($handle)) !== FALSE) {
            // Mapping CSV columns to database fields
            // Assuming the CSV follows the export format (ignoring Sr. No. and DateTime for import/update)
            InventoryVoucher::updateOrCreate(
                ['sku_id' => $data[1]], // SKU ID is column 1
                [
                    'brand_name' => $data[3],
                    'country_region' => $data[4],
                    'currency' => $data[5],
                    'voucher_variant' => $data[6],
                    'voucher_type' => $data[7],
                    'purchase_invoice_no' => $data[8],
                    'purchase_date' => !empty($data[9]) ? $data[9] : null,
                    'quantity' => $data[10],
                    'purchase_value' => $data[11],
                    'purchase_value_per_unit' => $data[12],
                    'taxes' => $data[13],
                    'local_currency' => $data[14],
                    'bank' => $data[15],
                    'currency_conversion_rate' => $data[16],
                    'referral_points_reseller' => $data[17],
                    'agent_referral_points_per_unit' => $data[18],
                    'agent_bonus_points_per_unit' => $data[19],
                    'agent_sale_price' => $data[20],
                    'student_referral_points_per_unit' => $data[21],
                    'student_bonus_points_per_unit' => $data[22],
                    'student_sale_price' => $data[23],
                    'status' => 'IN STOCK', // Default status for imported items
                ]
            );
        }

        fclose($handle);
        return back()->with('success', 'Inventory imported successfully.');
    }

    public function createInventory()
    {
        return view('admin.inventory.create');
    }

    public function storeInventory(Request $request)
    {
        $data = $request->validate([
            'sku_id' => 'required|string|unique:inventory_vouchers,sku_id',
            'brand_name' => 'required|string',
            'country_region' => 'required|string',
            'currency' => 'required|string',
            'voucher_variant' => 'nullable|string',
            'voucher_type' => 'nullable|string',
            'purchase_invoice_no' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'quantity' => 'required|integer|min:1',
            'purchase_value' => 'required|numeric|min:0',
            'purchase_value_per_unit' => 'required|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'local_currency' => 'nullable|string',
            'bank' => 'nullable|string',
            'currency_conversion_rate' => 'nullable|numeric|min:0',
            'referral_points_reseller' => 'nullable|integer|min:0',
            'agent_referral_points_per_unit' => 'nullable|integer|min:0',
            'agent_bonus_points_per_unit' => 'nullable|integer|min:0',
            'agent_sale_price' => 'required|numeric|min:0',
            'student_referral_points_per_unit' => 'nullable|integer|min:0',
            'student_bonus_points_per_unit' => 'nullable|integer|min:0',
            'student_sale_price' => 'required|numeric|min:0',


            'upload_vouchers' => 'nullable|string',
            'status' => 'required|string'
        ]);

        if ($request->hasFile('logo')) {
            $imageName = time() . '.' . $request->logo->extension();
            $request->logo->move(public_path('uploads/logos'), $imageName);
            $data['logo'] = '/uploads/logos/' . $imageName;
        }

        InventoryVoucher::create($data);

        return redirect()->route('admin.inventory.index')->with('success', 'Voucher added successfully.');
    }

    public function editInventory($id)
    {
        $voucher = InventoryVoucher::findOrFail($id);
        return view('admin.inventory.edit', compact('voucher'));
    }

    public function updateInventory(Request $request, $id)
    {
        $voucher = InventoryVoucher::findOrFail($id);

        $data = $request->validate([
            'sku_id' => 'required|string|unique:inventory_vouchers,sku_id,' . $id,
            'brand_name' => 'required|string',
            'country_region' => 'required|string',
            'currency' => 'required|string',
            'voucher_variant' => 'nullable|string',
            'voucher_type' => 'nullable|string',
            'purchase_invoice_no' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'quantity' => 'required|integer|min:1',
            'purchase_value' => 'required|numeric|min:0',
            'purchase_value_per_unit' => 'required|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'local_currency' => 'nullable|string',
            'bank' => 'nullable|string',
            'currency_conversion_rate' => 'nullable|numeric|min:0',
            'referral_points_reseller' => 'nullable|integer|min:0',
            'agent_referral_points_per_unit' => 'nullable|integer|min:0',
            'agent_bonus_points_per_unit' => 'nullable|integer|min:0',
            'agent_sale_price' => 'required|numeric|min:0',
            'student_referral_points_per_unit' => 'nullable|integer|min:0',
            'student_bonus_points_per_unit' => 'nullable|integer|min:0',
            'student_sale_price' => 'required|numeric|min:0',



            'upload_vouchers' => 'nullable|string',
            'status' => 'required|string'
        ]);

        if ($request->hasFile('logo')) {
            $imageName = time() . '.' . $request->logo->extension();
            $request->logo->move(public_path('uploads/logos'), $imageName);
            $data['logo'] = '/uploads/logos/' . $imageName;
        }

        $voucher->update($data);

        return redirect()->route('admin.inventory.index')->with('success', 'Voucher updated successfully.');
    }

    public function destroyInventory($id)
    {
        $voucher = InventoryVoucher::findOrFail($id);
        $voucher->delete();
        return response()->json(['success' => 'Voucher deleted successfully.']);
    }

    public function uploadStock(Request $request)
    {
        $codes = explode("\n", str_replace("\r", "", $request->codes));
        foreach ($codes as $code) {
            if (trim($code)) {
                VoucherInventory::insertOrIgnore([
                    'voucher_id' => $request->voucher_id,
                    'voucher_code' => trim($code),
                    'created_at' => now()
                ]);
            }
        }
        return back()->with('success', 'Stock uploaded.');
    }

    public function creditWallet(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $newBalance = $user->wallet_balance + $request->amount;

        $user->update(['wallet_balance' => $newBalance]);

        WalletLedger::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $request->amount,
            'description' => $request->note ?? 'Manual Credit',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Wallet credited successfully.']);
        }

        return back()->with('success', 'Wallet credited successfully.');
    }

    public function debitWallet(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string'
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->wallet_balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Insufficient wallet balance.'], 422);
            }
            return back()->with('error', 'Insufficient wallet balance.');
        }

        $newBalance = $user->wallet_balance - $request->amount;

        $user->update(['wallet_balance' => $newBalance]);

        WalletLedger::create([
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => $request->amount,
            'description' => $request->note ?? 'Manual Debit',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => 'Wallet debited successfully.']);
        }

        return back()->with('success', 'Wallet debited successfully.');
    }

    public function kycCompliance(Request $request)
    {
        return $this->approvals($request);
    }


    public function notifications()
    {
        // Placeholder notifications
        $notifications = [
            ['title' => 'New Agent Registration', 'message' => 'A new agent has registered and is waiting for approval.', 'time' => now()->diffForHumans()],
            ['title' => 'Low Stock Alert', 'message' => 'University Voucher stock is low (5 remaining).', 'time' => now()->subHours(2)->diffForHumans()],
        ];
        return view('admin.notifications.index', compact('notifications'));
    }

    public function manageAccount()
    {
        $user = auth()->user();
        return view('admin.account.manage', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('first_name', 'last_name', 'email'));

        return back()->with('success', 'Account updated successfully.');
    }

    public function viewUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function viewKyc(User $user)
    {
        $user->load(['agentDetail', 'studentDetail']);
        return view('admin.kyc-compliance.show', compact('user'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'account_type' => 'required|in:manager,reseller_agent,support_team,student,agent',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'user_id' => User::generateNextUserId($request->account_type),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
            'account_type' => $request->account_type,
            'phone' => $request->phone,
            'profile_verification_status' => 'verified', // Admin created users are verified by default
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return redirect()->route('admin.users.management')->with('success', 'User created successfully.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'account_type' => 'required|in:manager,reseller_agent,support_team,student,agent',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:pending,verified,suspended',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'account_type' => $request->account_type,
            'phone' => $request->phone,
            'profile_verification_status' => $request->status,
        ]);

        return redirect()->route('admin.users.management')->with('success', 'User updated successfully.');
    }

    public function suspendUser(User $user)
    {

        $newStatus = $user->profile_verification_status === 'suspended' ? 'verified' : 'suspended';
        $user->update(['profile_verification_status' => $newStatus]);


        $message = $newStatus === 'suspended' ? 'User suspended successfully.' : 'User unsuspended successfully.';

        if (request()->ajax()) {
            return response()->json(['success' => $message]);
        }

        return back()->with('success', $message);
    }

    public function downloadPDF(Request $request)
    {
        // Simple CSV export as a fallback for PDF
        $query = User::where('account_type', '!=', 'admin');

        if ($request->has('role') && $request->role != 'all') {
            $query->where('account_type', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('user_id', 'like', "%{$search}%");
            });
        }

        $users = $query->get();
        $filename = "users_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['User ID', 'Name', 'Email', 'Role', 'Phone', 'Status', 'Created At']);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->user_id,
                $user->first_name . ' ' . $user->last_name,
                $user->email,
                $user->account_type,
                $user->phone,
                $user->profile_verification_status,
                $user->created_at
            ]);
        }

        fclose($handle);
        exit;
    }

    public function deleteUser(User $user)
    {
        $user->delete();

        if (request()->ajax()) {
            return response()->json(['success' => 'User deleted successfully']);
        }

        return redirect()->route('admin.users.management')->with('success', 'User deleted successfully');
    }

    public function updatePassword(Request $request, User $user)
    {


        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => \Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function auditLogsIndex()
    {
        $logs = AuditLog::with('user')->latest()->paginate(20);
        return view('admin.audit.index', compact('logs'));
    }

    public function systemControlIndex()
    {
        $settings = SystemSetting::all()->pluck('settings_value', 'settings_key');
        $logs = AuditLog::where('action', 'like', 'System %')->latest()->limit(10)->get();
        return view('admin.system.control', compact('settings', 'logs'));
    }

    public function updateSystemControl(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['settings_key' => $key],
                ['settings_value' => $value]
            );
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'System Update',
            'description' => 'System settings updated',
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'System settings updated.');
    }

    public function reportsIndex()
    {
        $stats = [
            'total_sales' => Order::where('status', 'delivered')->sum('amount') ?? 0,
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_vouchers' => Vouchar::count(),
            'total_users' => User::count(),
        ];

        $recent_orders = Order::with(['user', 'voucher'])->latest()->limit(5)->get();

        return view('admin.reports.index', compact('stats', 'recent_orders'));
    }
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        // Admin apna account freeze na kare
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot freeze your own account'
            ], 403);
        }

        // Toggle status
        $user->is_active = $user->is_active ? 0 : 1;
        $user->save();

        return response()->json([
            'status'  => $user->is_active ? 'active' : 'frozen',
            'message' => $user->is_active
                ? 'User account unfrozen successfully'
                : 'User account frozen successfully'
        ]);
    }
}
