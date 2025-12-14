<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use App\Repositories\UserRepository;
use App\Services\BaseService;

class OrderService extends BaseService
{
    /**
     * Points earned per dollar spent.
     */
    const POINTS_PER_DOLLAR = 10;

    /**
     * OrderService constructor.
     *
     * @param OrderRepository $repository
     * @param ProductRepository $productRepository
     * @param ProductVariantRepository $variantRepository
     * @param UserRepository $userRepository
     * @param OrderItemRepository $orderItemRepository
     */
    public function __construct(
        protected OrderRepository $repository,
        protected ProductRepository $productRepository,
        protected ProductVariantRepository $variantRepository,
        protected UserRepository $userRepository,
        protected OrderItemRepository $orderItemRepository
    ) {}

    /**
     * Create a new order.
     *
     * @param array $orderData
     * @param array $items
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createOrder(array $orderData, array $items)
    {
        // Calculate total price from items
        $totalPrice = $this->calculateOrderTotal($items);

        // Apply points redemption if requested (deduct points immediately)
        $pointsSpent = $orderData['points_spent'] ?? 0;
        if ($pointsSpent > 0) {
            $totalPrice = $this->applyPointsRedemption($orderData['user_id'], $pointsSpent, $totalPrice);
            // If user used points, they don't earn new points
            $pointsEarned = 0;
        } else {
            // Calculate points earned based on original total (only if no points used)
            $originalTotal = $this->calculateOrderTotal($items);
            $pointsEarned = $this->calculatePointsEarned($originalTotal);
        }

        // Create order
        $order = $this->repository->create([
            'user_id' => $orderData['user_id'] ?? null,
            'customer_name' => $orderData['customer_name'] ?? null,
            'customer_email' => $orderData['customer_email'] ?? null,
            'customer_phone' => $orderData['customer_phone'] ?? null,
            'customer_address' => $orderData['customer_address'] ?? null,
            'total_price' => $totalPrice,
            'total_points_earned' => $pointsEarned,
            'total_points_spent' => $pointsSpent,
            'order_status' => $orderData['order_status'] ?? 'pending',
        ]);

        // Create order items and deduct stock
        foreach ($items as $item) {
            $product = $this->productRepository->getById($item['product_id']);
            $quantity = $item['quantity'] ?? 1;
            
            // Determine price: use provided price, or variant price, or product price
            if (isset($item['price'])) {
                $price = $item['price'];
            } elseif (!empty($item['variant_id'])) {
                // If variant selected, use variant price if available
                $variant = $this->variantRepository->getById($item['variant_id']);
                $price = $variant && $variant->variant_price ? $variant->variant_price : $product->price;
            } else {
                // Use product price
                $price = $product->price;
            }
            
            // Create order item
            $this->orderItemRepository->create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $quantity,
                'price' => $price,
            ]);

            // Deduct stock when order is created
            if (!empty($item['variant_id'])) {
                // Deduct from variant stock
                $variant = $this->variantRepository->getById($item['variant_id']);
                if ($variant) {
                    $newStock = max(0, ($variant->variant_stock_quantity ?? 0) - $quantity);
                    $this->variantRepository->update($variant->id, [
                        'variant_stock_quantity' => $newStock
                    ]);
                }
            } else {
                // Deduct from product stock
                $newStock = max(0, ($product->stock_quantity ?? 0) - $quantity);
                $this->productRepository->update($product->id, [
                    'stock_quantity' => $newStock
                ]);
            }
        }

        // Deduct points immediately if used (regardless of order status)
        if ($pointsSpent > 0 && !empty($orderData['user_id'])) {
            $this->deductUserPoints($orderData['user_id'], $pointsSpent);
        }
        
        // Award loyalty points ONLY if order status is "completed" (delivered) AND no points were used
        // Points are earned when order is delivered/completed, not when pending/processing
        if ($order->order_status === 'completed' && !empty($orderData['user_id']) && $pointsEarned > 0) {
            $this->addUserPoints($orderData['user_id'], $pointsEarned);
        }
        // If order is not completed, points will be awarded when status changes to "completed" via updateOrderStatus()

        return $order;
    }

    /**
     * Calculate order total from items.
     *
     * @param array $items
     * @return float
     */
    protected function calculateOrderTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $product = $this->productRepository->getById($item['product_id']);
            
