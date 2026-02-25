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

        if ($request->filled('user_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('user_id', 'like', '%' . $request->user_id . '%');
            });
        }

        if ($request->filled('country')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('country', $request->country);
            });
        }

        if ($request->filled('state')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', function ($sq) use ($request) {
                    $sq->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('user_id', 'like', '%' . $request->search . '%');
                })->orWhere('order_id', 'like', '%' . $request->search . '%');
            });
        }

        $refunds = $query->paginate(10)->withQueryString();
        return view('dashboard.refunds.index', compact('refunds'));
    }

    public function adminExport(Request $request)
    {
        $query = RefundRequest::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('user_id', 'like', '%' . $request->user_id . '%');
            });
        }
        if ($request->filled('country')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('country', $request->country);
            });
        }
        if ($request->filled('state')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('state', $request->state);
            });
        }

        $refunds = $query->get();
        $filename = "refunds_report_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $columns = [
            'User ID', 'Name', 'Email', 'Country', 'State', 'Order ID', 
            'Amount', 'Status', 'Reason', 'User TRX ID', 'Admin TRX ID', 
            'Processed At', 'Bank Details'
        ];

        fputcsv($handle, $columns);

        foreach ($refunds as $refund) {
            fputcsv($handle, [
                $refund->user->user_id ?? 'N/A',
                $refund->user->name ?? 'N/A',
                $refund->user->email ?? 'N/A',
                $refund->user->country ?? 'N/A',
                $refund->user->state ?? 'N/A',
                $refund->order_id,
                $refund->amount,
                $refund->status,
                $refund->reason,
                $refund->user_transaction_id,
                $refund->transaction_id,
                $refund->processed_at,
                $refund->bank_details
            ]);
        }

        fclose($handle);
        exit;
    }

    public function userRefunds(Request $request)
    {
        $query = RefundRequest::where('user_id', Auth::id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $refunds = $query->paginate(10)->withQueryString();

        // Fetch orders where voucher codes are NOT received (status != delivered)
        $eligibleOrders = Order::where('user_id', Auth::id())
            ->whereIn('status', ['delivered'])
            ->latest()
            ->get();

        return view('dashboard.refunds.user-index', compact('refunds', 'eligibleOrders'));
    }

    public function userExport(Request $request)
    {
        $query = RefundRequest::where('user_id', Auth::id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $refunds = $query->get();
        $filename = "my_refunds_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $columns = [
            'Date', 'Order ID', 'Amount', 'Reason', 'Status', 'Admin Proof ID', 'Processed At', 'Admin Note'
        ];

        fputcsv($handle, $columns);

        foreach ($refunds as $refund) {
            fputcsv($handle, [
                $refund->created_at->format('Y-m-d'),
                $refund->order_id,
                $refund->amount,
                $refund->reason,
                $refund->status,
                $refund->transaction_id ?? 'N/A',
                $refund->processed_at ?? 'N/A',
                $refund->admin_note ?? 'N/A'
            ]);
        }

        fclose($handle);
        exit;
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string',
            'bank_details' => 'required|string',
            'user_transaction_id' => 'required|string',
            'transaction_slip' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $slipPath = $request->file('transaction_slip')->store('refund_slips', 'public');

        RefundRequest::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'bank_details' => $request->bank_details,
            'user_transaction_id' => $request->user_transaction_id,
            'transaction_slip' => $slipPath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Refund request submitted successfully.');
    }

    public function processRefund(Request $request, $id)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'refund_receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'processed_date' => 'required|date',
            'processed_time' => 'required',
        ]);

        $refund = RefundRequest::findOrFail($id);

        $receiptPath = $request->file('refund_receipt')->store('refund_receipts', 'public');
        $processedAt = $request->processed_date . ' ' . $request->processed_time;

        $refund->update([
            'status' => 'approved',
            'transaction_id' => $request->transaction_id,
            'refund_receipt' => $receiptPath,
            'processed_at' => $processedAt,
        ]);

        return back()->with('success', 'Refund processed and proof uploaded successfully.');
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
