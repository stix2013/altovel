<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function addOrRemove(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $userId = Auth::id();
        $productId = $request->product_id;

        $wishlistItem = WishlistItem::where('user_id', $userId)
                                  ->where('product_id', $productId)
                                  ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $message = 'Product removed from wishlist.';
        } else {
            WishlistItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $message = 'Product added to wishlist.';
        }

        return back()->with('success', $message);
    }
    // Optional: Method to view wishlist
    // public function index() {
    //     $wishlistItems = Auth::user()->wishlistItems()->with('product')->get();
    //     return Inertia::render('WishlistPage', ['items' => $wishlistItems]);
    // }
}
