<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_product_to_wishlist(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->post(route('wishlist.toggle'), [
            'product_id' => $product->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product added to wishlist.');
        $this->assertDatabaseHas('wishlist_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_authenticated_user_can_remove_product_from_wishlist(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        WishlistItem::factory()->create(['user_id' => $user->id, 'product_id' => $product->id]);

        $this->assertDatabaseHas('wishlist_items', ['user_id' => $user->id, 'product_id' => $product->id]);

        $response = $this->actingAs($user)->post(route('wishlist.toggle'), [
            'product_id' => $product->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product removed from wishlist.');
        $this->assertDatabaseMissing('wishlist_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_toggling_wishlist_item_twice_reverts_to_original_state(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // First toggle: Add
        $this->actingAs($user)->post(route('wishlist.toggle'), ['product_id' => $product->id]);
        $this->assertDatabaseHas('wishlist_items', ['user_id' => $user->id, 'product_id' => $product->id]);

        // Second toggle: Remove
        $this->actingAs($user)->post(route('wishlist.toggle'), ['product_id' => $product->id]);
        $this->assertDatabaseMissing('wishlist_items', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    public function test_wishlist_toggle_requires_product_id(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('wishlist.toggle'), []);
        $response->assertSessionHasErrors('product_id');
    }

    public function test_wishlist_toggle_requires_valid_product_id(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('wishlist.toggle'), ['product_id' => 999]); // Non-existent
        $response->assertSessionHasErrors('product_id');
    }

    public function test_unauthenticated_user_cannot_toggle_wishlist(): void
    {
        $product = Product::factory()->create();
        $response = $this->post(route('wishlist.toggle'), ['product_id' => $product->id]);
        $response->assertRedirect(route('login')); // Assuming auth middleware redirects to login
        $this->assertDatabaseCount('wishlist_items', 0);
    }

    public function test_wishlist_item_is_unique_per_user_and_product(): void
    {
        // This is implicitly tested by the add/remove logic,
        // but a direct attempt to create a duplicate via controller would be good.
        // The database unique constraint should prevent duplicates regardless.
        $user = User::factory()->create();
        $product = Product::factory()->create();

        WishlistItem::create(['user_id' => $user->id, 'product_id' => $product->id]);
        $this->assertCount(1, WishlistItem::all());

        // Attempt to add again via controller - it should be removed by the toggle logic
        $response = $this->actingAs($user)->post(route('wishlist.toggle'), [
            'product_id' => $product->id,
        ]);
        $response->assertSessionHas('success', 'Product removed from wishlist.');
        $this->assertCount(0, WishlistItem::all());
    }
}
