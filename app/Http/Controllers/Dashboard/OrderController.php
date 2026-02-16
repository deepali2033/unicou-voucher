<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\InventoryVoucher;
use App\Models\User;
use App\Notifications\VoucherDeliveredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'voucher', 'inventoryVoucher'])->latest();

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
        
        // Get delivered codes across all orders to identify used codes
        $deliveredCodes = Order::whereNotNull('delivery_details')
            ->pluck('delivery_details')
            ->flatMap(function($details) {
                return array_map('trim', explode("\n", str_replace("\r", "", $details)));
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return view('dashboard.orders.oder-deli', compact('orders', 'deliveredCodes'));
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

        // Notify User
        try {
            $user = $order->user;
            $codes = $request->codes;
            
            // Database Notification for User
            $user->notify(new VoucherDeliveredNotification(
                $order, 
                "You have received voucher codes for Order #{$order->order_id}. Total amount: RS {$order->amount}."
            ));

            // Email to User
            Mail::send('emails.order-delivered', ['order' => $order, 'user' => $user, 'codes' => $codes], function($message) use ($user) {
                $message->to($user->email);
                $message->subject('Your Voucher Codes Have Been Delivered! - UniCou');
            });
        } catch (\Exception $e) {
            // Log error but continue
        }

        // Notify Admins, Managers, and Support Team
        try {
            $staff = User::whereIn('account_type', ['admin', 'manager', 'support_team'])->get();
            Notification::send($staff, new VoucherDeliveredNotification(
                $order,
                "Voucher codes delivered for Order #{$order->order_id} by " . auth()->user()->name
            ));
        } catch (\Exception $e) {
            // Log error but continue
        }

        return back()->with('success', 'Order marked as delivered and codes sent to user.');
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $reason = $request->reason ?? 'Order cancelled by administrator.';
        
        $order->update(['status' => 'cancelled']);

        // Send Email to User
        try {
            $user = $order->user;
            Mail::send('emails.order-cancelled', ['order' => $order, 'user' => $user, 'reason' => $reason], function($message) use ($user) {
                $message->to($user->email);
                $message->subject('Update Regarding Your Order - UniCou');
            });
        } catch (\Exception $e) {
            // Log error but continue
        }

        return back()->with('success', 'Order cancelled and notification sent to user.');
    }

    public function approve($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be approved.');
        }

        // Deduct Inventory here if not already deducted (manual transfer cases)
        $voucher = InventoryVoucher::where('sku_id', $order->voucher_id)->first();
        if ($voucher) {
            if ($voucher->quantity < $order->quantity) {
                return back()->with('error', 'Insufficient stock to approve this order.');
            }
            $voucher->decrement('quantity', $order->quantity);
        }

        $order->update(['status' => 'completed']);

        // Send Email to User
        try {
            $user = $order->user;
            Mail::send('emails.order-approved', ['order' => $order, 'user' => $user], function($message) use ($user) {
                $message->to($user->email);
                $message->subject('Your Order Has Been Approved - UniCou');
            });
        } catch (\Exception $e) {
            // Log error but continue
        }

        return back()->with('success', 'Order approved successfully.');
    }

    public function orderHistory()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);
        return view('dashboard.orders.history', compact('orders'));
    }
}
