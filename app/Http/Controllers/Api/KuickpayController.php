<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KuickpayController extends Controller
{
    private $username;
    private $password;

    public function __construct()
    {
        $this->username = config('services.kuickpay.username', 'Kuickpaytest');
        $this->password = config('services.kuickpay.password', 'Kuickpay@test12');
    }

    private function validateAuth(Request $request)
    {
        $user = $request->header('username');
        $pass = $request->header('password');

        if ($user !== $this->username || $pass !== $this->password) {
            return false;
        }
        return true;
    }

    public function billInquiry(Request $request)
    {
        Log::info('Kuickpay BillInquiry Request:', $request->all());

        if (!$this->validateAuth($request)) {
            return response()->json(['response_Code' => '03', 'message' => 'Authentication Failed'], 401);
        }

        $consumerNumber = $request->input('consumer_number');
        $order = Order::where('kuickpay_consumer_number', $consumerNumber)->first();

        if (!$order) {
            return response()->json([
                'response_Code' => '01', // Invalid inquiry number
                'consumer_Detail' => '',
                'bill_status' => '',
                'due_date' => '',
                'amount_within_dueDate' => '',
                'amount_after_dueDate' => '',
                'email_address' => '',
                'contact_number' => '',
                'billing_month' => '',
                'date_paid' => '',
                'amount_paid' => '',
                'tran_auth_Id' => '',
                'reserved' => 'Order not found'
            ]);
        }

        $status = $order->status === 'completed' ? 'P' : 'U';
        $dueDate = Carbon::parse($order->created_at)->addDays(7)->format('Ymd');
        $billingMonth = Carbon::parse($order->created_at)->format('ym');
        
        // Formatting amount: +0000000186900 (14 chars total including +, last 2 digits are minor units)
        $formattedAmount = '+' . str_pad(number_format($order->amount * 100, 0, '', ''), 12, '0', STR_PAD_LEFT);

        $response = [
            'response_Code' => '00',
            'consumer_Detail' => strtoupper(substr($order->user->name ?? 'CUSTOMER', 0, 30)),
            'bill_status' => $status,
            'due_date' => $dueDate,
            'amount_within_dueDate' => $formattedAmount,
            'amount_after_dueDate' => $formattedAmount,
            'email_address' => $order->user->email ?? '',
            'contact_number' => $order->user->phone ?? '03000000000',
            'billing_month' => $billingMonth,
            'date_paid' => $order->status === 'completed' ? Carbon::parse($order->updated_at)->format('Ymd') : '',
            'amount_paid' => $order->status === 'completed' ? str_pad(number_format($order->amount * 100, 0, '', ''), 12, '0', STR_PAD_LEFT) : '',
            'tran_auth_Id' => $order->status === 'completed' ? substr($order->transaction_id, -6) : '',
            'reserved' => ''
        ];

        Log::info('Kuickpay BillInquiry Response:', $response);
        return response()->json($response);
    }

    public function billPayment(Request $request)
    {
        Log::info('Kuickpay BillPayment Request:', $request->all());

        if (!$this->validateAuth($request)) {
            return response()->json(['response_Code' => '03', 'message' => 'Authentication Failed'], 401);
        }

        $consumerNumber = $request->input('consumer_number');
        $tranAuthId = $request->input('tran_auth_id');
        $amount = $request->input('transaction_amount');
        
        $order = Order::where('kuickpay_consumer_number', $consumerNumber)->first();

        if (!$order) {
            return response()->json(['response_Code' => '01']); // Invalid voucher
        }

        if ($order->status === 'completed') {
            return response()->json(['response_Code' => '03', 'message' => 'Duplicate/Already Paid']);
        }

        // Validate amount (ignoring minor units for simple comparison if needed, but here we just trust the payment notification if order exists)
        // You might want to add more strict validation here.

        $order->status = 'completed';
        $order->transaction_id = $tranAuthId;
        $order->amount_transferred = $amount;
        $order->save();

        // Optional: Trigger any post-payment logic (sending vouchers, notifications etc)
        // This is usually handled in an event listener or directly here.

        $response = [
            'response_Code' => '00',
            'Identification_parameter' => $order->user->email ?? 'success',
            'reserved' => 'Payment Successful'
        ];

        Log::info('Kuickpay BillPayment Response:', $response);
        return response()->json($response);
    }
}
