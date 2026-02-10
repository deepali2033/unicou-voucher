<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InventoryVoucher;
use App\Models\VoucherPriceRule;
use App\Helpers\CountryHelper;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index(Request $request)
    {
        $vouchers = InventoryVoucher::select('id', 'brand_name', 'sku_id')->get();
        $allCountries = CountryHelper::getAllCountries();

        $query = VoucherPriceRule::with('inventoryVoucher');

        // Filters
        if ($request->filled('country_name')) {
            $query->where('country_name', 'like', '%' . $request->country_name . '%');
        }

        if ($request->filled('voucher_id')) {
            $query->where('inventory_voucher_id', $request->voucher_id);
        }

        if ($request->filled('brand_name')) {
            $query->where('brand_name', 'like', '%' . $request->brand_name . '%');
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('voucher_variant')) {
            $query->where('voucher_variant', 'like', '%' . $request->voucher_variant . '%');
        }

        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', 'like', '%' . $request->voucher_type . '%');
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('purchase_date', [$request->from_date, $request->to_date]);
        }

        $rules = $query->latest()->get();

        return view('dashboard.pricing.index', compact('vouchers', 'allCountries', 'rules'));
    }

    public function create()
    {
        $vouchers = InventoryVoucher::all();
        $allCountries = CountryHelper::getAllCountries();
        return view('dashboard.pricing.pricing-set-form', compact('vouchers', 'allCountries'));
    }

    public function export(Request $request)
    {
        $query = VoucherPriceRule::with('inventoryVoucher');

        // Apply same filters as index
        if ($request->filled('country_name')) {
            $query->where('country_name', 'like', '%' . $request->country_name . '%');
        }
        if ($request->filled('voucher_id')) {
            $query->where('inventory_voucher_id', $request->voucher_id);
        }
        if ($request->filled('brand_name')) {
            $query->where('brand_name', 'like', '%' . $request->brand_name . '%');
        }
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('purchase_date', [$request->from_date, $request->to_date]);
        }

        $rules = $query->latest()->get();

        $filename = "pricing_report_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Sr. No.',
            'P.ID.',
            'Date',
            'Time',
            'Brand Name',
            'Currency',
            'Country/Region',
            'Voucher Variant',
            'Voucher Type',
            'Purchase Invoice No.',
            'Purchase Date',
            'Total Quantity',
            'Purchase Value',
            'Taxes',
            'Per Unit Price',
            'Issue Date',
            'Expiry Date',
            'Credit Limit',
            'Sale Price',
            'Status'
        ];

        $callback = function () use ($rules, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rules as $index => $rule) {
                fputcsv($file, [
                    $index + 1,
                    $rule->purchase_id,
                    $rule->created_at->format('d M Y'),
                    $rule->created_at->format('H:i A'),
                    $rule->brand_name,
                    $rule->currency,
                    $rule->country_name,
                    $rule->voucher_variant,
                    $rule->voucher_type,
                    $rule->purchase_invoice_no,
                    $rule->purchase_date,
                    $rule->total_quantity,
                    $rule->purchase_value,
                    $rule->taxes,
                    $rule->per_unit_price,
                    $rule->issue_date,
                    $rule->expiry_date,
                    $rule->credit_limit,
                    $rule->sale_price,
                    $rule->is_active ? 'Active' : 'Inactive'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getVoucherDetails($id)
    {
        $voucher = InventoryVoucher::find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found']);
        }
        return response()->json(['success' => true, 'data' => $voucher]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_voucher_id' => 'required|exists:inventory_vouchers,id',
            'countries' => 'required|array',
            'sale_price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',

            'brand_name' => 'nullable|string',
            'currency' => 'nullable|string',
            'country_region' => 'nullable|string',
            'voucher_variant' => 'nullable|string',
            'voucher_type' => 'nullable|string',
            'purchase_invoice_no' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'total_quantity' => 'nullable|integer',
            'purchase_value' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'per_unit_price' => 'nullable|numeric',
            'issue_date' => 'nullable|date',
            'credit_limit' => 'nullable|numeric',
        ]);

        $allCountries = CountryHelper::getAllCountries();
        $purchase_id = 'PID-' . time() . rand(100, 999);

        foreach ($request->countries as $country_code) {
            $country_name = $allCountries[$country_code] ?? $country_code;

            VoucherPriceRule::updateOrCreate(
                [
                    'inventory_voucher_id' => $request->inventory_voucher_id,
                    'country_code' => $country_code
                ],
                [
                    'purchase_id' => $purchase_id,
                    'brand_name' => $request->brand_name,
                    'currency' => $request->currency,
                    'country_region' => $request->country_region,
                    'voucher_variant' => $request->voucher_variant,
                    'voucher_type' => $request->voucher_type,
                    'purchase_invoice_no' => $request->purchase_invoice_no,
                    'purchase_date' => $request->purchase_date,
                    'total_quantity' => $request->total_quantity,
                    'purchase_value' => $request->purchase_value,
                    'taxes' => $request->taxes,
                    'per_unit_price' => $request->per_unit_price,
                    'issue_date' => $request->issue_date,
                    'credit_limit' => $request->credit_limit,
                    'country_name' => $country_name,
                    'sale_price' => $request->sale_price,
                    'discount_type' => $request->discount_type,
                    'discount_value' => $request->discount_value,
                    'expiry_date' => $request->expiry_date,
                    'is_active' => true
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Price rules saved successfully.']);
    }

    public function destroy($id)
    {
        VoucherPriceRule::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Price rule deleted successfully.']);
    }

    public function toggleStatus(Request $request, $id)
    {
        $rule = VoucherPriceRule::findOrFail($id);
        $rule->update(['is_active' => $request->is_active]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
