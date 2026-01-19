<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Models\Notification;

class BookServiceNotificationTest extends TestCase
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
     * Get valid booking form data for testing
     */
    private function getValidBookingData($serviceName = 'Test Service')
    {
        return [
            'form_fields' => [
                'service_booking_form' => $serviceName,
                'bedrooms_booking_form' => 3,
                'bathrooms_booking_form' => 2,
                'extras_booking_form' => 'Deep clean kitchen',
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
    }

    /**
     * Test: Booking notifies service creator user
     *
     * This test verifies that when a service is booked and the service was created by a user,
     * the notification goes to that user's dashboard with the correct route.
     */
    public function test_booking_notifies_service_creator_user()
    {
        // Arrange: Create a regular user and a service created by them
        $serviceCreatorUser = User::factory()->create([
            'name' => 'Service Creator User',
            'account_type' => 'user',
        ]);

        $service = Service::factory()->create([
            'name' => 'Regular House Cleaning',
            'user_id' => $serviceCreatorUser->id,
            'is_active' => true,
        ]);

        $formData = $this->getValidBookingData('Regular House Cleaning');

        // Act: Submit the booking form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify the service creator received a notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $serviceCreatorUser->id,
            'title' => 'New Booking for Your Service',
            'type' => 'service',
            'is_read' => false,
        ]);

        $notification = Notification::where('user_id', $serviceCreatorUser->id)
            ->where('title', 'New Booking for Your Service')
            ->first();

        $this->assertStringContainsString('John Doe', $notification->description);
        $this->assertStringContainsString('Regular House Cleaning', $notification->description);
        $this->assertStringContainsString('your service:', $notification->description);

        // Verify the action route is for user dashboard
        $this->assertStringContainsString('/user/book-services/', $notification->action);

        $response->assertStatus(302);
    }

    /**
     * Test: Booking notifies service creator recruiter
     *
     * This test verifies that when a service is booked and the service was created by a recruiter,
     * the notification goes to that recruiter's dashboard with the correct route.
     */
    public function test_booking_notifies_service_creator_recruiter()
    {
        // Arrange: Create a recruiter and a service created by them
        $serviceCreatorRecruiter = User::factory()->create([
            'name' => 'Service Creator Recruiter',
            'account_type' => 'recruiter',
        ]);

        $service = Service::factory()->create([
            'name' => 'Office Cleaning Service',
            'user_id' => $serviceCreatorRecruiter->id,
            'is_active' => true,
        ]);

        $formData = $this->getValidBookingData('Office Cleaning Service');

        // Act: Submit the booking form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify the service creator received a notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $serviceCreatorRecruiter->id,
            'title' => 'New Booking for Your Service',
            'type' => 'service',
            'is_read' => false,
        ]);

        $notification = Notification::where('user_id', $serviceCreatorRecruiter->id)
            ->where('title', 'New Booking for Your Service')
            ->first();

        $this->assertStringContainsString('John Doe', $notification->description);
        $this->assertStringContainsString('Office Cleaning Service', $notification->description);

        // Verify the action route is for recruiter dashboard
        $this->assertStringContainsString('/recruiter/book-services/', $notification->action);

        $response->assertStatus(302);
    }

    /**
     * Test: Admin always receives notifications
     *
     * This test verifies that all admin users receive notifications for every booking,
     * regardless of who created the service.
     */
    public function test_admin_always_receives_notifications()
    {
        // Arrange: Create a service creator user and a service
        $serviceCreatorUser = User::factory()->create([
            'name' => 'Regular User',
            'account_type' => 'user',
        ]);

        $service = Service::factory()->create([
            'name' => 'Deep Cleaning Service',
            'user_id' => $serviceCreatorUser->id,
            'is_active' => true,
        ]);

        $admins = User::where('account_type', 'admin')->get();
        $this->assertCount(2, $admins); // Verify we have 2 admins from setup

        $formData = $this->getValidBookingData('Deep Cleaning Service');

        // Act: Submit the booking form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify all admins received notifications
        foreach ($admins as $admin) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $admin->id,
                'title' => 'New Service Booking',
                'type' => 'service',
                'is_read' => false,
            ]);

            $adminNotification = Notification::where('user_id', $admin->id)
                ->where('title', 'New Service Booking')
                ->first();

            $this->assertStringContainsString('John Doe', $adminNotification->description);
            $this->assertStringContainsString('Deep Cleaning Service', $adminNotification->description);
            $this->assertStringContainsString('/admin/book-services/', $adminNotification->action);
        }

        // Also verify the service creator received their notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $serviceCreatorUser->id,
            'title' => 'New Booking for Your Service',
            'type' => 'service',
            'is_read' => false,
        ]);

        $response->assertStatus(302);
    }

    /**
     * Test: Service without creator only notifies admins
     *
     * This test verifies that when a service exists but has no creator (user_id is null),
     * only admin users receive notifications.
     */
    public function test_service_without_creator_only_notifies_admins()
    {
        // Arrange: Create a service without a creator (user_id is null)
        $service = Service::factory()->create([
            'name' => 'System Service',
            'user_id' => null, // No creator
            'is_active' => true,
        ]);

        $admins = User::where('account_type', 'admin')->get();
        $this->assertCount(2, $admins);

        $formData = $this->getValidBookingData('System Service');

        // Act: Submit the booking form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify only admins received notifications
        foreach ($admins as $admin) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $admin->id,
                'title' => 'New Service Booking',
                'type' => 'service',
                'is_read' => false,
            ]);
        }

        // Verify no notifications with "New Booking for Your Service" title exist
        $creatorNotifications = Notification::where('title', 'New Booking for Your Service')->get();
        $this->assertCount(0, $creatorNotifications);

        // Total notifications should be 2 (one for each admin)
        $totalNotifications = Notification::count();
        $this->assertEquals(2, $totalNotifications);

        $response->assertStatus(302);
    }

    /**
     * Test: Nonexistent service only notifies admins
     *
     * This test verifies that when a booking is made for a service that doesn't exist in the database,
     * only admin users receive notifications.
     */
    public function test_nonexistent_service_only_notifies_admins()
    {
        // Arrange: Use a service name that doesn't exist in the database
        $formData = $this->getValidBookingData('Non-existent Service');

        $admins = User::where('account_type', 'admin')->get();
        $this->assertCount(2, $admins);

        // Verify the service doesn't exist
        $this->assertDatabaseMissing('services', [
            'name' => 'Non-existent Service'
        ]);

        // Act: Submit the booking form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify only admins received notifications
        foreach ($admins as $admin) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $admin->id,
                'title' => 'New Service Booking',
                'type' => 'service',
                'is_read' => false,
            ]);
        }

        // Verify no notifications with "New Booking for Your Service" title exist
        $creatorNotifications = Notification::where('title', 'New Booking for Your Service')->get();
        $this->assertCount(0, $creatorNotifications);

        // Total notifications should be 2 (one for each admin)
        $totalNotifications = Notification::count();
        $this->assertEquals(2, $totalNotifications);

        $response->assertStatus(302);
    }

    /**
     * Test: Service creator routes based on account type
     *
     * This test verifies that the notification action routes are correctly set
     * based on the service creator's account type.
     */
    public function test_service_creator_routes_based_on_account_type()
    {
        // Test with different account types
        $testCases = [
            'user' => '/user/book-services/',
            'recruiter' => '/recruiter/book-services/',
            'freelancer' => '/admin/book-services/', // Fallback to admin
        ];

        foreach ($testCases as $accountType => $expectedRoute) {
            // Clear notifications from previous iterations
            Notification::truncate();

            // Arrange: Create a service creator with specific account type
            $serviceCreator = User::factory()->create([
                'name' => "Service Creator {$accountType}",
                'account_type' => $accountType,
            ]);

            $service = Service::factory()->create([
                'name' => "Service for {$accountType}",
                'user_id' => $serviceCreator->id,
                'is_active' => true,
            ]);

            $formData = $this->getValidBookingData("Service for {$accountType}");

            // Act: Submit the booking form
            $response = $this->post(route('book-services.store'), $formData);

            // Assert: Verify the service creator's notification has the correct route
            $creatorNotification = Notification::where('user_id', $serviceCreator->id)
                ->where('title', 'New Booking for Your Service')
                ->first();

            $this->assertNotNull($creatorNotification, "Creator notification should exist for account type: {$accountType}");
            $this->assertStringContainsString($expectedRoute, $creatorNotification->action, "Route should match expected for account type: {$accountType}");
        }
    }

    /**
     * Test: Both admin and creator get notifications
     *
     * This test verifies that when a service is booked, both the admin users and
     * the service creator receive separate notifications with appropriate content.
     */
    public function test_both_admin_and_creator_get_notifications()
    {
        // Arrange: Create a service creator and a service
        $serviceCreator = User::factory()->create([
            'name' => 'Service Owner',
            'account_type' => 'user',
        ]);

        $service = Service::factory()->create([
            'name' => 'Premium Cleaning Service',
            'user_id' => $serviceCreator->id,
            'is_active' => true,
        ]);

        $admins = User::where('account_type', 'admin')->get();
        $this->assertCount(2, $admins);

        $formData = $this->getValidBookingData('Premium Cleaning Service');

        // Act: Submit the booking form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify admin notifications
        foreach ($admins as $admin) {
            $adminNotification = Notification::where('user_id', $admin->id)
                ->where('title', 'New Service Booking')
                ->first();

            $this->assertNotNull($adminNotification);
            $this->assertEquals('service', $adminNotification->type);
            $this->assertFalse($adminNotification->is_read);
            $this->assertStringContainsString('New booking from John Doe for Premium Cleaning Service', $adminNotification->description);
            $this->assertStringContainsString('/admin/book-services/', $adminNotification->action);
        }

        // Assert: Verify service creator notification
        $creatorNotification = Notification::where('user_id', $serviceCreator->id)
            ->where('title', 'New Booking for Your Service')
            ->first();

        $this->assertNotNull($creatorNotification);
        $this->assertEquals('service', $creatorNotification->type);
        $this->assertFalse($creatorNotification->is_read);
        $this->assertStringContainsString('New booking from John Doe for your service: Premium Cleaning Service', $creatorNotification->description);
        $this->assertStringContainsString('/user/book-services/', $creatorNotification->action);

        // Verify total notification count (2 admins + 1 creator = 3)
        $totalNotifications = Notification::count();
        $this->assertEquals(3, $totalNotifications);

        $response->assertStatus(302);
    }

    /**
     * Test: Service creator does not exist
     *
     * This test verifies that when a service has a user_id that references a deleted/non-existent user,
     * the system handles it gracefully and only notifies admins.
     */
    public function test_service_creator_does_not_exist()
    {
        // Arrange: Create a service with a non-existent user_id
        $service = Service::factory()->create([
            'name' => 'Orphaned Service',
            'user_id' => 99999, // Non-existent user ID
            'is_active' => true,
        ]);

        // Verify the user doesn't exist
        $this->assertNull(User::find(99999));

        $admins = User::where('account_type', 'admin')->get();
        $this->assertCount(2, $admins);

        $formData = $this->getValidBookingData('Orphaned Service');

        // Act: Submit the booking form
        $response = $this->post(route('book-services.store'), $formData);

        // Assert: Verify only admins received notifications
        foreach ($admins as $admin) {
            $this->assertDatabaseHas('notifications', [
                'user_id' => $admin->id,
                'title' => 'New Service Booking',
                'type' => 'service',
                'is_read' => false,
            ]);
        }

        // Verify no creator notifications exist
        $creatorNotifications = Notification::where('title', 'New Booking for Your Service')->get();
        $this->assertCount(0, $creatorNotifications);

        // Total notifications should be 2 (one for each admin)
        $totalNotifications = Notification::count();
        $this->assertEquals(2, $totalNotifications);

        $response->assertStatus(302);
    }
}
