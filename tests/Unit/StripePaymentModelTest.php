<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StripePaymentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Payment model attributes are fillable
     */
    public function test_payment_model_attributes_are_fillable()
    {
        $payment = new Payment();

        $expectedFillable = [
            'plan_id',
            'stripe_session_id',
            'stripe_payment_intent_id',
            'amount',
            'currency',
            'status',
            'customer_email',
            'customer_name',
        ];

        $this->assertEquals($expectedFillable, $payment->getFillable());
    }

    /**
     * Test: Payment belongs to plan relationship
     */
    public function test_payment_belongs_to_plan_relationship()
    {
        $plan = Plan::factory()->create();
        $payment = Payment::factory()->create(['plan_id' => $plan->id]);

        $this->assertInstanceOf(Plan::class, $payment->plan);
        $this->assertEquals($plan->id, $payment->plan->id);
    }

    /**
     * Test: Payment status check methods
     */
    public function test_payment_status_check_methods()
    {
        // Test completed payment
        $completedPayment = Payment::factory()->create(['status' => 'completed']);
        $this->assertTrue($completedPayment->isCompleted());
        $this->assertFalse($completedPayment->isFailed());
        $this->assertFalse($completedPayment->isPending());

        // Test failed payment
        $failedPayment = Payment::factory()->create(['status' => 'failed']);
        $this->assertFalse($failedPayment->isCompleted());
        $this->assertTrue($failedPayment->isFailed());
        $this->assertFalse($failedPayment->isPending());

        // Test pending payment
        $pendingPayment = Payment::factory()->create(['status' => 'pending']);
        $this->assertFalse($pendingPayment->isCompleted());
        $this->assertFalse($pendingPayment->isFailed());
        $this->assertTrue($pendingPayment->isPending());
    }

    /**
     * Test: Formatted amount attribute
     */
    public function test_formatted_amount_attribute()
    {
        $payment = Payment::factory()->create(['amount' => 99.99]);

        $this->assertEquals('$99.99', $payment->getFormattedAmountAttribute());
        $this->assertEquals('$99.99', $payment->formatted_amount);
    }

    /**
     * Test: Amount is cast to decimal
     */
    public function test_amount_is_cast_to_decimal()
    {
        $payment = Payment::factory()->create(['amount' => '99.99']);

        $this->assertIsFloat($payment->amount);
        $this->assertEquals(99.99, $payment->amount);
    }

    /**
     * Test: Payment creation with all attributes
     */
    public function test_payment_creation_with_all_attributes()
    {
        $plan = Plan::factory()->create();

        $paymentData = [
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_123',
            'stripe_payment_intent_id' => 'pi_test_123',
            'amount' => 149.99,
            'currency' => 'USD',
            'status' => 'completed',
            'customer_email' => 'test@stripe.com',
            'customer_name' => 'Test Customer',
        ];

        $payment = Payment::create($paymentData);

        $this->assertDatabaseHas('payments', $paymentData);
        $this->assertEquals('completed', $payment->status);
        $this->assertEquals(149.99, $payment->amount);
    }
}