            if (!$product) {
                throw new \InvalidArgumentException("Product with ID {$item['product_id']} not found.");
            }

            // Stock validation
            $quantity = $item['quantity'] ?? 1;
            
            if (!empty($item['variant_id'])) {
                // Check variant stock
                $variant = $this->variantRepository->getById($item['variant_id']);
                if (!$variant) {
                    throw new \InvalidArgumentException("Variant with ID {$item['variant_id']} not found.");
                }
                
                // Validate variant belongs to product
                if ($variant->product_id != $product->id) {
                    throw new \InvalidArgumentException("Variant does not belong to the selected product.");
                }
                
                // Check variant stock
                $availableStock = $variant->variant_stock_quantity ?? 0;
                if ($availableStock < $quantity) {
                    throw new \InvalidArgumentException(
                        "Insufficient stock for variant. Available: {$availableStock}, Requested: {$quantity}"
                    );
                }
            } else {
                // Check product stock
                $availableStock = $product->stock_quantity ?? 0;
                if ($availableStock < $quantity) {
                    throw new \InvalidArgumentException(
                        "Insufficient stock for product '{$product->name}'. Available: {$availableStock}, Requested: {$quantity}"
                    );
                }
            }
       
            // Determine price: use provided price, or variant price, or product price
            if (isset($item['price'])) {
                $price = $item['price'];
            } elseif (!empty($item['variant_id'])) {
                // If variant selected, use variant price if available
                $variant = $this->variantRepository->getById($item['variant_id']);
                $price = $variant && $variant->variant_price ? $variant->variant_price : $product->price;
            } else {
                // Use product price
                $price = $product->price;
            }
            
