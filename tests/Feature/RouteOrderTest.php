<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that delete-all-read routes work correctly for all user types
     * This verifies that the route order fix is working.
     *
     * @test
     */
    public function delete_all_read_routes_work_for_all_user_types()
    {
        // Test for recruiter
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);
        Notification::factory()->create(['user_id' => $recruiter->id, 'is_read' => true]);

        $this->actingAs($recruiter);
        $response = $this->delete('/recruiter/notifications/delete-all-read');
        $response->assertStatus(302); // Should redirect, not 404

        // Test for freelancer
        $freelancer = User::factory()->create(['account_type' => 'freelancer']);
        Notification::factory()->create(['user_id' => $freelancer->id, 'is_read' => true]);

        $this->actingAs($freelancer);
        $response = $this->delete('/freelancer/notifications/delete-all-read');
        $response->assertStatus(302); // Should redirect, not 404

        // Test for user
        $user = User::factory()->create(['account_type' => 'user']);
        Notification::factory()->create(['user_id' => $user->id, 'is_read' => true]);

        $this->actingAs($user);
        $response = $this->delete('/user/notifications/delete-all-read');
        $response->assertStatus(302); // Should redirect, not 404

        // Test for admin
        $admin = User::factory()->create(['account_type' => 'admin']);
        Notification::factory()->create(['user_id' => $admin->id, 'is_read' => true]);

        $this->actingAs($admin);
        $response = $this->delete('/admin/notifications/delete-all-read');
        $response->assertStatus(302); // Should redirect, not 404
    }

    /**
     * Test that specific notification deletion still works
     * This verifies that our route order change didn't break existing functionality.
     *
     * @test
     */
    public function specific_notification_deletion_still_works()
    {
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);
        $notification = Notification::factory()->create(['user_id' => $recruiter->id]);

        $this->actingAs($recruiter);
        $response = $this->delete("/recruiter/notifications/{$notification->id}");
        $response->assertStatus(302); // Should redirect, not 404

        // Verify notification was actually deleted
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }
}
