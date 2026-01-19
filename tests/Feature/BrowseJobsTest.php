<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrowseJobsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function freelancer_can_access_browse_jobs_page()
    {
        // Create a freelancer user
        $freelancer = User::factory()->create([
            'account_type' => 'freelancer'
        ]);

        // Create some job listings with different employment types
        JobListing::factory()->create([
            'employment_type' => 'full-time',
            'is_active' => true,
            'is_approved' => true
        ]);

        JobListing::factory()->create([
            'employment_type' => 'part-time',
            'is_active' => true,
            'is_approved' => true
        ]);

        JobListing::factory()->create([
            'employment_type' => 'contract',
            'is_active' => true,
            'is_approved' => true
        ]);

        // Act as freelancer and visit browse jobs page
        $response = $this->actingAs($freelancer)->get('/browse-jobs');

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('freelancer.jobs');
        $response->assertViewHas('jobs');
    }

    /** @test */
    public function browse_jobs_page_only_shows_full_time_and_part_time_jobs()
    {
        $freelancer = User::factory()->create([
            'account_type' => 'freelancer'
        ]);

        // Create jobs with different employment types
        $fullTimeJob = JobListing::factory()->create([
            'employment_type' => 'full-time',
            'is_active' => true,
            'is_approved' => true
        ]);

        $partTimeJob = JobListing::factory()->create([
            'employment_type' => 'part-time',
            'is_active' => true,
            'is_approved' => true
        ]);

        $contractJob = JobListing::factory()->create([
            'employment_type' => 'contract',
            'is_active' => true,
            'is_approved' => true
        ]);

        $temporaryJob = JobListing::factory()->create([
            'employment_type' => 'temporary',
            'is_active' => true,
            'is_approved' => true
        ]);

        // Act as freelancer and visit browse jobs page
        $response = $this->actingAs($freelancer)->get('/browse-jobs');

        // Assert only full-time and part-time jobs are shown
        $jobs = $response->viewData('jobs');

        $this->assertCount(2, $jobs);
        $this->assertTrue($jobs->contains($fullTimeJob));
        $this->assertTrue($jobs->contains($partTimeJob));
        $this->assertFalse($jobs->contains($contractJob));
        $this->assertFalse($jobs->contains($temporaryJob));
    }

    /** @test */
    public function non_freelancer_cannot_access_browse_jobs_page()
    {
        // Create a regular user (not freelancer)
        $user = User::factory()->create([
            'account_type' => 'user'
        ]);

        // Try to access browse jobs page
        $response = $this->actingAs($user)->get('/browse-jobs');

        // Should be redirected or forbidden
        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_browse_jobs_page()
    {
        // Try to access browse jobs page without authentication
        $response = $this->get('/browse-jobs');

        // Should be redirected to login
        $response->assertRedirect('/login');
    }

    /** @test */
    public function header_shows_browse_jobs_link_only_for_freelancers()
    {
        $freelancer = User::factory()->create([
            'account_type' => 'freelancer'
        ]);

        $user = User::factory()->create([
            'account_type' => 'user'
        ]);

        // Test for freelancer - should see Browse Jobs link
        $response = $this->actingAs($freelancer)->get('/');
        $response->assertSeeText('Browse Jobs');

        // Test for regular user - should not see Browse Jobs link
        $response = $this->actingAs($user)->get('/');
        $response->assertDontSeeText('Browse Jobs');
    }
}
