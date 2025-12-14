<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Repositories\CategoryRepository;
use App\Models\Category;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * CategoryController constructor.
     *
     * @param CategoryRepository $repository
     */
    public function __construct(
        protected CategoryRepository $repository,
        protected MediaService $mediaService
    ) {
        // Middleware is applied in routes
    }

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = $this->repository->getAll();
        // Load products count for each category
        $categories->load('products');
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = $this->repository->create($request->validated());
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $this->mediaService->uploadAndAttach(
                $request->file('image'),
                Category::class,
                $category->id
            );
        }
        
        $routeName = request()->is('admin/*') ? 'admin.categories.show' : 'customer.categories.show';
        return redirect()->route($routeName, $category->id)
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(int $id): View
    {
        $category = $this->repository->getById($id);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        // Load media relationship
        $category->load('media');

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(int $id): View
    {
        $category = $this->repository->getById($id);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        // Load media relationship
        $category->load('media');

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(CategoryRequest $request, int $id): RedirectResponse
    {
        $updated = $this->repository->update($id, $request->validated());
        
        if (!$updated) {
            abort(404, 'Category not found.');
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $category = $this->repository->getById($id);
            
            // Delete existing image if any
            $existingImage = $category->media()->where('file_type', 'image')->first();
            if ($existingImage) {
                $this->mediaService->deleteMedia($existingImage->id);
            }
            
            // Upload new image
            $this->mediaService->uploadAndAttach(
                $request->file('image'),
                Category::class,
                $id
            );
        }

        $routeName = request()->is('admin/*') ? 'admin.categories.show' : 'customer.categories.show';
        return redirect()->route($routeName, $id)
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->repository->delete($id);
        
        if (!$deleted) {
            abort(404, 'Category not found.');
        }

        $routeName = request()->is('admin/*') ? 'admin.categories.index' : 'customer.categories.index';
        return redirect()->route($routeName)
            ->with('success', 'Category deleted successfully.');
    }
}

