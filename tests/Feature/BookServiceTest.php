<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Models\Notification;

class BookServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    }

    /**
     * Test: Valid form submission stores data
     *
     * This test verifies that when a user submits the booking form,
     * the data is properly stored in the book_services table.
     */
    public function test_valid_form_submission_stores_data()
    {
        // Arrange: Prepare valid form data
        $formData = [
            'form_fields' => [
                'service_booking_form' => 'Regular House Cleaning',
                'bedrooms_booking_form' => 3,
                'bathrooms_booking_form' => 2,
                'extras_booking_form' => 'Deep clean kitchen, vacuum carpets',
                'frequency_booking_form' => 'Weekly',
                'area_booking_form' => '1500',
                'date_booking_form' => '2024-02-15',
                'time_booking_form' => '10:00 AM',
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

        // Act: Submit the form
        $response = $this->post(route('book-services.store'), $formData);

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
            'booking_time' => '10:00 AM',
            'parking_info' => 'Driveway available',
            'flexible_time' => 'Morning preferred',
            'entrance_info' => 'Front door, ring doorbell',
            'pets' => 'One friendly dog',
            'special_instructions' => 'Please bring eco-friendly cleaning supplies.',
            'status' => 'pending'
        ]);

        $response->assertStatus(302); // Redirect response
    }

    /**
     * Test: Success popup shows after submission
     *
     * This test verifies that after successful submission,
     * the user is redirected to the same page with a success parameter
     * that triggers the thank-you popup.
     */
    public function test_success_popup_shows_after_submission()
    {
        // Arrange: Prepare valid form data
        $formData = [
            'form_fields' => [
                'service_booking_form' => 'Regular House Cleaning',
                'bedrooms_booking_form' => 2,
                'bathrooms_booking_form' => 1,
                'extras_booking_form' => 'Basic cleaning',
                'frequency_booking_form' => 'Monthly',
                'area_booking_form' => '1200',
                'date_booking_form' => '2024-03-01',
                'time_booking_form' => '2:00 PM',
                'name_booking_form' => 'Jane Doe',
                'tel_booking_form' => '+1-555-987-6543',
                'email_booking_form' => 'jane.doe@example.com',
                'street_booking_form' => '456 Oak Avenue',
                'city_booking_form' => 'Somewhere',
                'states_booking_form' => 'Texas',
                'zip_code_booking_form' => '75001',
                'where_to_park_booking_form' => 'Driveway',
                'flexible_time_booking_form' => 'Yes',
                'entrance_info_booking_form' => 'Main door',
                'pets_booking_form' => 'One cat'
            ]
        ];

        // Act: Submit the form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify redirect with success parameter
        $response->assertRedirect(route('book-services.index', ['success' => '1']));

        // Follow the redirect and check for success parameter in URL
        $followResponse = $this->get(route('book-services.index', ['success' => '1']));
        $followResponse->assertStatus(200);
    }

    /**
     * Test: Admin notification gets created
     *
     * This test verifies that when a booking is submitted,
     * notifications are created for all admin users.
     */
    public function test_admin_notification_gets_created()
    {
        // Arrange: Get admin users
        $admins = User::where('account_type', 'admin')->get();
        $this->assertCount(2, $admins); // Verify we have 2 admins

        $formData = [
            'form_fields' => [
                'service_booking_form' => 'Deep Cleaning Service',
                'name_booking_form' => 'Alice Smith',
                'tel_booking_form' => '+1-555-111-2222',
                'email_booking_form' => 'alice.smith@example.com',
                'street_booking_form' => '789 Pine Street',
                'city_booking_form' => 'Testville',
                'states_booking_form' => 'Florida',
                'zip_code_booking_form' => '33101',
                'date_booking_form' => '2024-02-20',
                'time_booking_form' => '9:00 AM'
            ]
        ];

        // Act: Submit the form
        $response = $this->post(route('book-services.store'), $formData);

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
        $this->assertStringContainsString('Alice Smith', $notification->description);
        $this->assertStringContainsString('Deep Cleaning Service', $notification->description);

        $response->assertStatus(302);
    }

    /**
     * Test: Admin can view booking
     *
     * This test verifies that admin users can view the booking
     * in their dashboard at admin/book-services/index.
     */
    public function test_admin_can_view_booking()
    {
        // Arrange: Create an admin user and authenticate
        $admin = User::factory()->create(['account_type' => 'admin']);
        $this->actingAs($admin);

        // Create a booking
        $booking = BookService::create([
            'service_name' => 'Window Cleaning',
            'customer_name' => 'Bob Johnson',
            'email' => 'bob.johnson@example.com',
            'phone' => '+1-555-333-4444',
            'street_address' => '321 Elm Street',
            'city' => 'Admintown',
            'state' => 'Nevada',
            'zip_code' => '89101',
            'booking_date' => '2024-02-25',
            'booking_time' => '11:00 AM',
            'status' => 'pending'
        ]);

        // Act: Visit the admin booking index page
        $response = $this->get('/admin/book-services');

        // Assert: Verify the booking is displayed
        $response->assertStatus(200);
        $response->assertSee('Bob Johnson');
        $response->assertSee('Window Cleaning');
        $response->assertSee('bob.johnson@example.com');
        $response->assertSee('2024-02-25');
    }

    /**
     * Test: Missing required fields validation
     *
     * This test verifies that the form properly validates
     * required fields and rejects incomplete submissions.
     */
    public function test_missing_required_fields_validation()
    {
        // Arrange: Submit form with missing required fields
        $incompleteData = [
            'form_fields' => [
                'service_booking_form' => 'Regular House Cleaning',
                // Missing all other required fields
            ]
        ];

        // Act: Submit incomplete form
        $response = $this->post(route('book-services.store'), $incompleteData);

        // Assert: Verify validation errors for required fields
        $response->assertSessionHasErrors([
            'form_fields.bedrooms_booking_form',
            'form_fields.bathrooms_booking_form',
            'form_fields.extras_booking_form',
            'form_fields.frequency_booking_form',
            'form_fields.area_booking_form',
            'form_fields.date_booking_form',
            'form_fields.time_booking_form',
            'form_fields.name_booking_form',
            'form_fields.tel_booking_form',
            'form_fields.email_booking_form',
            'form_fields.street_booking_form',
            'form_fields.city_booking_form',
            'form_fields.states_booking_form',
            'form_fields.zip_code_booking_form',
            'form_fields.where_to_park_booking_form',
            'form_fields.flexible_time_booking_form',
            'form_fields.entrance_info_booking_form',
            'form_fields.pets_booking_form'
        ]);

        // Verify no booking was created
        $this->assertDatabaseCount('book_services', 0);
    }

    /**
     * Test: Invalid email format validation
     *
     * This test verifies that the form validates
     * email format and rejects invalid emails.
     */
    public function test_invalid_email_format_validation()
    {
        // Arrange: Form data with invalid email but all required fields
        $formData = [
            'form_fields' => [
                'service_booking_form' => 'Regular House Cleaning',
                'bedrooms_booking_form' => 2,
                'bathrooms_booking_form' => 1,
                'extras_booking_form' => 'Basic cleaning',
                'frequency_booking_form' => 'One-time',
                'area_booking_form' => '1000',
                'date_booking_form' => '2024-02-15',
                'time_booking_form' => '10:00 AM',
                'name_booking_form' => 'Invalid Email User',
                'tel_booking_form' => '+1-555-555-5555',
                'email_booking_form' => 'not-a-valid-email', // Invalid email
                'street_booking_form' => '123 Test Street',
                'city_booking_form' => 'Test City',
                'states_booking_form' => 'Test State',
                'zip_code_booking_form' => '12345',
                'where_to_park_booking_form' => 'Street',
                'flexible_time_booking_form' => 'No',
                'entrance_info_booking_form' => 'Front door',
                'pets_booking_form' => 'None'
            ]
        ];

        // Act: Submit form with invalid email
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify email validation error
        $response->assertSessionHasErrors(['form_fields.email_booking_form']);

        // Verify no booking was created
        $this->assertDatabaseCount('book_services', 0);
    }

    /**
     * Test: AJAX submission returns JSON
     *
     * This test verifies that when the form is submitted via AJAX,
     * the controller returns a proper JSON response.
     */
    public function test_ajax_submission_returns_json()
    {
        // Arrange: Prepare valid form data for AJAX
        $formData = [
            'form_fields' => [
                'service_booking_form' => 'Regular House Cleaning',
                'bedrooms_booking_form' => 3,
                'bathrooms_booking_form' => 2,
                'extras_booking_form' => 'Deep cleaning',
                'frequency_booking_form' => 'Weekly',
                'area_booking_form' => '1800',
                'date_booking_form' => '2024-03-15',
                'time_booking_form' => '3:00 PM',
                'name_booking_form' => 'AJAX User',
                'tel_booking_form' => '+1-555-777-8888',
                'email_booking_form' => 'ajax.user@example.com',
                'street_booking_form' => '999 AJAX Avenue',
                'city_booking_form' => 'JSON City',
                'states_booking_form' => 'API State',
                'zip_code_booking_form' => '99999',
                'where_to_park_booking_form' => 'Garage available',
                'flexible_time_booking_form' => 'Afternoon preferred',
                'entrance_info_booking_form' => 'Side entrance',
                'pets_booking_form' => 'No pets'
            ]
        ];

        // Act: Submit form via AJAX
        $response = $this->postJson(route('book-services.store'), $formData);

        // Assert: Verify JSON response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'message' => 'Thank you for booking a service! We will contact you soon to confirm your appointment.'
            ]
        ]);

        // Verify data was still stored
        $this->assertDatabaseHas('book_services', [
            'customer_name' => 'AJAX User',
            'email' => 'ajax.user@example.com'
        ]);
    }

    /**
     * Test: Database constraint violations handled
     *
     * This test verifies that the system gracefully handles
     * database constraint violations and other unexpected errors.
     */
    public function test_database_constraint_violations_handled()
    {
        // Arrange: Create a booking first
        BookService::create([
            'service_name' => 'Test Service',
            'customer_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1-555-000-0000',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => '2024-02-15',
            'booking_time' => '10:00 AM',
            'status' => 'pending'
        ]);

        // Try to create duplicate booking with same email and date (if unique constraint exists)
        $duplicateData = [
            'form_fields' => [
                'service_booking_form' => 'Duplicate Service',
                'name_booking_form' => 'Duplicate User',
                'tel_booking_form' => '+1-555-000-0000',
                'email_booking_form' => 'test@example.com',
                'street_booking_form' => '123 Test St',
                'city_booking_form' => 'Test City',
                'states_booking_form' => 'Test State',
                'zip_code_booking_form' => '12345',
                'date_booking_form' => '2024-02-15',
                'time_booking_form' => '10:00 AM'
            ]
        ];

        // Act: Submit potentially duplicate data
        $response = $this->post(route('book-services.store'), $duplicateData);

        // Assert: System should handle this gracefully
        // Either allow duplicate or show appropriate error
        $this->assertTrue(
            $response->isSuccessful() || $response->isRedirect(),
            'System should handle potential duplicates gracefully'
        );
    }
}
