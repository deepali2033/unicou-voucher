<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BookService;
use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookServiceModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: BookService model has correct fillable attributes
     *
     * This test verifies that the BookService model can be
     * mass assigned with all the expected attributes.
     */
    public function test_book_service_model_has_correct_fillable_attributes()
    {
        // Arrange: Expected fillable attributes
        $expectedFillable = [
            'user_id',
            'service_id',
            'service_name',
            'customer_name',
            'email',
            'phone',
            'street_address',
            'city',
            'state',
            'zip_code',
            'bedrooms',
            'bathrooms',
            'extras',
            'frequency',
            'square_feet',
            'booking_date',
            'booking_time',
            'parking_info',
            'flexible_time',
            'entrance_info',
            'pets',
            'special_instructions',
            'price',
            'status',
        ];

        // Act: Get the actual fillable attributes
        $bookService = new BookService();
        $actualFillable = $bookService->getFillable();

        // Assert: Verify all expected attributes are fillable
        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $actualFillable, "Attribute '{$attribute}' should be fillable");
        }
    }

    /**
     * Test: BookService model casts attributes correctly
     *
     * This test verifies that the model properly casts
     * attributes to their expected types.
     */
    public function test_book_service_model_casts_attributes_correctly()
    {
        // Arrange & Act: Create a BookService with test data
        $bookService = BookService::create([
            'service_name' => 'Test Service',
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'bedrooms' => '3', // String input
            'bathrooms' => '2', // String input
            'booking_date' => '2024-02-15', // String input
            'price' => '99.99', // String input
            'status' => 'pending'
        ]);

        // Assert: Verify proper casting
        $this->assertIsInt($bookService->bedrooms, 'Bedrooms should be cast to integer');
        $this->assertIsInt($bookService->bathrooms, 'Bathrooms should be cast to integer');
        $this->assertInstanceOf(\Carbon\Carbon::class, $bookService->booking_date, 'Booking date should be cast to Carbon instance');
        $this->assertEquals(99.99, $bookService->price, 'Price should be cast to decimal');

        // Verify integer values
        $this->assertEquals(3, $bookService->bedrooms);
        $this->assertEquals(2, $bookService->bathrooms);
    }

    /**
     * Test: BookService belongs to User relationship
     *
     * This test verifies the relationship between
     * BookService and User models.
     */
    public function test_book_service_belongs_to_user_relationship()
    {
        // Arrange: Create a user and book service
        $user = User::factory()->create();
        $bookService = BookService::create([
            'user_id' => $user->id,
            'service_name' => 'Test Service',
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => '2024-02-15',
            'booking_time' => '10:00 AM',
            'status' => 'pending'
        ]);

        // Act: Load the relationship
        $relatedUser = $bookService->user;

        // Assert: Verify the relationship works
        $this->assertInstanceOf(User::class, $relatedUser);
        $this->assertEquals($user->id, $relatedUser->id);
        $this->assertEquals($user->email, $relatedUser->email);
    }

    /**
     * Test: BookService belongs to Service relationship
     *
     * This test verifies the relationship between
     * BookService and Service models if Service model exists.
     */
    public function test_book_service_belongs_to_service_relationship()
    {
        // Skip if Service model doesn't exist
        if (!class_exists(\App\Models\Service::class)) {
            $this->markTestSkipped('Service model does not exist');
        }

        // Arrange: Create a service and book service
        $service = Service::factory()->create([
            'name' => 'House Cleaning',
            'description' => 'Regular house cleaning service'
        ]);

        $bookService = BookService::create([
            'service_id' => $service->id,
            'service_name' => $service->name,
            'customer_name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567',
            'street_address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'zip_code' => '12345',
            'booking_date' => '2024-02-15',
            'booking_time' => '10:00 AM',
            'status' => 'pending'
        ]);

        // Act: Load the relationship
        $relatedService = $bookService->service;

        // Assert: Verify the relationship works
        $this->assertInstanceOf(Service::class, $relatedService);
        $this->assertEquals($service->id, $relatedService->id);
        $this->assertEquals($service->name, $relatedService->name);
    }

    /**
     * Test: BookService model validation creates record
     *
     * This test ensures that BookService can be created
     * with minimal required data.
     */
    public function test_book_service_model_creates_record_with_minimal_data()
    {
        // Arrange: Minimal required data
        $minimalData = [
            'service_name' => 'Basic Service',
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1-555-987-6543',
            'street_address' => '456 Main St',
            'city' => 'Anytown',
            'state' => 'Anystate',
            'zip_code' => '54321',
            'booking_date' => '2024-03-01',
            'booking_time' => '2:00 PM',
            'status' => 'pending'
        ];

        // Act: Create the record
        $bookService = BookService::create($minimalData);

        // Assert: Verify the record was created successfully
        $this->assertNotNull($bookService->id);
        $this->assertEquals('Basic Service', $bookService->service_name);
        $this->assertEquals('John Doe', $bookService->customer_name);
        $this->assertEquals('john@example.com', $bookService->email);
        $this->assertEquals('pending', $bookService->status);

        // Verify it exists in database
        $this->assertDatabaseHas('book_services', [
            'id' => $bookService->id,
            'customer_name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    /**
     * Test: BookService model handles optional fields
     *
     * This test verifies that the model correctly handles
     * optional fields that might be null or empty.
     */
    public function test_book_service_model_handles_optional_fields()
    {
        // Arrange: Data with some optional fields filled and others empty
        $data = [
            'service_name' => 'Premium Service',
            'customer_name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+1-555-111-2222',
            'street_address' => '789 Oak Ave',
            'city' => 'Somewhere',
            'state' => 'Texas',
            'zip_code' => '75001',
            'bedrooms' => 4,
            'bathrooms' => 3,
            'extras' => 'Window cleaning, carpet steam',
            'frequency' => 'Bi-weekly',
            'square_feet' => '2000',
            'booking_date' => '2024-03-15',
            'booking_time' => '1:00 PM',
            'parking_info' => 'Street parking only',
            'flexible_time' => null, // Optional field left null
            'entrance_info' => 'Side door',
            'pets' => null, // Optional field left null
            'special_instructions' => 'Please use eco-friendly products',
            'price' => 150.00,
            'status' => 'confirmed'
        ];

        // Act: Create the record
        $bookService = BookService::create($data);

        // Assert: Verify all fields are handled correctly
        $this->assertNotNull($bookService->id);
        $this->assertEquals(4, $bookService->bedrooms);
        $this->assertEquals(3, $bookService->bathrooms);
        $this->assertEquals('Window cleaning, carpet steam', $bookService->extras);
        $this->assertNull($bookService->flexible_time);
        $this->assertNull($bookService->pets);
        $this->assertEquals(150.00, $bookService->price);
        $this->assertEquals('confirmed', $bookService->status);
    }
}
