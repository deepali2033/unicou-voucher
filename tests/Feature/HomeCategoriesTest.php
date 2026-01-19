<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;

class HomeCategoriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for services if needed
        $this->user = User::factory()->create([
            'role' => 'freelancer'
        ]);
    }

    /** @test */
    public function home_page_shows_active_categories()
    {
        // Arrange: Create some active and inactive categories
        $activeCategory1 = Category::factory()->create([
            'name' => 'House Cleaning',
            'description' => 'Professional house cleaning services',
            'is_active' => true,
            'Image' => 'images/categories/house-cleaning.jpg'
        ]);

        $activeCategory2 = Category::factory()->create([
            'name' => 'Lawn Care',
            'description' => 'Professional lawn maintenance',
            'is_active' => true,
            'Image' => 'images/categories/lawn-care.jpg'
        ]);

        $inactiveCategory = Category::factory()->create([
            'name' => 'Disabled Service',
            'is_active' => false
        ]);

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Check that only active categories are displayed
        $response->assertStatus(200);
        $response->assertSee('House Cleaning');
        $response->assertSee('Lawn Care');
        $response->assertSee('Professional house cleaning services');
        $response->assertSee('Professional lawn maintenance');
        $response->assertDontSee('Disabled Service');
    }

    /** @test */
    public function categories_display_proper_responsive_layout()
    {
        // Arrange: Create multiple categories to test responsive layout
        Category::factory()->count(4)->create(['is_active' => true]);

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Check that responsive CSS classes are present
        $response->assertStatus(200);
        $response->assertSee('flex: 0 0 calc(33.333% - 20px)');
        $response->assertSee('max-width: calc(33.333% - 20px)');
        $response->assertSee('margin: 10px');

        // Check for responsive CSS rules in the style section
        $response->assertSee('@media (min-width: 992px)');
        $response->assertSee('@media (max-width: 991px) and (min-width: 768px)');
        $response->assertSee('@media (max-width: 767px)');
    }

    /** @test */
    public function categories_link_to_services_correctly()
    {
        // Arrange: Create a category with and without services
        $categoryWithServices = Category::factory()->create(['is_active' => true]);
        $categoryWithoutServices = Category::factory()->create(['is_active' => true]);

        // Create a service for the first category
        Service::factory()->create([
            'category_id' => $categoryWithServices->id,
            'is_active' => true,
            'user_id' => $this->user->id
        ]);

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Check that links are generated correctly
        $response->assertStatus(200);
        $response->assertSee('/services?category=' . $categoryWithServices->id);
        $response->assertSee('/services'); // Fallback for category without services
    }

    /** @test */
    public function inactive_categories_are_excluded()
    {
        // Arrange: Create both active and inactive categories
        $activeCategory = Category::factory()->create([
            'name' => 'Active Category',
            'is_active' => true
        ]);

        $inactiveCategory = Category::factory()->create([
            'name' => 'Inactive Category',
            'is_active' => false
        ]);

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Only active category should be visible
        $response->assertStatus(200);
        $response->assertSee('Active Category');
        $response->assertDontSee('Inactive Category');
    }

    /** @test */
    public function no_categories_shows_fallback_content()
    {
        // Arrange: Ensure no categories exist (RefreshDatabase handles this)
        $this->assertEquals(0, Category::count());

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Fallback content should be displayed
        $response->assertStatus(200);
        $response->assertSee('Household Services');
        $response->assertSee('Job Agency Services');
        $response->assertSee('/services/householdservices');
        $response->assertSee('/services/agencysevices');
    }

    /** @test */
    public function categories_with_no_image_show_fallback()
    {
        // Arrange: Create a category without an image
        $categoryNoImage = Category::factory()->create([
            'name' => 'No Image Category',
            'is_active' => true,
            'Image' => null
        ]);

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Fallback image should be used
        $response->assertStatus(200);
        $response->assertSee('/images/service/2.png');
    }

    /** @test */
    public function categories_ordered_by_creation_date()
    {
        // Arrange: Create categories with specific timestamps
        $older = Category::factory()->create([
            'name' => 'Older Category',
            'is_active' => true,
            'created_at' => now()->subDays(2)
        ]);

        $newer = Category::factory()->create([
            'name' => 'Newer Category',
            'is_active' => true,
            'created_at' => now()->subDays(1)
        ]);

        // Act: Visit the home page and get the response content
        $response = $this->get('/');
        $content = $response->getContent();

        // Assert: Newer category should appear before older one
        $response->assertStatus(200);
        $newerPos = strpos($content, 'Newer Category');
        $olderPos = strpos($content, 'Older Category');

        $this->assertLessThan($olderPos, $newerPos, 'Categories should be ordered by creation date descending');
    }

    /** @test */
    public function database_error_handling()
    {
        // Arrange: Create a category first
        Category::factory()->create(['is_active' => true]);

        // Act & Assert: Test that the page still loads even if there are database issues
        // This is more of a smoke test to ensure the page doesn't crash
        $response = $this->get('/');
        $response->assertStatus(200);

        // Ensure the view variables are passed correctly
        $response->assertViewHas('categories');
        $response->assertViewHas('services');
    }

    /** @test */
    public function categories_have_unique_element_ids()
    {
        // Arrange: Create multiple categories
        $category1 = Category::factory()->create(['is_active' => true]);
        $category2 = Category::factory()->create(['is_active' => true]);

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Each category should have unique IDs based on their database ID
        $response->assertStatus(200);
        $response->assertSee('category-' . $category1->id);
        $response->assertSee('category-' . $category2->id);
        $response->assertSee('img-' . $category1->id);
        $response->assertSee('img-' . $category2->id);
        $response->assertSee('image-' . $category1->id);
        $response->assertSee('image-' . $category2->id);
    }

    /** @test */
    public function categories_have_proper_animation_delays()
    {
        // Arrange: Create multiple categories to test animation timing
        Category::factory()->count(3)->create(['is_active' => true]);

        // Act: Visit the home page
        $response = $this->get('/');

        // Assert: Animation delays should increment by 100ms
        $response->assertStatus(200);
        $response->assertSee('animation_delay&quot;:200'); // First category
        $response->assertSee('animation_delay&quot;:300'); // Second category
        $response->assertSee('animation_delay&quot;:400'); // Third category
    }
}
