<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminUserSearchFilterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for testing
        $this->admin = User::factory()->create([
            'account_type' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_index_returns_all_users()
    {
        // Create test users with different account types
        User::factory()->create([
            'name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'account_type' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'account_type' => 'freelancer',
        ]);

        User::factory()->create([
            'name' => 'Bob Wilson',
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'account_type' => 'recruiter',
        ]);

        // Login as admin and access users index
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);

        // Should see all users including admin (4 total)
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 4;
        });
    }

    public function test_search_by_user_name()
    {
        // Create test users
        User::factory()->create([
            'name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'account_type' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'account_type' => 'freelancer',
        ]);

        User::factory()->create([
            'name' => 'Johnny Cash',
            'first_name' => 'Johnny',
            'last_name' => 'Cash',
            'email' => 'johnny@example.com',
            'account_type' => 'recruiter',
        ]);

        // Search for users with "John" in their name
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'John']));

        $response->assertStatus(200);

        // Should find 2 users (John Doe and Johnny Cash)
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 2;
        });
    }

    public function test_search_by_email_address()
    {
        // Create test users
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@gmail.com',
            'account_type' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@yahoo.com',
            'account_type' => 'freelancer',
        ]);

        User::factory()->create([
            'name' => 'Bob Wilson',
            'email' => 'bob@gmail.com',
            'account_type' => 'recruiter',
        ]);

        // Search for users with "gmail" in their email
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'gmail']));

        $response->assertStatus(200);

        // Should find 2 users with gmail addresses
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 2;
        });
    }

    public function test_filter_by_account_type()
    {
        // Create test users with different account types
        User::factory()->create(['account_type' => 'user']);
        User::factory()->create(['account_type' => 'freelancer']);
        User::factory()->create(['account_type' => 'freelancer']);
        User::factory()->create(['account_type' => 'recruiter']);

        // Filter by freelancer account type
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['account_type' => 'freelancer']));

        $response->assertStatus(200);

        // Should find 2 freelancers
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 2;
        });
    }

    public function test_combined_search_and_filter()
    {
        // Create test users
        User::factory()->create([
            'name' => 'John Freelancer',
            'first_name' => 'John',
            'last_name' => 'Freelancer',
            'email' => 'john.freelancer@example.com',
            'account_type' => 'freelancer',
        ]);

        User::factory()->create([
            'name' => 'John Recruiter',
            'first_name' => 'John',
            'last_name' => 'Recruiter',
            'email' => 'john.recruiter@example.com',
            'account_type' => 'recruiter',
        ]);

        User::factory()->create([
            'name' => 'Jane Freelancer',
            'first_name' => 'Jane',
            'last_name' => 'Freelancer',
            'email' => 'jane.freelancer@example.com',
            'account_type' => 'freelancer',
        ]);

        // Search for "John" and filter by "freelancer"
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', [
                'search' => 'John',
                'account_type' => 'freelancer'
            ]));

        $response->assertStatus(200);

        // Should find only 1 user (John Freelancer)
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 1;
        });
    }

    public function test_empty_search_query()
    {
        // Create test users
        User::factory()->create(['account_type' => 'user']);
        User::factory()->create(['account_type' => 'freelancer']);

        // Send empty search query
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => '']));

        $response->assertStatus(200);

        // Should return all users including admin (3 total)
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 3;
        });
    }

    public function test_invalid_account_type_filter()
    {
        // Create test users
        User::factory()->create(['account_type' => 'user']);
        User::factory()->create(['account_type' => 'freelancer']);

        // Send invalid account type filter
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['account_type' => 'invalid_type']));

        $response->assertStatus(200);

        // Should return no users (filter won't match any account types)
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 0;
        });
    }

    public function test_pagination_with_search_filters()
    {
        // Create many users with similar names
        for ($i = 1; $i <= 20; $i++) {
            User::factory()->create([
                'name' => "Test User {$i}",
                'first_name' => 'Test',
                'last_name' => "User{$i}",
                'email' => "testuser{$i}@example.com",
                'account_type' => 'user',
            ]);
        }

        // Search for "Test" with pagination (should paginate at 15 per page)
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'Test']));

        $response->assertStatus(200);

        // Should find 15 users on first page (pagination limit)
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 15 && $users->total() === 20;
        });

        // Test second page
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'Test', 'page' => 2]));

        $response->assertStatus(200);

        // Should find remaining 5 users on second page
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 5 && $users->total() === 20;
        });
    }

    public function test_search_preserves_query_parameters_in_pagination()
    {
        // Create test users
        for ($i = 1; $i <= 20; $i++) {
            User::factory()->create([
                'name' => "Freelancer User {$i}",
                'account_type' => 'freelancer',
            ]);
        }

        // Search with filters
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', [
                'search' => 'Freelancer',
                'account_type' => 'freelancer'
            ]));

        $response->assertStatus(200);

        // Check that pagination links preserve search parameters
        $users = $response->viewData('users');
        $paginationUrl = $users->url(2);

        $this->assertStringContainsString('search=Freelancer', $paginationUrl);
        $this->assertStringContainsString('account_type=freelancer', $paginationUrl);
    }

    public function test_case_insensitive_search()
    {
        // Create test users
        User::factory()->create([
            'name' => 'John DOE',
            'first_name' => 'John',
            'last_name' => 'DOE',
            'email' => 'JOHN@EXAMPLE.COM',
            'account_type' => 'user',
        ]);

        // Search with lowercase
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'john']));

        $response->assertStatus(200);

        // Should find the user regardless of case
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 1;
        });

        // Search with mixed case in email
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => 'example.com']));

        $response->assertStatus(200);

        // Should find the user by email
        $response->assertViewHas('users', function ($users) {
            return $users->count() === 1;
        });
    }
}
