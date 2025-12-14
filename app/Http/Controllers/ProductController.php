<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * ProductController constructor.
     *
     * @param ProductService $service
     */
    public function __construct(
        protected ProductService $service,
        protected MediaService $mediaService
    ) {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $products = $this->service->getAllProducts();
        // Load relationships for better performance
        $products->load(['category']);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['variants'] = $request->input('variants', []);
        
        $product = $this->service->createProduct($data);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $this->mediaService->uploadAndAttach(
                $request->file('image'),
                Product::class,
                $product->id
            );
        }
        
        $routeName = request()->is('admin/*') ? 'admin.products.show' : 'customer.products.show';
        return redirect()->route($routeName, $product->id)
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): View
    {
        $product = $this->service->getProductById($id);
        
        if (!$product) {
            abort(404, 'Product not found.');
        }

        // Load relationships
        $product->load(['category', 'variants', 'media']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(int $id): View
    {
        $product = $this->service->getProductById($id);
        
        if (!$product) {
            abort(404, 'Product not found.');
        }

        // Load variants and media for editing
        $product->load(['variants', 'media']);

        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(ProductRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        $data['variants'] = $request->input('variants', []);
        
        $updated = $this->service->updateProduct($id, $data);
        
        if (!$updated) {
            abort(404, 'Product not found.');
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $product = $this->service->getProductById($id);
            
            // Delete existing image if any
            $existingImage = $product->media()->where('file_type', 'image')->first();
            if ($existingImage) {
                $this->mediaService->deleteMedia($existingImage->id);
            }
            
            // Upload new image
            $this->mediaService->uploadAndAttach(
                $request->file('image'),
                Product::class,
                $id
            );
        }

        $routeName = request()->is('admin/*') ? 'admin.products.show' : 'customer.products.show';
        return redirect()->route($routeName, $id)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->service->deleteProduct($id);
        
        if (!$deleted) {
            abort(404, 'Product not found.');
        }

        $routeName = request()->is('admin/*') ? 'admin.products.index' : 'customer.products.index';
        return redirect()->route($routeName)
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Get variants for a product.
     *
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVariants(int $productId)
    {
        $product = $this->service->getProductById($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $variants = $product->variants;
        
        return response()->json($variants);
    }

    /**
     * Get products by category.
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(int $categoryId)
    {
        $products = $this->service->getProductsByCategory($categoryId);
        
        return response()->json($products);
    }

    /**
     * Get product price.
     *
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrice(int $productId)
    {
        $product = $this->service->getProductById($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'price' => $product->price,
            'formatted_price' => number_format($product->price, 2)
        ]);
    }
}

