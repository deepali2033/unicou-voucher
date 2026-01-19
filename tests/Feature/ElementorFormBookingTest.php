<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Models\Notification;

class ElementorFormBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin users for notification testing
        User::factory()->create([
            'email' => 'admin1@example.com',
            'account_type' => 'admin',
        ]);

        User::factory()->create([
            'email' => 'admin2@example.com',
            'account_type' => 'admin',
        ]);

        // Create a service for testing
        Service::factory()->create([
            'name' => 'Regular House Cleaning',
        ]);
    }

    /**
     * Test: Elementor Pro Forms booking submission stores data
     */
    public function test_elementor_form_submission_stores_booking()
    {
        // Arrange: Simulate Elementor Pro Forms AJAX request
        $elementorData = [
            'action' => 'elementor_pro_forms_send_form',
            'post_id' => '140',
            'form_id' => '33d2c75',
            'referer_title' => 'Free Quote',
            'queried_id' => '140',
            'form_fields' => [
                'service_booking_form' => 'Regular House Cleaning',
                'bedrooms_booking_form' => '3',
                'bathrooms_booking_form' => '2',
                'extras_booking_form' => 'Deep clean kitchen, vacuum carpets',
                'frequency_booking_form' => 'Weekly',
                'area_booking_form' => '1500',
                'date_booking_form' => '2024-02-15',
                'time_booking_form' => '10:00',
                'name_booking_form' => 'John Doe',
                'tel_booking_form' => '+1-555-123-4567',
                'email_booking_form' => 'john.doe@example.com',
                'street_booking_form' => '123 Main Street',
                'city_booking_form' => 'Anytown',
                'states_booking_form' => 'California',
                'zip_code_booking_form' => '90210',
                'where_to_park_booking_form' => 'Driveway available',
                'flexible_time_booking_form' => 'Morning preferred',
                'entrance_info_booking_form' => 'Front door, ring doorbell',
                'pets_booking_form' => 'One friendly dog',
                'message_booking_form' => 'Please bring eco-friendly cleaning supplies.'
            ]
        ];

        // Act: Submit via AJAX route
        $response = $this->postJson('/ajax', $elementorData);

        // Assert: Verify JSON response
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Thank you for booking a service! We will contact you soon to confirm your appointment.'
                ]);

        // Assert: Verify data was stored correctly
        $this->assertDatabaseHas('book_services', [
            'service_name' => 'Regular House Cleaning',
            'customer_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Main Street',
            'city' => 'Anytown',
            'state' => 'California',
            'zip_code' => '90210',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'extras' => 'Deep clean kitchen, vacuum carpets',
            'frequency' => 'Weekly',
            'square_feet' => '1500',
            'booking_date' => '2024-02-15',
            'booking_time' => '10:00',
            'parking_info' => 'Driveway available',
            'flexible_time' => 'Morning preferred',
            'entrance_info' => 'Front door, ring doorbell',
            'pets' => 'One friendly dog',
            'special_instructions' => 'Please bring eco-friendly cleaning supplies.',
            'status' => 'pending'
        ]);
    }

    /**
     * Test: Elementor form submission creates admin notifications
     */
    public function test_elementor_form_submission_creates_admin_notifications()
    {
        // Arrange: Get admin users
        $admins = User::where('account_type', 'admin')->get();
        $this->assertCount(2, $admins);

        $elementorData = [
            'action' => 'elementor_pro_forms_send_form',
            'form_fields' => [
                'service_booking_form' => 'Deep Cleaning Service',
                'bedrooms_booking_form' => '2',
                'bathrooms_booking_form' => '1',
                'extras_booking_form' => 'Basic cleaning',
                'frequency_booking_form' => 'One-time',
                'area_booking_form' => '1200',
                'date_booking_form' => '2024-03-01',
                'time_booking_form' => '14:00',
                'name_booking_form' => 'Jane Smith',
                'tel_booking_form' => '+1-555-987-6543',
                'email_booking_form' => 'jane.smith@example.com',
                'street_booking_form' => '456 Oak Avenue',
                'city_booking_form' => 'Somewhere',
                'states_booking_form' => 'Texas',
                'zip_code_booking_form' => '75001',
                'where_to_park_booking_form' => 'Street parking',
                'flexible_time_booking_form' => 'Flexible',
                'entrance_info_booking_form' => 'Main door',
                'pets_booking_form' => 'One cat'
            ]
        ];

        // Act: Submit via AJAX route
        $response = $this->postJson('/ajax', $elementorData);

        // Assert: Verify response
        $response->assertStatus(200);

        // Assert: Verify notifications were created for all admins
        foreach ($admins as $admin) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $admin->id,
                'title' => 'New Service Booking',
                'type' => 'service',
                'is_read' => false,
            ]);
        }

        // Verify the notification description contains the customer name
        $notification = Notification::where('title', 'New Service Booking')->first();
        $this->assertStringContainsString('Jane Smith', $notification->description);
        $this->assertStringContainsString('Deep Cleaning Service', $notification->description);
    }

    /**
     * Test: Elementor form validation errors
     */
    public function test_elementor_form_validation_errors()
    {
        // Arrange: Submit incomplete data
        $elementorData = [
            'action' => 'elementor_pro_forms_send_form',
            'form_fields' => [
                'service_booking_form' => 'Regular House Cleaning',
                // Missing required fields
            ]
        ];

        // Act: Submit incomplete form
        $response = $this->postJson('/ajax', $elementorData);

        // Assert: Verify validation error response
        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Please fill all required fields correctly.'
                ]);

        // Verify no booking was created
        $this->assertDatabaseCount('book_services', 0);
    }

    /**
     * Test: Non-booking form is handled gracefully
     */
    public function test_non_booking_elementor_form_handled_gracefully()
    {
        // Arrange: Submit Elementor form without booking form fields (e.g., contact form)
        $elementorData = [
            'action' => 'elementor_pro_forms_send_form',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Contact form message'
        ];

        // Act: Submit non-booking form
        $response = $this->postJson('/ajax', $elementorData);

        // Assert: Verify graceful handling
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Form submitted successfully'
                ]);

        // Verify no booking was created
        $this->assertDatabaseCount('book_services', 0);
    }
}
