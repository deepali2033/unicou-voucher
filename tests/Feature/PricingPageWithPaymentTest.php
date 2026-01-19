<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PricingPageWithPaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test pricing page loads with payment buttons
     */
    public function test_pricing_page_loads_with_payment_buttons()
    {
        // Create test plans
        $plans = Plan::factory()->count(4)->create([
            'is_active' => true
        ]);

        $response = $this->get('/pricing');

        $response->assertStatus(200);

        // Check that payment buttons are present
        foreach ($plans as $plan) {
            $response->assertSee('payment-btn');
            $response->assertSee('data-plan-id="' . $plan->id . '"');
            $response->assertSee('Pay Now');
        }
    }

    /**
     * Test pricing page includes payment JavaScript
     */
    public function test_pricing_page_includes_payment_javascript()
    {
        Plan::factory()->create(['is_active' => true]);

        $response = $this->get('/pricing');

        $response->assertStatus(200)
                 ->assertSee('payment-btn')
                 ->assertSee('create-checkout-session')
                 ->assertSee('Processing...');
    }

    /**
     * Test pricing page shows correct plan information
     */
    public function test_pricing_page_shows_correct_plan_information()
    {
        $plan = Plan::factory()->create([
            'name' => 'Premium Cleaning',
            'price' => 150.00,
            'is_active' => true
        ]);

        $response = $this->get('/pricing');

        $response->assertStatus(200)
                 ->assertSee('Premium Cleaning')
                 ->assertSee('€150')
                 ->assertSee('data-plan-name="Premium Cleaning"')
                 ->assertSee('data-plan-price="150"');
    }

    /**
     * Test inactive plans are not shown
     */
    public function test_inactive_plans_are_not_shown()
    {
        $activePlan = Plan::factory()->create([
            'name' => 'Active Plan',
            'is_active' => true
        ]);

        $inactivePlan = Plan::factory()->create([
            'name' => 'Inactive Plan',
            'is_active' => false
        ]);

        $response = $this->get('/pricing');

        $response->assertStatus(200)
                 ->assertSee('Active Plan')
                 ->assertDontSee('Inactive Plan');
    }

    /**
     * Test pricing page includes CSRF token for AJAX requests
     */
    public function test_pricing_page_includes_csrf_token()
    {
        Plan::factory()->create(['is_active' => true]);

        $response = $this->get('/pricing');

        $response->assertStatus(200)
                 ->assertSee('csrf-token');
    }

    /**
     * Test payment button styling is preserved
     */
    public function test_payment_button_styling_is_preserved()
    {
        Plan::factory()->create(['is_active' => true]);

        $response = $this->get('/pricing');

        $response->assertStatus(200)
                 ->assertSee('elementor-button')
                 ->assertSee('elementor-button-link')
                 ->assertSee('elementor-size-sm')
                 ->assertSee('vamtam-has-hover-anim');
    }
}
