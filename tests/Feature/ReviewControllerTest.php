<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_submit_review(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $reviewData = [
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'This is a great product!',
        ];

        $response = $this->actingAs($user)->post(route('reviews.store'), $reviewData);

        $response->assertRedirect(); // Or assert session has 'success'
        // Check for session success message
        $response->assertSessionHas('success', 'Review submitted successfully!');

        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'This is a great product!',
        ]);
    }

    public function test_review_requires_rating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $reviewData = [
            'product_id' => $product->id,
            'comment' => 'This product is okay.',
            // 'rating' is missing
        ];

        $response = $this->actingAs($user)->post(route('reviews.store'), $reviewData);

        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseMissing('reviews', [
            'product_id' => $product->id,
            'comment' => 'This product is okay.',
        ]);
    }

    public function test_review_requires_comment(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $reviewData = [
            'product_id' => $product->id,
            'rating' => 4,
            // 'comment' is missing
        ];

        $response = $this->actingAs($user)->post(route('reviews.store'), $reviewData);

        $response->assertSessionHasErrors('comment');
        $this->assertDatabaseMissing('reviews', [
            'product_id' => $product->id,
            'rating' => 4,
        ]);
    }

    public function test_review_rating_must_be_between_1_and_5(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $reviewDataInvalidLow = [
            'product_id' => $product->id,
            'rating' => 0,
            'comment' => 'Too low rating.',
        ];
        $reviewDataInvalidHigh = [
            'product_id' => $product->id,
            'rating' => 6,
            'comment' => 'Too high rating.',
        ];

        $responseLow = $this->actingAs($user)->post(route('reviews.store'), $reviewDataInvalidLow);
        $responseLow->assertSessionHasErrors('rating');

        $responseHigh = $this->actingAs($user)->post(route('reviews.store'), $reviewDataInvalidHigh);
        $responseHigh->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_unauthenticated_user_cannot_submit_review(): void
    {
        $product = Product::factory()->create();
        $reviewData = [
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Attempting review as guest.',
        ];

        $response = $this->post(route('reviews.store'), $reviewData);

        // Expect redirect to login page since middleware('auth') is applied
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('reviews', $reviewData);
    }
}
