<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompleteFreelancerNotificationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Complete freelancer service booking notification workflow
     *
     * This test verifies the complete end-to-end workflow:
     * 1. Service booking is created
     * 2. System identifies freelancer service creator
     * 3. Notification is created with correct routing
     * 4. Freelancer can access and manage the booking
     */
    public function test_complete_freelancer_service_booking_notification_workflow()
    {
        // Arrange: Create freelancer and their service
        $freelancer = User::factory()->create([
            'account_type' => 'freelancer',
            'name' => 'Professional Service Provider',
            'email' => 'freelancer@example.com'
        ]);

        $service = Service::factory()->create([
            'user_id' => $freelancer->id,
            'name' => 'Professional House Cleaning',
            'description' => 'High-quality house cleaning services'
        ]);

        // Step 1: Service gets booked by a customer
        $booking = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Sarah Thompson',
            'email' => 'sarah@example.com',
            'phone' => '+1-555-987-6543',
            'street_address' => '789 Maple Avenue',
            'city' => 'Portland',
            'state' => 'Oregon',
            'zip_code' => '97201',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'square_feet' => '1500',
            'frequency' => 'Weekly',
            'booking_date' => now()->addDays(2)->toDateString(),
            'booking_time' => '10:00:00',
            'special_instructions' => 'Please focus on kitchen and bathrooms',
            'status' => 'pending'
        ]);

        // Step 2: Notification system processes the booking
        $notifications = NotificationService::serviceBooked($booking);

        // Assert: Notification is created for the freelancer
        $this->assertCount(1, $notifications);

        $notification = $notifications[0];
        $this->assertEquals('Your Service Has Been Booked', $notification->title);
        $this->assertStringContainsString('Professional House Cleaning', $notification->message);
        $this->assertStringContainsString('Sarah Thompson', $notification->message);
        $this->assertEquals($freelancer->id, $notification->user_id);
        $this->assertEquals('service', $notification->type);
        $this->assertStringContainsString('/freelancer/book-services/', $notification->action_url);

        // Step 3: Freelancer can view their bookings list
        $indexResponse = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.index'));

        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Sarah Thompson');
        $indexResponse->assertSee('Professional House Cleaning');
        $indexResponse->assertSee('pending');

        // Step 4: Freelancer can view booking details
        $showResponse = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.show', $booking));

        $showResponse->assertStatus(200);
        $showResponse->assertSee('Sarah Thompson');
        $showResponse->assertSee('sarah@example.com');
        $showResponse->assertSee('789 Maple Avenue');
        $showResponse->assertSee('Portland');
        $showResponse->assertSee('Oregon');
        $showResponse->assertSee('kitchen and bathrooms');
        $showResponse->assertSee('Weekly');

        // Step 5: Freelancer can edit the booking
        $editResponse = $this->actingAs($freelancer)
            ->get(route('freelancer.book-services.edit', $booking));

        $editResponse->assertStatus(200);
        $editResponse->assertSee('Sarah Thompson');

        // Step 6: Freelancer can update booking status and price
        $updateResponse = $this->actingAs($freelancer)
            ->patch(route('freelancer.book-services.update', $booking), [
                'status' => 'confirmed',
                'price' => '125.00'
            ]);

        $updateResponse->assertStatus(302);
        $updateResponse->assertRedirect(route('freelancer.book-services.show', $booking));
        $updateResponse->assertSessionHas('success', 'Booking updated successfully!');

        // Verify database was updated
        $this->assertDatabaseHas('book_services', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'price' => '125.00'
        ]);

        // Step 7: Verify notification action URL works correctly
        $notificationActionUrl = $notification->action_url;
        $actionResponse = $this->actingAs($freelancer)
            ->get($notificationActionUrl);

        $actionResponse->assertStatus(200);
        $actionResponse->assertSee('Sarah Thompson');
    }

    /**
     * Test: System correctly handles multiple bookings for different freelancers
     *
     * This test ensures the system properly segregates bookings and notifications
     * between different freelancers.
     */
    public function test_system_correctly_handles_multiple_freelancer_bookings()
    {
        // Arrange: Create two freelancers with their own services
        $freelancer1 = User::factory()->create([
            'account_type' => 'freelancer',
            'name' => 'Freelancer One'
        ]);

        $freelancer2 = User::factory()->create([
            'account_type' => 'freelancer',
            'name' => 'Freelancer Two'
        ]);

        $service1 = Service::factory()->create([
            'user_id' => $freelancer1->id,
            'name' => 'Service One'
        ]);

        $service2 = Service::factory()->create([
            'user_id' => $freelancer2->id,
            'name' => 'Service Two'
        ]);

        // Create bookings for both services
        $booking1 = BookService::create([
            'service_id' => $service1->id,
            'service_name' => $service1->name,
            'customer_name' => 'Customer A',
            'email' => 'customerA@example.com',
            'phone' => '+1-555-111-1111',
            'street_address' => '111 First St',
            'city' => 'City One',
            'state' => 'State One',
            'zip_code' => '11111',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        $booking2 = BookService::create([
            'service_id' => $service2->id,
            'service_name' => $service2->name,
            'customer_name' => 'Customer B',
            'email' => 'customerB@example.com',
            'phone' => '+1-555-222-2222',
            'street_address' => '222 Second St',
            'city' => 'City Two',
            'state' => 'State Two',
            'zip_code' => '22222',
            'booking_date' => now()->addDays(2)->toDateString(),
        ]);

        // Generate notifications
        $notifications1 = NotificationService::serviceBooked($booking1);
        $notifications2 = NotificationService::serviceBooked($booking2);

        // Assert: Each freelancer gets their own notification
        $this->assertCount(1, $notifications1);
        $this->assertCount(1, $notifications2);

        $this->assertEquals($freelancer1->id, $notifications1[0]->user_id);
        $this->assertEquals($freelancer2->id, $notifications2[0]->user_id);

        // Assert: Freelancer 1 can only see their booking
        $response1 = $this->actingAs($freelancer1)
            ->get(route('freelancer.book-services.index'));

        $response1->assertSee('Customer A');
        $response1->assertDontSee('Customer B');

        // Assert: Freelancer 2 can only see their booking
        $response2 = $this->actingAs($freelancer2)
            ->get(route('freelancer.book-services.index'));

        $response2->assertSee('Customer B');
        $response2->assertDontSee('Customer A');

        // Assert: Each freelancer can only access their own booking details
        $this->actingAs($freelancer1)
            ->get(route('freelancer.book-services.show', $booking1))
            ->assertStatus(200);

        $this->actingAs($freelancer1)
            ->get(route('freelancer.book-services.show', $booking2))
            ->assertStatus(403);

        $this->actingAs($freelancer2)
            ->get(route('freelancer.book-services.show', $booking2))
            ->assertStatus(200);

        $this->actingAs($freelancer2)
            ->get(route('freelancer.book-services.show', $booking1))
            ->assertStatus(403);
    }

    /**
     * Test: Integration with existing notification system
     *
     * This test verifies that the freelancer notification enhancement
     * doesn't interfere with existing notification functionality.
     */
    public function test_integration_with_existing_notification_system()
    {
        // Arrange: Create users of different types with services
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        $regularUser = User::factory()->create(['account_type' => 'user']);
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);

        $freelancerService = Service::factory()->create(['user_id' => $freelancer->id]);
        $userService = Service::factory()->create(['user_id' => $regularUser->id]);
        $recruiterService = Service::factory()->create(['user_id' => $recruiter->id]);

        // Create bookings for each service type
        $freelancerBooking = BookService::create([
            'service_id' => $freelancerService->id,
            'service_name' => $freelancerService->name,
            'customer_name' => 'Customer F',
            'email' => 'customerF@example.com',
            'phone' => '+1-555-333-3333',
            'street_address' => '333 Third St',
            'city' => 'City F',
            'state' => 'State F',
            'zip_code' => '33333',
            'booking_date' => now()->addDays(1)->toDateString(),
        ]);

        $userBooking = BookService::create([
            'service_id' => $userService->id,
            'service_name' => $userService->name,
            'customer_name' => 'Customer U',
            'email' => 'customerU@example.com',
            'phone' => '+1-555-444-4444',
            'street_address' => '444 Fourth St',
            'city' => 'City U',
            'state' => 'State U',
            'zip_code' => '44444',
            'booking_date' => now()->addDays(2)->toDateString(),
        ]);

        $recruiterBooking = BookService::create([
            'service_id' => $recruiterService->id,
            'service_name' => $recruiterService->name,
            'customer_name' => 'Customer R',
            'email' => 'customerR@example.com',
            'phone' => '+1-555-555-5555',
            'street_address' => '555 Fifth St',
            'city' => 'City R',
            'state' => 'State R',
            'zip_code' => '55555',
            'booking_date' => now()->addDays(3)->toDateString(),
        ]);

        // Generate notifications for all bookings
        $freelancerNotifications = NotificationService::serviceBooked($freelancerBooking);
        $userNotifications = NotificationService::serviceBooked($userBooking);
        $recruiterNotifications = NotificationService::serviceBooked($recruiterBooking);

        // Assert: All notification types are generated
        $this->assertCount(1, $freelancerNotifications);
        $this->assertCount(1, $userNotifications);
        $this->assertCount(1, $recruiterNotifications);

        // Verify correct routing for each account type
        $this->assertStringContainsString('/freelancer/book-services/', $freelancerNotifications[0]->action_url);
        $this->assertStringContainsString('/user/book-services/', $userNotifications[0]->action_url);
        $this->assertStringContainsString('/recruiter/book-services/', $recruiterNotifications[0]->action_url);

        // Verify notifications go to correct users
        $this->assertEquals($freelancer->id, $freelancerNotifications[0]->user_id);
        $this->assertEquals($regularUser->id, $userNotifications[0]->user_id);
        $this->assertEquals($recruiter->id, $recruiterNotifications[0]->user_id);
    }
}
