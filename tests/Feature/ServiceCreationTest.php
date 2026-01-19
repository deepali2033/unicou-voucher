<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_recruiter_can_create_service_successfully()
    {
        // Create a recruiter user
        $recruiter = User::factory()->create([
            'account_type' => 'recruiter',
            'name' => 'Test Recruiter',
            'email' => 'recruiter@example.com'
        ]);

        // Act as the recruiter
        $this->actingAs($recruiter);

        // Service data (similar to what would be submitted from the form)
        $serviceData = [
            'name' => 'Test Cleaning Service',
            'slug' => 'test-cleaning-service',
            'short_description' => 'Professional cleaning service for your home',
            'description' => 'Comprehensive cleaning service that includes all rooms, deep cleaning, and sanitization.',
            'icon' => 'fas fa-broom',
            'price_from' => 50.00,
            'price_to' => 150.00,
            'duration' => '2-3 hours',
            'features' => ['Professional equipment', 'Eco-friendly products', 'Insured staff'],
            'includes' => ['All room cleaning', 'Bathroom sanitization', 'Trash removal'],
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
            'meta_title' => 'Professional Cleaning Service',
            'meta_description' => 'Get your home professionally cleaned with our comprehensive service'
        ];

        // Make POST request to create service
        $response = $this->post(route('recruiter.services.store'), $serviceData);

        // Assert successful redirect
        $response->assertStatus(302);
        $response->assertRedirect(route('recruiter.services.index'));
        $response->assertSessionHas('success', 'Service created successfully.');

        // Assert service was created in database
        $this->assertDatabaseHas('services', [
            'name' => 'Test Cleaning Service',
            'user_id' => $recruiter->id,
            'approval_status' => 'Pending',
            'servicetoggle' => 'recruiter'
        ]);

        // Assert the service exists and has correct attributes
        $service = Service::where('name', 'Test Cleaning Service')->first();
        $this->assertNotNull($service);
        $this->assertEquals($recruiter->id, $service->user_id);
        $this->assertEquals('test-cleaning-service', $service->slug);
        $this->assertEquals('Pending', $service->approval_status);
        $this->assertEquals('recruiter', $service->servicetoggle);
        $this->assertEquals(50.00, $service->price_from);
        $this->assertEquals(150.00, $service->price_to);
        $this->assertTrue($service->is_active);
        $this->assertFalse($service->is_featured);
    }

    public function test_service_creation_validates_required_fields()
    {
        // Create a recruiter user
        $recruiter = User::factory()->create([
            'account_type' => 'recruiter'
        ]);

        $this->actingAs($recruiter);

        // Submit empty data
        $response = $this->post(route('recruiter.services.store'), []);

        // Assert validation errors
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name',
            'short_description',
            'description'
        ]);
    }

    public function test_service_auto_generates_slug_when_not_provided()
    {
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);
        $this->actingAs($recruiter);

        $serviceData = [
            'name' => 'Amazing Home Cleaning Service',
            'short_description' => 'Test description',
            'description' => 'Test detailed description'
        ];

        $response = $this->post(route('recruiter.services.store'), $serviceData);

        $response->assertStatus(302);

        $service = Service::where('name', 'Amazing Home Cleaning Service')->first();
        $this->assertEquals('amazing-home-cleaning-service', $service->slug);
    }

    public function test_service_creation_handles_features_and_includes_arrays()
    {
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);
        $this->actingAs($recruiter);

        $serviceData = [
            'name' => 'Feature Test Service',
            'short_description' => 'Test description',
            'description' => 'Test detailed description',
            'features' => ['Feature 1', 'Feature 2', ''],  // Include empty string to test filtering
            'includes' => ['Include 1', '', 'Include 2']   // Include empty string to test filtering
        ];

        $response = $this->post(route('recruiter.services.store'), $serviceData);

        $response->assertStatus(302);

        $service = Service::where('name', 'Feature Test Service')->first();
        $this->assertEquals(['Feature 1', 'Feature 2'], $service->features);
        $this->assertEquals(['Include 1', 'Include 2'], $service->includes);
    }

    public function test_unauthorized_user_cannot_access_service_creation()
    {
        // Test without authentication
        $response = $this->get(route('recruiter.services.create'));
        $response->assertStatus(302); // Should redirect to login

        $response = $this->post(route('recruiter.services.store'), []);
        $response->assertStatus(302); // Should redirect to login
    }

    public function test_service_creation_sets_correct_default_values()
    {
        $recruiter = User::factory()->create(['account_type' => 'recruiter']);
        $this->actingAs($recruiter);

        $serviceData = [
            'name' => 'Default Values Test Service',
            'short_description' => 'Test description',
            'description' => 'Test detailed description'
            // Not providing optional fields
        ];

        $response = $this->post(route('recruiter.services.store'), $serviceData);

        $response->assertStatus(302);

        $service = Service::where('name', 'Default Values Test Service')->first();
        $this->assertEquals($recruiter->id, $service->user_id);
        $this->assertEquals('Pending', $service->approval_status);
        $this->assertEquals('recruiter', $service->servicetoggle);
        $this->assertEquals(0, $service->sort_order); // Default value from database
    }
}
