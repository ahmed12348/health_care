<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Requests\OrderRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Show the checkout page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Use CartController to get cart items
        $cartItems = CartController::getCartItems();
        
        // Convert to products format (remove 'key' field)
        $products = array_map(function($item) {
            return [
                'product' => $item['product'],
                'variant' => $item['variant'],
                'quantity' => $item['quantity'],
                'variant_id' => $item['variant_id'],
                'price' => $item['price'],
            ];
        }, $cartItems);

        // Fallback: Get product from query parameter if cart is empty (for backward compatibility)
        if (empty($products)) {
            $productId = $request->query('product_id');
            $quantity = $request->query('quantity', 1);
            $variantId = $request->query('variant_id');
            
            if ($productId) {
                $product = Product::with(['category', 'variants', 'media'])->find($productId);
                if ($product) {
                    $price = $product->price;
                    $variant = null;
                    if ($variantId) {
                        $variant = $product->variants->where('id', $variantId)->first();
                        if ($variant) {
                            $price = $variant->variant_price ?? $product->price;
                        }
                    }
                    
                    $products[] = [
                        'product' => $product,
                        'variant' => $variant,
                        'quantity' => (int)$quantity,
                        'variant_id' => $variantId ? (int)$variantId : null,
                        'price' => $price,
                    ];
                }
            }
        }

        // If no products, redirect to cart page
        if (empty($products)) {
            return redirect()->route('frontend.cart.index')
                ->with('error', 'Please add a product to checkout');
        }

        // Check if any product has variants but no variant selected
        foreach ($products as $item) {
            if ($item['product']->variants->count() > 0 && empty($item['variant_id'])) {
                return redirect()->route('frontend.cart.index')
                    ->with('error', \App\Helpers\TranslationHelper::trans('please_select_variant'));
            }
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($products as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Get loyalty points from query parameters
        $useLoyaltyPoints = $request->query('use_loyalty_points', false);
        $loyaltyPointsToUse = (int) $request->query('loyalty_points_to_use', 0);
        
        // Calculate discount
        $loyaltyDiscount = 0;
        if ($useLoyaltyPoints && $loyaltyPointsToUse > 0 && Auth::check()) {
            $user = Auth::user();
            $maxPointsToUse = min($user->loyalty_points ?? 0, floor($subtotal * 10));
            $loyaltyPointsToUse = min($loyaltyPointsToUse, $maxPointsToUse);
            $loyaltyDiscount = $loyaltyPointsToUse / 10; // 10 points = $1
        }
        
        $total = max(0, $subtotal - $loyaltyDiscount);
        $userLoyaltyPoints = Auth::check() ? Auth::user()->loyalty_points : 0;

        return view('frontend.pages.checkout', compact('products', 'subtotal', 'total', 'loyaltyDiscount', 'loyaltyPointsToUse', 'useLoyaltyPoints', 'userLoyaltyPoints'));
    }

    /**
     * Process the checkout and create order.
     */
    public function store(OrderRequest $request): RedirectResponse
    {
        // Check if user is authenticated (required for checkout)
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to checkout.');
        }
        
        $user = Auth::user();
        $orderData = $request->validated();
        
        // Auto-fill customer info from authenticated user
        $orderData['user_id'] = $user->id;
        $orderData['customer_name'] = $request->input('customer_name', $user->name);
        $orderData['customer_email'] = $request->input('customer_email', $user->email);
        $orderData['customer_phone'] = $request->input('customer_phone', $user->phone_number ?? null);
        $orderData['customer_address'] = $request->input('customer_address', $user->address_line1 ?? null);
        $orderData['order_notes'] = $request->input('order_notes', null);

        // Set default order status
        $orderData['order_status'] = $orderData['order_status'] ?? 'pending';

        // Convert products array to items array format
        $items = [];
        $products = $request->input('products', []);
        
        // If no products in request, get from cart session using CartController
        if (empty($products)) {
            $cartItems = CartController::getCartItems();
            foreach ($cartItems as $item) {
                $items[] = [
                    'product_id' => $item['product']->id,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
            }
        } else {
            // Process products from request
            foreach ($products as $product) {
                if (!empty($product['product_id']) && !empty($product['quantity'])) {
                    $items[] = [
                        'product_id' => $product['product_id'],
                        'variant_id' => $product['variant_id'] ?? null,
                        'quantity' => $product['quantity'],
                        'price' => $product['price'] ?? null,
                    ];
                }
            }
        }

        if (empty($items)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['items' => 'At least one product is required.']);
        }

        // Handle loyalty points redemption (after items are calculated)
        if (Auth::check() && $request->input('use_loyalty_points')) {
            $loyaltyPointsToUse = (int) $request->input('loyalty_points_to_use', 0);
            if ($loyaltyPointsToUse > 0) {
                $user = Auth::user();
                // Calculate subtotal to determine max points
                $tempSubtotal = 0;
                foreach ($items as $item) {
                    $tempSubtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                }
                $maxPointsToUse = min($user->loyalty_points ?? 0, floor($tempSubtotal * 10));
                $orderData['points_spent'] = min($loyaltyPointsToUse, $maxPointsToUse);
            } else {
                $orderData['points_spent'] = 0;
            }
        } else {
            $orderData['points_spent'] = 0;
        }

        try {
            $order = $this->orderService->createOrder($orderData, $items);
            
            // Clear cart after successful order
            session()->forget('cart');
            
            // Redirect to home with success message
            $successMessage = \App\Helpers\TranslationHelper::trans('order_placed_successfully') . ' ' . \App\Helpers\TranslationHelper::trans('order_id') . ': #' . $order->id;
            return redirect()->route('frontend.home')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
        }
    }
}

