<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // To fetch product details

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $quantity = $request->input('quantity', 1);
        $product = Product::findOrFail($productId); // Ensure product exists

        $cart = $request->session()->get('cart', []);

        // If product already in cart, increment quantity, else add new
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                // Store other details if needed, e.g., a small image
            ];
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart!');
        // Or return redirect()->route('cart.view'); if a cart page exists
    }

    // Optional: A method to view the cart
    // public function view(Request $request)
    // {
    //     $cart = $request->session()->get('cart', []);
    //     return Inertia::render('CartPage', ['cartItems' => $cart]);
    // }
}
