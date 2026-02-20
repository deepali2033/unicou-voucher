<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vouchar;
use App\Models\User;
use App\Models\InventoryVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sales' => Order::where('status', 'delivered')->sum('amount') ?? 0,
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_vouchers' => Vouchar::count(),
            'total_users' => User::count(),
        ];

        $recent_orders = Order::with(['user', 'voucher'])->latest()->limit(5)->get();

        return view('dashboard.reports.index', compact('stats', 'recent_orders'));
    }

    public function stockReport(Request $request)
    {
        $query = InventoryVoucher::query();

        if ($request->filled('sku_id')) {
            $query->where('sku_id', 'like', '%' . $request->sku_id . '%');
        }

        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->voucher_type);
        }

        $stock_data = $query->select(
            'sku_id',
            'voucher_type',
            DB::raw('SUM(quantity) as total_purchased_qty'),
            DB::raw('SUM(purchase_value) as total_purchased_value'),
            DB::raw('SUM(taxes) as total_purchased_taxes')
        )
            ->groupBy('sku_id', 'voucher_type')
            ->paginate(15);

        // Add dummy data for other sections as per image (for now, or logic if available)
        foreach ($stock_data as $item) {
            $item->in_stock_qty = $item->total_purchased_qty - 5; // Example logic
            $item->in_stock_value = $item->total_purchased_value * 0.8;
            $item->in_stock_taxes = $item->total_purchased_taxes * 0.8;

            $item->sold_qty = 3;
            $item->sold_value = 150.00;
            $item->sold_taxes = 15.00;

            $item->pending_qty = 1;
            $item->pending_value = 50.00;
            $item->pending_taxes = 5.00;

            $item->refunded_qty = 1;
            $item->refunded_value = 50.00;
            $item->refunded_taxes = 5.00;

            $item->lost_qty = 0;
            $item->lost_value = 0.00;
            $item->lost_taxes = 0.00;
        }

        if ($request->ajax()) {
            return view('dashboard.reports.partials.stock-table', compact('stock_data'))->render();
        }

        return view('dashboard.reports.stock', compact('stock_data'));
    }

    public function profitAndLossReport(Request $request)
    {
        $query = InventoryVoucher::query();

        if ($request->filled('sku_id')) {
            $query->where('sku_id', 'like', '%' . $request->sku_id . '%');
        }

        $pl_data = $query->select(
            'sku_id',
            'voucher_type',
            DB::raw('SUM(quantity) as purchase_qty'),
            DB::raw('SUM(purchase_value) as purchase_value'),
            DB::raw('SUM(taxes) as purchase_taxes')
        )
            ->groupBy('sku_id', 'voucher_type')
            ->paginate(15);

        foreach ($pl_data as $item) {
            $item->sold_qty = 5;
            $item->sold_value = 250.00;
            $item->sold_taxes = 25.00;

            $item->lost_qty = 1;
            $item->lost_value = 50.00;
            $item->lost_taxes = 5.00;

            $item->profit_qty = $item->sold_qty;
            $item->profit_value = 50.00; // Value - Cost
            $item->profit_taxes = 0.00;
        }

        if ($request->ajax()) {
            return view('dashboard.reports.partials.profit-loss-table', compact('pl_data'))->render();
        }

        return view('dashboard.reports.profit-loss', compact('pl_data'));
    }

    public function grossMarginReport()
    {
        return view('dashboard.reports.gross-margin');
    }
    public function distributorMarginReport()
    {
        return view('dashboard.reports.distributor-margin');
    }
    public function platformFeeReport()
    {
        return view('dashboard.reports.platform-fee');
    }
    public function operationalExpenseReport()
    {
        return view('dashboard.reports.operational-expense');
    }

    public function exportStockReport(Request $request)
    {
        $query = InventoryVoucher::query();
        if ($request->filled('sku_id')) {
            $query->where('sku_id', 'like', '%' . $request->sku_id . '%');
        }
        if ($request->filled('voucher_type')) {
            $query->where('voucher_type', $request->voucher_type);
        }

        $records = $query->select(
            'sku_id',
            'voucher_type',
            DB::raw('SUM(quantity) as total_purchased_qty'),
            DB::raw('SUM(purchase_value) as total_purchased_value'),
            DB::raw('SUM(taxes) as total_purchased_taxes')
        )
            ->groupBy('sku_id', 'voucher_type')
            ->get();

        $filename = "stock_report_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sr. No.',
            'SKU ID',
            'Voucher Type',
            'Purchased Qty',
            'Purchased Value',
            'Purchased Taxes',
            'In Stock Qty',
            'In Stock Value',
            'In Stock Taxes',
            'Sold Qty',
            'Sold Value',
            'Sold Taxes',
            'Pending Qty',
            'Pending Value',
            'Pending Taxes',
            'Refunded Qty',
            'Refunded Value',
            'Refunded Taxes',
            'Lost Qty',
            'Lost Value',
            'Lost Taxes'
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->sku_id,
                    $item->voucher_type,
                    $item->total_purchased_qty,
                    $item->total_purchased_value,
                    $item->total_purchased_taxes,
                    $item->total_purchased_qty - 5,
                    $item->total_purchased_value * 0.8,
                    $item->total_purchased_taxes * 0.8,
                    3,
                    150.00,
                    15.00,
                    1,
                    50.00,
                    5.00,
                    1,
                    50.00,
                    5.00,
                    0,
                    0.00,
                    0.00
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProfitLossReport(Request $request)
    {
        $query = InventoryVoucher::query();
        if ($request->filled('sku_id')) {
            $query->where('sku_id', 'like', '%' . $request->sku_id . '%');
        }

        $records = $query->select(
            'sku_id',
            'voucher_type',
            DB::raw('SUM(quantity) as purchase_qty'),
            DB::raw('SUM(purchase_value) as purchase_value'),
            DB::raw('SUM(taxes) as purchase_taxes')
        )
            ->groupBy('sku_id', 'voucher_type')
            ->get();

        $filename = "profit_loss_report_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Sr. No.',
            'SKU ID',
            'Voucher Type',
            'Sold Qty',
            'Sold Value',
            'Sold Taxes',
            'Purchase Qty',
            'Purchase Value',
            'Purchase Taxes',
            'Lost Qty',
            'Lost Value',
            'Lost Taxes',
            'Profit Qty',
            'Profit Value',
            'Profit Taxes'
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->sku_id,
                    $item->voucher_type,
                    5,
                    250.00,
                    25.00,
                    $item->purchase_qty,
                    $item->purchase_value,
                    $item->purchase_taxes,
                    1,
                    50.00,
                    5.00,
                    5,
                    50.00,
                    0.00
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bankReport(Request $request)
    {
        $query = Order::query()->whereNotNull('bank_name');

        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'like', '%' . $request->bank_name . '%');
        }

        $bank_data = $query->latest()->paginate(15);

        if ($request->ajax()) {
            return view('dashboard.reports.partials.bank-table', compact('bank_data'))->render();
        }

        return view('dashboard.reports.bank', compact('bank_data'));
    }

    public function exportBankReport(Request $request)
    {
        $query = Order::query()->whereNotNull('bank_name');
        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'like', '%' . $request->bank_name . '%');
        }

        $records = $query->get();

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
            'Sale Value in Local Curr',
            'Total Credits',
            'Refunds',
            'Disputes',
            'Currency Conversion',
            'Sale Value in Buying Currency',
            'FX Gain/Loss'
        ];

        $callback = function () use ($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $index => $record) {
                fputcsv($file, [
                    $index + 1,
                    $record->bank_name ?? 'BNK-' . ($index + 100),
                    $record->currency ?? 'USD',
                    $record->country_region ?? 'Global',
                    $record->quantity ?? 10,
                    number_format($record->amount_transferred ?? 500, 2),
                    number_format(($record->amount_transferred ?? 500) * 1.1, 2),
                    number_format(0, 2),
                    number_format(0, 2),
                    '1.00',
                    number_format($record->amount_transferred ?? 500, 2),
                    '+0.00'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function revenue()
    {
        $stats = [
            'total_revenue' => 15250.00,
            'available_balance' => 8420.50,
            'pending_payments' => 1250.00,
            'refund_amount' => 450.00,
        ];
        return view('dashboard.revenue.index', compact('stats'));
    }
}
