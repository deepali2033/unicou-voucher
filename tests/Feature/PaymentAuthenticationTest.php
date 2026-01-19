<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Mockery;

class PaymentAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up Stripe mock
        Stripe::setApiKey('sk_test_fake_key');
    }

    /**
     * Test: Authenticated user creates checkout session
     *
     * Verifies that logged-in users can successfully create
     * a payment checkout session.
     */
    public function test_authenticated_user_creates_checkout_session()
    {
        // Arrange: Create authenticated user and plan
        $user = User::factory()->create(['account_type' => 'user']);
        $plan = Plan::factory()->create([
            'name' => 'Premium Plan',
            'price' => 99.99,
            'description' => 'Premium plan description'
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
                'url' => 'https://checkout.stripe.com/pay/cs_test_authenticated_123456'
            ]);

        // Act: Make authenticated request to create checkout session
        $response = $this->actingAs($user)
                         ->postJson("/payment/create-checkout-session/{$plan->id}");

        // Assert: Response is successful with checkout URL
        $response->assertStatus(200)
                 ->assertJson([
                     'checkout_url' => 'https://checkout.stripe.com/pay/cs_test_authenticated_123456'
                 ]);
    }

    /**
     * Test: Authenticated user accesses success page
     *
     * Verifies that logged-in users can access the payment
     * success page after completing a payment.
     */
    public function test_authenticated_user_accesses_success_page()
    {
        // Arrange: Create authenticated user and plan
        $user = User::factory()->create(['account_type' => 'user']);
        $plan = Plan::factory()->create(['name' => 'Test Plan']);

        // Mock successful Stripe session
        $mockSession = Mockery::mock('overload:' . Session::class);
        $mockSession->shouldReceive('retrieve')
            ->once()
            ->with('cs_test_authenticated_success')
            ->andReturn((object)[
                'id' => 'cs_test_authenticated_success',
                'payment_status' => 'paid',
                'payment_intent' => 'pi_test_authenticated_success',
                'amount_total' => 9999, // $99.99 in cents
                'currency' => 'usd',
                'metadata' => (object)[
                    'plan_id' => (string)$plan->id,
                    'plan_name' => 'Test Plan'
                ],
                'customer_details' => (object)[
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ]);

        // Act: Make authenticated request to success page
        $response = $this->actingAs($user)
                         ->get('/payment/success?session_id=cs_test_authenticated_success');

        // Assert: Success page is displayed
        $response->assertStatus(200)
                 ->assertViewIs('payments.success')
                 ->assertViewHas('plan_name', 'Test Plan');

        // Verify payment record was created
        $this->assertDatabaseHas('payments', [
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_authenticated_success',
            'status' => 'completed',
            'customer_email' => $user->email
        ]);
    }

    /**
     * Test: Unauthenticated user creates checkout redirects
     *
     * Verifies that non-authenticated users are redirected
     * to login when trying to create a checkout session.
     */
    public function test_unauthenticated_user_creates_checkout_redirects()
    {
        // Arrange: Create plan without authenticating user
        $plan = Plan::factory()->create([
            'name' => 'Premium Plan',
            'price' => 99.99
        ]);

        // Act: Make unauthenticated request to create checkout session
        $response = $this->postJson("/payment/create-checkout-session/{$plan->id}");

        // Assert: Request is redirected to login
        $response->assertStatus(401);
    }

    /**
     * Test: Unauthenticated user accesses success redirects
     *
     * Verifies that non-authenticated users are redirected
     * to login when trying to access payment success page.
     */
    public function test_unauthenticated_user_accesses_success_redirects()
    {
        // Act: Make unauthenticated request to success page
        $response = $this->get('/payment/success?session_id=cs_test_123456');

        // Assert: Request is redirected to login
        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Guest tries payment without login
     *
     * Verifies that guest users cannot access any payment-related
     * functionality and are properly redirected to authenticate.
     */
    public function test_guest_tries_payment_without_login()
    {
        // Arrange: Create plan
        $plan = Plan::factory()->create([
            'name' => 'Guest Test Plan',
            'price' => 49.99
        ]);

        // Act & Assert: Test all payment endpoints redirect unauthenticated users

        // Test create checkout session endpoint
        $response = $this->postJson("/payment/create-checkout-session/{$plan->id}");
        $response->assertStatus(401);

        // Test success page endpoint
        $response = $this->get('/payment/success?session_id=cs_test_guest_123');
        $response->assertRedirect(route('login'));

        // Test direct access to success page without session_id
        $response = $this->get('/payment/success');
        $response->assertRedirect(route('login'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
