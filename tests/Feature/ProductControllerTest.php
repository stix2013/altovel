<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_product_index_page_is_rendered(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        // $response->assertInertia(fn (Assert $page) => $page->component('products/Index'));
    }

    public function test_product_can_be_created(): void
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 99.99,
            'image_url' => 'http://example.com/image.png',
        ];

        $response = $this->post(route('products.store'), $productData);

        // $response->assertRedirect(route('products.index'));
        $response->assertStatus(302); // Or appropriate redirect status
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_product_show_page_is_rendered(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(200);
        // $response->assertInertia(fn (Assert $page) => $page
        //    ->component('products/Show')
        //    ->has('product', fn (Assert $p) => $p
        //        ->where('id', $product->id)
        //        ->etc()
        //    )
        // );
    }

    public function test_product_edit_page_is_rendered(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.edit', $product));
        $response->assertStatus(200);
        // $response->assertInertia(fn (Assert $page) => $page->component('products/Edit'));
    }


    public function test_product_can_be_updated(): void
    {
        $product = Product::factory()->create();
        $updatedData = [
            'name' => 'Updated Product Name',
            'description' => 'Updated product description.',
            'price' => 129.99,
        ];

        $response = $this->put(route('products.update', $product), $updatedData);

        // $response->assertRedirect(route('products.index'));
        $response->assertStatus(302); // Or appropriate redirect status
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Product Name']);
    }

    public function test_product_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        // $response->assertRedirect(route('products.index'));
        $response->assertStatus(302); // Or appropriate redirect status
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
