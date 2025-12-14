<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Category;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * OrderController constructor.
     *
     * @param OrderService $service
     */
    public function __construct(
        protected OrderService $service
    ) {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): View
    {
        $orders = auth()->user()->role === 'admin' 
            ? $this->service->handle()
            : $this->service->getOrdersByUserId(auth()->id());
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): View
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        return view('admin.orders.create', compact('users', 'categories'));
    }

    /**
     * Store a newly created order.
     */
    public function store(OrderRequest $request): RedirectResponse
    {
        $orderData = $request->validated();
        $orderData['user_id'] = $orderData['user_id'] ?? (auth()->user()->role === 'admin' ? null : auth()->id());
        
        // Convert products array to items array format
        $items = [];
        $products = $request->input('products', []);
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
        
        if (empty($items)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['items' => 'At least one product is required.']);
        }
        
        $order = $this->service->createOrder($orderData, $items);
        
        $routeName = request()->is('admin/*') ? 'admin.orders.show' : 'customer.orders.show';
        return redirect()->route($routeName, $order->id)
            ->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(int $id): View
    {
        $order = $this->service->getOrderById($id);
        
        if (!$order) {
            abort(404, 'Order not found.');
        }

        // Check if user owns the order or is admin
        if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['orderItems.product', 'orderItems.variant', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(int $id): View
    {
        $order = $this->service->getOrderById($id);
        
        if (!$order) {
            abort(404, 'Order not found.');
        }

        // Check if user owns the order or is admin
        if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $order->load(['orderItems.product', 'orderItems.variant', 'user']);
        $users = User::where('role', 'user')->get();
        $categories = Category::all();

        return view('admin.orders.edit', compact('order', 'users', 'categories'));
    }

    /**
     * Update the specified order.
     */
    public function update(OrderRequest $request, int $id): RedirectResponse
    {
        $order = $this->service->getOrderById($id);
        
        if (!$order) {
            abort(404, 'Order not found.');
        }

        // Check if user owns the order or is admin
        if (auth()->user()->role !== 'admin' && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $orderData = $request->validated();
        
        // Convert products array to items array format
        $items = [];
        $products = $request->input('products', []);
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
        
        if (empty($items)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['items' => 'At least one product is required.']);
        }

        $updated = $this->service->updateOrder($id, $orderData, $items);
        
        if (!$updated) {
            abort(404, 'Order not found.');
        }

        $routeName = request()->is('admin/*') ? 'admin.orders.show' : 'customer.orders.show';
        return redirect()->route($routeName, $id)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
        ]);

        $updated = $this->service->updateOrderStatus($id, $request->input('status'));
        
        if (!$updated) {
            abort(404, 'Order not found.');
        }

        $routeName = request()->is('admin/*') ? 'admin.orders.show' : 'customer.orders.show';
        return redirect()->route($routeName, $id)
            ->with('success', 'Order status updated successfully.');
    }
}

