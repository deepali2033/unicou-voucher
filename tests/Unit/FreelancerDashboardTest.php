<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Http\Controllers\Freelancer\FreelancerController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class FreelancerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $freelancerController;
    protected $freelancer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freelancerController = new FreelancerController();

        // Create a freelancer user
        $this->freelancer = User::factory()->create([
            'email' => 'freelancer@test.com',
            'user_type' => 'freelancer'
        ]);
    }

    /** @test */
    public function test_dashboard_returns_view_with_stats()
    {
        // Authenticate as freelancer
        Auth::login($this->freelancer);

        // Create some test data
        $this->createTestJobApplications();

        // Call the dashboard method
        $response = $this->freelancerController->dashboard();

        // Assert that view is returned
        $this->assertEquals('freelancer.dashboard', $response->getName());

        // Assert that stats are passed to view
        $viewData = $response->getData();
        $this->assertArrayHasKey('stats', $viewData);

        $stats = $viewData['stats'];
        $this->assertArrayHasKey('applied_jobs_count', $stats);
    }

    /** @test */
    public function test_applied_jobs_count_is_correct_for_authenticated_freelancer()
    {
        // Authenticate as freelancer
        Auth::login($this->freelancer);

        // Create job applications for this freelancer
        $this->createTestJobApplications();

        // Call the dashboard method
        $response = $this->freelancerController->dashboard();
        $stats = $response->getData()['stats'];

        // Assert correct count
        $this->assertEquals(3, $stats['applied_jobs_count']);
    }

    /** @test */
    public function test_applied_jobs_count_only_shows_current_freelancer_applications()
    {
        // Create another freelancer
        $otherFreelancer = User::factory()->create([
            'email' => 'other@test.com',
            'user_type' => 'freelancer'
        ]);

        // Authenticate as first freelancer
        Auth::login($this->freelancer);

        // Create job applications for both freelancers
        $this->createTestJobApplications(); // 3 for current freelancer

        // Create applications for other freelancer
        JobApplication::factory()->count(2)->create([
            'freelancer_id' => $otherFreelancer->id
        ]);

        // Call the dashboard method for authenticated freelancer
        $response = $this->freelancerController->dashboard();
        $stats = $response->getData()['stats'];

        // Should only count applications for authenticated freelancer
        $this->assertEquals(3, $stats['applied_jobs_count']);
    }

    /** @test */
    public function test_applied_jobs_count_is_zero_when_no_applications()
    {
        // Authenticate as freelancer
        Auth::login($this->freelancer);

        // Don't create any applications

        // Call the dashboard method
        $response = $this->freelancerController->dashboard();
        $stats = $response->getData()['stats'];

        // Should be zero
        $this->assertEquals(0, $stats['applied_jobs_count']);
    }

    private function createTestJobApplications()
    {
        // Create some job listings first
        $jobListings = JobListing::factory()->count(3)->create();

        // Create job applications for the authenticated freelancer
        foreach ($jobListings as $jobListing) {
            JobApplication::factory()->create([
                'freelancer_id' => $this->freelancer->id,
                'job_listing_id' => $jobListing->id,
                'status' => 'pending'
            ]);
        }
    }
}
