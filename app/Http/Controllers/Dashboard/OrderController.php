<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'voucher'])->latest();

        if ($request->filled('order_id')) {
            $query->where('order_id', 'like', '%' . $request->order_id . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        $orders = $query->paginate(10)->withQueryString();
        return view('dashboard.orders.oder-deli', compact('orders'));
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'voucher'])->latest();

        if ($request->filled('order_id')) {
            $query->where('order_id', 'like', '%' . $request->order_id . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('user_role', $request->role);
        }

        $orders = $query->get();
        $filename = "orders_export_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Order ID', 'Customer', 'Email', 'Role', 'Voucher', 'Amount', 'Ref Points', 'Bonus Points', 'Status', 'Date']);

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->order_id,
                $order->user->name ?? 'N/A',
                $order->user->email ?? 'N/A',
                $order->user_role,
                $order->voucher_type,
                $order->amount,
                $order->referral_points,
                $order->bonus_amount,
                $order->status,
                $order->created_at->format('Y-m-d H:i')
            ]);
        }

        fclose($handle);
        exit;
    }

    public function deliver(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'delivered',
            'delivery_details' => $request->codes
        ]);
        return back()->with('success', 'Order marked as delivered and codes sent.');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order cancelled.');
    }

    public function orderHistory()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);
        return view('dashboard.orders.history', compact('orders'));
    }
}
