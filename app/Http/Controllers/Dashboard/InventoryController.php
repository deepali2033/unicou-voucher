<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InventoryVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryVoucher::query();

        // Basic stats
        $stats = [
            'total_stock' => InventoryVoucher::sum('quantity'),
            'total_valuation' => InventoryVoucher::sum('purchase_value'),
            'active_brands' => InventoryVoucher::distinct('brand_name')->count('brand_name'),
        ];

        // Unique countries for filter
        $countries = InventoryVoucher::distinct('country_region')->pluck('country_region')->filter();

        // Filters
        if ($request->has('countries')) {
            $query->whereIn('country_region', $request->countries);
        }
        if ($request->has('types')) {
            $query->whereIn('voucher_type', $request->types);
        }
        if ($request->has('status')) {
            $query->whereIn('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort == 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $inventory = $query->paginate(10);

        if ($request->ajax()) {
            return view('dashboard.inventory.partials.voucher-list', compact('inventory'))->render();
        }

        return view('dashboard.inventory.index', compact('inventory', 'stats', 'countries'));
    }

    public function create()
    {
        $last_voucher = InventoryVoucher::orderBy('id', 'desc')->first();
        $next_id = 1;

        if ($last_voucher) {
            $next_id = (int)$last_voucher->id + 1;
        }

        return view('dashboard.inventory.create', compact('next_id'));
    }

    public function duplicate($id)
    {
        $original = InventoryVoucher::findOrFail($id);
        $new = $original->replicate();

        // Generate new unique SKU ID
        $baseSku = $original->sku_id;
        // Remove trailing numbers if any to get the base
        $basePrefix = preg_replace('/-\d+$/', '', $baseSku);

        $count = 1;
        $newSku = $basePrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        while (InventoryVoucher::where('sku_id', $newSku)->exists()) {
            $count++;
            $newSku = $basePrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $new->sku_id = $newSku;
        $new->quantity = 0; // Reset quantity for duplicate
        $new->status = 'OUT OF STOCK';
        $new->save();

        return back()->with('success', 'Voucher duplicated successfully with new SKU: ' . $newSku);
    }

    public function bulkDuplicate(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No vouchers selected.'], 400);
        }

        $duplicatedCount = 0;
        foreach ($ids as $id) {
            $original = InventoryVoucher::find($id);
            if ($original) {
                $new = $original->replicate();

                $baseSku = $original->sku_id;
                $basePrefix = preg_replace('/-\d+$/', '', $baseSku);

                $count = 1;
                $newSku = $basePrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

                while (InventoryVoucher::where('sku_id', $newSku)->exists()) {
                    $count++;
                    $newSku = $basePrefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                }

                $new->sku_id = $newSku;
                $new->quantity = 0;
                $new->status = 'OUT OF STOCK';
                $new->save();
                $duplicatedCount++;
            }
        }

        return response()->json(['success' => true, 'message' => "$duplicatedCount vouchers duplicated successfully."]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku_id' => 'required|string|unique:inventory_vouchers,sku_id',
            'brand_name' => 'required|string|max:255',
            'country_region' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'voucher_variant' => 'nullable|string|max:255',
            'voucher_type' => 'required|string|max:255',
            'purchase_invoice_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|string|in:IN STOCK,OUT OF STOCK',
            'purchase_value' => 'required|numeric|min:0',
            'purchase_value_per_unit' => 'required|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'bank' => 'nullable|string|max:255',
            'local_currency' => 'nullable|string|max:10',
            'currency_conversion_rate' => 'nullable|numeric|min:0',
            'agent_sale_price' => 'required|numeric|min:0',
            'student_sale_price' => 'required|numeric|min:0',
            'referral_points_reseller' => 'nullable|integer|min:0',
            'agent_referral_points_per_unit' => 'nullable|integer|min:0',
            'agent_bonus_points_per_unit' => 'nullable|integer|min:0',
            'student_referral_points_per_unit' => 'nullable|integer|min:0',
            'student_bonus_points_per_unit' => 'nullable|integer|min:0',
            'upload_vouchers' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('inventory_logos', 'public');
            $validated['logo'] = Storage::url($validated['logo']);
        }

        if ($request->filled('upload_vouchers')) {
            $validated['upload_vouchers'] = array_map('trim', explode(',', $request->upload_vouchers));
        }

        InventoryVoucher::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventory added successfully.');
    }

    public function edit($id)
    {
        $inventory = InventoryVoucher::findOrFail($id);
        return view('dashboard.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, $id)
    {
        $inventory = InventoryVoucher::findOrFail($id);
        $validated = $request->validate([
            'sku_id' => 'required|string|unique:inventory_vouchers,sku_id,' . $id,
            'brand_name' => 'required|string|max:255',
            'country_region' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'voucher_variant' => 'nullable|string|max:255',
            'voucher_type' => 'required|string|max:255',
            'purchase_invoice_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|string|in:IN STOCK,OUT OF STOCK',
            'purchase_value' => 'required|numeric|min:0',
            'purchase_value_per_unit' => 'required|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'bank' => 'nullable|string|max:255',
            'local_currency' => 'nullable|string|max:10',
            'currency_conversion_rate' => 'nullable|numeric|min:0',
            'agent_sale_price' => 'required|numeric|min:0',
            'student_sale_price' => 'required|numeric|min:0',
            'referral_points_reseller' => 'nullable|integer|min:0',
            'agent_referral_points_per_unit' => 'nullable|integer|min:0',
            'agent_bonus_points_per_unit' => 'nullable|integer|min:0',
            'student_referral_points_per_unit' => 'nullable|integer|min:0',
            'student_bonus_points_per_unit' => 'nullable|integer|min:0',
            'upload_vouchers' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($inventory->logo) {
                $oldPath = str_replace('/storage/', '', $inventory->logo);
                Storage::disk('public')->delete($oldPath);
            }
            $validated['logo'] = $request->file('logo')->store('inventory_logos', 'public');
            $validated['logo'] = Storage::url($validated['logo']);
        }

        if ($request->filled('upload_vouchers')) {
            $validated['upload_vouchers'] = array_map('trim', explode(',', $request->upload_vouchers));
        }

        $inventory->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventory updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $inventory = InventoryVoucher::findOrFail($id);
        if ($inventory->logo) {
            $oldPath = str_replace('/storage/', '', $inventory->logo);
            Storage::disk('public')->delete($oldPath);
        }
        $inventory->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Inventory deleted successfully.']);
        }

        return redirect()->route('inventory.index')->with('success', 'Inventory deleted successfully.');
    }

    public function export(Request $request)
    {
        $query = InventoryVoucher::query();

        // Apply same filters as index
        if ($request->has('countries')) {
            $query->whereIn('country_region', $request->countries);
        }
        if ($request->has('types')) {
            $query->whereIn('voucher_type', $request->types);
        }
        if ($request->has('status')) {
            $query->whereIn('status', $request->status);
        }

        $records = $query->latest()->get();

        $filename = "inventory_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sr No.',
            'SKU ID',
            'Brand Name',
            'Country/Region',
            'Currency',
            'Voucher Variant',
            'Voucher Type',
            'Quantity',
            'Status',
            'Purchase Value',
            'Agent Sale Price',
            'Student Sale Price'
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $serial = 1;
            foreach ($records as $record) {
                fputcsv($file, [
                    $serial++,
                    $record->sku_id,
                    $record->brand_name,
                    $record->country_region,
                    $record->currency,
                    $record->voucher_variant,
                    $record->voucher_type,
                    $record->quantity,
                    $record->status,
                    $record->purchase_value,
                    $record->agent_sale_price,
                    $record->student_sale_price,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        return back()->with('success', 'Import functionality coming soon.');
    }

    public function upload(Request $request)
    {
        return back()->with('success', 'Upload functionality coming soon.');
    }
}
