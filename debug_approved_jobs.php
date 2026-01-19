<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::create('/', 'GET')
);

echo "Debugging Job Approval Status:\n";
echo "==============================\n";

// Check all jobs and their approval status
$allJobs = App\Models\JobListing::all();
echo "Total jobs in database: " . $allJobs->count() . "\n\n";

foreach ($allJobs as $job) {
    echo "Job ID: {$job->id}\n";
    echo "Title: {$job->title}\n";
    echo "Category: {$job->category}\n";
    echo "Is Active: " . ($job->is_active ? 'Yes' : 'No') . "\n";
    echo "Is Approved: " . ($job->is_approved ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

echo "\nActive Jobs: " . App\Models\JobListing::active()->count() . "\n";
echo "Approved Jobs: " . App\Models\JobListing::approved()->count() . "\n";
echo "Active + Approved Jobs: " . App\Models\JobListing::active()->approved()->count() . "\n";

echo "\nJobs by category with approval status:\n";
$jobsByCategory = App\Models\JobListing::select('category', 'is_active', 'is_approved')
    ->get()
    ->groupBy('category');

foreach ($jobsByCategory as $category => $jobs) {
    $totalJobs = $jobs->count();
    $activeJobs = $jobs->where('is_active', true)->count();
    $approvedJobs = $jobs->where('is_approved', true)->count();
    $activeApprovedJobs = $jobs->where('is_active', true)->where('is_approved', true)->count();

    echo "Category: {$category}\n";
    echo "  Total: {$totalJobs}, Active: {$activeJobs}, Approved: {$approvedJobs}, Active+Approved: {$activeApprovedJobs}\n";
}

$kernel->terminate($request, $response);
