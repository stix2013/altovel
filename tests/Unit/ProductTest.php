<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Review; // If testing review relationship
use Illuminate\Foundation\Testing\RefreshDatabase; // Useful if your tests interact with DB
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase; // Resets DB for each test

    public function test_product_can_be_created(): void
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
        ]);
        $this->assertModelExists($product);
        $this->assertEquals('Test Product', $product->name);
    }

    public function test_product_has_reviews_relationship(): void
    {
        $product = Product::factory()->create();
        Review::factory()->for($product)->count(3)->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $product->reviews);
        $this->assertCount(3, $product->reviews);
    }

    // Add test for casts if important (e.g., images, specifications are arrays)
    public function test_product_casts_attributes(): void
    {
        $product = Product::factory()->create([
            'images' => ['image1.jpg', 'image2.jpg'],
            'specifications' => ['color' => 'red'],
        ]);
        $this->assertIsArray($product->images);
        $this->assertIsArray($product->specifications);
    }
}
