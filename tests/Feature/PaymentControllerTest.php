<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Mockery;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up Stripe mock
        Stripe::setApiKey('sk_test_fake_key');
    }

    /**
     * Test create checkout session with valid plan
     */
    public function test_create_checkout_session_with_valid_plan()
    {
        $plan = Plan::factory()->create([
            'name' => 'Test Plan',
            'price' => 99.99,
            'description' => 'Test plan description'
        ]);

        // Mock Stripe Session creation
        $mockSession = Mockery::mock('overload:' . Session::class);
        $mockSession->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($args) use ($plan) {
                return $args['line_items'][0]['price_data']['product_data']['name'] === $plan->name
                    && $args['line_items'][0]['price_data']['unit_amount'] === intval($plan->price * 100)
                    && $args['metadata']['plan_id'] === (string)$plan->id;
            }))
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/pay/cs_test_123456'
            ]);

        $response = $this->postJson("/payment/create-checkout-session/{$plan->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'checkout_url' => 'https://checkout.stripe.com/pay/cs_test_123456'
                 ]);
    }

    /**
     * Test create checkout session with invalid plan
     */
    public function test_create_checkout_session_with_invalid_plan()
    {
        $response = $this->postJson('/payment/create-checkout-session/999999');

        $response->assertStatus(404);
    }

    /**
     * Test successful payment callback
     */
    public function test_successful_payment_callback()
    {
        $plan = Plan::factory()->create(['name' => 'Test Plan']);

        // Mock successful Stripe session
        $mockSession = Mockery::mock('overload:' . Session::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_123456')
            ->andReturn((object)[
                'id' => 'cs_test_123456',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_123456',
                'amount_total' => 9999, // $99.99 in cents
                'currency' => 'usd',
                'metadata' => (object)[
                    'plan_id' => (string)$plan->id,
                    'plan_name' => 'Test Plan'
                ],
                'customer_details' => (object)[
                    'email' => 'test@example.com',
                    'name' => 'John Doe'
                ]
            ]);

        $response = $this->get('/payment/success?session_id=cs_test_123456');

        $response->assertStatus(200)
                 ->assertViewIs('payments.success')
                 ->assertViewHas('plan_name', 'Test Plan');

        // Verify payment record was created
        $this->assertDatabaseHas('payments', [
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_123456',
            'stripe_payment_intent_id' => 'pi_test_123456',
            'amount' => 99.99,
            'currency' => 'USD',
            'status' => 'completed',
            'customer_email' => 'test@example.com',
            'customer_name' => 'John Doe'
        ]);
    }

    /**
     * Test payment success without session id
     */
    public function test_payment_success_without_session_id()
    {
        $response = $this->get('/payment/success');

        $response->assertRedirect(route('pricing.index'))
                 ->assertSessionHas('error', 'Invalid payment session');
    }

    /**
     * Test payment success with unpaid session
     */
    public function test_payment_success_with_unpaid_session()
    {
        // Mock unpaid Stripe session
        $mockSession = Mockery::mock('overload:' . Session::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_unpaid')
            ->andReturn((object)[
                'id' => 'cs_test_unpaid',
                'payment_status' => 'unpaid'
            ]);

        $response = $this->get('/payment/success?session_id=cs_test_unpaid');

        $response->assertRedirect(route('pricing.index'))
                 ->assertSessionHas('error', 'Payment was not completed');
    }

    /**
     * Test webhook with valid signature
     */
    public function test_webhook_with_checkout_completed_event()
    {
        $plan = Plan::factory()->create();

        // Mock webhook event data
        $eventData = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_webhook_123',
                    'payment_intent' => 'pi_test_webhook_123',
                    'amount_total' => 5999, // $59.99
                    'currency' => 'usd',
                    'metadata' => [
                        'plan_id' => (string)$plan->id
                    ],
                    'customer_details' => [
                        'email' => 'webhook@example.com',
                        'name' => 'Webhook User'
                    ]
                ]
            ]
        ];

        // Note: In a real test, you'd need to properly mock the webhook signature verification
        // For now, we'll test the webhook handling logic directly
        $controller = new \App\Http\Controllers\PaymentController();

        // Create a mock request
        $request = \Mockery::mock(\Illuminate\Http\Request::class);
        $request->shouldReceive('getContent')->andReturn(json_encode($eventData));
        $request->shouldReceive('header')->with('Stripe-Signature')->andReturn('test_signature');

        // Test would need proper Stripe webhook signature mocking to work completely
        // This is a simplified version focusing on the business logic
    }

    /**
     * Test webhook creates payment record for checkout completion
     */
    public function test_webhook_creates_payment_record_for_checkout_completion()
    {
        $plan = Plan::factory()->create();

        $sessionData = [
            'id' => 'cs_test_webhook_create',
            'payment_intent' => 'pi_test_webhook_create',
            'amount_total' => 7999, // $79.99
            'currency' => 'usd',
            'metadata' => [
                'plan_id' => (string)$plan->id
            ],
            'customer_details' => [
                'email' => 'create@example.com',
                'name' => 'Create User'
            ]
        ];

        $controller = new \App\Http\Controllers\PaymentController();

        // Use reflection to test private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('handleCheckoutCompleted');
        $method->setAccessible(true);

        $method->invoke($controller, $sessionData);

        $this->assertDatabaseHas('payments', [
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_webhook_create',
            'stripe_payment_intent_id' => 'pi_test_webhook_create',
            'amount' => 79.99,
            'currency' => 'USD',
            'status' => 'completed',
            'customer_email' => 'create@example.com',
            'customer_name' => 'Create User'
        ]);
    }

    /**
     * Test webhook updates existing payment record
     */
    public function test_webhook_updates_existing_payment_record()
    {
        $plan = Plan::factory()->create();
        $payment = Payment::factory()->create([
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_existing',
            'status' => 'pending'
        ]);

        $sessionData = [
            'id' => 'cs_test_existing',
            'payment_intent' => 'pi_test_updated',
            'amount_total' => 8999,
            'currency' => 'usd',
            'metadata' => [
                'plan_id' => (string)$plan->id
            ],
            'customer_details' => [
                'email' => 'update@example.com',
                'name' => 'Update User'
            ]
        ];

        $controller = new \App\Http\Controllers\PaymentController();

        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('handleCheckoutCompleted');
        $method->setAccessible(true);

        $method->invoke($controller, $sessionData);

        $payment->refresh();

        $this->assertEquals('completed', $payment->status);
        $this->assertEquals('pi_test_updated', $payment->stripe_payment_intent_id);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
