<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Display the store homepage.
     */
    public function index(): View
    {
        // Set Arabic as default language if not set
        if (!session()->has('locale')) {
            session(['locale' => 'ar', 'direction' => 'rtl']);
        }
        
        $categories = $this->categoryRepository->getAll();
        $featuredProducts = $this->productRepository->getAll()->where('is_featured', true)->take(8);
        $allProducts = $this->productRepository->getAll()->take(12); // Get all products (limit to 12 for homepage)
        $sliderCategories = $categories->take(6);
        
        // Load relationships
        $featuredProducts->load(['category', 'variants', 'media']);
        $allProducts->load(['category', 'variants', 'media']);
        $categories->load('media');
        
        return view('frontend.pages.home', compact('categories', 'featuredProducts', 'allProducts', 'sliderCategories'));
    }
}

