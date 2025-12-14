<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;
    protected OrderRepository $orderRepository;
    protected ProductRepository $productRepository;
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->orderRepository = new OrderRepository(new Order());
        $this->productRepository = new ProductRepository(new Product());
        $this->userRepository = new UserRepository(new User());
        $this->orderService = new OrderService(
            $this->orderRepository,
            $this->productRepository,
            $this->userRepository
        );
    }

    /**
     * Test loyalty points calculation: 10 points per dollar spent.
     */
    public function test_points_earned_calculation()
    {
        // Create a user
        $user = User::factory()->create(['loyalty_points' => 0]);

        // Create a product
        $product = Product::factory()->create(['price' => 50.00]);

        // Create order with $50 total
        $orderData = [
            'user_id' => $user->id,
            'order_status' => 'pending',
        ];

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 50.00,
            ],
        ];

        $order = $this->orderService->createOrder($orderData, $items);

        // Should earn 10 points per dollar = 50 * 10 = 500 points
        $this->assertEquals(500, $order->total_points_earned);

        // Refresh user to get updated points
        $user->refresh();
        $this->assertEquals(500, $user->loyalty_points);
    }

    /**
     * Test points redemption: 10 points = $1 discount.
     */
    public function test_points_redemption_discount()
    {
        // Create a user with 100 points
        $user = User::factory()->create(['loyalty_points' => 100]);

        // Create a product
        $product = Product::factory()->create(['price' => 50.00]);

        // Create order with $50 total, redeeming 50 points
        $orderData = [
            'user_id' => $user->id,
            'points_spent' => 50,
            'order_status' => 'pending',
        ];

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 50.00,
            ],
        ];

        $order = $this->orderService->createOrder($orderData, $items);

        // 50 points = $5 discount, so $50 - $5 = $45
        $this->assertEquals(45.00, $order->total_price);
        $this->assertEquals(50, $order->total_points_spent);

        // User should have: 100 (initial) + 450 (earned) - 50 (spent) = 500 points
        $user->refresh();
        $this->assertEquals(500, $user->loyalty_points);
    }

    /**
     * Test that insufficient points throws exception.
     */
    public function test_insufficient_points_throws_exception()
    {
        // Create a user with only 10 points
        $user = User::factory()->create(['loyalty_points' => 10]);

        // Create a product
        $product = Product::factory()->create(['price' => 50.00]);

        // Try to redeem 50 points (user only has 10)
        $orderData = [
            'user_id' => $user->id,
            'points_spent' => 50,
            'order_status' => 'pending',
        ];

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 50.00,
            ],
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient loyalty points.');

        $this->orderService->createOrder($orderData, $items);
    }

    /**
     * Test complete order flow with points earning and redemption.
     */
    public function test_complete_order_flow_with_points()
    {
        // Create a user with 200 points
        $user = User::factory()->create(['loyalty_points' => 200]);

        // Create products
        $product1 = Product::factory()->create(['price' => 30.00]);
        $product2 = Product::factory()->create(['price' => 20.00]);

        // Create order: $50 total, redeeming 100 points
        $orderData = [
            'user_id' => $user->id,
            'points_spent' => 100,
            'order_status' => 'pending',
        ];

        $items = [
            [
                'product_id' => $product1->id,
                'quantity' => 1,
                'price' => 30.00,
            ],
            [
                'product_id' => $product2->id,
                'quantity' => 1,
                'price' => 20.00,
            ],
        ];

        $order = $this->orderService->createOrder($orderData, $items);

        // Verify order details
        // Original total: $50
        // Points redeemed: 100 points = $10 discount
        // Final total: $40
        $this->assertEquals(40.00, $order->total_price);
        
        // Points earned: $50 * 10 = 500 points
        $this->assertEquals(500, $order->total_points_earned);
        $this->assertEquals(100, $order->total_points_spent);

        // User points: 200 (initial) + 500 (earned) - 100 (spent) = 600
        $user->refresh();
        $this->assertEquals(600, $user->loyalty_points);
    }
}

