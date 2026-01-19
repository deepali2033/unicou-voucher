<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Models\Notification;

class UserBookServiceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $otherUser;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users for testing
        $this->user = User::factory()->create([
            'account_type' => 'client',
        ]);

        $this->otherUser = User::factory()->create([
            'account_type' => 'client',
        ]);

        // Create a service for testing
        $this->service = Service::factory()->create([
            'name' => 'House Cleaning',
            'is_active' => true
        ]);

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
     * Test: User can view bookings list
     *
     * This test verifies that a user can view their own bookings
     * and only their own bookings on the index page.
     */
    public function test_user_can_view_bookings_list()
    {
        // Arrange: Create bookings for both users
        $userBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'status' => 'pending'
        ]);

        $otherUserBooking = BookService::factory()->create([
            'user_id' => $this->otherUser->id,
            'service_name' => 'Office Cleaning',
            'customer_name' => 'Jane Smith',
            'status' => 'confirmed'
        ]);

        // Act: User views their bookings
        $response = $this->actingAs($this->user)
            ->get(route('user.book-services.index'));

        // Assert: User sees only their own bookings
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('House Cleaning');
        $response->assertDontSee('Jane Smith');
        $response->assertDontSee('Office Cleaning');
    }

    /**
     * Test: User can create booking
     *
     * This test verifies that a user can successfully create
     * a new service booking through the create form.
     */
    public function test_user_can_create_booking()
    {
        // Arrange: Prepare valid booking data
        $bookingData = [
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Main Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'extras' => 'Deep cleaning',
            'frequency' => 'Weekly',
            'square_feet' => '1500',
            'booking_date' => '2024-03-15',
            'booking_time' => '10:00 AM',
            'parking_info' => 'Driveway available',
            'flexible_time' => 'Morning preferred',
            'entrance_info' => 'Front door',
            'pets' => 'One cat',
            'special_instructions' => 'Please be gentle with antiques'
        ];

        // Act: Submit booking creation form
        $response = $this->actingAs($this->user)
            ->post(route('user.book-services.store'), $bookingData);

        // Assert: Booking is created and user is redirected
        $response->assertRedirect(route('user.book-services.index'));
        $response->assertSessionHas('success', 'Service booking created successfully!');

        $this->assertDatabaseHas('book_services', [
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'pending'
        ]);

        // Assert: Admin notifications are created
        $admins = User::where('account_type', 'admin')->get();
        foreach ($admins as $admin) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $admin->id,
                'title' => 'New Service Booking',
                'type' => 'service'
            ]);
        }
    }

    /**
     * Test: User can view booking details
     *
     * This test verifies that a user can view the details
     * of their own booking but not others' bookings.
     */
    public function test_user_can_view_booking_details()
    {
        // Arrange: Create a booking for the user
        $booking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'pending'
        ]);

        // Act: User views their booking details
        $response = $this->actingAs($this->user)
            ->get(route('user.book-services.show', $booking));

        // Assert: User can see their booking details
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('House Cleaning');
        $response->assertSee('john.doe@example.com');
        $response->assertSee('pending');
    }

    /**
     * Test: User can edit pending booking
     *
     * This test verifies that a user can access the edit form
     * for their pending bookings but not for confirmed/completed ones.
     */
    public function test_user_can_edit_pending_booking()
    {
        // Arrange: Create a pending booking for the user
        $pendingBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'status' => 'pending'
        ]);

        // Act: User accesses edit form for pending booking
        $response = $this->actingAs($this->user)
            ->get(route('user.book-services.edit', $pendingBooking));

        // Assert: User can access edit form
        $response->assertStatus(200);
        $response->assertSee('Edit Service Booking');
        $response->assertSee($pendingBooking->customer_name);
    }

    /**
     * Test: User can update pending booking
     *
     * This test verifies that a user can successfully update
     * their pending service booking.
     */
    public function test_user_can_update_pending_booking()
    {
        // Arrange: Create a pending booking for the user
        $booking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'status' => 'pending'
        ]);

        $updatedData = [
            'service_name' => 'Deep Cleaning',
            'customer_name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'phone' => '+1-555-987-6543',
            'street_address' => '456 Oak Avenue',
            'city' => 'Updated City',
            'state' => 'Updated State',
            'zip_code' => '54321',
            'bedrooms' => 4,
            'bathrooms' => 3,
            'extras' => 'Extra deep cleaning',
            'frequency' => 'Monthly',
            'square_feet' => '2000',
            'booking_date' => '2024-04-15',
            'booking_time' => '2:00 PM',
            'parking_info' => 'Street parking',
            'flexible_time' => 'Afternoon preferred',
            'entrance_info' => 'Side door',
            'pets' => 'Two dogs',
            'special_instructions' => 'Updated instructions'
        ];

        // Act: Submit booking update
        $response = $this->actingAs($this->user)
            ->put(route('user.book-services.update', $booking), $updatedData);

        // Assert: Booking is updated and user is redirected
        $response->assertRedirect(route('user.book-services.index'));
        $response->assertSessionHas('success', 'Service booking updated successfully!');

        $this->assertDatabaseHas('book_services', [
            'id' => $booking->id,
            'user_id' => $this->user->id,
            'service_name' => 'Deep Cleaning',
            'customer_name' => 'John Smith',
            'email' => 'john.smith@example.com'
        ]);
    }

    /**
     * Test: User can delete pending booking
     *
     * This test verifies that a user can delete their own
     * pending service booking.
     */
    public function test_user_can_delete_pending_booking()
    {
        // Arrange: Create a pending booking for the user
        $booking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'status' => 'pending'
        ]);

        // Act: User deletes their booking
        $response = $this->actingAs($this->user)
            ->delete(route('user.book-services.destroy', $booking));

        // Assert: Booking is deleted and user is redirected
        $response->assertRedirect(route('user.book-services.index'));
        $response->assertSessionHas('success', 'Service booking deleted successfully!');

        $this->assertDatabaseMissing('book_services', [
            'id' => $booking->id
        ]);
    }

    /**
     * Test: Unauthorized access prevention
     *
     * This test verifies that users cannot access, edit, or delete
     * bookings that belong to other users.
     */
    public function test_unauthorized_access_prevention()
    {
        // Arrange: Create a booking for another user
        $otherUserBooking = BookService::factory()->create([
            'user_id' => $this->otherUser->id,
            'service_name' => 'Office Cleaning',
            'customer_name' => 'Jane Smith',
            'status' => 'pending'
        ]);

        // Act & Assert: User cannot view other user's booking
        $response = $this->actingAs($this->user)
            ->get(route('user.book-services.show', $otherUserBooking));
        $response->assertStatus(403);

        // Act & Assert: User cannot edit other user's booking
        $response = $this->actingAs($this->user)
            ->get(route('user.book-services.edit', $otherUserBooking));
        $response->assertStatus(403);

        // Act & Assert: User cannot update other user's booking
        $response = $this->actingAs($this->user)
            ->put(route('user.book-services.update', $otherUserBooking), [
                'service_name' => 'Hacked Cleaning'
            ]);
        $response->assertStatus(403);

        // Act & Assert: User cannot delete other user's booking
        $response = $this->actingAs($this->user)
            ->delete(route('user.book-services.destroy', $otherUserBooking));
        $response->assertStatus(403);

        // Assert: Original booking remains unchanged
        $this->assertDatabaseHas('book_services', [
            'id' => $otherUserBooking->id,
            'user_id' => $this->otherUser->id,
            'service_name' => 'Office Cleaning',
            'customer_name' => 'Jane Smith'
        ]);
    }

    /**
     * Test: Invalid booking data validation
     *
     * This test verifies that the controller properly validates
     * required fields and data types when creating/updating bookings.
     */
    public function test_invalid_booking_data_validation()
    {
        // Test missing required fields
        $invalidData = [
            'service_name' => '', // Required field missing
            'customer_name' => '',
            'email' => 'invalid-email', // Invalid email format
            'phone' => '',
            'bedrooms' => 'not-a-number', // Invalid data type
            'bathrooms' => -1, // Invalid value
        ];

        // Act: Submit invalid booking data
        $response = $this->actingAs($this->user)
            ->post(route('user.book-services.store'), $invalidData);

        // Assert: Validation errors are returned
        $response->assertSessionHasErrors([
            'service_name',
            'customer_name',
            'email',
            'phone',
            'bedrooms',
            'bathrooms'
        ]);

        // Assert: No booking was created
        $this->assertDatabaseCount('book_services', 0);
    }

    /**
     * Test: Cannot edit non-pending booking
     *
     * This test verifies that users cannot edit bookings
     * that are not in pending status.
     */
    public function test_cannot_edit_non_pending_booking()
    {
        // Arrange: Create a confirmed booking for the user
        $confirmedBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'status' => 'confirmed'
        ]);

        // Act & Assert: User cannot edit confirmed booking
        $response = $this->actingAs($this->user)
            ->get(route('user.book-services.edit', $confirmedBooking));
        $response->assertStatus(403);

        // Act & Assert: User cannot update confirmed booking
        $response = $this->actingAs($this->user)
            ->put(route('user.book-services.update', $confirmedBooking), [
                'service_name' => 'Updated Service'
            ]);
        $response->assertStatus(403);
    }

    /**
     * Test: Cannot delete non-pending booking
     *
     * This test verifies that users cannot delete bookings
     * that are not in pending status.
     */
    public function test_cannot_delete_non_pending_booking()
    {
        // Arrange: Create a completed booking for the user
        $completedBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'status' => 'completed'
        ]);

        // Act & Assert: User cannot delete completed booking
        $response = $this->actingAs($this->user)
            ->delete(route('user.book-services.destroy', $completedBooking));
        $response->assertStatus(403);

        // Assert: Booking still exists
        $this->assertDatabaseHas('book_services', [
            'id' => $completedBooking->id,
            'status' => 'completed'
        ]);
    }

    /**
     * Test: Unauthenticated users cannot access routes
     *
     * This test verifies that unauthenticated users are redirected
     * to login when trying to access user booking routes.
     */
    public function test_unauthenticated_users_cannot_access_routes()
    {
        // Act & Assert: Unauthenticated access redirects to login
        $this->get(route('user.book-services.index'))
            ->assertRedirect(route('login'));

        $this->get(route('user.book-services.create'))
            ->assertRedirect(route('login'));

        $this->post(route('user.book-services.store'), [])
            ->assertRedirect(route('login'));
    }
}
