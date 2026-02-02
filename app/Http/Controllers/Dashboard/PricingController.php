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

        if ($request->filled('country_name')) {
            $query->where('country_name', 'like', '%' . $request->country_name . '%');
        }

        if ($request->filled('voucher_id')) {
            $query->where('inventory_voucher_id', $request->voucher_id);
        }

        $rules = $query->latest()->get();

        return view('dashboard.pricing.index', compact('vouchers', 'allCountries', 'rules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_voucher_id' => 'required|exists:inventory_vouchers,id',
            'country_code' => 'required|string',
            'country_name' => 'required|string',
            'sale_price' => 'required|numeric|min:0',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'is_active' => 'nullable|boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        VoucherPriceRule::updateOrCreate(
            [
                'inventory_voucher_id' => $request->inventory_voucher_id,
                'country_code' => $request->country_code
            ],
            $data
        );

        return response()->json(['success' => true, 'message' => 'Price rule saved successfully.']);
    }

    public function destroy($id)
    {
        VoucherPriceRule::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Price rule deleted successfully.']);
    }
}
