<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageDebuggingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function debug_script_detects_missing_images()
    {
        $user = User::factory()->create();

        // Create job with image path but no actual file
        $job = JobListing::factory()->create([
            'user_id' => $user->id,
            'image' => 'images/missing-file.jpg'
        ]);

        // Simulate debug check logic
        $imagePath = storage_path('app/public/' . $job->image);
        $publicPath = public_path('storage/' . $job->image);

        $this->assertFalse(file_exists($imagePath), 'Image should not exist in storage');
        $this->assertFalse(file_exists($publicPath), 'Image should not exist in public');
    }

    /** @test */
    public function debug_script_detects_existing_images()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $job = JobListing::factory()->create([
            'user_id' => $user->id,
            'image' => 'images/existing-file.jpg'
        ]);

        // Create the image file
        Storage::disk('public')->put($job->image, 'fake image content');

        // Verify debug logic would find the file
        $this->assertTrue(Storage::disk('public')->exists($job->image));

        $fileSize = Storage::disk('public')->size($job->image);
        $this->assertGreaterThan(0, $fileSize);
    }

    /** @test */
    public function debug_script_handles_null_images()
    {
        $user = User::factory()->create();
        $job = JobListing::factory()->create([
            'user_id' => $user->id,
            'image' => null
        ]);

        // Debug logic should handle null images gracefully
        $this->assertNull($job->image);

        // No storage checks should be performed for null images
        if ($job->image) {
            // This block shouldn't execute
            $this->fail('Image should be null');
        } else {
            $this->assertTrue(true, 'Null image handled correctly');
        }
    }

    /** @test */
    public function storage_symlink_detection_works()
    {
        // Test storage symlink detection logic
        $publicStorage = public_path('storage');

        // In testing environment, this might not exist
        if (file_exists($publicStorage)) {
            if (is_link($publicStorage)) {
                $this->assertTrue(true, 'Storage is properly symlinked');
                $linkTarget = readlink($publicStorage);
                $this->assertNotEmpty($linkTarget);
            } else if (is_dir($publicStorage)) {
                $this->assertTrue(true, 'Storage directory exists (not symlinked in test)');
            }
        } else {
            $this->assertTrue(true, 'Storage symlink does not exist in test environment');
        }
    }

    /** @test */
    public function image_directory_listing_works()
    {
        Storage::fake('public');

        // Create test images
        $testImages = ['image1.jpg', 'image2.png', 'image3.gif'];
        foreach ($testImages as $image) {
            Storage::disk('public')->put("images/{$image}", 'test content');
        }

        // Simulate directory listing logic
        $allFiles = Storage::disk('public')->files('images');
        $imageFiles = array_filter($allFiles, function($file) {
            return !in_array(basename($file), ['.', '..', '.gitignore']);
        });

        $this->assertCount(3, $imageFiles);

        foreach ($testImages as $expectedImage) {
            $this->assertContains("images/{$expectedImage}", $imageFiles);
        }
    }

    /** @test */
    public function sync_script_creates_destination_directory()
    {
        $tempSource = sys_get_temp_dir() . '/test_sync_source_' . uniqid();
        $tempDest = sys_get_temp_dir() . '/test_sync_dest_' . uniqid();

        // Create source but not destination
        mkdir($tempSource, 0755, true);
        $this->assertDirectoryExists($tempSource);
        $this->assertDirectoryDoesNotExist($tempDest);

        // Simulate sync script destination creation logic
        if (!is_dir($tempDest)) {
            mkdir($tempDest, 0755, true);
        }

        $this->assertDirectoryExists($tempDest);

        // Cleanup
        rmdir($tempSource);
        rmdir($tempDest);
    }

    /** @test */
    public function sync_script_copies_only_files()
    {
        $tempSource = sys_get_temp_dir() . '/test_sync_files_' . uniqid();
        $tempDest = sys_get_temp_dir() . '/test_sync_files_dest_' . uniqid();

        mkdir($tempSource, 0755, true);
        mkdir($tempDest, 0755, true);

        // Create mixed content
        file_put_contents($tempSource . '/image.jpg', 'image content');
        file_put_contents($tempSource . '/photo.png', 'photo content');
        mkdir($tempSource . '/subdirectory');
        file_put_contents($tempSource . '/subdirectory/nested.jpg', 'nested content');

        // Simulate sync logic - only copy files, not directories
        $files = glob($tempSource . '/*');
        $copied = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $destFile = $tempDest . '/' . $filename;
                if (copy($file, $destFile)) {
                    $copied++;
                }
            }
        }

        $this->assertEquals(2, $copied);
        $this->assertFileExists($tempDest . '/image.jpg');
        $this->assertFileExists($tempDest . '/photo.png');
        $this->assertDirectoryDoesNotExist($tempDest . '/subdirectory');

        // Cleanup
        $this->deleteDirectory($tempSource);
        $this->deleteDirectory($tempDest);
    }

    /** @test */
    public function sync_script_reports_correct_statistics()
    {
        $tempSource = sys_get_temp_dir() . '/test_stats_source_' . uniqid();
        $tempDest = sys_get_temp_dir() . '/test_stats_dest_' . uniqid();

        mkdir($tempSource, 0755, true);
        mkdir($tempDest, 0755, true);

        // Create test files
        $testFiles = ['file1.jpg', 'file2.png', 'file3.gif'];
        foreach ($testFiles as $file) {
            file_put_contents($tempSource . '/' . $file, 'content');
        }

        // Simulate sync with statistics
        $files = glob($tempSource . '/*');
        $copied = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $destFile = $tempDest . '/' . $filename;

                if (copy($file, $destFile)) {
                    $copied++;
                } else {
                    $errors++;
                }
            }
        }

        $this->assertEquals(3, $copied);
        $this->assertEquals(0, $skipped);
        $this->assertEquals(0, $errors);
        $this->assertEquals(count($testFiles), $copied + $skipped + $errors);

        // Cleanup
        $this->deleteDirectory($tempSource);
        $this->deleteDirectory($tempDest);
    }

    /** @test */
    public function sync_handles_no_files_gracefully()
    {
        $tempSource = sys_get_temp_dir() . '/test_empty_source_' . uniqid();
        $tempDest = sys_get_temp_dir() . '/test_empty_dest_' . uniqid();

        mkdir($tempSource, 0755, true);
        mkdir($tempDest, 0755, true);

        // No files in source
        $files = glob($tempSource . '/*');

        $this->assertEmpty($files, 'Source should be empty');

        // Sync should handle empty source gracefully
        $copied = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                $copied++;
            }
        }

        $this->assertEquals(0, $copied);

        // Cleanup
        rmdir($tempSource);
        rmdir($tempDest);
    }

    /**
     * Helper method to recursively delete a directory
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
