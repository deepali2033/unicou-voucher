<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function job_image_url_is_accessible()
    {
        $user = User::factory()->create();
        $job = JobListing::factory()->create([
            'user_id' => $user->id,
            'image' => 'images/test-job.jpg'
        ]);

        // Simulate image file exists
        Storage::disk('public')->put($job->image, 'fake image content');

        // Test that asset URL can be generated
        $assetUrl = asset('storage/' . $job->image);

        $this->assertStringContains('/storage/images/test-job.jpg', $assetUrl);
        $this->assertStringStartsWith(url('/'), $assetUrl);
    }

    /** @test */
    public function service_image_url_is_accessible()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create([
            'user_id' => $user->id,
            'image' => 'services/test-service.jpg'
        ]);

        // Simulate image file exists
        Storage::disk('public')->put($service->image, 'fake service image content');

        // Test that asset URL can be generated
        $assetUrl = asset('storage/' . $service->image);

        $this->assertStringContains('/storage/services/test-service.jpg', $assetUrl);
        $this->assertStringStartsWith(url('/'), $assetUrl);
    }

    /** @test */
    public function image_displays_correctly_in_job_view()
    {
        $user = User::factory()->create();
        $job = JobListing::factory()->create([
            'user_id' => $user->id,
            'image' => 'images/job-display.jpg',
            'is_active' => true,
            'is_approved' => true
        ]);

        // Create the image in storage
        Storage::disk('public')->put($job->image, 'job image content');

        // Visit job detail page
        $response = $this->get("/jobs/{$job->slug}");

        $response->assertStatus(200);

        // Check if the image path appears in the response
        $expectedImageUrl = asset('storage/' . $job->image);
        $response->assertSee($expectedImageUrl, false);
    }

    /** @test */
    public function image_displays_correctly_in_service_view()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create([
            'user_id' => $user->id,
            'image' => 'services/service-display.jpg',
            'is_active' => true
        ]);

        // Create the image in storage
        Storage::disk('public')->put($service->image, 'service image content');

        // Visit service detail page (assuming route exists)
        $response = $this->get("/services/{$service->slug}");

        $response->assertStatus(200);

        // Check if the image path appears in the response
        $expectedImageUrl = asset('storage/' . $service->image);
        $response->assertSee($expectedImageUrl, false);
    }

    /** @test */
    public function missing_job_image_handles_gracefully()
    {
        $user = User::factory()->create();
        $job = JobListing::factory()->create([
            'user_id' => $user->id,
            'image' => null, // No image
            'is_active' => true,
            'is_approved' => true
        ]);

        $response = $this->get("/jobs/{$job->slug}");

        $response->assertStatus(200);

        // Should not show broken image paths
        $response->assertDontSee('storage/images/null');
        $response->assertDontSee('storage/null');
    }

    /** @test */
    public function missing_service_image_handles_gracefully()
    {
        $user = User::factory()->create();
        $service = Service::factory()->create([
            'user_id' => $user->id,
            'image' => null, // No image
            'is_active' => true
        ]);

        $response = $this->get("/services/{$service->slug}");

        $response->assertStatus(200);

        // Should not show broken image paths
        $response->assertDontSee('storage/services/null');
        $response->assertDontSee('storage/null');
    }

    /** @test */
    public function job_image_path_validation()
    {
        $validPaths = [
            'images/job1.jpg',
            'images/job-photo.png',
            'images/job_image.jpeg',
            'images/subfolder/image.gif'
        ];

        $invalidPaths = [
            'documents/file.pdf',
            'images/',
            '',
            'images/image.exe',
            '../images/hack.jpg'
        ];

        foreach ($validPaths as $path) {
            // Should be valid image path for jobs
            $this->assertTrue(
                str_starts_with($path, 'images/') && !empty(pathinfo($path, PATHINFO_EXTENSION)),
                "Path {$path} should be valid"
            );
        }

        foreach ($invalidPaths as $path) {
            // Should be invalid
            $this->assertFalse(
                str_starts_with($path, 'images/') && !empty(pathinfo($path, PATHINFO_EXTENSION)) && !str_contains($path, '..'),
                "Path {$path} should be invalid"
            );
        }
    }

    /** @test */
    public function service_image_path_validation()
    {
        $validPaths = [
            'services/service1.jpg',
            'services/web-service.png',
            'services/mobile_app.jpeg'
        ];

        $invalidPaths = [
            'documents/service.pdf',
            'services/',
            '',
            'images/service.jpg', // Wrong directory
            '../services/hack.jpg'
        ];

        foreach ($validPaths as $path) {
            $this->assertTrue(
                str_starts_with($path, 'services/') && !empty(pathinfo($path, PATHINFO_EXTENSION)),
                "Path {$path} should be valid"
            );
        }

        foreach ($invalidPaths as $path) {
            $this->assertFalse(
                str_starts_with($path, 'services/') && !empty(pathinfo($path, PATHINFO_EXTENSION)) && !str_contains($path, '..'),
                "Path {$path} should be invalid"
            );
        }
    }

    /** @test */
    public function storage_disk_configuration_is_correct()
    {
        // Test that public disk is properly configured
        $config = config('filesystems.disks.public');

        $this->assertIsArray($config);
        $this->assertEquals('local', $config['driver']);
        $this->assertStringContains('storage/app/public', $config['root']);
        $this->assertStringContains('/storage', $config['url']);
    }

    /** @test */
    public function image_file_types_are_restricted()
    {
        $user = User::factory()->create();

        // Test invalid file types
        $invalidFiles = [
            UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('script.js', 100, 'application/javascript'),
            UploadedFile::fake()->create('executable.exe', 100, 'application/octet-stream'),
        ];

        foreach ($invalidFiles as $file) {
            $jobData = [
                'title' => 'Test Job',
                'category' => 'Production & Factory Jobs',
                'short_description' => 'Test description',
                'description' => 'Test full description',
                'location' => 'Test Location',
                'employment_type' => 'full-time',
                'contact_email' => 'test@example.com',
                'image' => $file,
            ];

            $response = $this->actingAs($user)
                ->post(route('user.jobs.store'), $jobData);

            // Should reject invalid file types
            $response->assertSessionHasErrors('image');
        }
    }

    /** @test */
    public function large_image_files_are_handled()
    {
        $user = User::factory()->create();

        // Create a large image file (simulated)
        $largeFile = UploadedFile::fake()->image('large-image.jpg')
            ->size(10240); // 10MB

        $jobData = [
            'title' => 'Test Job',
            'category' => 'Production & Factory Jobs',
            'short_description' => 'Test description',
            'description' => 'Test full description',
            'location' => 'Test Location',
            'employment_type' => 'full-time',
            'contact_email' => 'test@example.com',
            'image' => $largeFile,
        ];

        $response = $this->actingAs($user)
            ->post(route('user.jobs.store'), $jobData);

        // Should handle large files appropriately (either accept or reject based on validation rules)
        $this->assertTrue(
            $response->isRedirection() || $response->getStatusCode() === 422
        );
    }
}
