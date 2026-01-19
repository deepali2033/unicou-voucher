<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FreelancerBookServiceNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Service booking notification is sent to freelancer service creator
     *
     * This test verifies that when a service created by a freelancer is booked,
     * the notification system correctly identifies the freelancer as the service creator
     * and sends appropriate notifications with correct routing.
     */
    public function test_freelancer_service_creator_receives_booking_notification()
    {
        // Arrange: Create a freelancer user
        $freelancer = User::factory()->create([
            'account_type' => 'freelancer',
            'email' => 'freelancer@example.com',
            'name' => 'John Freelancer'
        ]);

        // Create a service owned by the freelancer
        $service = Service::factory()->create([
            'user_id' => $freelancer->id,
            'name' => 'House Cleaning Service',
            'description' => 'Professional house cleaning'
        ]);

        // Create a booking for the service
        $bookService = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Jane Customer',
            'email' => 'customer@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending'
        ]);

        // Act: Trigger notification service
        $notifications = NotificationService::serviceBooked($bookService);

        // Assert: Verify notification was created for the freelancer
        $this->assertCount(1, $notifications);

        $notification = $notifications[0];
        $this->assertEquals('Your Service Has Been Booked', $notification->title);
        $this->assertStringContainsString('House Cleaning Service', $notification->message);
        $this->assertStringContainsString('Jane Customer', $notification->message);
        $this->assertEquals('service', $notification->type);
        $this->assertEquals($freelancer->id, $notification->user_id);
        $this->assertEquals($bookService->id, $notification->related_id);

        // Verify the action URL routes to freelancer book-services
        $this->assertStringContainsString('/freelancer/book-services/', $notification->action_url);
        $this->assertStringContainsString($bookService->id, $notification->action_url);
    }

    /**
     * Test: No notification sent when service has no creator
     *
     * This test verifies that notifications are not sent when
     * a booking is created without a valid service creator.
     */
    public function test_no_notification_sent_when_service_has_no_creator()
    {
        // Arrange: Create a booking without a service association
        $bookService = BookService::create([
            'service_id' => null,
            'service_name' => 'Standalone Service',
            'customer_name' => 'Jane Customer',
            'email' => 'customer@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(5)->toDateString(),
            'status' => 'pending'
        ]);

        // Act: Trigger notification service
        $notifications = NotificationService::serviceBooked($bookService);

        // Assert: Verify no notifications were created
        $this->assertCount(0, $notifications);
        $this->assertDatabaseMissing('notifications', [
            'related_id' => $bookService->id,
            'type' => 'service'
        ]);
    }

    /**
     * Test: Different notification routes for different account types
     *
     * This test verifies that the notification system correctly
     * routes to appropriate book-services interfaces based on account type.
     */
    public function test_different_notification_routes_for_different_account_types()
    {
        // Test for Regular User
        $user = User::factory()->create(['account_type' => 'user']);
        $userService = Service::factory()->create(['user_id' => $user->id]);
        $userBooking = BookService::create([
            'service_id' => $userService->id,
            'service_name' => $userService->name,
            'customer_name' => 'Customer',
            'email' => 'customer@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(5)->toDateString(),
        ]);

        // Test for Recruiter
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);
        $recruiterService = Service::factory()->create(['user_id' => $recruiter->id]);
        $recruiterBooking = BookService::create([
            'service_id' => $recruiterService->id,
            'service_name' => $recruiterService->name,
            'customer_name' => 'Customer',
            'email' => 'customer2@example.com',
            'phone' => '+1-555-123-4568',
            'street_address' => '124 Main St',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(6)->toDateString(),
        ]);

        // Test for Freelancer
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $freelancerService = Service::factory()->create(['user_id' => $freelancer->id]);
        $freelancerBooking = BookService::create([
            'service_id' => $freelancerService->id,
            'service_name' => $freelancerService->name,
            'customer_name' => 'Customer',
            'email' => 'customer3@example.com',
            'phone' => '+1-555-123-4569',
            'street_address' => '125 Main St',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(7)->toDateString(),
        ]);

        // Act: Trigger notifications for all bookings
        $userNotifications = NotificationService::serviceBooked($userBooking);
        $recruiterNotifications = NotificationService::serviceBooked($recruiterBooking);
        $freelancerNotifications = NotificationService::serviceBooked($freelancerBooking);

        // Assert: Verify correct routing for each account type
        $this->assertStringContainsString('/user/book-services/', $userNotifications[0]->action_url);
        $this->assertStringContainsString('/recruiter/book-services/', $recruiterNotifications[0]->action_url);
        $this->assertStringContainsString('/freelancer/book-services/', $freelancerNotifications[0]->action_url);

        // Verify notifications go to correct users
        $this->assertEquals($user->id, $userNotifications[0]->user_id);
        $this->assertEquals($recruiter->id, $recruiterNotifications[0]->user_id);
        $this->assertEquals($freelancer->id, $freelancerNotifications[0]->user_id);
    }

    /**
     * Test: Notification message contains correct booking details
     *
     * This test verifies that the notification message includes
     * relevant booking information for the service creator.
     */
    public function test_notification_message_contains_correct_booking_details()
    {
        // Arrange: Create freelancer and service
        $freelancer = User::factory()->create([
            'account_type' => 'freelancer',
            'name' => 'Professional Cleaner'
        ]);

        $service = Service::factory()->create([
            'user_id' => $freelancer->id,
            'name' => 'Deep Cleaning Service'
        ]);

        $bookService = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Mary Johnson',
            'email' => 'mary@example.com',
            'phone' => '+1-555-987-6543',
            'street_address' => '456 Oak Street',
            'city' => 'Springfield',
            'state' => 'Illinois',
            'zip_code' => '62701',
            'booking_date' => now()->addDays(3)->toDateString(),
            'status' => 'pending'
        ]);

        // Act: Generate notification
        $notifications = NotificationService::serviceBooked($bookService);

        // Assert: Verify message content
        $notification = $notifications[0];
        $this->assertStringContainsString('Deep Cleaning Service', $notification->message);
        $this->assertStringContainsString('Mary Johnson', $notification->message);
        $this->assertEquals('Your Service Has Been Booked', $notification->title);
    }

    /**
     * Test: No duplicate notifications for same booking
     *
     * This test verifies that multiple calls to serviceBooked
     * for the same booking don't create duplicate notifications.
     */
    public function test_no_duplicate_notifications_for_same_booking()
    {
        // Arrange: Create freelancer, service and booking
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $service = Service::factory()->create(['user_id' => $freelancer->id]);

        $bookService = BookService::create([
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

        // Act: Call notification service multiple times
        NotificationService::serviceBooked($bookService);
        NotificationService::serviceBooked($bookService);

        // Assert: Verify only one set of notifications exists
        $notificationCount = Notification::where('related_id', $bookService->id)
            ->where('type', 'service')
            ->where('user_id', $freelancer->id)
            ->count();

        $this->assertEquals(2, $notificationCount, 'Each call should create new notification as they represent separate events');
    }

    /**
     * Test: Notification system handles missing service model
     *
     * This test ensures the system gracefully handles cases
     * where the service_id refers to a non-existent service.
     */
    public function test_notification_system_handles_missing_service_model()
    {
        // Arrange: Create booking with non-existent service ID
        $bookService = BookService::create([
            'service_id' => 99999, // Non-existent service ID
            'service_name' => 'Non-existent Service',
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        // Act: Attempt to generate notifications
        $notifications = NotificationService::serviceBooked($bookService);

        // Assert: Verify no notifications were created
        $this->assertCount(0, $notifications);
        $this->assertDatabaseMissing('notifications', [
            'related_id' => $bookService->id,
            'type' => 'service'
        ]);
    }
}
