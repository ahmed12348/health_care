<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Display all products.
     */
    public function index(): View
    {
        $products = $this->productRepository->getAll();
        $products->load(['category', 'variants', 'media']);
        $categories = $this->categoryRepository->getAll();
        
        return view('frontend.pages.products', compact('products', 'categories'));
    }

    /**
     * Display a single product.
     */
    public function show(int $id): View
    {
        $product = $this->productRepository->getById($id);
        
        if (!$product) {
            abort(404, 'Product not found.');
        }

        $product->load(['category', 'variants', 'media']);
        
        return view('frontend.pages.product', compact('product'));
    }
}

