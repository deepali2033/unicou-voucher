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
}
