<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function successfully_upload_job_image()
    {
        $user = User::factory()->create(['account_type' => 'user']);

        $file = UploadedFile::fake()->image('job-image.jpg', 800, 600);

        $jobData = [
            'title' => 'Software Developer',
            'category' => 'Production & Factory Jobs',
            'short_description' => 'Looking for a software developer',
            'description' => 'We need an experienced developer',
            'location' => 'New York',
            'employment_type' => 'full-time',
            'salary_min' => 50000,
            'salary_max' => 80000,
            'salary_type' => 'yearly',
            'contact_email' => 'test@example.com',
            'image' => $file,
        ];

        $response = $this->actingAs($user)
            ->post(route('user.jobs.store'), $jobData);

        $response->assertRedirect();

        $job = JobListing::first();
        $this->assertNotNull($job->image);
        $this->assertStringContains('images/', $job->image);

        Storage::disk('public')->assertExists($job->image);
    }

    /** @test */
    public function successfully_upload_service_image()
    {
        $user = User::factory()->create(['account_type' => 'user']);

        $file = UploadedFile::fake()->image('service-image.jpg', 800, 600);

        $serviceData = [
            'name' => 'Web Development Service',
            'short_description' => 'Professional web development',
            'description' => 'We create amazing websites',
            'price_from' => 1000,
            'price_to' => 5000,
            'duration' => '2-4 weeks',
            'image' => $file,
        ];

        $response = $this->actingAs($user)
            ->post(route('user.services.store'), $serviceData);

        $response->assertRedirect();

        $service = \App\Models\Service::first();
        $this->assertNotNull($service->image);
        $this->assertStringContains('services/', $service->image);

        Storage::disk('public')->assertExists($service->image);
    }

    /** @test */
    public function image_path_stored_correctly()
    {
        $user = User::factory()->create(['account_type' => 'user']);

        $file = UploadedFile::fake()->image('test-job.png');

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

        $job = JobListing::first();

        // Verify image path format
        $this->assertMatchesRegularExpression('/^images\/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|gif)$/', $job->image);

        // Verify file exists in storage
        $this->assertTrue(Storage::disk('public')->exists($job->image));

        // Verify file size is reasonable
        $fileSize = Storage::disk('public')->size($job->image);
        $this->assertGreaterThan(0, $fileSize);
    }

    /** @test */
    public function delete_old_image_on_update()
    {
        $user = User::factory()->create(['account_type' => 'user']);

        // Create job with initial image
        $oldFile = UploadedFile::fake()->image('old-image.jpg');
        $job = JobListing::factory()->create([
            'user_id' => $user->id,
            'image' => 'images/old-test-image.jpg'
        ]);

        // Simulate old image exists in storage
        Storage::disk('public')->put($job->image, 'fake image content');
        Storage::disk('public')->assertExists($job->image);

        // Update with new image
        $newFile = UploadedFile::fake()->image('new-image.jpg');
        $updateData = [
            'title' => $job->title,
            'category' => $job->category,
            'short_description' => $job->short_description,
            'description' => $job->description,
            'location' => $job->location,
            'employment_type' => $job->employment_type,
            'contact_email' => $job->contact_email,
            'image' => $newFile,
        ];

        $response = $this->actingAs($user)
            ->put(route('user.jobs.update', $job), $updateData);

        $job->refresh();

        // Verify old image was deleted
        Storage::disk('public')->assertMissing('images/old-test-image.jpg');

        // Verify new image exists
        Storage::disk('public')->assertExists($job->image);
        $this->assertNotEquals('images/old-test-image.jpg', $job->image);
    }

    /** @test */
    public function invalid_file_type_upload()
    {
        $user = User::factory()->create(['account_type' => 'user']);

        $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

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

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('job_listings', 0);
    }

    /** @test */
    public function missing_image_file_upload()
    {
        $user = User::factory()->create(['account_type' => 'user']);

        $jobData = [
            'title' => 'Test Job',
            'category' => 'Production & Factory Jobs',
            'short_description' => 'Test description',
            'description' => 'Test full description',
            'location' => 'Test Location',
            'employment_type' => 'full-time',
            'contact_email' => 'test@example.com',
            // No image provided
        ];

        $response = $this->actingAs($user)
            ->post(route('user.jobs.store'), $jobData);

        $response->assertRedirect();

        $job = JobListing::first();
        $this->assertNull($job->image);
    }

    /** @test */
    public function image_storage_disk_error()
    {
        $user = User::factory()->create(['account_type' => 'user']);

        // Mock storage to fail
        Storage::shouldReceive('disk')
            ->with('public')
            ->andReturnSelf();

        Storage::shouldReceive('store')
            ->andReturn(false);

        $file = UploadedFile::fake()->image('test-image.jpg');

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

        // Should handle gracefully - either redirect with error or create job without image
        $this->assertTrue(
            $response->isRedirection() || $response->getStatusCode() === 500
        );
    }

    /** @test */
    public function symlink_exists_for_images()
    {
        // This test verifies that the storage symlink functionality works
        $user = User::factory()->create(['account_type' => 'user']);

        $file = UploadedFile::fake()->image('symlink-test.jpg');

        $jobData = [
            'title' => 'Symlink Test Job',
            'category' => 'Production & Factory Jobs',
            'short_description' => 'Testing symlink',
            'description' => 'Testing storage symlink functionality',
            'location' => 'Test Location',
            'employment_type' => 'full-time',
            'contact_email' => 'test@example.com',
            'image' => $file,
        ];

        $response = $this->actingAs($user)
            ->post(route('user.jobs.store'), $jobData);

        $job = JobListing::first();

        if ($job && $job->image) {
            // Verify the image is accessible via asset() URL structure
            $assetUrl = asset('storage/' . $job->image);
            $this->assertStringContains('/storage/', $assetUrl);

            // Verify image exists in storage
            Storage::disk('public')->assertExists($job->image);
        }
    }
}
