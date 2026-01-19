<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Models\Notification;
use App\Services\NotificationService;

class UserBookServiceNotificationViewTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_type' => 'user',
            'name' => 'Test User',
            'email' => 'testuser@example.com'
        ]);

        $this->notificationService = app(NotificationService::class);
    }

    /**
     * Test: User receives booking confirmation notification
     *
     * Verifies that when a user creates a booking, they receive a confirmation
     * notification that appears in their notification view.
     */
    public function test_user_receives_booking_confirmation_notification()
    {
        // Arrange: Create a booking for the user
        $bookService = BookService::factory()->create([
            'user_id' => $this->user->id,
            'customer_name' => $this->user->name,
            'email' => $this->user->email,
            'service_name' => 'House Cleaning',
            'status' => 'pending'
        ]);

        // Act: Call the notification service to create booking confirmation
        $this->notificationService->bookingCreated($bookService);

        // Authenticate as the user and visit notifications page
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Check the response and database
        $response->assertStatus(200);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => 'book_service',
            'title' => 'Booking Confirmation',
            'is_read' => false
        ]);

        // Verify notification appears in the view
        $response->assertSee('Booking Confirmation');
        $response->assertSee('House Cleaning');
        $response->assertSee('calendar-check'); // Icon should be visible
        $response->assertSee('New'); // Badge for unread notification
    }

    /**
     * Test: Status change notifications appear correctly
     *
     * Verifies that when booking status changes, users receive proper notifications
     * that display correctly in the notification view.
     */
    public function test_status_change_notifications_appear_correctly()
    {
        // Arrange: Create a booking
        $bookService = BookService::factory()->create([
            'user_id' => $this->user->id,
            'customer_name' => $this->user->name,
            'email' => $this->user->email,
            'service_name' => 'Deep Cleaning',
            'status' => 'pending'
        ]);

        // Act: Change status from pending to confirmed
        $oldStatus = 'pending';
        $newStatus = 'confirmed';

        $this->notificationService->bookingStatusChanged($bookService, $oldStatus, $newStatus);

        // Authenticate and visit notifications
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Check notification appears correctly
        $response->assertStatus(200);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => 'book_service',
            'title' => 'Booking Status Updated',
            'is_read' => false
        ]);

        $response->assertSee('Booking Status Updated');
        $response->assertSee('Deep Cleaning');
        $response->assertSee('confirmed'); // New status should be visible
        $response->assertSee('calendar-check'); // Icon class
    }

    /**
     * Test: Book-service notifications display proper icons
     *
     * Verifies that book-service type notifications show the correct
     * calendar-check icons in the notification list.
     */
    public function test_book_service_notifications_display_proper_icons()
    {
        // Arrange: Create notifications with book_service type
        $notification1 = Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'book_service',
            'title' => 'Test Booking Notification',
            'description' => 'This is a book service notification',
            'is_read' => false
        ]);

        $notification2 = Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'booking',
            'title' => 'Another Booking Notification',
            'description' => 'This is another booking notification',
            'is_read' => false
        ]);

        // Act: Visit notifications page
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Check that proper icons are displayed
        $response->assertStatus(200);
        $response->assertSee('fas fa-calendar-check'); // Icon for book_service type
        $response->assertSee('Test Booking Notification');
        $response->assertSee('Another Booking Notification');

        // Verify notification count
        $response->assertSee('2 Notifications'); // Total count
    }

    /**
     * Test: Book-service notifications show correct type
     *
     * Verifies that book-service notifications are properly categorized
     * and display with the correct styling and badge colors.
     */
    public function test_book_service_notifications_show_correct_type()
    {
        // Arrange: Create book_service notification
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'book_service',
            'title' => 'Service Booking Update',
            'description' => 'Your booking has been updated',
            'is_read' => false
        ]);

        // Act: Visit notifications page
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Check notification type and styling
        $response->assertStatus(200);
        $response->assertSee('Service Booking Update');

        // Check that notification model methods work correctly
        $this->assertEquals('fas fa-calendar-check', $notification->icon);
        $this->assertEquals('bg-warning', $notification->badge_color);

        // Verify badge color is applied in view
        $response->assertSee('bg-warning bg-opacity-10'); // Badge background
        $response->assertSee('text-warning'); // Text color
    }

    /**
     * Test: User notification view filters correctly
     *
     * Verifies that the notification view only shows notifications
     * belonging to the authenticated user.
     */
    public function test_user_notification_view_filters_correctly()
    {
        // Arrange: Create notifications for different users
        $otherUser = User::factory()->create(['account_type' => 'user']);

        // Notification for current user
        $userNotification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'book_service',
            'title' => 'User Booking Notification',
            'description' => 'This belongs to the user',
            'is_read' => false
        ]);

        // Notification for other user
        $otherNotification = Notification::factory()->create([
            'user_id' => $otherUser->id,
            'type' => 'book_service',
            'title' => 'Other User Notification',
            'description' => 'This belongs to someone else',
            'is_read' => false
        ]);

        // Act: Visit notifications page as current user
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Only current user's notifications are shown
        $response->assertStatus(200);
        $response->assertSee('User Booking Notification');
        $response->assertDontSee('Other User Notification');

        // Verify counts
        $response->assertSee('1 Notification'); // Only one for current user
    }

    /**
     * Test: Missing notification type handling
     *
     * Verifies that notifications with missing or unknown types
     * are handled gracefully without breaking the view.
     */
    public function test_missing_notification_type_handling()
    {
        // Arrange: Create notification with unknown type
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'unknown_type',
            'title' => 'Unknown Type Notification',
            'description' => 'This has an unknown type',
            'is_read' => false
        ]);

        // Act: Visit notifications page
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Page loads without errors
        $response->assertStatus(200);
        $response->assertSee('Unknown Type Notification');

        // Check that fallback icon/badge are used
        $this->assertEquals('fas fa-bell', $notification->icon); // Default icon
        $this->assertEquals('bg-secondary', $notification->badge_color); // Default badge
    }

    /**
     * Test: Empty notification list displays correctly
     *
     * Verifies that when a user has no notifications, the empty state
     * is displayed properly with appropriate messaging.
     */
    public function test_empty_notification_list_displays_correctly()
    {
        // Act: Visit notifications page with no notifications
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Empty state is displayed
        $response->assertStatus(200);
        $response->assertSee('No notifications found');
        $response->assertSee('You\'ll see notifications here when you create or update services and jobs.');
        $response->assertSee('0 Notifications'); // Count should be zero
        $response->assertSee('fas fa-bell-slash'); // Empty state icon
    }

    /**
     * Test: Notification pagination works properly
     *
     * Verifies that notification pagination works correctly when there
     * are many notifications.
     */
    public function test_notification_pagination_works_properly()
    {
        // Arrange: Create 25 notifications (more than the default 20 per page)
        Notification::factory()->count(25)->create([
            'user_id' => $this->user->id,
            'type' => 'book_service'
        ]);

        // Act: Visit first page
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: Check pagination
        $response->assertStatus(200);
        $response->assertSee('25 Notifications'); // Total count

        // Should see pagination links since we have more than 20 notifications
        $response->assertSeeText('Next'); // Pagination next link should exist

        // Visit second page
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index', ['page' => 2]));

        $response->assertStatus(200);
        $response->assertSeeText('Previous'); // Previous link should exist on page 2
    }

    /**
     * Test: Book-service notification creation through controller
     *
     * Integration test to verify that when booking status is updated through
     * the controller, notifications are created and appear correctly.
     */
    public function test_book_service_notification_creation_through_controller()
    {
        // Arrange: Create a booking
        $bookService = BookService::factory()->create([
            'user_id' => $this->user->id,
            'customer_name' => $this->user->name,
            'email' => $this->user->email,
            'service_name' => 'Kitchen Cleaning',
            'status' => 'pending'
        ]);

        // Act: Update status through controller
        $response = $this->actingAs($this->user)
            ->patch(route('user.book-services.update-status', $bookService), [
                'status' => 'confirmed'
            ]);

        // Assert: Status update succeeds
        $response->assertRedirect();

        // Check notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->user->id,
            'type' => 'book_service',
            'title' => 'Booking Status Updated'
        ]);

        // Visit notifications page to verify it appears
        $notificationResponse = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        $notificationResponse->assertStatus(200);
        $notificationResponse->assertSee('Booking Status Updated');
        $notificationResponse->assertSee('Kitchen Cleaning');
        $notificationResponse->assertSee('confirmed');
    }

    /**
     * Test: Multiple book-service notifications display correctly
     *
     * Verifies that when a user has multiple book-service related notifications,
     * they all display correctly with proper ordering and styling.
     */
    public function test_multiple_book_service_notifications_display_correctly()
    {
        // Arrange: Create multiple notifications
        $notifications = [
            [
                'type' => 'book_service',
                'title' => 'Booking Confirmation',
                'description' => 'Your house cleaning booking has been confirmed',
                'created_at' => now()->subHours(1)
            ],
            [
                'type' => 'book_service',
                'title' => 'Booking Status Updated',
                'description' => 'Your office cleaning is now in progress',
                'created_at' => now()->subMinutes(30)
            ],
            [
                'type' => 'booking',
                'title' => 'Booking Completed',
                'description' => 'Your deep cleaning service has been completed',
                'created_at' => now()->subMinutes(10)
            ]
        ];

        foreach ($notifications as $data) {
            Notification::factory()->create(array_merge([
                'user_id' => $this->user->id,
                'is_read' => false
            ], $data));
        }

        // Act: Visit notifications page
        $response = $this->actingAs($this->user)
            ->get(route('user.notifications.index'));

        // Assert: All notifications appear
        $response->assertStatus(200);
        $response->assertSee('Booking Confirmation');
        $response->assertSee('Booking Status Updated');
        $response->assertSee('Booking Completed');

        // Check proper icons for each type
        $response->assertSee('fas fa-calendar-check'); // book_service and booking icons

        // Verify count
        $response->assertSee('3 Notifications');
        $response->assertSee('3'); // Unread count
    }
}
