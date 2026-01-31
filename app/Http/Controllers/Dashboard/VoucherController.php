<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\VoucherPriceRule;
use App\Models\InventoryVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

        // Mock data for points and store credit (you may want to fetch these from actual user models)
        $userPoints = [
            'quarterly' => 0,
            'yearly' => 0,
            'store_credit' => 0,
        ];

        return view('dashboard.voucher.order-now', compact('rule', 'userPoints'));
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
