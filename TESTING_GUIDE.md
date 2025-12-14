# Testing Guide for Loyalty Points System

## Overview
This guide explains how to test the loyalty points calculation and redemption system.

## Points System Rules
- **Points Earned**: 10 points per dollar spent
- **Points Redemption**: 10 points = $1 discount

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
php artisan test tests/Unit/Services/OrderServiceTest.php
```

### Run Specific Test Method
```bash
php artisan test --filter test_points_earned_calculation
```

## Test Cases Included

### 1. Points Earned Calculation Test
**Test**: `test_points_earned_calculation`
- Creates an order for $50
- Verifies that 500 points are earned (50 * 10)
- Verifies user's loyalty points are updated correctly

### 2. Points Redemption Discount Test
**Test**: `test_points_redemption_discount`
- User starts with 100 points
- Creates a $50 order, redeeming 50 points
- Verifies discount: 50 points = $5 off, final price = $45
- Verifies final points: 100 + 450 (earned) - 50 (spent) = 500

### 3. Insufficient Points Exception Test
**Test**: `test_insufficient_points_throws_exception`
- User has only 10 points
- Tries to redeem 50 points
- Verifies that an exception is thrown

### 4. Complete Order Flow Test
**Test**: `test_complete_order_flow_with_points`
- User starts with 200 points
- Creates order with multiple products totaling $50
- Redeems 100 points ($10 discount)
- Verifies final price: $40
- Verifies points earned: 500
- Verifies final user points: 600

## Manual Testing Steps

### Test Points Earning
1. Create a user
2. Create a product with price $100
3. Create an order for that product
4. Check that user earned 1000 points (100 * 10)

### Test Points Redemption
1. Create a user with 100 points
2. Create a product with price $50
3. Create an order, redeeming 50 points
4. Verify:
   - Order total: $45 (50 - 5)
   - Points spent: 50
   - Points earned: 500 (50 * 10)
   - Final user points: 550 (100 + 500 - 50)

### Test Insufficient Points
1. Create a user with 10 points
2. Try to create an order redeeming 50 points
3. Verify that an exception is thrown

## Example Usage in Code

```php
use App\Services\OrderService;

// Create order with points
$orderService = app(OrderService::class);

$orderData = [
    'user_id' => 1,
    'points_spent' => 100, // Redeem 100 points = $10 discount
    'order_status' => 'pending',
];

$items = [
    [
        'product_id' => 1,
        'quantity' => 1,
        'price' => 50.00,
    ],
];

$order = $orderService->createOrder($orderData, $items);

// Order total will be: $50 - $10 = $40
// Points earned: 500 (50 * 10)
// Points spent: 100
```

## Notes
- Points are calculated based on the original order total (before discount)
- Points redemption happens after calculating points earned
- User points are updated automatically when order is created
- Minimum points redemption is 10 points ($1 discount)

