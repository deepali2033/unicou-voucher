<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class CategoryModalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that categories display with modal functionality on home page.
     */
    public function test_categories_display_with_modal_functionality()
    {
        // Create test categories
        $category1 = Category::create([
            'name' => 'Test Category 1',
            'description' => 'Test description 1',
            'Image' => 'images/test1.jpg',
            'is_active' => true
        ]);

        $category2 = Category::create([
            'name' => 'Test Category 2',
            'description' => 'Test description 2',
            'Image' => 'images/test2.jpg',
            'is_active' => true
        ]);

        // Visit home page
        $response = $this->get('/');

        $response->assertStatus(200);

        // Assert categories are displayed
        $response->assertSee('Test Category 1');
        $response->assertSee('Test Category 2');

        // Assert modal structure is present
        $response->assertSee('id="categoryModal"', false);
        $response->assertSee('class="category-modal"', false);
        $response->assertSee('id="modalCategoryImage"', false);
        $response->assertSee('id="modalCategoryName"', false);
        $response->assertSee('id="modalJobsLink"', false);
        $response->assertSee('id="modalServicesLink"', false);

        // Assert JavaScript functions are present
        $response->assertSee('function openCategoryModal(element)', false);
        $response->assertSee('function closeCategoryModal()', false);

        // Assert clickable elements have correct data attributes
        $response->assertSee('data-category-id="' . $category1->id . '"', false);
        $response->assertSee('data-category-name="' . $category1->name . '"', false);
        $response->assertSee('onclick="openCategoryModal(this)"', false);

        // Assert modal buttons text
        $response->assertSee('Find a Job');
        $response->assertSee('Find a Service');
    }

    /**
     * Test that inactive categories are not displayed.
     */
    public function test_inactive_categories_not_displayed()
    {
        // Create active and inactive categories
        $activeCategory = Category::create([
            'name' => 'Active Category',
            'description' => 'Active description',
            'is_active' => true
        ]);

        $inactiveCategory = Category::create([
            'name' => 'Inactive Category',
            'description' => 'Inactive description',
            'is_active' => false
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Active Category');
        $response->assertDontSee('Inactive Category');
    }

    /**
     * Test that modal CSS styles are included.
     */
    public function test_modal_styles_are_included()
    {
        Category::create([
            'name' => 'Test Category',
            'is_active' => true
        ]);

        $response = $this->get('/');

        // Assert modal CSS classes are present
        $response->assertSee('.category-modal {', false);
        $response->assertSee('.category-modal-overlay {', false);
        $response->assertSee('.category-modal-content {', false);
        $response->assertSee('.category-modal-btn {', false);
        $response->assertSee('position: fixed;', false);
        $response->assertSee('z-index: 9999;', false);
    }

    /**
     * Test fallback content when no categories exist.
     */
    public function test_fallback_content_when_no_categories()
    {
        // Don't create any categories
        $response = $this->get('/');

        $response->assertStatus(200);

        // Assert fallback content is displayed
        $response->assertSee('Household Services');
        $response->assertSee('Job Agency Services');
        $response->assertSee('/services/householdservices', false);
        $response->assertSee('/services/agencysevices', false);
    }

    /**
     * Test that categories use default image when no image is set.
     */
    public function test_categories_use_default_image_when_none_set()
    {
        $category = Category::create([
            'name' => 'No Image Category',
            'description' => 'No image description',
            'Image' => null,
            'is_active' => true
        ]);

        $response = $this->get('/');

        // Assert default image path is used
        $response->assertSee('data-category-image="/images/service/2.png"', false);
    }
}
