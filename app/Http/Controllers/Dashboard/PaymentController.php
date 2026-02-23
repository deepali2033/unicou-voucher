<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InventoryVoucher;
use App\Models\VoucherPriceRule;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create(Request $request, $rule)
    {
        $voucher = InventoryVoucher::findOrFail($rule);
        $user = auth()->user();

        if (!$request->filled('quantity')) {
            return response()->json(['error' => 'Quantity required'], 400);
        }

        $quantity = (int) $request->quantity;
        $unitPrice = $user->isStudent() ? $voucher->student_sale_price : $voucher->agent_sale_price;
        $amount = $unitPrice * $quantity;

        try {
            // ✅ Use sandbox full endpoint
            $baseUrl = 'https://sandbox.api.getsafepay.com/orders';
            $secretKey = env('SAFEPAY_SECRET_KEY'); // secret key from .env

            // Log request
            Log::info('SafePay Request:', [
                'url' => $baseUrl,
                'amount' => (int)($amount * 100),
                'currency' => 'PKR',
                'description' => $voucher->brand_name . ' x' . $quantity,
            ]);

            // Prepare payload
            $payload = [
                'amount' => (int)($amount * 100), // in smallest currency unit
                'currency' => 'PKR',
                'description' => $voucher->brand_name . ' x' . $quantity,
                'success_url' => route('safepay.success'),
                'failed_url' => route('vouchers.order', $voucher->id),
                'webhook_url' => route('safepay.webhook'),
                'metadata' => [
                    'rule_id' => $voucher->id,
                    'quantity' => $quantity,
                    'user_id' => $user->id,
                ],
            ];

            // API call
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/json',
            ])->post($baseUrl, $payload);

            // Log raw response for debugging
            Log::info('SafePay Raw Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Parse JSON safely
            $data = $response->json();
            if (!$data) {
                // JSON null → probably HTML, log for debug
                $data = ['error' => $response->body()];
            }

            Log::info('SafePay Parsed Data:', ['data' => $data, 'type' => gettype($data)]);

            if ($response->successful() && isset($data['data']['id'])) {

                session(['safepay_order' => [
                    'rule_id' => $voucher->id,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'safepay_order_id' => $data['data']['id'],
                ]]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $data['data']['checkout_url'] ?? null
                ]);
            } else {
                Log::error('SafePay Order Creation Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'parsed' => $data,
                ]);

                $errorMsg = is_array($data) ? ($data['message'] ?? $data['error'] ?? 'Payment order failed') : $data;

                return response()->json(['error' => $errorMsg], 400);
            }
        } catch (\Exception $e) {
            Log::error('SafePay API Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Payment gateway error: ' . $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        $safepayData = session('safepay_order');

        if (!$safepayData) {
            return redirect()->route('vouchers')
                ->with('error', 'Payment session expired');
        }

        try {
            $rule = VoucherPriceRule::with('inventoryVoucher')->findOrFail($safepayData['rule_id']);
            $voucher = $rule->inventoryVoucher;
            $user = auth()->user();

            $order = Order::create([
                'order_id' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(10)),
                'user_id' => $user->id,
                'user_role' => $user->account_type,
                'voucher_type' => $voucher->voucher_type,
                'voucher_id' => $voucher->sku_id,
                'quantity' => $safepayData['quantity'],
                'amount' => $safepayData['amount'],
                'status' => 'completed',
                'payment_method' => 'safepay',
                'referral_points' => 0,
                'bonus_amount' => 0,
            ]);

            $voucher->decrement('quantity', $safepayData['quantity']);

            session()->forget('safepay_order');

            return redirect()->route('orders.history')
                ->with('success', 'Payment successful! Order #' . $order->order_id);
        } catch (\Exception $e) {
            Log::error('SafePay Success Handler Error: ' . $e->getMessage());
            return redirect()->route('vouchers')
                ->with('error', 'Error processing payment');
        }
    }

    public function webhook(Request $request)
    {
        Log::info('SafePay Webhook', $request->all());

        if ($request->status === 'COMPLETED') {
            // Order was already created in success endpoint
        }

        return response()->json(['status' => 'ok']);
    }
}
