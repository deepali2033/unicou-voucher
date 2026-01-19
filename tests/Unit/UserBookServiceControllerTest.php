<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\User\BookServiceController;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBookServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new BookServiceController();
        $this->user = User::factory()->create(['account_type' => 'client']);
    }

    /**
     * Test: Index method returns user bookings only
     *
     * This test verifies that the index method returns only
     * the authenticated user's bookings.
     */
    public function test_index_returns_user_bookings_only()
    {
        // Arrange: Create bookings for different users
        $userBooking = BookService::factory()->create(['user_id' => $this->user->id]);
        $otherUserBooking = BookService::factory()->create(['user_id' => User::factory()->create()->id]);

        // Act: Call the index method as the authenticated user
        $this->actingAs($this->user);
        $request = new Request();
        $response = $this->controller->index($request);

        // Assert: Response contains only user's bookings
        $this->assertEquals(200, $response->getStatusCode());

        $bookings = $response->getData()['bookings'];
        $this->assertCount(1, $bookings->items());
        $this->assertEquals($userBooking->id, $bookings->items()[0]->id);
    }

    /**
     * Test: Store method validates required fields
     *
     * This test verifies that the store method properly validates
     * required fields before creating a booking.
     */
    public function test_store_validates_required_fields()
    {
        // Arrange: Create incomplete booking data
        $incompleteData = [
            'service_name' => 'House Cleaning',
            // Missing required fields: customer_name, email, etc.
        ];

        // Act & Assert: Request with incomplete data should fail validation
        $this->actingAs($this->user);
        $request = new Request($incompleteData);

        $response = $this->post(route('user.book-services.store'), $incompleteData);
        $response->assertSessionHasErrors(['customer_name', 'email', 'phone']);
    }

    /**
     * Test: Show method only allows access to own bookings
     *
     * This test verifies that users can only view their own bookings
     * and are denied access to others' bookings.
     */
    public function test_show_allows_access_to_own_bookings_only()
    {
        // Arrange: Create bookings for different users
        $userBooking = BookService::factory()->create(['user_id' => $this->user->id]);
        $otherUserBooking = BookService::factory()->create(['user_id' => User::factory()->create()->id]);

        // Act & Assert: User can access their own booking
        $this->actingAs($this->user);
        $response = $this->get(route('user.book-services.show', $userBooking));
        $response->assertStatus(200);

        // Act & Assert: User cannot access other's booking
        $response = $this->get(route('user.book-services.show', $otherUserBooking));
        $response->assertStatus(403);
    }

    /**
     * Test: Update method only allows editing pending bookings
     *
     * This test verifies that users can only edit bookings
     * that are in pending status.
     */
    public function test_update_only_allows_editing_pending_bookings()
    {
        // Arrange: Create bookings with different statuses
        $pendingBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $confirmedBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed'
        ]);

        $updateData = ['customer_name' => 'Updated Name'];

        // Act & Assert: Can update pending booking
        $this->actingAs($this->user);
        $response = $this->put(route('user.book-services.update', $pendingBooking), $updateData);
        $response->assertRedirect(route('user.book-services.index'));

        // Act & Assert: Cannot update confirmed booking
        $response = $this->put(route('user.book-services.update', $confirmedBooking), $updateData);
        $response->assertStatus(403);
    }

    /**
     * Test: Destroy method only allows deleting pending bookings
     *
     * This test verifies that users can only delete bookings
     * that are in pending status.
     */
    public function test_destroy_only_allows_deleting_pending_bookings()
    {
        // Arrange: Create bookings with different statuses
        $pendingBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $completedBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        // Act & Assert: Can delete pending booking
        $this->actingAs($this->user);
        $response = $this->delete(route('user.book-services.destroy', $pendingBooking));
        $response->assertRedirect(route('user.book-services.index'));
        $this->assertDatabaseMissing('book_services', ['id' => $pendingBooking->id]);

        // Act & Assert: Cannot delete completed booking
        $response = $this->delete(route('user.book-services.destroy', $completedBooking));
        $response->assertStatus(403);
        $this->assertDatabaseHas('book_services', ['id' => $completedBooking->id]);
    }

    /**
     * Test: Create method shows form with services
     *
     * This test verifies that the create method returns
     * the form with available services.
     */
    public function test_create_shows_form_with_services()
    {
        // Arrange: Create some active services
        Service::factory()->create(['is_active' => true, 'name' => 'House Cleaning']);
        Service::factory()->create(['is_active' => true, 'name' => 'Office Cleaning']);
        Service::factory()->create(['is_active' => false, 'name' => 'Inactive Service']);

        // Act: Access create form
        $this->actingAs($this->user);
        $response = $this->get(route('user.book-services.create'));

        // Assert: Form is shown with active services
        $response->assertStatus(200);
        $response->assertSee('House Cleaning');
        $response->assertSee('Office Cleaning');
        $response->assertDontSee('Inactive Service');
    }

    /**
     * Test: Edit method only shows form for pending bookings
     *
     * This test verifies that the edit method only allows
     * editing pending bookings owned by the user.
     */
    public function test_edit_only_shows_form_for_pending_bookings()
    {
        // Arrange: Create bookings with different statuses
        $pendingBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $confirmedBooking = BookService::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed'
        ]);

        // Act & Assert: Can access edit form for pending booking
        $this->actingAs($this->user);
        $response = $this->get(route('user.book-services.edit', $pendingBooking));
        $response->assertStatus(200);
        $response->assertSee('Edit Service Booking');

        // Act & Assert: Cannot access edit form for confirmed booking
        $response = $this->get(route('user.book-services.edit', $confirmedBooking));
        $response->assertStatus(403);
    }

    /**
     * Test: Store method creates admin notifications
     *
     * This test verifies that creating a new booking
     * generates notifications for admin users.
     */
    public function test_store_creates_admin_notifications()
    {
        // Arrange: Create admin users
        $admin1 = User::factory()->create(['account_type' => 'admin']);
        $admin2 = User::factory()->create(['account_type' => 'admin']);

        $bookingData = [
            'service_name' => 'House Cleaning',
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Main St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'extras' => 'Deep clean',
            'frequency' => 'Weekly',
            'square_feet' => '1500',
            'booking_date' => '2024-03-15',
            'booking_time' => '10:00 AM',
            'parking_info' => 'Driveway',
            'flexible_time' => 'Morning',
            'entrance_info' => 'Front door',
            'pets' => 'None',
            'special_instructions' => 'Test instructions'
        ];

        // Act: Create booking
        $this->actingAs($this->user);
        $response = $this->post(route('user.book-services.store'), $bookingData);

        // Assert: Notifications created for admin users
        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin1->id,
            'title' => 'New Service Booking',
            'type' => 'service'
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin2->id,
            'title' => 'New Service Booking',
            'type' => 'service'
        ]);
    }
}
