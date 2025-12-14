# Order Creation - Business Logic & Process Flow

## Overview
This document explains what happens when a customer creates an order in the healthcare e-commerce system, and how it meets business requirements.

---

## Business Requirements

### 1. Order Processing
- Customers can purchase multiple products in one order
- Each product can have variants (Size, Color, etc.)
- Orders must track exact prices at time of purchase
- Orders must support promotions and discounts
- Orders must support loyalty points system

### 2. Pricing & Discounts
- Base price from product or variant
- Promotions can apply discounts
- Loyalty points can be redeemed (10 points = $1)
- Final price must be accurate and stored

### 3. Loyalty Points System
- Customers earn 10 points per dollar spent
- Points earned on ORIGINAL total (before discounts)
- Customers can spend points for discounts
- Points balance must be updated correctly

---

## Order Creation Process Flow

### STEP 1: Receive Order Data

**Input from Controller:**
```php
$orderData = [
    'user_id' => 1,              // Customer who placed order
    'promotion_id' => 5,         // Optional: Promotion code
    'category_id' => 3,          // Optional: Category for promotion
    'points_spent' => 100,       // Optional: Points to redeem
    'order_status' => 'pending'   // Default status
]

$items = [
    [
        'product_id' => 10,      // Required: Product ID
        'variant_id' => 5,       // Optional: Variant ID
        'quantity' => 2,         // Required: How many
        'price' => 29.99         // Optional: Price override
    ],
    // ... more items
]
```

---

### STEP 2: Calculate Order Total

**What happens:**
1. Loop through each item in `$items` array
2. For each item:
   - Fetch product from database
   - Validate product exists (throws error if not found)
   - Get price: Use `$item['price']` if provided, otherwise use `$product->price`
   - Get quantity: Use `$item['quantity']` or default to 1
   - Calculate: `price * quantity`
3. Sum all items: `$totalPrice = sum of (price * quantity) for all items`

**Example:**
- Item 1: $29.99 × 2 = $59.98
- Item 2: $19.99 × 1 = $19.99
- **Total: $79.97**

**Business Rule:** Product must exist, otherwise order fails.

---

### STEP 3: Apply Promotion Discount

**What happens:**
1. Check if `promotion_id` or `category_id` provided
2. If yes, call `PromotionService->applyPromotion()`
3. Promotion reduces the total price
4. New total = `$totalPrice - discount`

**Example:**
- Original total: $79.97
- Promotion: 10% off
- New total: $71.97

**Business Rule:** Promotions are optional and reduce price.

---

### STEP 4: Calculate Loyalty Points Earned

**What happens:**
1. Calculate points on ORIGINAL total (before promotion)
2. Formula: `floor(originalTotal * 10)`
3. Example: $79.97 × 10 = 799 points (rounded down)

**Important:** Points are earned on FULL purchase value, not discounted price.

**Business Rule:** Customers always earn points on original purchase amount.

---

### STEP 5: Apply Points Redemption

**What happens:**
1. Check if `points_spent > 0`
2. If yes:
   - Validate user exists
   - Validate user has enough points
   - Convert points to discount: `discount = points_spent / 10`
   - Apply discount: `$totalPrice = $totalPrice - discount`
   - Ensure total doesn't go below $0

**Example:**
- Current total: $71.97
- Points spent: 100 points
- Discount: 100 / 10 = $10.00
- New total: $71.97 - $10.00 = $61.97

**Business Rules:**
- User must exist for points redemption
- User must have enough points
- 10 points = $1 discount
- Total cannot go negative

---

### STEP 6: Create Order Record

**What happens:**
1. Create order in database with:
   - `user_id`: Customer ID (or null for guest)
   - `total_price`: Final price after all discounts
   - `total_points_earned`: Points customer earned
   - `total_points_spent`: Points customer spent
   - `order_status`: 'pending' (default)

**Database Record Example:**
```
Order ID: 123
User ID: 1
Total Price: $61.97
Points Earned: 799
Points Spent: 100
Status: pending
```

**Business Rule:** Order must be saved before creating items.

---

### STEP 7: Create Order Items

