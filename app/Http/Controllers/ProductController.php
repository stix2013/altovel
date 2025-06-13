<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\WishlistItem; // Import WishlistItem
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // Import Auth

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $isWishlisted = false;
        if (Auth::check()) {
            $isWishlisted = WishlistItem::where('user_id', Auth::id())
                                       ->where('product_id', $product->id)
                                       ->exists();
        }

        $currentProductId = $product->id;

        // Fetch, for example, up to 4 random products, excluding the current one
        $relatedProducts = Product::where('id', '!=', $currentProductId)
                                    ->inRandomOrder()
                                    ->take(4) // Adjust the number as needed
                                    ->get();

        return Inertia::render('ProductDetailsPage', [
            'product' => $product->load(['reviews' => function ($query) {
                $query->with('user:id,name')->latest(); // Load user name with review, order by latest
            }]),
            'isWishlisted' => $isWishlisted, // Pass this to the component
            'relatedProducts' => $relatedProducts, // Pass related products
        ]);
    }
}
