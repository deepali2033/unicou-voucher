<?php

namespace Tests\Unit;

use App\Models\BookService;
use App\Models\Service;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceBookingNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_booking_creates_notifications_for_service_creator_and_admins()
    {
        // Create a service creator (freelancer)
        $serviceCreator = User::factory()->create([
            'account_type' => 'freelancer',
            'name' => 'John Doe',
        ]);

        // Create admin users
        $admin1 = User::factory()->create(['account_type' => 'admin']);
        $admin2 = User::factory()->create(['account_type' => 'admin']);

        // Create a service by the creator
        $service = Service::create([
            'user_id' => $serviceCreator->id,
            'name' => 'House Cleaning',
            'slug' => 'house-cleaning',
            'short_description' => 'Professional house cleaning service',
            'description' => 'We provide comprehensive house cleaning services',
            'price_from' => 50.00,
            'price_to' => 100.00,
            'is_active' => true,
        ]);

        // Create a booking for this service
        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Jane Customer',
            'email' => 'jane@example.com',
            'phone' => '123-456-7890',
            'street_address' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'zip_code' => '12345',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'extras' => 'None',
            'frequency' => 'weekly',
            'square_feet' => '1200',
            'booking_date' => '2024-01-15',
            'booking_time' => '10:00 AM',
            'status' => 'pending',
        ]);

        // Call the notification service
        $notifications = NotificationService::serviceBooked($booking);

        // Verify notifications were created
        $this->assertNotEmpty($notifications);

        // Check that notifications were created in the database
        $this->assertEquals(3, Notification::count()); // 2 admins + 1 service creator

        // Verify admin notifications
        $adminNotifications = Notification::whereIn('user_id', [$admin1->id, $admin2->id])->get();
        $this->assertEquals(2, $adminNotifications->count());

        foreach ($adminNotifications as $notification) {
            $this->assertEquals('New Service Booking', $notification->title);
            $this->assertStringContains('New booking from Jane Customer for House Cleaning', $notification->description);
            $this->assertEquals('service', $notification->type);
            $this->assertEquals($booking->id, $notification->related_id);
            $this->assertStringContains('admin.book-services.show', $notification->action);
        }

        // Verify service creator notification
        $creatorNotification = Notification::where('user_id', $serviceCreator->id)->first();
        $this->assertNotNull($creatorNotification);
        $this->assertEquals('Your Service Has Been Booked', $creatorNotification->title);
        $this->assertStringContains("Your service 'House Cleaning' has been booked by Jane Customer", $creatorNotification->description);
        $this->assertEquals('service', $creatorNotification->type);
        $this->assertEquals($booking->id, $creatorNotification->related_id);
        $this->assertStringContains('freelancer.book-services.show', $creatorNotification->action);
    }

    public function test_service_booking_with_user_account_type_creator()
    {
        // Create a service creator with 'user' account type
        $serviceCreator = User::factory()->create([
            'account_type' => 'user',
            'name' => 'User Creator',
        ]);

        // Create an admin
        $admin = User::factory()->create(['account_type' => 'admin']);

        // Create a service by the user
        $service = Service::create([
            'user_id' => $serviceCreator->id,
            'name' => 'Garden Maintenance',
            'slug' => 'garden-maintenance',
            'short_description' => 'Garden maintenance service',
            'description' => 'Professional garden maintenance',
            'price_from' => 30.00,
            'is_active' => true,
        ]);

        // Create a booking for this service
        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Customer Name',
            'email' => 'customer@example.com',
            'phone' => '123-456-7890',
            'street_address' => '456 Oak St',
            'city' => 'Somewhere',
            'state' => 'NY',
            'zip_code' => '54321',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'status' => 'pending',
        ]);

        // Call the notification service
        NotificationService::serviceBooked($booking);

        // Verify service creator notification uses user route
        $creatorNotification = Notification::where('user_id', $serviceCreator->id)->first();
        $this->assertNotNull($creatorNotification);
        $this->assertStringContains('user.book-services.show', $creatorNotification->action);
    }

    public function test_service_booking_with_recruiter_account_type_creator()
    {
        // Create a service creator with 'recruiter' account type
        $serviceCreator = User::factory()->create([
            'account_type' => 'recruiter',
            'name' => 'Recruiter Creator',
        ]);

        // Create an admin
        $admin = User::factory()->create(['account_type' => 'admin']);

        // Create a service by the recruiter
        $service = Service::create([
            'user_id' => $serviceCreator->id,
            'name' => 'Consulting Service',
            'slug' => 'consulting-service',
            'short_description' => 'Business consulting',
            'description' => 'Professional business consulting',
            'price_from' => 100.00,
            'is_active' => true,
        ]);

        // Create a booking for this service
        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Business Client',
            'email' => 'client@business.com',
            'phone' => '987-654-3210',
            'street_address' => '789 Business Ave',
            'city' => 'Corporate City',
            'state' => 'TX',
            'zip_code' => '78901',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'status' => 'pending',
        ]);

        // Call the notification service
        NotificationService::serviceBooked($booking);

        // Verify service creator notification uses recruiter route
        $creatorNotification = Notification::where('user_id', $serviceCreator->id)->first();
        $this->assertNotNull($creatorNotification);
        $this->assertStringContains('recruiter.book-services.show', $creatorNotification->action);
    }

    public function test_service_booking_without_service_id_only_notifies_admins()
    {
        // Create an admin
        $admin = User::factory()->create(['account_type' => 'admin']);

        // Create a booking without service_id (legacy booking or manual entry)
        $booking = BookService::create([
            'service_id' => null,
            'service_name' => 'Custom Service',
            'customer_name' => 'Walk-in Customer',
            'email' => 'walkin@example.com',
            'phone' => '555-123-4567',
            'street_address' => '999 Walk St',
            'city' => 'Walktown',
            'state' => 'FL',
            'zip_code' => '33333',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'status' => 'pending',
        ]);

        // Call the notification service
        NotificationService::serviceBooked($booking);

        // Verify only admin notification was created
        $this->assertEquals(1, Notification::count());

        $adminNotification = Notification::where('user_id', $admin->id)->first();
        $this->assertNotNull($adminNotification);
        $this->assertEquals('New Service Booking', $adminNotification->title);
    }

    public function test_service_booking_with_nonexistent_service_id_only_notifies_admins()
    {
        // Create an admin
        $admin = User::factory()->create(['account_type' => 'admin']);

        // Create a booking with non-existent service_id
        $booking = BookService::create([
            'service_id' => 999, // Non-existent service
            'service_name' => 'Non-existent Service',
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '555-999-8888',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'CA',
            'zip_code' => '12345',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'status' => 'pending',
        ]);

        // Call the notification service
        NotificationService::serviceBooked($booking);

        // Verify only admin notification was created
        $this->assertEquals(1, Notification::count());

        $adminNotification = Notification::where('user_id', $admin->id)->first();
        $this->assertNotNull($adminNotification);
        $this->assertEquals('New Service Booking', $adminNotification->title);
    }
}