**What happens:**
1. Loop through each item in `$items` array
2. For each item, create an OrderItem record:
   - `order_id`: Link to order
   - `product_id`: Product purchased
   - `variant_id`: Variant selected (if any)
   - `quantity`: How many purchased
   - `price`: Price at time of order (snapshot)

**Important:** Price is stored at order time, so if product price changes later, the order price stays the same.

**Database Records Example:**
```
OrderItem 1:
  Order ID: 123
  Product ID: 10
  Variant ID: 5
  Quantity: 2
  Price: $29.99

OrderItem 2:
  Order ID: 123
  Product ID: 15
  Variant ID: null
  Quantity: 1
  Price: $19.99
```

**Business Rule:** Each product in order becomes one OrderItem record.

---

### STEP 8: Update User Loyalty Points

**What happens:**
1. Check if `user_id` exists (guest orders don't earn points)
2. If yes:
   - Get current user points balance
   - Calculate: `new_points = current_points + points_earned - points_spent`
   - Update user's loyalty_points in database
   - Ensure balance never goes below 0

**Example:**
- Current points: 500
- Points earned: 799
- Points spent: 100
- New balance: 500 + 799 - 100 = 1199 points

**Business Rules:**
- Only registered users earn points
- Points balance cannot go negative
- Points are updated immediately after order creation

---

## Complete Example Flow

### Customer Places Order:

**Input:**
- User ID: 1
- Products: 2x Product A ($29.99), 1x Product B ($19.99)
- Promotion: 10% off
- Points to redeem: 100 points

**Process:**
1. Calculate total: ($29.99 × 2) + ($19.99 × 1) = $79.97
2. Apply promotion: $79.97 - 10% = $71.97
3. Calculate points: floor($79.97 × 10) = 799 points
4. Apply points: $71.97 - ($100/10) = $61.97
5. Create order: Total = $61.97, Points earned = 799, Points spent = 100
6. Create items: 2 order items saved
7. Update user: Add 799 points, subtract 100 points

**Result:**
- Order created with ID 123
- Final price: $61.97
- Customer earned: 799 points
- Customer spent: 100 points
- Customer's new balance: +699 points

---

## Potential Issues & Improvements

### Issue 1: Variant Price Not Used ✅ FIXED
**Problem:** When `variant_id` is provided, code was using product price instead of variant price.

**Fixed:** Code now checks for variant and uses variant price if available, otherwise falls back to product price.

### Issue 2: No Stock Validation
**Problem:** Code doesn't check if product/variant is in stock before creating order.

**Should add:**
- Check product stock_quantity >= quantity
- If variant selected, check variant stock_quantity >= quantity
- Throw error if insufficient stock

### Issue 3: Inefficient Database Queries
**Problem:** Product is fetched twice - once in `calculateOrderTotal()` and again when creating items.

**Solution:** Cache product data or pass it along to avoid duplicate queries.

### Issue 4: No Variant Validation
**Problem:** Code doesn't verify that variant belongs to the product.

**Should add:**
- Validate variant.product_id matches item.product_id
- Throw error if mismatch

---

## Business Benefits

### 1. Accurate Pricing
- Prices stored at order time prevent price change issues
- All discounts applied correctly
- Final price is always accurate

### 2. Loyalty Program
- Customers earn points on full purchase value
- Points can be redeemed for discounts
- Points balance tracked accurately

### 3. Order History
- Complete order record saved
- All items tracked with quantities and prices
- Can recreate order details anytime

### 4. Promotions Support
- Flexible promotion system
- Can apply by promotion ID or category
- Discounts calculated correctly

---

## Summary

When a customer creates an order:

1. **Calculate** total from all items
2. **Apply** promotion discount (if any)
3. **Calculate** loyalty points earned
4. **Apply** points redemption discount (if any)
5. **Create** order record in database
6. **Create** order items (one per product)
7. **Update** customer's loyalty points balance

The system ensures:
- ✅ Accurate pricing
- ✅ Proper discount application
- ✅ Correct points calculation
- ✅ Order history preservation
- ✅ Customer loyalty tracking

