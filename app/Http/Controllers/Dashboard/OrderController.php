<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'voucher'])->latest()->paginate(10);
        return view('dashboard.orders.index', compact('orders'));
    }

    public function export()
    {
        // Placeholder for export logic
        return back()->with('success', 'Orders export started.');
    }

    public function deliver($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'delivered']);
        return back()->with('success', 'Order marked as delivered.');
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order cancelled.');
    }

    public function orderHistory()
    {
        return view('dashboard.orders.history');
    }
}
