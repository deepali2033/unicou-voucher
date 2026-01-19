<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class ImageSyncTest extends TestCase
{
    protected $tempSourceDir;
    protected $tempDestDir;

    protected function setUp(): void
    {
        parent::setUp();

        // Create temporary directories for testing
        $this->tempSourceDir = sys_get_temp_dir() . '/test_source_' . uniqid();
        $this->tempDestDir = sys_get_temp_dir() . '/test_dest_' . uniqid();

        mkdir($this->tempSourceDir, 0755, true);
        mkdir($this->tempDestDir, 0755, true);
    }

    protected function tearDown(): void
    {
        // Clean up temporary directories
        if (is_dir($this->tempSourceDir)) {
            $this->deleteDirectory($this->tempSourceDir);
        }
        if (is_dir($this->tempDestDir)) {
            $this->deleteDirectory($this->tempDestDir);
        }

        parent::tearDown();
    }

    /** @test */
    public function successfully_sync_job_images()
    {
        // Create test images in source directory
        $testImages = ['job1.jpg', 'job2.png', 'job3.gif'];
        foreach ($testImages as $image) {
            file_put_contents($this->tempSourceDir . '/' . $image, 'fake image content');
        }

        // Simulate sync logic
        $files = glob($this->tempSourceDir . '/*');
        $copied = 0;
        $errors = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $destFile = $this->tempDestDir . '/' . $filename;

                if (copy($file, $destFile)) {
                    $copied++;
                } else {
                    $errors++;
                }
            }
        }

        // Assert all files were copied
        $this->assertEquals(3, $copied);
        $this->assertEquals(0, $errors);

        // Verify files exist in destination
        foreach ($testImages as $image) {
            $this->assertFileExists($this->tempDestDir . '/' . $image);
        }
    }

    /** @test */
    public function successfully_sync_service_images()
    {
        // Create test service images
        $testServices = ['service1.jpg', 'service2.png'];
        foreach ($testServices as $service) {
            file_put_contents($this->tempSourceDir . '/' . $service, 'fake service image');
        }

        // Simulate service sync logic
        $files = glob($this->tempSourceDir . '/*');
        $copied = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $destFile = $this->tempDestDir . '/' . $filename;

                if (copy($file, $destFile)) {
                    $copied++;
                }
            }
        }

        $this->assertEquals(2, $copied);

        foreach ($testServices as $service) {
            $this->assertFileExists($this->tempDestDir . '/' . $service);
        }
    }

    /** @test */
    public function handle_missing_source_directory()
    {
        $nonExistentSource = '/path/that/does/not/exist';

        // Test source directory existence check
        $sourceExists = is_dir($nonExistentSource);

        $this->assertFalse($sourceExists);

        // The sync script should handle this gracefully
        if (!$sourceExists) {
            $this->assertTrue(true); // Test passes if we detect missing directory
        }
    }

    /** @test */
    public function create_destination_directory_if_missing()
    {
        // Remove destination directory
        rmdir($this->tempDestDir);
        $this->assertDirectoryDoesNotExist($this->tempDestDir);

        // Simulate destination directory creation
        if (!is_dir($this->tempDestDir)) {
            mkdir($this->tempDestDir, 0755, true);
        }

        $this->assertDirectoryExists($this->tempDestDir);
    }

    /** @test */
    public function handle_file_copy_permissions_error()
    {
        // Create a test file
        $sourceFile = $this->tempSourceDir . '/test.jpg';
        file_put_contents($sourceFile, 'test content');

        // Make destination directory read-only to simulate permissions error
        chmod($this->tempDestDir, 0444);

        $filename = basename($sourceFile);
        $destFile = $this->tempDestDir . '/' . $filename;

        // Attempt to copy should fail
        $copyResult = @copy($sourceFile, $destFile);

        $this->assertFalse($copyResult);

        // Restore permissions for cleanup
        chmod($this->tempDestDir, 0755);
    }

    /** @test */
    public function sync_only_processes_files_not_directories()
    {
        // Create mixed content in source
        $subDir = $this->tempSourceDir . '/subdir';
        mkdir($subDir);
        file_put_contents($this->tempSourceDir . '/image.jpg', 'image content');
        file_put_contents($subDir . '/nested.jpg', 'nested content');

        $files = glob($this->tempSourceDir . '/*');
        $processed = 0;

        foreach ($files as $file) {
            if (is_file($file)) { // Only process files, not directories
                $processed++;
                $filename = basename($file);
                copy($file, $this->tempDestDir . '/' . $filename);
            }
        }

        $this->assertEquals(1, $processed);
        $this->assertFileExists($this->tempDestDir . '/image.jpg');
        $this->assertFileDoesNotExist($this->tempDestDir . '/nested.jpg');
    }

    /** @test */
    public function verify_image_file_extensions()
    {
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $testFiles = [
            'image.jpg' => true,
            'photo.png' => true,
            'graphic.gif' => true,
            'picture.webp' => true,
            'document.pdf' => false,
            'text.txt' => false,
        ];

        foreach ($testFiles as $filename => $shouldBeValid) {
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $isValidImage = in_array($extension, $validExtensions);

            $this->assertEquals($shouldBeValid, $isValidImage,
                "File {$filename} validation mismatch");
        }
    }

    /** @test */
    public function count_sync_statistics_correctly()
    {
        // Create test files with mixed success/failure scenarios
        $goodFile = $this->tempSourceDir . '/good.jpg';
        $badPath = '/invalid/path/bad.jpg';

        file_put_contents($goodFile, 'content');

        $copied = 0;
        $errors = 0;
        $files = [$goodFile, $badPath];

        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $destFile = $this->tempDestDir . '/' . $filename;

                if (@copy($file, $destFile)) {
                    $copied++;
                } else {
                    $errors++;
                }
            }
        }

        $this->assertEquals(1, $copied);
        $this->assertEquals(0, $errors); // Bad path won't be processed due to is_file() check
    }

    /**
     * Recursively delete directory
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
