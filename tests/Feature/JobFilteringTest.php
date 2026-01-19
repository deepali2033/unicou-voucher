<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobFilteringTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $admin;
    private $recruiter;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with different account types
        $this->admin = User::factory()->create([
            'account_type' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->recruiter = User::factory()->create([
            'account_type' => 'recruiter',
            'email_verified_at' => now(),
        ]);

        $this->user = User::factory()->create([
            'account_type' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_dashboard_shows_all_jobs_and_services()
    {
        // Create jobs for different users
        $adminJob = JobListing::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $recruiterJob = JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        $userJob = JobListing::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        // Create services for different users
        $adminService = Service::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $recruiterService = Service::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        // Login as admin and check dashboard
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // Admin should see all jobs and services (3 jobs, 2 services)
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_jobs'] === 3 &&
                   $stats['total_services'] === 2 &&
                   $stats['active_jobs'] === 3 &&
                   $stats['active_services'] === 2;
        });
    }

    public function test_recruiter_dashboard_shows_only_own_jobs_and_services()
    {
        // Create jobs for different users
        JobListing::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $recruiterJob = JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        JobListing::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        // Create services for different users
        Service::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $recruiterService = Service::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        // Login as recruiter and check dashboard
        $response = $this->actingAs($this->recruiter)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Recruiter should see only their own jobs and services (1 job, 1 service)
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_jobs'] === 1 &&
                   $stats['total_services'] === 1 &&
                   $stats['active_jobs'] === 1 &&
                   $stats['active_services'] === 1;
        });
    }

    public function test_user_dashboard_shows_only_own_jobs_and_services()
    {
        // Create jobs for different users
        JobListing::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        $userJob = JobListing::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        // Create services for different users
        Service::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $userService = Service::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        // Login as user and check dashboard
        $response = $this->actingAs($this->user)
            ->get(route('user.dashboard'));

        $response->assertStatus(200);

        // User should see only their own jobs and services (1 job, 1 service)
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_jobs'] === 1 &&
                   $stats['total_services'] === 1 &&
                   $stats['active_jobs'] === 1 &&
                   $stats['active_services'] === 1;
        });
    }

    public function test_dashboard_stats_correctly_filter_inactive_jobs()
    {
        // Create active and inactive jobs for recruiter
        JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => false,
        ]);

        // Create active and inactive services for recruiter
        Service::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        Service::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => false,
        ]);

        // Login as recruiter and check dashboard
        $response = $this->actingAs($this->recruiter)
            ->get(route('recruiter.dashboard'));

        $response->assertStatus(200);

        // Should show correct active/inactive counts
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_jobs'] === 2 &&
                   $stats['active_jobs'] === 1 &&
                   $stats['inactive_jobs'] === 1 &&
                   $stats['total_services'] === 2 &&
                   $stats['active_services'] === 1 &&
                   $stats['inactive_services'] === 1;
        });
    }

    public function test_recruiter_job_listing_shows_only_own_jobs()
    {
        // Create jobs for different users
        JobListing::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
            'is_approved' => true,
            'jobtoggle' => 'admin',
        ]);

        $recruiterJob = JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
            'is_approved' => true,
            'jobtoggle' => 'recruiter',
        ]);

        JobListing::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'is_approved' => true,
            'jobtoggle' => 'customer',
        ]);

        // Login as recruiter and check job listing
        $response = $this->actingAs($this->recruiter)
            ->get(route('recruiter.jobs.index'));

        $response->assertStatus(200);

        // Should only see recruiter's jobs (1 job)
        $response->assertViewHas('jobs', function ($jobs) {
            return $jobs->count() === 1;
        });
    }

    public function test_user_job_listing_shows_only_own_jobs()
    {
        // Create jobs for different users
        JobListing::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        $userJob = JobListing::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        // Login as user and check job listing
        $response = $this->actingAs($this->user)
            ->get(route('user.jobs.index'));

        $response->assertStatus(200);

        // Should only see user's jobs (1 job)
        $response->assertViewHas('jobs', function ($jobs) {
            return $jobs->count() === 1;
        });
    }

    public function test_admin_job_listing_shows_all_jobs()
    {
        // Create jobs for different users
        $adminJob = JobListing::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $recruiterJob = JobListing::factory()->create([
            'user_id' => $this->recruiter->id,
            'is_active' => true,
        ]);

        $userJob = JobListing::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        // Login as admin and check job listing
        $response = $this->actingAs($this->admin)
            ->get(route('admin.jobs.index'));

        $response->assertStatus(200);

        // Should see all jobs (3 jobs)
        $response->assertViewHas('jobs', function ($jobs) {
            return $jobs->count() === 3;
        });
    }
}
