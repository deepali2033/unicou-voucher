<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationRestrictionTest extends TestCase
{
    use RefreshDatabase;

    private User $freelancer;
    private User $recruiter;
    private User $regularUser;
    private JobListing $jobListing;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with different account types
        $this->freelancer = User::factory()->create([
            'account_type' => 'freelancer',
            'email_verified_at' => now(),
        ]);

        $this->recruiter = User::factory()->create([
            'account_type' => 'recruiter',
            'email_verified_at' => now(),
        ]);

        $this->regularUser = User::factory()->create([
            'account_type' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create a test job listing
        $this->jobListing = JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);
    }

    public function test_freelancer_can_access_job_application_form()
    {
        $response = $this->actingAs($this->freelancer)
            ->get(route('candidates.create'));

        $response->assertStatus(200);
        $response->assertViewIs('candidates.create');
        $response->assertViewHas('jobListings');
    }

    public function test_freelancer_can_submit_job_application()
    {
        $applicationData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
            'position_applied' => 'Software Developer',
            'job_listing_id' => $this->jobListing->id,
            'work_experience' => 'Some experience',
            'education' => 'Computer Science Degree',
            'willing_to_relocate' => false,
            'has_transportation' => true,
            'background_check_consent' => true,
        ];

        $response = $this->actingAs($this->freelancer)
            ->post(route('candidates.store'), $applicationData);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verify the application was created
        $this->assertDatabaseHas('candidates', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_recruiter_cannot_access_job_application_form()
    {
        $response = $this->actingAs($this->recruiter)
            ->get(route('candidates.create'));

        $response->assertStatus(403);
    }

    public function test_recruiter_cannot_submit_job_application()
    {
        $applicationData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '0987654321',
            'position_applied' => 'Project Manager',
            'job_listing_id' => $this->jobListing->id,
            'willing_to_relocate' => false,
            'has_transportation' => true,
            'background_check_consent' => true,
        ];

        $response = $this->actingAs($this->recruiter)
            ->post(route('candidates.store'), $applicationData);

        $response->assertStatus(403);

        // Verify the application was not created
        $this->assertDatabaseMissing('candidates', [
            'email' => 'jane.smith@example.com',
        ]);
    }

    public function test_regular_user_cannot_access_job_application_form()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('candidates.create'));

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_submit_job_application()
    {
        $applicationData = [
            'first_name' => 'Bob',
            'last_name' => 'Johnson',
            'email' => 'bob.johnson@example.com',
            'phone' => '5555555555',
            'position_applied' => 'Designer',
            'job_listing_id' => $this->jobListing->id,
            'willing_to_relocate' => false,
            'has_transportation' => true,
            'background_check_consent' => true,
        ];

        $response = $this->actingAs($this->regularUser)
            ->post(route('candidates.store'), $applicationData);

        $response->assertStatus(403);

        // Verify the application was not created
        $this->assertDatabaseMissing('candidates', [
            'email' => 'bob.johnson@example.com',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_job_application_form()
    {
        $response = $this->get(route('candidates.create'));

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_submit_job_application()
    {
        $applicationData = [
            'first_name' => 'Anonymous',
            'last_name' => 'User',
            'email' => 'anonymous@example.com',
            'phone' => '1111111111',
            'position_applied' => 'Tester',
            'willing_to_relocate' => false,
            'has_transportation' => true,
            'background_check_consent' => true,
        ];

        $response = $this->post(route('candidates.store'), $applicationData);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));

        // Verify the application was not created
        $this->assertDatabaseMissing('candidates', [
            'email' => 'anonymous@example.com',
        ]);
    }

    public function test_freelancer_can_access_job_application_form_with_specific_job()
    {
        $response = $this->actingAs($this->freelancer)
            ->get(route('candidates.create', ['job' => $this->jobListing->slug]));

        $response->assertStatus(200);
        $response->assertViewIs('candidates.create');
        $response->assertViewHas('selectedJob', function ($selectedJob) {
            return $selectedJob && $selectedJob->id === $this->jobListing->id;
        });
    }
}
