<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test payment creation with all fields
     */
    public function test_payment_can_be_created_with_all_fields()
    {
        $plan = Plan::factory()->create();

        $payment = Payment::create([
            'plan_id' => $plan->id,
            'stripe_session_id' => 'cs_test_123456',
            'stripe_payment_intent_id' => 'pi_test_123456',
            'amount' => 99.99,
            'currency' => 'USD',
            'status' => 'completed',
            'customer_email' => 'test@example.com',
            'customer_name' => 'John Doe',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($plan->id, $payment->plan_id);
        $this->assertEquals('cs_test_123456', $payment->stripe_session_id);
        $this->assertEquals('pi_test_123456', $payment->stripe_payment_intent_id);
        $this->assertEquals(99.99, $payment->amount);
        $this->assertEquals('USD', $payment->currency);
        $this->assertEquals('completed', $payment->status);
        $this->assertEquals('test@example.com', $payment->customer_email);
        $this->assertEquals('John Doe', $payment->customer_name);
    }

    /**
     * Test payment belongs to plan relationship
     */
    public function test_payment_belongs_to_plan()
    {
        $plan = Plan::factory()->create(['name' => 'Test Plan']);
        $payment = Payment::factory()->create(['plan_id' => $plan->id]);

        $this->assertInstanceOf(Plan::class, $payment->plan);
        $this->assertEquals('Test Plan', $payment->plan->name);
    }

    /**
     * Test formatted amount attribute
     */
    public function test_formatted_amount_attribute()
    {
        $payment = Payment::factory()->create(['amount' => 123.45]);

        $this->assertEquals('$123.45', $payment->formatted_amount);
    }

    /**
     * Test amount is cast to decimal
     */
    public function test_amount_is_cast_to_decimal()
    {
        $payment = Payment::factory()->create(['amount' => '99.99']);

        $this->assertEquals(99.99, $payment->amount);
        $this->assertIsFloat($payment->amount);
    }

    /**
     * Test payment status helper methods
     */
    public function test_payment_status_helper_methods()
    {
        $completedPayment = Payment::factory()->create(['status' => 'completed']);
        $failedPayment = Payment::factory()->create(['status' => 'failed']);
        $pendingPayment = Payment::factory()->create(['status' => 'pending']);

        $this->assertTrue($completedPayment->isCompleted());
        $this->assertFalse($completedPayment->isFailed());
        $this->assertFalse($completedPayment->isPending());

        $this->assertFalse($failedPayment->isCompleted());
        $this->assertTrue($failedPayment->isFailed());
        $this->assertFalse($failedPayment->isPending());

        $this->assertFalse($pendingPayment->isCompleted());
        $this->assertFalse($pendingPayment->isFailed());
        $this->assertTrue($pendingPayment->isPending());
    }

    /**
     * Test required fields validation
     */
    public function test_payment_requires_essential_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // This should fail due to missing required fields
        Payment::create([]);
    }

    /**
     * Test unique stripe session id constraint
     */
    public function test_stripe_session_id_must_be_unique()
    {
        Payment::factory()->create(['stripe_session_id' => 'cs_unique_123']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // This should fail due to duplicate stripe_session_id
        Payment::factory()->create(['stripe_session_id' => 'cs_unique_123']);
    }
}