            $total += $price * $quantity;
        }

        return $total;
    }


    /**
     * Calculate points earned based on order total.
     *
     * @param float $totalPrice
     * @return int
     */
    protected function calculatePointsEarned(float $totalPrice): int
    {
        return (int) floor($totalPrice * self::POINTS_PER_DOLLAR);
    }

    /**
     * Apply points redemption to order total.
     *
     * @param int|null $userId
     * @param int $pointsSpent
     * @param float $totalPrice
     * @return float
     */
    protected function applyPointsRedemption(?int $userId, int $pointsSpent, float $totalPrice): float
    {
        if (!$userId) {
            throw new \InvalidArgumentException('User ID is required for points redemption.');
        }

        $user = $this->userRepository->getById($userId);
        
        if (!$user) {
            throw new \InvalidArgumentException("User with ID {$userId} not found.");
        }

        if ($user->loyalty_points < $pointsSpent) {
            throw new \InvalidArgumentException('Insufficient loyalty points.');
        }

        // Convert points to discount (10 points = $1)
        $discount = $pointsSpent / 10;
        
        // Apply discount but ensure total doesn't go negative
        return max(0, $totalPrice - $discount);
    }

    /**
     * Update user loyalty points.
     *
     * @param int $userId
     * @param int $pointsEarned
     * @param int $pointsSpent
     * @return bool
     */
    protected function updateUserLoyaltyPoints(int $userId, int $pointsEarned, int $pointsSpent): bool
    {
        $user = $this->userRepository->getById($userId);
        
        if (!$user) {
            return false;
        }

        $newPoints = $user->loyalty_points + $pointsEarned - $pointsSpent;
        
        return $this->userRepository->update($userId, [
            'loyalty_points' => max(0, $newPoints)
        ]);
    }

    /**
     * Deduct user points immediately.
     *
     * @param int $userId
     * @param int $pointsSpent
     * @return bool
     */
    protected function deductUserPoints(int $userId, int $pointsSpent): bool
    {
        $user = $this->userRepository->getById($userId);
        
        if (!$user) {
            return false;
        }

        $newPoints = max(0, $user->loyalty_points - $pointsSpent);
        
        return $this->userRepository->update($userId, [
            'loyalty_points' => $newPoints
        ]);
    }

    /**
     * Add user points.
     *
     * @param int $userId
     * @param int $pointsEarned
     * @return bool
     */
    protected function addUserPoints(int $userId, int $pointsEarned): bool
    {
        $user = $this->userRepository->getById($userId);
        
        if (!$user) {
            return false;
        }

        $newPoints = $user->loyalty_points + $pointsEarned;
        
        return $this->userRepository->update($userId, [
            'loyalty_points' => max(0, $newPoints)
        ]);
    }

    /**
     * Get order by ID.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getOrderById(int $id)
    {
        return $this->repository->getById($id);
    }

    /**
     * Get orders by user ID.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByUserId(int $userId)
    {
        return $this->repository->findBy('user_id', $userId);
    }

    /**
     * Update order status.
     * 
     * IMPORTANT: Loyalty points are awarded ONLY when order status changes to "completed"
     *
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(int $orderId, string $status)
    {
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid order status: {$status}");
        }

        $order = $this->repository->getById($orderId);
        
        if (!$order) {
            return false;
        }

        $oldStatus = $order->order_status;
        $updated = $this->repository->update($orderId, ['order_status' => $status]);

        // Handle stock and points based on status change
        if ($updated) {
            // If order is cancelled, restore stock
            if ($status === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->restoreOrderStock($orderId);
            }
            
            // If order was cancelled and now being reactivated, deduct stock again
            if ($oldStatus === 'cancelled' && $status !== 'cancelled') {
                $this->deductOrderStock($orderId);
            }
            
            // Award loyalty points ONLY when order status changes to "completed"
            // Points are earned when order is delivered/completed, not when created
            // BUT: If points were used, no new points are earned (points_earned = 0)
            if ($status === 'completed' && $oldStatus !== 'completed') {
                // Award points when order is completed/delivered (only if points were not used)
                if ($order->user_id && $order->total_points_earned > 0) {
                    // Points earned are already calculated and stored in order
                    // If points_spent > 0, points_earned should be 0, so this won't add points
                    $this->addUserPoints($order->user_id, $order->total_points_earned);
                }
            }
            
            // If order was completed and now cancelled, remove points
            if ($oldStatus === 'completed' && $status === 'cancelled') {
                if ($order->user_id && $order->total_points_earned > 0) {
                    // Remove points that were awarded
                    $this->updateUserLoyaltyPoints(
                        $order->user_id, 
                        -$order->total_points_earned, 
                        -$order->total_points_spent
                    );
                }
            }
        }

        return $updated;
    }

    /**
     * Update an existing order.
     *
     * @param int $orderId
     * @param array $orderData
     * @param array $items
     * @return bool
     */
    public function updateOrder(int $orderId, array $orderData, array $items)
    {
        $order = $this->repository->getById($orderId);
        
        if (!$order) {
            return false;
        }

        // Get old points values for recalculation
        $oldPointsEarned = $order->total_points_earned;
        $oldPointsSpent = $order->total_points_spent;
        $oldUserId = $order->user_id;

        // Calculate new total price from items
        $totalPrice = $this->calculateOrderTotal($items);

        // Calculate new points earned
        $originalTotal = $this->calculateOrderTotal($items);
        $pointsEarned = $this->calculatePointsEarned($originalTotal);

        // Apply points redemption if requested
        $pointsSpent = $orderData['points_spent'] ?? 0;
        if ($pointsSpent > 0) {
            $userId = $orderData['user_id'] ?? $order->user_id;
            $totalPrice = $this->applyPointsRedemption($userId, $pointsSpent, $totalPrice);
        }

        // Update order
        $updated = $this->repository->update($orderId, [
            'user_id' => $orderData['user_id'] ?? $order->user_id,
            'customer_name' => $orderData['customer_name'] ?? $order->customer_name,
            'customer_email' => $orderData['customer_email'] ?? $order->customer_email,
            'customer_phone' => $orderData['customer_phone'] ?? $order->customer_phone,
            'customer_address' => $orderData['customer_address'] ?? $order->customer_address,
            'total_price' => $totalPrice,
            'total_points_earned' => $pointsEarned,
            'total_points_spent' => $pointsSpent,
            'order_status' => $orderData['order_status'] ?? $order->order_status,
        ]);

        if ($updated) {
            // Restore stock from old items before deleting them
            $this->restoreOrderStock($orderId);
            
            // Delete old order items
            $this->orderItemRepository->deleteByOrderId($orderId);

            // Create new order items and deduct stock
            foreach ($items as $item) {
                $product = $this->productRepository->getById($item['product_id']);
                $quantity = $item['quantity'] ?? 1;
                
                // Determine price: use provided price, or variant price, or product price
                if (isset($item['price'])) {
                    $price = $item['price'];
                } elseif (!empty($item['variant_id'])) {
                    // If variant selected, use variant price if available
                    $variant = $this->variantRepository->getById($item['variant_id']);
                    $price = $variant && $variant->variant_price ? $variant->variant_price : $product->price;
                } else {
                    // Use product price
                    $price = $product->price;
                }
                
                // Create order item
                $this->orderItemRepository->create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                // Deduct stock for new items
                if (!empty($item['variant_id'])) {
                    // Deduct from variant stock
                    $variant = $this->variantRepository->getById($item['variant_id']);
                    if ($variant) {
                        $newStock = max(0, ($variant->variant_stock_quantity ?? 0) - $quantity);
                        $this->variantRepository->update($variant->id, [
                            'variant_stock_quantity' => $newStock
                        ]);
                    }
                } else {
                    // Deduct from product stock
                    $newStock = max(0, ($product->stock_quantity ?? 0) - $quantity);
                    $this->productRepository->update($product->id, [
                        'stock_quantity' => $newStock
                    ]);
                }
            }

            // Recalculate user loyalty points
            $newUserId = $orderData['user_id'] ?? $order->user_id;
            if ($newUserId) {
                // If user changed, remove old points from old user
                if ($oldUserId && $oldUserId != $newUserId) {
                    $this->updateUserLoyaltyPoints($oldUserId, -$oldPointsEarned, -$oldPointsSpent);
                }
                
                // Calculate points difference for same user
                if ($oldUserId == $newUserId) {
                    $pointsDiff = ($pointsEarned - $oldPointsEarned) - ($pointsSpent - $oldPointsSpent);
                    if ($pointsDiff != 0) {
                        $user = $this->userRepository->getById($newUserId);
                        if ($user) {
                            $newPoints = $user->loyalty_points + $pointsDiff;
                            $this->userRepository->update($newUserId, [
                                'loyalty_points' => max(0, $newPoints)
                            ]);
                        }
                    }
                } else {
                    // Add new points to new user
                    $this->updateUserLoyaltyPoints($newUserId, $pointsEarned, $pointsSpent);
                }
            }
        }

        return $updated;
    }

    /**
     * Restore stock when order is cancelled.
     *
     * @param int $orderId
     * @return void
     */
    protected function restoreOrderStock(int $orderId): void
    {
        $order = $this->repository->getById($orderId);
        
        if (!$order) {
            return;
        }

        $order->load('orderItems');
        
        foreach ($order->orderItems as $item) {
            $quantity = $item->quantity;
            
            if ($item->variant_id) {
                // Restore variant stock
                $variant = $this->variantRepository->getById($item->variant_id);
                if ($variant) {
                    $newStock = ($variant->variant_stock_quantity ?? 0) + $quantity;
                    $this->variantRepository->update($variant->id, [
                        'variant_stock_quantity' => $newStock
                    ]);
                }
            } else {
                // Restore product stock
                $product = $this->productRepository->getById($item->product_id);
                if ($product) {
                    $newStock = ($product->stock_quantity ?? 0) + $quantity;
                    $this->productRepository->update($product->id, [
                        'stock_quantity' => $newStock
                    ]);
                }
            }
        }
    }

    /**
     * Deduct stock for order items.
     *
     * @param int $orderId
     * @return void
     */
    protected function deductOrderStock(int $orderId): void
    {
        $order = $this->repository->getById($orderId);
        
        if (!$order) {
            return;
        }

        $order->load('orderItems');
        
        foreach ($order->orderItems as $item) {
            $quantity = $item->quantity;
            
            if ($item->variant_id) {
                // Deduct variant stock
                $variant = $this->variantRepository->getById($item->variant_id);
                if ($variant) {
                    $newStock = max(0, ($variant->variant_stock_quantity ?? 0) - $quantity);
                    $this->variantRepository->update($variant->id, [
                        'variant_stock_quantity' => $newStock
                    ]);
                }
            } else {
                // Deduct product stock
                $product = $this->productRepository->getById($item->product_id);
                if ($product) {
                    $newStock = max(0, ($product->stock_quantity ?? 0) - $quantity);
                    $this->productRepository->update($product->id, [
                        'stock_quantity' => $newStock
                    ]);
                }
            }
        }
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
        return $this->repository->getAll();
    }
}

