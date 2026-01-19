<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\JobListing;
use Illuminate\Support\Facades\Route;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for testing
        $this->admin = User::factory()->create([
            'account_type' => 'admin',
            'email' => 'admin@test.com',
        ]);
    }

    /** @test */
    public function analytics_route_exists()
    {
        $this->assertTrue(Route::has('admin.analytics'));
    }

    /** @test */
    public function analytics_page_requires_authentication()
    {
        $response = $this->get(route('admin.analytics'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_admin_can_access_analytics_page()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.analytics'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.analytics.index');
    }

    /** @test */
    public function analytics_page_displays_correct_data()
    {
        // Create test data
        User::factory()->count(5)->create(['account_type' => 'candidate']);
        User::factory()->count(3)->create(['account_type' => 'recruiter']);
        Service::factory()->count(4)->create();
        JobListing::factory()->count(6)->create();

        $response = $this->actingAs($this->admin)
                         ->get(route('admin.analytics'));

        $response->assertStatus(200);

        // Check that the view receives the expected data
        $response->assertViewHas([
            'totalUsers',
            'totalServices',
            'totalJobs',
            'userStats',
            'serviceStats',
            'jobStats',
            'growthData'
        ]);
    }

    /** @test */
    public function analytics_page_contains_required_sections()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.analytics'));

        $response->assertStatus(200);

        // Check for key sections in the analytics dashboard
        $response->assertSee('Reports & Analytics');
        $response->assertSee('Overview Statistics');
        $response->assertSee('Growth Trends');
        $response->assertSee('User Analytics');
        $response->assertSee('Services Analytics');
        $response->assertSee('Jobs Analytics');
        $response->assertSee('Quick Actions');
    }

    /** @test */
    public function analytics_page_has_navigation_links()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.analytics'));

        $response->assertStatus(200);

        // Check for navigation links in Quick Actions section
        $response->assertSee(route('admin.users.index'));
        $response->assertSee(route('admin.services.index'));
        $response->assertSee(route('admin.jobs.index'));
        $response->assertSee(route('admin.dashboard'));
    }

    /** @test */
    public function reports_and_analytics_links_exist_in_admin_pages()
    {
        // Test users index page
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.users.index'));
        $response->assertSee('Reports & Analytics');
        $response->assertSee(route('admin.analytics'));

        // Test services index page
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.services.index'));
        $response->assertSee('Reports & Analytics');
        $response->assertSee(route('admin.analytics'));

        // Test jobs index page
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.jobs.index'));
        $response->assertSee('Reports & Analytics');
        $response->assertSee(route('admin.analytics'));
    }

    /** @test */
    public function analytics_page_includes_chart_js()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Chart.js');
        $response->assertSee('canvas');
    }
}
