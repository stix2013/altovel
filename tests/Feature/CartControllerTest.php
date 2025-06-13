<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User; // Though cart is session based, user might be logged in
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_product_to_cart(): void
    {
        $product = Product::factory()->create(['price' => 29.99, 'name' => 'Test Item']);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product added to cart!');
        $response->assertSessionHas('cart', [
            $product->id => [
                'name' => 'Test Item',
                'price' => 29.99,
                'quantity' => 1,
            ]
        ]);
    }

    public function test_can_add_multiple_quantity_of_product_to_cart(): void
    {
        $product = Product::factory()->create(['price' => 15.00, 'name' => 'Another Item']);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product added to cart!');
        $response->assertSessionHas('cart.'.$product->id.'.quantity', 3);
    }

    public function test_adding_same_product_increments_quantity(): void
    {
        $product = Product::factory()->create();

        // Add product first time
        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Add same product again
        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('cart.'.$product->id.'.quantity', 3); // 1 + 2 = 3
    }

    public function test_add_to_cart_requires_product_id(): void
    {
        $response = $this->post(route('cart.add'), ['quantity' => 1]);
        $response->assertSessionHasErrors('product_id');
    }

    public function test_add_to_cart_requires_valid_product_id(): void
    {
        $response = $this->post(route('cart.add'), [
            'product_id' => 999, // Non-existent product
            'quantity' => 1,
        ]);
        $response->assertSessionHasErrors('product_id');
    }

    public function test_add_to_cart_quantity_must_be_integer(): void
    {
        $product = Product::factory()->create();
        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 'not-an-integer',
        ]);
        $response->assertSessionHasErrors('quantity');
    }

    public function test_add_to_cart_quantity_must_be_at_least_1(): void
    {
        $product = Product::factory()->create();
        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 0,
        ]);
        $response->assertSessionHasErrors('quantity');
    }

    public function test_add_to_cart_defaults_quantity_to_1_if_not_provided(): void
    {
        $product = Product::factory()->create();
        $response = $this->post(route('cart.add'), ['product_id' => $product->id]);
        $response->assertSessionHas('cart.'.$product->id.'.quantity', 1);
    }
}
