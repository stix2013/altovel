<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Database\Factories\ProductFactory; // Ensure ProductFactory is imported if used directly for reviews
use Database\Factories\ReviewFactory;  // Ensure ReviewFactory is imported
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_method_displays_product_details_page(): void
    {
        $user = User::factory()->create();
        // Create a product and explicitly associate reviews with it
        $product = Product::factory()->create();
        ReviewFactory::new()->for($product)->count(2)->create(['user_id' => $user->id]); // Ensure reviews are for this product

        // Create a related product to be picked up by the controller logic
        // Ensure it's distinct enough if your related product logic is specific
        $relatedProduct = Product::factory()->create();

        // Make the main product wishlisted by the user
        WishlistItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)->get(route('products.show', $product));

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('ProductDetailsPage')
            ->has('product', fn (Assert $prop) => $prop
                ->where('id', $product->id)
                ->where('name', $product->name)
                ->has('reviews', 2) // Check number of reviews
                ->etc()
            )
            ->where('isWishlisted', true)
            ->has('relatedProducts') // Check that relatedProducts prop exists
            ->has('auth.user', fn(Assert $prop) => $prop
                ->where('id', $user->id)
                ->etc()
            )
            // Example to check if at least one related product is present, if logic guarantees it.
            // ->has('relatedProducts.0', fn (Assert $prop) => $prop->where('id', $relatedProduct->id)->etc())
            // Or simply check if the array is not empty if products exist
             ->has('relatedProducts') // Check the prop exists
             ->whereType('relatedProducts', 'array') // Check it's an array
             ->count('relatedProducts', 1) // Check it has 1 item (based on current test setup)
             ->has('relatedProducts.0', fn (Assert $item) => $item // Check the first related item
                ->where('id', $relatedProduct->id)
                ->etc()
            )
        );
    }
}
