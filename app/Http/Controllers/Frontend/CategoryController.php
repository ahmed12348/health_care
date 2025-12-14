<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository
    ) {
    }

    /**
     * Display products by category.
     */
    public function show(int $id): View
    {
        $category = $this->categoryRepository->getById($id);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        $products = $this->productRepository->getByCategoryId($category->id);
        $products->load(['category', 'variants', 'media']);
        $categories = $this->categoryRepository->getAll();
        
        return view('frontend.pages.category', compact('category', 'products', 'categories'));
    }
}

