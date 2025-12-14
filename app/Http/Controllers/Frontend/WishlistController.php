<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Toggle product in wishlist (add if not exists, remove if exists).
     */
    public function toggle(int $productId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to wishlist',
                'requires_login' => true
            ], 401);
        }

        $userId = Auth::id();
        
        $wishlistItem = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            // Remove from wishlist
            $wishlistItem->delete();
            $isInWishlist = false;
            $message = 'Product removed from wishlist';
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $isInWishlist = true;
            $message = 'Product added to wishlist';
        }

        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_in_wishlist' => $isInWishlist,
            'wishlist_count' => $wishlistCount
        ]);
    }

    /**
     * Get wishlist count for authenticated user.
     */
    public function count(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Wishlist::where('user_id', Auth::id())->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Check if product is in wishlist.
     */
    public function check(int $productId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['is_in_wishlist' => false]);
        }

        $isInWishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();
        
        return response()->json(['is_in_wishlist' => $isInWishlist]);
    }

    /**
     * Display all wishlist products for authenticated user.
     */
    public function index(): \Illuminate\View\View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your wishlist');
        }

        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with(['product.category', 'product.media', 'product.variants'])
            ->latest()
            ->get();

        // Filter out wishlist items where product was deleted
        $wishlists = $wishlists->filter(function($wishlist) {
            return $wishlist->product !== null;
        });

        return view('frontend.pages.wishlist', compact('wishlists'));
    }
}
