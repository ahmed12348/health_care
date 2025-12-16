<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Media;

class CategoryProductSeeder extends Seeder
{
    public function run()
    {
        // Define the categories with products, variants, and media
        $categories = [
            [
                'name' => 'Men',
                'description' => 'Health and beauty products for men.',
                'image_path' => 'categories/men_category.jpg',
                'products' => [
                    [
                        'name' => 'Shampoo',
                        'description' => 'A nourishing shampoo for men.',
                        'price' => 12.99,
                        'stock_quantity' => 50,
                        'is_featured' => true,
                        'image_path' => 'products/shampoo_men.jpg',
                        'variants' => [
                            [
                                'variant_type' => 'Size',
                                'variant_value' => '250ml',
                                'variant_price' => 12.99,
                                'variant_stock_quantity' => 30,
                            ],
                            [
                                'variant_type' => 'Size',
                                'variant_value' => '500ml',
                                'variant_price' => 22.99,
                                'variant_stock_quantity' => 20,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Vaseline',
                        'description' => 'Vaseline for skin hydration.',
                        'price' => 4.99,
                        'stock_quantity' => 100,
                        'is_featured' => false,
                        'image_path' => 'products/vaseline_men.jpg',
                        'variants' => [
                            [
                                'variant_type' => 'Size',
                                'variant_value' => 'Small',
                                'variant_price' => 4.99,
                                'variant_stock_quantity' => 50,
                            ],
                            [
                                'variant_type' => 'Size',
                                'variant_value' => 'Large',
                                'variant_price' => 8.99,
                                'variant_stock_quantity' => 50,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Aftershave',
                        'description' => 'Aftershave lotion for men.',
                        'price' => 15.99,
                        'stock_quantity' => 30,
                        'is_featured' => true,
                        'image_path' => 'products/aftershave_men.jpg',
                        'variants' => [
                            [
                                'variant_type' => 'Fragrance',
                                'variant_value' => 'Classic',
                                'variant_price' => 15.99,
                                'variant_stock_quantity' => 15,
                            ],
                            [
                                'variant_type' => 'Fragrance',
                                'variant_value' => 'Fresh',
                                'variant_price' => 17.99,
                                'variant_stock_quantity' => 15,
                            ],
                        ],
                    ]
                ]
            ],
            [
                'name' => 'Women',
                'description' => 'Beauty and healthcare products for women.',
                'image_path' => 'categories/women_category.jpg',
                'products' => [
                    [
                        'name' => 'Shampoo',
                        'description' => 'Shampoo for smooth and shiny hair.',
                        'price' => 10.99,
                        'stock_quantity' => 60,
                        'is_featured' => false,
                        'image_path' => 'products/shampoo_women.jpg',
                        'variants' => [
                            [
                                'variant_type' => 'Size',
                                'variant_value' => '250ml',
                                'variant_price' => 10.99,
                                'variant_stock_quantity' => 30,
                            ],
                            [
                                'variant_type' => 'Size',
                                'variant_value' => '500ml',
                                'variant_price' => 19.99,
                                'variant_stock_quantity' => 30,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Face Cream',
                        'description' => 'Moisturizing face cream.',
                        'price' => 18.99,
                        'stock_quantity' => 40,
                        'is_featured' => true,
                        'image_path' => 'products/face_cream_women.jpg',
                        'variants' => [
                            [
                                'variant_type' => 'Skin Type',
                                'variant_value' => 'Dry',
                                'variant_price' => 18.99,
                                'variant_stock_quantity' => 20,
                            ],
                            [
                                'variant_type' => 'Skin Type',
                                'variant_value' => 'Oily',
                                'variant_price' => 18.99,
                                'variant_stock_quantity' => 20,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Vaseline',
                        'description' => 'Hydrating vaseline for skin care.',
                        'price' => 5.99,
                        'stock_quantity' => 120,
                        'is_featured' => false,
                        'image_path' => 'products/vaseline_women.jpg',
                        'variants' => [],
                    ]
                ]
            ],
            [
                'name' => 'Baby Care',
                'description' => 'Health and beauty products for babies.',
                'image_path' => 'categories/baby_care_category.jpg',
                'products' => [
                    [
                        'name' => 'Baby Shampoo',
                        'description' => 'Gentle shampoo for babies.',
                        'price' => 7.99,
                        'stock_quantity' => 70,
                        'is_featured' => false,
                        'image_path' => 'products/baby_shampoo.jpg',
                        'variants' => [
                            [
                                'variant_type' => 'Size',
                                'variant_value' => '200ml',
                                'variant_price' => 7.99,
                                'variant_stock_quantity' => 35,
                            ],
                            [
                                'variant_type' => 'Size',
                                'variant_value' => '400ml',
                                'variant_price' => 13.99,
                                'variant_stock_quantity' => 35,
                            ],
                        ],
                    ],
                    [
                        'name' => 'Baby Lotion',
                        'description' => 'Moisturizing lotion for babies.',
                        'price' => 8.99,
                        'stock_quantity' => 90,
                        'is_featured' => true,
                        'image_path' => 'products/baby_lotion.jpg',
                        'variants' => [],
                    ],
                    [
                        'name' => 'Diapers',
                        'description' => 'Soft and comfortable diapers for babies.',
                        'price' => 25.99,
                        'stock_quantity' => 150,
                        'is_featured' => true,
                        'image_path' => 'products/baby_diapers.jpg',
                        'variants' => [
                            [
                                'variant_type' => 'Size',
                                'variant_value' => 'Small',
                                'variant_price' => 25.99,
                                'variant_stock_quantity' => 50,
                            ],
                            [
                                'variant_type' => 'Size',
                                'variant_value' => 'Medium',
                                'variant_price' => 27.99,
                                'variant_stock_quantity' => 50,
                            ],
                            [
                                'variant_type' => 'Size',
                                'variant_value' => 'Large',
                                'variant_price' => 29.99,
                                'variant_stock_quantity' => 50,
                            ],
                        ],
                    ]
                ]
            ]
        ];

        // Seed the categories and their products
        foreach ($categories as $categoryData) {
            // Create the category
            $category = Category::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
            ]);

            // Create category media (image)
            if (isset($categoryData['image_path'])) {
                Media::create([
                    'model_type' => Category::class,
                    'model_id' => $category->id,
                    'file_path' => $categoryData['image_path'],
                    'file_type' => 'image',
                ]);
            }

            // Add products for the current category
            foreach ($categoryData['products'] as $productData) {
                $product = Product::create([
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'category_id' => $category->id,
                    'price' => $productData['price'],
                    'stock_quantity' => $productData['stock_quantity'],
                    'is_featured' => $productData['is_featured'],
                ]);

                // Create product media (image)
                if (isset($productData['image_path'])) {
                    Media::create([
                        'model_type' => Product::class,
                        'model_id' => $product->id,
                        'file_path' => $productData['image_path'],
                        'file_type' => 'image',
                    ]);
                }

                // Create product variants
                if (isset($productData['variants']) && !empty($productData['variants'])) {
                    foreach ($productData['variants'] as $variantData) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'variant_type' => $variantData['variant_type'],
                            'variant_value' => $variantData['variant_value'],
                            'variant_price' => $variantData['variant_price'] ?? null,
                            'variant_stock_quantity' => $variantData['variant_stock_quantity'] ?? 0,
                        ]);
                    }
                }
            }
        }
    }
}
