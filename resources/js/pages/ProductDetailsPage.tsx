import React, { useState } from 'react'; // Import useState
import { Head, useForm } from '@inertiajs/react'; // Assuming you use Inertia for Head management and useForm

// Define an interface for the product prop
interface User { // Add User interface if not already defined
    id: number;
    name: string;
}

interface Review {
    id: number;
    user: User; // Or just user_id and fetch user details separately
    rating: number;
    comment: string;
    created_at: string; // Or Date
}

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    images: string[]; // Assuming images is an array of URLs
    specifications: Record<string, string>; // Or a more specific type
    variations: Record<string, string[]>; // Or a more specific type
    stock_status: string;
    reviews?: Review[]; // Make it optional or ensure it's always passed
    // Add other fields as per your Product model
}

interface ProductDetailsPageProps {
    product: Product;
    isWishlisted?: boolean;
    auth: any;
    relatedProducts?: Product[]; // Add relatedProducts
}

// In a real app, you would have a more sophisticated star rating component
const StarRating: React.FC<{ rating: number; setRating?: (rating: number) => void; readonly?: boolean }> = ({ rating, setRating, readonly }) => {
    return (
        <div>
            {[1, 2, 3, 4, 5].map((star) => (
                <span
                    key={star}
                    className={`cursor-pointer text-2xl ${star <= rating ? 'text-yellow-400' : 'text-gray-300'}`}
                    onClick={() => !readonly && setRating && setRating(star)}
                >
                    ★
                </span>
            ))}
        </div>
    );
};


