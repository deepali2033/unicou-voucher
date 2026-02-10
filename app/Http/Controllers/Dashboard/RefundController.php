<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RefundRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = RefundRequest::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })->orWhere('order_id', 'like', '%' . $request->search . '%');
        }

        $refunds = $query->paginate(10)->withQueryString();
        return view('dashboard.refunds.index', compact('refunds'));
    }

    public function userRefunds()
    {
        $refunds = RefundRequest::where('user_id', Auth::id())->latest()->paginate(10);
        
        // Fetch orders where voucher codes are NOT received (status != delivered)
        $eligibleOrders = Order::where('user_id', Auth::id())
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->latest()
            ->get();
            
        return view('dashboard.refunds.user-index', compact('refunds', 'eligibleOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string',
            'bank_details' => 'required|string',
        ]);

        RefundRequest::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'bank_details' => $request->bank_details,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Refund request submitted successfully.');
    }

    public function approve($id)
    {
        $refund = RefundRequest::findOrFail($id);
        $refund->update(['status' => 'approved']);
        return back()->with('success', 'Refund request approved.');
    }

    public function reject(Request $request, $id)
    {
        $refund = RefundRequest::findOrFail($id);
        $refund->update([
            'status' => 'rejected',
            'admin_note' => $request->note
        ]);
        return back()->with('success', 'Refund request rejected.');
    }
}
