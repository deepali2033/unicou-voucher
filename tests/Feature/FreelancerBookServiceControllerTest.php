<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FreelancerBookServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Freelancer can view bookings for their services
     *
     * This test verifies that freelancers can access the index page
     * and see only bookings for services they created.
     */
    public function test_freelancer_can_view_bookings_for_their_services()
    {
        // Arrange: Create freelancer and their services
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $otherFreelancer = User::factory()->create(['account_type' => 'freelancer']);

        $freelancerService = Service::factory()->create(['user_id' => $freelancer->id]);
        $otherService = Service::factory()->create(['user_id' => $otherFreelancer->id]);

        // Create bookings - one for freelancer's service, one for other's service
        $freelancerBooking = BookService::create([
            'service_id' => $freelancerService->id,
            'service_name' => $freelancerService->name,
            'customer_name' => 'Customer A',
            'email' => 'customerA@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Main St',
            'city' => 'City A',
            'state' => 'State A',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        $otherBooking = BookService::create([
            'service_id' => $otherService->id,
            'service_name' => $otherService->name,
            'customer_name' => 'Customer B',
            'email' => 'customerB@example.com',
            'phone' => '+1-555-123-4568',
            'street_address' => '124 Main St',
            'city' => 'City B',
            'state' => 'State B',
            'zip_code' => '12346',
            'booking_date' => now()->addDays(2)->toDateString(),
        ]);

        // Act: Access the index page as the freelancer
        $response = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.index'));

        // Assert: Response is successful and shows correct bookings
        $response->assertStatus(200);
        $response->assertSee('Customer A'); // Should see their booking
        $response->assertDontSee('Customer B'); // Should not see other's booking
        $response->assertViewHas('bookServices');
    }

    /**
     * Test: Freelancer can view specific booking details
     *
     * This test verifies that freelancers can view detailed information
     * about bookings for their services.
     */
    public function test_freelancer_can_view_specific_booking_details()
    {
        // Arrange: Create freelancer, service, and booking
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $service = Service::factory()->create(['user_id' => $freelancer->id]);

        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'John Customer',
            'email' => 'john@example.com',
            'phone' => '+1-555-987-6543',
            'street_address' => '456 Oak Street',
            'city' => 'Springfield',
            'state' => 'Illinois',
            'zip_code' => '62701',
            'booking_date' => now()->addDays(3)->toDateString(),
            'special_instructions' => 'Please use eco-friendly products',
            'status' => 'pending'
        ]);

        // Act: Access the booking details page
        $response = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.show', $booking));

        // Assert: Response shows booking details
        $response->assertStatus(200);
        $response->assertSee('John Customer');
        $response->assertSee('john@example.com');
        $response->assertSee('456 Oak Street');
        $response->assertSee('Springfield');
        $response->assertSee('eco-friendly products');
        $response->assertSee('pending');
        $response->assertViewHas('bookService');
    }

    /**
     * Test: Freelancer cannot access other freelancer's bookings
     *
     * This test ensures proper access control - freelancers should only
     * be able to access bookings for their own services.
     */
    public function test_freelancer_cannot_access_other_freelancer_bookings()
    {
        // Arrange: Create two freelancers with their own services
        $freelancer1 = User::factory()->create(['account_type' => 'freelancer']);
        $freelancer2 = User::factory()->create(['account_type' => 'freelancer']);

        $service1 = Service::factory()->create(['user_id' => $freelancer1->id]);
        $service2 = Service::factory()->create(['user_id' => $freelancer2->id]);

        $booking1 = BookService::create([
            'service_id' => $service1->id,
            'service_name' => $service1->name,
            'customer_name' => 'Customer 1',
            'email' => 'customer1@example.com',
            'phone' => '+1-555-111-1111',
            'street_address' => '111 First St',
            'city' => 'City 1',
            'state' => 'State 1',
            'zip_code' => '11111',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        $booking2 = BookService::create([
            'service_id' => $service2->id,
            'service_name' => $service2->name,
            'customer_name' => 'Customer 2',
            'email' => 'customer2@example.com',
            'phone' => '+1-555-222-2222',
            'street_address' => '222 Second St',
            'city' => 'City 2',
            'state' => 'State 2',
            'zip_code' => '22222',
            'booking_date' => now()->addDays(2)->toDateString(),
        ]);

        // Act & Assert: Freelancer 1 trying to access Freelancer 2's booking
        $response = $this->actingAs($freelancer1)
            ->get(route('freelancer.book-services.show', $booking2));

        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test: Freelancer can edit and update booking status and price
     *
     * This test verifies that freelancers can modify booking status
     * and pricing for their services.
     */
    public function test_freelancer_can_edit_and_update_booking_status_and_price()
    {
        // Arrange: Create freelancer, service, and booking
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $service = Service::factory()->create(['user_id' => $freelancer->id]);

        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(1)->toDateString(),
            'status' => 'pending',
            'price' => null
        ]);

        // Act: Access edit form
        $editResponse = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.edit', $booking));

        // Assert: Edit form loads successfully
        $editResponse->assertStatus(200);
        $editResponse->assertViewHas('bookService');

        // Act: Update the booking
        $updateResponse = $this->actingAs($freelancer)
            ->patch(route('freelancer.book-services.update', $booking), [
                'status' => 'confirmed',
                'price' => '150.00'
            ]);

        // Assert: Update is successful and redirects
        $updateResponse->assertStatus(302);
        $updateResponse->assertRedirect(route('freelancer.book-services.show', $booking));
        $updateResponse->assertSessionHas('success', 'Booking updated successfully!');

        // Verify database changes
        $this->assertDatabaseHas('book_services', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'price' => '150.00'
        ]);
    }

    /**
     * Test: Freelancer cannot create new bookings
     *
     * This test ensures freelancers cannot create bookings since
     * they receive bookings rather than create them.
     */
    public function test_freelancer_cannot_create_new_bookings()
    {
        // Arrange: Create freelancer
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);

        // Act: Try to access create route
        $createResponse = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.create'));

        // Assert: Redirects with error message
        $createResponse->assertStatus(302);
        $createResponse->assertRedirect(route('freelancer.book-services.index'));
        $createResponse->assertSessionHas('error');

        // Act: Try to store new booking
        $storeResponse = $this->actingAs($freelancer)
            ->post(route('freelancer.book-services.store'), [
                'service_name' => 'Test Service',
                'customer_name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '+1-555-123-4567',
                'street_address' => '123 Test St',
                'city' => 'Test City',
                'state' => 'Test State',
                'zip_code' => '12345',
                'booking_date' => now()->addDays(1)->toDateString(),
            ]);

        // Assert: Redirects with error message
        $storeResponse->assertStatus(302);
        $storeResponse->assertRedirect(route('freelancer.book-services.index'));
        $storeResponse->assertSessionHas('error');
    }

    /**
     * Test: Freelancer cannot delete bookings
     *
     * This test ensures freelancers cannot delete bookings
     * but can update status to cancelled instead.
     */
    public function test_freelancer_cannot_delete_bookings()
    {
        // Arrange: Create freelancer, service, and booking
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $service = Service::factory()->create(['user_id' => $freelancer->id]);

        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        // Act: Try to delete the booking
        $response = $this->actingAs($freelancer)
            ->delete(route('freelancer.book-services.destroy', $booking));

        // Assert: Redirects with error message and booking still exists
        $response->assertStatus(302);
        $response->assertRedirect(route('freelancer.book-services.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('book_services', [
            'id' => $booking->id
        ]);
    }

    /**
     * Test: Update validation rules work correctly
     *
     * This test verifies that the update method properly validates
     * input data and rejects invalid submissions.
     */
    public function test_update_validation_rules_work_correctly()
    {
        // Arrange: Create freelancer, service, and booking
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $service = Service::factory()->create(['user_id' => $freelancer->id]);

        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        // Act: Try to update with invalid data
        $response = $this->actingAs($freelancer)
            ->patch(route('freelancer.book-services.update', $booking), [
                'status' => 'invalid_status',
                'price' => '-50.00' // Negative price
            ]);

        // Assert: Validation fails and redirects back
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['status']);

        // Try with price too high
        $response2 = $this->actingAs($freelancer)
            ->patch(route('freelancer.book-services.update', $booking), [
                'status' => 'confirmed',
                'price' => '9999999.99' // Too high
            ]);

        $response2->assertStatus(302);
        $response2->assertSessionHasErrors(['price']);
    }

    /**
     * Test: Non-freelancer users cannot access freelancer routes
     *
     * This test ensures proper middleware protection for freelancer-specific routes.
     */
    public function test_non_freelancer_users_cannot_access_freelancer_routes()
    {
        // Arrange: Create users of different account types
        $regularUser = User::factory()->create(['account_type' => 'user']);
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);

        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $service = Service::factory()->create(['user_id' => $freelancer->id]);
        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        // Act & Assert: Regular user cannot access freelancer routes
        $response = $this->actingAs($regularUser)
            ->get(route('freelancer.book-services.index'));
        $response->assertStatus(403);

        // Act & Assert: Recruiter cannot access freelancer routes
        $response2 = $this->actingAs($recruiter)
            ->get(route('freelancer.book-services.show', $booking));
        $response2->assertStatus(403);
    }

    /**
     * Test: Empty booking list shows appropriate message
     *
     * This test verifies that when a freelancer has no bookings,
     * appropriate messaging is displayed.
     */
    public function test_empty_booking_list_shows_appropriate_message()
    {
        // Arrange: Create freelancer with no bookings
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);

        // Act: Access the index page
        $response = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.index'));

        // Assert: Page loads and shows empty state
        $response->assertStatus(200);
        $response->assertViewHas('bookServices');

        // The view should handle empty collection gracefully
        $bookServices = $response->viewData('bookServices');
        $this->assertEquals(0, $bookServices->count());
    }
}
