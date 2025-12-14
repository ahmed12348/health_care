<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Helpers\TranslationHelper;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $key => $item) {
            $product = Product::with(['category', 'variants', 'media'])->find($item['product_id']);
            if ($product) {
                $variant = null;
                $price = $product->price;
                
                if (!empty($item['variant_id'])) {
                    $variant = $product->variants->where('id', $item['variant_id'])->first();
                    if ($variant) {
                        $price = $variant->variant_price ?? $product->price;
                    }
                }

                $quantity = $item['quantity'] ?? 1;
                $subtotal = $price * $quantity;
                $total += $subtotal;

                $cartItems[] = [
                    'key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            }
        }

        // Get user loyalty points if logged in
        $userLoyaltyPoints = Auth::check() ? Auth::user()->loyalty_points : 0;
        
        return view('frontend.pages.cart', compact('cartItems', 'total', 'userLoyaltyPoints'));
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $variantId = $request->input('variant_id');

        $product = Product::findOrFail($productId);

        // Check stock
        $stock = $product->stock_quantity ?? 0;
        if ($variantId) {
            $variant = $product->variants->where('id', $variantId)->first();
            if ($variant) {
                $stock = $variant->variant_stock_quantity ?? 0;
            }
        }

        if ($stock < $quantity) {
            return redirect()->back()
                ->with('error', __('Insufficient stock available.'));
        }

        $cart = session()->get('cart', []);
        
        // Generate unique key for cart item
        $key = $productId . '_' . ($variantId ?? 'default');
        
        // Check if item already exists in cart
        if (isset($cart[$key])) {
            $newQuantity = $cart[$key]['quantity'] + $quantity;
            if ($newQuantity > $stock) {
                return redirect()->back()
                    ->with('error', __('Cannot add more items. Stock limit reached.'));
            }
            $cart[$key]['quantity'] = $newQuantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('frontend.cart.index')
            ->with('success', __('Product added to cart successfully.'));
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, string $key): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        
        if (!isset($cart[$key])) {
            return redirect()->route('frontend.cart.index')
                ->with('error', TranslationHelper::trans('cart_item_not_found'));
        }

        $quantity = $request->input('quantity');
        $item = $cart[$key];
        
        $product = Product::findOrFail($item['product_id']);
        $stock = $product->stock_quantity ?? 0;
        
        if ($item['variant_id']) {
            $variant = $product->variants->where('id', $item['variant_id'])->first();
            if ($variant) {
                $stock = $variant->variant_stock_quantity ?? 0;
            }
        }

        if ($quantity > $stock) {
            return redirect()->route('frontend.cart.index')
                ->with('error', __('Insufficient stock available.'));
        }

        $cart[$key]['quantity'] = $quantity;
        session()->put('cart', $cart);

        return redirect()->route('frontend.cart.index')
            ->with('success', TranslationHelper::trans('cart_updated_successfully'));
    }

    /**
     * Update cart item variant.
     */
    public function updateVariant(Request $request, string $key): RedirectResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        $cart = session()->get('cart', []);
        
        if (!isset($cart[$key])) {
            return redirect()->route('frontend.cart.index')
                ->with('error', TranslationHelper::trans('cart_item_not_found'));
        }

        $item = $cart[$key];
        $product = Product::findOrFail($item['product_id']);
        $variantId = $request->input('variant_id');
        
        // Verify variant belongs to product
        $variant = $product->variants->where('id', $variantId)->first();
        if (!$variant) {
            return redirect()->route('frontend.cart.index')
                ->with('error', TranslationHelper::trans('invalid_variant'));
        }

        // Check stock
        $stock = $variant->variant_stock_quantity ?? 0;
        $quantity = $item['quantity'] ?? 1;
        
        if ($stock < $quantity) {
            return redirect()->route('frontend.cart.index')
                ->with('error', __('Insufficient stock available.'));
        }

        // Remove old item and create new one with variant
        unset($cart[$key]);
        
        // Generate new key with variant
        $newKey = $item['product_id'] . '_' . $variantId;
        
        // Check if item with this variant already exists
        if (isset($cart[$newKey])) {
            // If exists, update quantity
            $cart[$newKey]['quantity'] += $quantity;
        } else {
            // Create new item
            $cart[$newKey] = [
                'product_id' => $item['product_id'],
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('frontend.cart.index')
            ->with('success', TranslationHelper::trans('variant_selected_successfully'));
    }

    /**
     * Remove item from cart.
     */
    public function remove(string $key): RedirectResponse
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
            
            return redirect()->route('frontend.cart.index')
                ->with('success', __('Item removed from cart.'));
        }

        return redirect()->route('frontend.cart.index')
            ->with('error', TranslationHelper::trans('cart_item_not_found'));
    }

    /**
     * Clear entire cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        
        return redirect()->route('frontend.cart.index')
            ->with('success', __('Cart cleared successfully.'));
    }

    /**
     * Get cart items with product details (helper method for other controllers).
     * 
     * @return array
     */
    public static function getCartItems(): array
    {
        $cart = session()->get('cart', []);
        $items = [];

        foreach ($cart as $key => $item) {
            $product = Product::with(['category', 'variants', 'media'])->find($item['product_id']);
            if ($product) {
                $variant = null;
                $price = $product->price;
                
                if (!empty($item['variant_id'])) {
                    $variant = $product->variants->where('id', $item['variant_id'])->first();
                    if ($variant) {
                        $price = $variant->variant_price ?? $product->price;
                    }
                }

                $items[] = [
                    'key' => $key,
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $item['quantity'] ?? 1,
                    'variant_id' => $item['variant_id'] ?? null,
                    'price' => $price,
                ];
            }
        }

        return $items;
    }
}