const ProductDetailsPage: React.FC<ProductDetailsPageProps> = ({ product, isWishlisted: initialIsWishlisted, auth }) => {
    const [quantity, setQuantity] = useState(1); // State for quantity
    const [currentIsWishlisted, setIsWishlisted] = useState(initialIsWishlisted);

    const { post: addToCart, processing: addingToCart, errors: cartErrors, recentlySuccessful: addedToCartSuccess } = useForm({
        product_id: product.id,
        quantity: quantity, // Initialize with current quantity state
    });

    const handleAddToCart = (e: React.FormEvent) => {
        e.preventDefault();
        // Update quantity in form data just before posting
        const dataToSubmit = { product_id: product.id, quantity: quantity };
        addToCart(route('cart.add', dataToSubmit), {
            preserveScroll: true,
            onSuccess: () => {
                // setQuantity(1); // Reset quantity after adding
            }
        });
    };

    const { post: toggleWishlist, processing: updatingWishlist, recentlySuccessful: wishlistToggledSuccess } = useForm({ product_id: product.id });

    const handleToggleWishlist = () => {
        toggleWishlist(route('wishlist.toggle'), {
            preserveScroll: true,
            onSuccess: () => {
                setIsWishlisted(!currentIsWishlisted); // Toggle local state on success
            }
        });
    };


    // const { data, setData, post, errors, processing, reset } = useForm({
    //     product_id: product.id,
    //     rating: 0,
    //     comment: '',
    // });

    // const submitReview = (e: React.FormEvent) => {
    //     e.preventDefault();
    //     post(route('reviews.store'), {
    //         onSuccess: () => reset(),
    //     });
    // };

    return (
        <>
            <Head title={product.name} />
            <div className="container mx-auto p-4">
                <h1 className="text-2xl font-bold mb-4">{product.name}</h1>
                <p className="mb-4">{product.description}</p>
                {/* Price and Stock Status */}
                <div className="flex items-center justify-between mb-6">
                    <div className="text-3xl font-bold text-blue-600">
                        ${product.price.toFixed(2)}
                    </div>
                    <div className={`px-3 py-1 rounded-full text-sm font-semibold ${product.stock_status === 'in stock' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`}>
                        {product.stock_status.toUpperCase()}
                    </div>
                </div>

                {/* Add to Cart Section */}
                <form onSubmit={handleAddToCart} className="flex items-center space-x-4 mb-8">
                    <div>
                        <label htmlFor="quantity" className="sr-only">Quantity</label>
                        <input
                            type="number"
                            id="quantity"
                            value={quantity}
                            onChange={(e) => setQuantity(parseInt(e.target.value, 10))}
                            min="1"
                            className="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                    </div>
                    <button
                        type="submit"
                        disabled={addingToCart || product.stock_status !== 'in stock'}
                        className="px-6 py-2.5 bg-blue-600 text-white font-medium text-sm uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out disabled:opacity-50"
                    >
                        {addingToCart ? 'Adding...' : 'Add to Cart'}
                    </button>
                    { auth.user && ( // Only show if user is logged in
                        <button
                            type="button" // Important: type="button" to prevent form submission if inside another form
                            onClick={handleToggleWishlist}
                            disabled={updatingWishlist}
                            className={`p-2.5 border rounded-md ml-4 transition-colors duration-150 ease-in-out
                                        ${currentIsWishlisted ? 'bg-pink-100 border-pink-300 text-pink-600 hover:bg-pink-200'
                                                            : 'bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200'}`}
                            aria-label={currentIsWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd" />
                            </svg>
                        </button>
                    )}
                </form>
                {addedToCartSuccess && <div className="mb-4 text-green-600">Product added to cart!</div>}
                {wishlistToggledSuccess && <div className="mb-4 text-green-600">Wishlist updated!</div>}
                {cartErrors.product_id && <div className="mb-4 text-red-600">{cartErrors.product_id}</div>}
                {cartErrors.quantity && <div className="mb-4 text-red-600">{cartErrors.quantity}</div>}


                {/* Display Reviews */}
                {product.reviews && product.reviews.length > 0 ? (
                    <div className="mt-8">
                        <h2 className="text-xl font-semibold mb-4">Customer Reviews</h2>
                        {product.reviews.map((review) => (
                            <div key={review.id} className="mb-4 p-4 border rounded-lg shadow-sm">
                                <div className="flex items-center mb-1">
                                    <span className="font-semibold mr-2">{review.user?.name || 'Anonymous'}</span>
                                    <StarRating rating={review.rating} readonly />
                                </div>
                                <p className="text-gray-700 mb-1">{review.comment}</p>
                                <p className="text-sm text-gray-500">{new Date(review.created_at).toLocaleDateString()}</p>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="mt-8 text-gray-600">No reviews yet. Be the first to review!</p>
                )}

                {/* Review Form (Simplified - requires useForm and route setup) */}
                {/*
                <form onSubmit={submitReview} className="mt-8 p-4 border rounded-lg shadow-sm bg-white">
                    <h2 className="text-xl font-semibold mb-4">Write a Review</h2>
                    <div className="mb-4">
                        <label htmlFor="rating" className="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                        <StarRating rating={data.rating} setRating={(r) => setData('rating', r)} />
                        {errors.rating && <p className="text-red-500 text-xs mt-1">{errors.rating}</p>}
                    </div>
                    <div className="mb-4">
                        <label htmlFor="comment" className="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                        <textarea
                            id="comment"
                            value={data.comment}
                            onChange={(e) => setData('comment', e.target.value)}
                            rows={4}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        ></textarea>
                        {errors.comment && <p className="text-red-500 text-xs mt-1">{errors.comment}</p>}
                    </div>
                    <button
                        type="submit"
                        disabled={processing}
                        className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        {processing ? 'Submitting...' : 'Submit Review'}
                    </button>
                </form>
                */}

                {/* Placeholder for other sections like detailed specs, related products etc. */}
                {/* <div className="mt-12 p-6 border-t border-gray-200">
                    <h2 className="text-xl font-semibold mb-3">Product Specifications</h2>
                     Render specifications here
                </div> */}

                {/* Related Products Section */}
                {relatedProducts && relatedProducts.length > 0 && (
                    <div className="mt-12 pt-8 border-t border-gray-200">
                        <h2 className="text-2xl font-bold mb-6 text-gray-800">You Might Also Like</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            {relatedProducts.map((relatedProduct) => (
                                <div key={relatedProduct.id} className="border rounded-lg shadow-sm overflow-hidden transition-shadow duration-300 hover:shadow-lg">
                                    {relatedProduct.images && relatedProduct.images.length > 0 ? (
                                        <a href={route('products.show', relatedProduct.id)}>
                                            <img
                                                src={relatedProduct.images[0]} // Assuming first image is representative
                                                alt={relatedProduct.name}
                                                className="w-full h-48 object-cover" // Fixed height for consistency
                                            />
                                        </a>
                                    ) : (
                                        <div className="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                            No Image
                                        </div>
                                    )}
                                    <div className="p-4">
                                        <h3 className="font-semibold text-lg mb-1 truncate" title={relatedProduct.name}>
                                            <a href={route('products.show', relatedProduct.id)} className="hover:text-blue-600">
                                                {relatedProduct.name}
                                            </a>
                                        </h3>
                                        <p className="text-gray-700 font-bold text-md">${relatedProduct.price.toFixed(2)}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </>
    );
};

export default ProductDetailsPage;
