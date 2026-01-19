<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery;

class StripePaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test Stripe configuration
        Config::set('services.stripe.secret', 'sk_test_fake_key');
        Config::set('services.stripe.public', 'pk_test_fake_key');
        Config::set('services.stripe.webhook_secret', 'whsec_test_secret');
    }

    /**
     * Test: Successful payment with valid data
     */
    public function test_successful_payment_with_valid_data()
    {
        // Skip if Stripe is not installed
        if (!class_exists('\Stripe\Stripe')) {
            $this->markTestSkipped('Stripe PHP library not installed');
            return;
        }

        $plan = Plan::factory()->create([
            'name' => 'Premium Plan',
            'price' => 99.99,
            'description' => 'Premium features'
        ]);

        // Mock successful Stripe session
        $mockSession = Mockery::mock('overload:\Stripe\Checkout\Session');
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_success')
            ->andReturn((object)[
                'id' => 'cs_test_success',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_success',
                'amount_total' => 9999, // $99.99 in cents
                'currency' => 'usd',
                'metadata' => (object)[
                    'plan_id' => (string)$plan->id,
                    'plan_name' => 'Premium Plan'
                ],
                'customer_details' => (object)[
                    'email' => 'test@example.com',
                    'name' => 'John Doe'
                ]
            ]);

        $response = $this->get('/payment/success?session_id=cs_test_success');

        $response->assertStatus(200)
                 ->assertViewIs('payments.success');

        // Verify payment record was created
        $this->assertDatabaseHas('payments', [
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_success',
            'amount' => 99.99,
            'currency' => 'USD',
            'status' => 'completed'
        ]);
    }

    /**
     * Test: Create checkout session successfully
     */
    public function test_create_checkout_session_successfully()
    {
        // Skip if Stripe is not installed
        if (!class_exists('\Stripe\Stripe')) {
            $this->markTestSkipped('Stripe PHP library not installed');
            return;
        }

        $plan = Plan::factory()->create([
            'name' => 'Basic Plan',
            'price' => 29.99,
            'description' => 'Basic features'
        ]);

        // Mock Stripe Session creation
        $mockSession = Mockery::mock('overload:\Stripe\Checkout\Session');
        $mockSession->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/pay/cs_test_checkout'
            ]);

        $response = $this->postJson("/payment/create-checkout-session/{$plan->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'checkout_url' => 'https://checkout.stripe.com/pay/cs_test_checkout'
                 ]);
    }

    /**
     * Test: Webhook handles completed payment
     */
    public function test_webhook_handles_completed_payment()
    {
        // Skip if Stripe is not installed
        if (!class_exists('\Stripe\Stripe')) {
            $this->markTestSkipped('Stripe PHP library not installed');
            return;
        }

        $plan = Plan::factory()->create();

        // Create the payment controller instance
        $controller = new \App\Http\Controllers\PaymentController();

        // Test the private method using reflection
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('handleCheckoutCompleted');
        $method->setAccessible(true);

        $sessionData = [
            'id' => 'cs_test_webhook',
            'payment_intent' => 'pi_test_webhook',
            'amount_total' => 5999, // $59.99
            'currency' => 'usd',
            'metadata' => [
                'plan_id' => (string)$plan->id
            ],
            'customer_details' => [
                'email' => 'webhook@example.com',
                'name' => 'Webhook User'
            ]
        ];

        $method->invoke($controller, $sessionData);

        $this->assertDatabaseHas('payments', [
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_webhook',
            'amount' => 59.99,
            'status' => 'completed'
        ]);
    }

    /**
     * Test: Invalid plan ID rejection
     */
    public function test_invalid_plan_id_rejection()
    {
        $response = $this->postJson('/payment/create-checkout-session/99999');

        $response->assertStatus(404);
    }

    /**
     * Test: Missing session ID handling
     */
    public function test_missing_session_id_handling()
    {
        $response = $this->get('/payment/success');

        $response->assertRedirect(route('pricing.index'))
                 ->assertSessionHas('error', 'Invalid payment session');
    }

    /**
     * Test: Stripe API failure handling
     */
    public function test_stripe_api_failure_handling()
    {
        // Skip if Stripe is not installed
        if (!class_exists('\Stripe\Stripe')) {
            $this->markTestSkipped('Stripe PHP library not installed');
            return;
        }

        $plan = Plan::factory()->create();

        // Mock Stripe exception
        $mockSession = Mockery::mock('overload:\Stripe\Checkout\Session');
        $mockSession->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Stripe API error'));

        $response = $this->postJson("/payment/create-checkout-session/{$plan->id}");

        $response->assertStatus(500)
                 ->assertJson([
                     'error' => 'Unable to create payment session'
                 ]);
    }

    /**
     * Test: Invalid webhook signature rejection
     */
    public function test_invalid_webhook_signature_rejection()
    {
        // Skip if Stripe is not installed
        if (!class_exists('\Stripe\Stripe')) {
            $this->markTestSkipped('Stripe PHP library not installed');
            return;
        }

        // Mock Stripe webhook verification exception
        $mockWebhook = Mockery::mock('overload:\Stripe\Webhook');
        $mockWebhook->shouldReceive('constructEvent')
            ->once()
            ->andThrow(new \Stripe\Exception\SignatureVerificationException('Invalid signature'));

        $response = $this->postJson('/payment/webhook', [], [
            'Stripe-Signature' => 'invalid_signature'
        ]);

        $response->assertStatus(400)
                 ->assertSeeText('Invalid signature');
    }

    /**
     * Test: Payment failure webhook processing
     */
    public function test_payment_failure_webhook_processing()
    {
        // Skip if Stripe is not installed
        if (!class_exists('\Stripe\Stripe')) {
            $this->markTestSkipped('Stripe PHP library not installed');
            return;
        }

        $plan = Plan::factory()->create();
        $payment = Payment::factory()->create([
            'plan_id' => $plan->id,
            'stripe_payment_intent_id' => 'pi_test_failed',
            'status' => 'pending'
        ]);

        $controller = new \App\Http\Controllers\PaymentController();

        // Test private method using reflection
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('handlePaymentFailed');
        $method->setAccessible(true);

        $paymentIntentData = [
            'id' => 'pi_test_failed'
        ];

        $method->invoke($controller, $paymentIntentData);

        $payment->refresh();
        $this->assertEquals('failed', $payment->status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
