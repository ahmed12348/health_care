<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use App\Services\BaseService;

class ProductService extends BaseService
{
    /**
     * ProductService constructor.
     *
     * @param ProductRepository $repository
     * @param ProductVariantRepository $variantRepository
     */
    public function __construct(
        protected ProductRepository $repository,
        protected ProductVariantRepository $variantRepository
    ) {}

    /**
     * Get all products.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProducts()
    {
        return $this->repository->getAll();
    }

    /**
     * Get product by ID.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getProductById(int $id)
    {
        return $this->repository->getById($id);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createProduct(array $data)
    {
        // Business rule: Ensure price is positive
        if (isset($data['price']) && $data['price'] < 0) {
            throw new \InvalidArgumentException('Product price cannot be negative.');
        }

        // Business rule: Ensure stock quantity is non-negative
        if (isset($data['stock_quantity']) && $data['stock_quantity'] < 0) {
            throw new \InvalidArgumentException('Stock quantity cannot be negative.');
        }

        // Extract variants if present
        $variants = $data['variants'] ?? [];
        unset($data['variants']);

        // Create product
        $product = $this->repository->create($data);

        // Create variants if provided
        if (!empty($variants)) {
            $productStock = $product->stock_quantity;
            foreach ($variants as $variantData) {
                if (!empty($variantData['variant_type']) && !empty($variantData['variant_value'])) {
                    // Business rule: Variant stock cannot exceed product stock
                    $variantStock = $variantData['variant_stock_quantity'] ?? 0;
                    if ($variantStock > $productStock) {
                        throw new \InvalidArgumentException(
                            "Variant stock ({$variantStock}) cannot exceed product stock ({$productStock})."
                        );
                    }
                    $variantData['product_id'] = $product->id;
                    $this->variantRepository->create($variantData);
                }
            }
        }

        return $product;
    }

    /**
     * Update a product.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProduct(int $id, array $data)
    {
        // Business rule: Ensure price is positive if provided
        if (isset($data['price']) && $data['price'] < 0) {
            throw new \InvalidArgumentException('Product price cannot be negative.');
        }

        // Business rule: Ensure stock quantity is non-negative if provided
        if (isset($data['stock_quantity']) && $data['stock_quantity'] < 0) {
            throw new \InvalidArgumentException('Stock quantity cannot be negative.');
        }

        // Extract variants if present
        $variants = $data['variants'] ?? null;
        unset($data['variants']);

        // Update product
        $updated = $this->repository->update($id, $data);

        // Handle variants if provided (null means don't update variants, empty array means delete all)
        if ($updated && $variants !== null) {
            $product = $this->repository->getById($id);
            
            if ($product) {
                if (empty($variants)) {
                    // Empty array means delete all variants
                    $product->variants()->delete();
                } else {
                    // Get existing variant IDs from the form
                    $existingVariantIds = [];
                    foreach ($variants as $variantData) {
                        if (isset($variantData['id']) && !empty($variantData['id'])) {
                            $existingVariantIds[] = $variantData['id'];
                        }
                    }

                    // Delete variants that are not in the update list
                    if (!empty($existingVariantIds)) {
                        $product->variants()->whereNotIn('id', $existingVariantIds)->delete();
                    } else {
                        // If no IDs provided, delete all existing variants (user is replacing them)
                        $product->variants()->delete();
                    }

                    // Update or create variants
                    $productStock = $product->stock_quantity;
                    foreach ($variants as $variantData) {
                        // Only process if variant has type and value
                        if (!empty($variantData['variant_type']) && !empty($variantData['variant_value'])) {
                            // Business rule: Variant stock cannot exceed product stock
                            $variantStock = $variantData['variant_stock_quantity'] ?? 0;
                            if ($variantStock > $productStock) {
                                throw new \InvalidArgumentException(
                                    "Variant stock ({$variantStock}) cannot exceed product stock ({$productStock})."
                                );
                            }
                            
                            $variantData['product_id'] = $id;
                            
                            if (isset($variantData['id']) && !empty($variantData['id'])) {
                                // Update existing variant
                                $variantId = $variantData['id'];
                                $updateData = $variantData;
                                unset($updateData['id']);
                                $this->variantRepository->update($variantId, $updateData);
                            } else {
                                // Create new variant
                                unset($variantData['id']);
                                $this->variantRepository->create($variantData);
                            }
                        }
                    }
                }
            }
        }

        return $updated;
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Get featured products.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedProducts()
    {
        return $this->repository->findBy('is_featured', true);
    }

    /**
     * Get products by category.
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsByCategory(int $categoryId)
    {
        return $this->repository->findBy('category_id', $categoryId);
    }

    /**
     * Check if product is in stock.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function isInStock(int $productId, int $quantity = 1)
    {
        $product = $this->repository->getById($productId);
        
        if (!$product) {
            return false;
        }

        return $product->stock_quantity >= $quantity;
    }

    /**
     * Handle the service logic (required by BaseService).
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function handle(...$args)
    {
        // Default handler - can be overridden for specific use cases
        return $this->getAllProducts();
    }
}

