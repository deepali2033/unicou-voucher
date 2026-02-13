<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;

class SalesController extends Controller
{
    public function SalesReport(Request $request)
    {
        $query = Order::with(['user', 'voucher', 'inventoryVoucher'])->where('status', 'delivered');

        // Date Wise Report (From - To)
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Voucher Type
        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->voucher_type);
        }

        // Buyer Type
        if ($request->filled('user_role')) {
            $query->where('user_role', $request->user_role);
        }

        // Filter by Inventory Voucher fields
        if ($request->anyFilled(['currency', 'country_region', 'brand_name', 'voucher_variant'])) {
            $query->whereHas('inventoryVoucher', function($q) use ($request) {
                if ($request->filled('currency')) {
                    $q->where('currency', $request->currency);
                }
                if ($request->filled('country_region')) {
                    $q->where('country_region', $request->country_region);
                }
                if ($request->filled('brand_name')) {
                    $q->where('brand_name', 'like', "%{$request->brand_name}%");
                }
                if ($request->filled('voucher_variant')) {
                    $q->where('voucher_variant', 'like', "%{$request->voucher_variant}%");
                }
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%$search%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        $sales = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('dashboard.sales.sales-table', compact('sales'))->render();
        }

        return view('dashboard.sales.sales-index', compact('sales'));
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'voucher', 'inventoryVoucher'])->where('status', 'delivered');

        // Date Wise Report (From - To)
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Voucher Type
        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->voucher_type);
        }

        // Buyer Type
        if ($request->filled('user_role')) {
            $query->where('user_role', $request->user_role);
        }

        // Filter by Inventory Voucher fields
        if ($request->anyFilled(['currency', 'country_region', 'brand_name', 'voucher_variant'])) {
            $query->whereHas('inventoryVoucher', function($q) use ($request) {
                if ($request->filled('currency')) {
                    $q->where('currency', $request->currency);
                }
                if ($request->filled('country_region')) {
                    $q->where('country_region', $request->country_region);
                }
                if ($request->filled('brand_name')) {
                    $q->where('brand_name', 'like', "%{$request->brand_name}%");
                }
                if ($request->filled('voucher_variant')) {
                    $q->where('voucher_variant', 'like', "%{$request->voucher_variant}%");
                }
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%$search%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                  });
            });
        }

        $records = $query->latest()->get();

        $filename = "sales_report_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sale ID', 'Date', 'Time', 'Buyer Type', 'Local currency', 
            'Country/Region', 'Voucher Variant', 'Voucher Type', 
            'Sales Invoice No.', 'Quantity', 'Bank', 'Currency Conversion @',
            'Referral Points to Earned', 'Referral Points Redeemed', 'Customer Bonus Points'
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->order_id,
                    $record->created_at->format('d M Y'),
                    $record->created_at->format('H:i A'),
                    ucfirst(str_replace('_', ' ', $record->user_role)),
                    $record->inventoryVoucher->currency ?? 'N/A',
                    $record->inventoryVoucher->country_region ?? 'N/A',
                    $record->inventoryVoucher->voucher_variant ?? 'N/A',
                    $record->voucher_type,
                    $record->order_id,
                    $record->quantity,
                    $record->inventoryVoucher->bank ?? 'N/A',
                    number_format($record->inventoryVoucher->currency_conversion_rate ?? 0, 4),
                    $record->referral_points ?? 0,
                    $record->points_redeemed ?? 0,
                    number_format($record->bonus_amount ?? 0, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
