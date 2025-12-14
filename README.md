# Health Care E-Commerce Platform

A comprehensive Laravel-based e-commerce platform for health care products with full Arabic (RTL) support, featuring product management, shopping cart, checkout, loyalty points system, and admin dashboard.

## 🚀 Features

### Frontend Features
- **Multi-language Support**: Full Arabic (RTL) and English support with dynamic language switching
- **Product Catalog**: Browse products by category with filtering and sorting
- **Product Details**: Detailed product pages with variants selection, quantity selector, and stock information
- **Shopping Cart**: Session-based cart with variant selection, quantity management, and loyalty points integration
- **Checkout System**: Streamlined checkout process with user authentication, address management, and order notes
- **Wishlist**: Save favorite products for later
- **Loyalty Points System**: Earn points on purchases and redeem for discounts
- **User Authentication**: Separate login flows for users and admins
- **Responsive Design**: Mobile-friendly interface with RTL support

### Admin Features
- **Dashboard**: Overview of orders, products, and categories
- **Product Management**: Full CRUD operations for products with variants support
- **Category Management**: Organize products into categories
- **Order Management**: View and update order statuses
- **Stock Management**: Track inventory levels for products and variants

### Technical Features
- **Repository Pattern**: Clean separation of data access logic
- **Service Layer**: Business logic encapsulation
- **Request Validation**: Form validation using Laravel Request classes
- **Middleware**: Role-based access control (admin/user)
- **Session Management**: Cart and language preferences stored in session
- **Database Migrations**: Well-structured database schema

## 📋 Requirements

- PHP >= 8.2
- Laravel >= 12.0
- MySQL/MariaDB or SQLite
- Composer
- Node.js & NPM

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/ahmed12348/health_care.git
   cd health_care
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` with your database credentials
   - Run migrations:
     ```bash
     php artisan migrate
     ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📁 Project Structure

```
health_care/
├── app/
│   ├── Helpers/
│   │   └── TranslationHelper.php      # Custom translation helper
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Frontend/               # Frontend controllers
│   │   │   │   ├── CartController.php
│   │   │   │   ├── CheckoutController.php
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   └── WishlistController.php
│   │   │   ├── Auth/
│   │   │   │   └── AuthenticatedSessionController.php
│   │   │   ├── ProductController.php  # Admin
│   │   │   ├── CategoryController.php # Admin
│   │   │   └── OrderController.php    # Admin
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php
│   │   └── Requests/
│   │       └── OrderRequest.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── ProductVariant.php
│   │   └── Wishlist.php
│   ├── Repositories/
│   │   ├── BaseRepository.php
│   │   ├── ProductRepository.php
│   │   ├── OrderRepository.php
│   │   └── UserRepository.php
│   └── Services/
│       ├── BaseService.php
│       ├── OrderService.php
│       └── ProductService.php
├── resources/
│   └── views/
│       ├── frontend/
│       │   ├── layouts/
│       │   │   └── app.blade.php
│       │   ├── pages/
│       │   │   ├── home.blade.php
│       │   │   ├── product.blade.php
│       │   │   ├── cart.blade.php
│       │   │   ├── checkout.blade.php
│       │   │   └── wishlist.blade.php
│       │   └── partials/
│       │       └── header.blade.php
│       └── admin/
│           └── [Admin views]
├── routes/
│   ├── web.php          # Frontend routes
│   ├── admin.php        # Admin routes
│   └── auth.php         # Authentication routes
└── public/
    └── front/
        └── assets/
            ├── css/
            │   └── rtl.css    # RTL-specific styles
            └── js/
```

## 🎯 Key Features Explained

### 1. Shopping Cart System
- **Session-based**: Cart stored in Laravel session
- **Variant Support**: Products with variants require selection before checkout
- **Quantity Management**: Update quantities with validation against stock
- **Loyalty Points Integration**: Apply points discount in cart

### 2. Checkout Process
- **Authentication Required**: Users must login to checkout
- **Auto-filled User Data**: Name, email, phone auto-populated
- **Editable Address**: Users can modify shipping address
- **Order Notes**: Optional notes field for special instructions
- **Payment Method**: Cash payment only (configurable)

### 3. Loyalty Points System
- **Earning**: 10 points per $1 spent (only on completed orders)
- **Redemption**: 10 points = $1 discount
- **Rules**:
  - Points earned only when order status = "completed"
  - If points are used, no new points earned
  - Points deducted immediately when used
  - Points restored if order cancelled

### 4. Product Variants
- **Variant Types**: Size, Color, etc. (configurable)
- **Variant Pricing**: Each variant can have different price
- **Stock Management**: Separate stock tracking per variant
- **Cart Integration**: Variant selection required in cart if not selected

### 5. Arabic (RTL) Support
- **Translation Helper**: Custom `TranslationHelper` for static text
- **RTL CSS**: Comprehensive RTL styles in `rtl.css`
- **Dynamic Language Switching**: Session-based language preference
- **Default Language**: Arabic (RTL) as default

### 6. Authentication System
- **Separate Login Flows**: User and admin login on same template
- **Role-based Access**: Middleware ensures admin-only routes
- **Session Management**: Automatic logout before new login
- **Login Tracking**: User login/logout events logged

## 🔐 User Roles

### User (Customer)
- Browse products
- Add to cart
- View wishlist
- Place orders
- Earn/redeem loyalty points

### Admin
- All user capabilities
- Manage products
- Manage categories
- Manage orders
- Update order statuses
- Access admin dashboard

## 📊 Database Schema

### Main Tables
- `users`: User accounts with roles and loyalty points
- `categories`: Product categories
- `products`: Product information
- `product_variants`: Product variants (size, color, etc.)
- `orders`: Customer orders
- `order_items`: Order line items
- `wishlists`: User wishlists
- `user_logins`: Login tracking

## 🎨 Frontend Technologies

- **Laravel Blade**: Template engine
- **Bootstrap**: CSS framework
- **jQuery**: JavaScript library
- **Custom RTL CSS**: Right-to-left layout support
- **Font Awesome**: Icons

## 🔧 Configuration

### Language Settings
Default language is Arabic. To change:
- Edit `app/Helpers/TranslationHelper.php`
- Update `session('locale', 'ar')` to desired default

### Loyalty Points
- Earning rate: 10 points per $1 (configurable in `OrderService`)
- Redemption rate: 10 points = $1 (configurable in `OrderService`)

## 📝 Routes

### Frontend Routes
- `/` - Home page
- `/products` - Product listing
- `/products/{id}` - Product details
- `/categories/{id}` - Category products
- `/cart` - Shopping cart
- `/checkout` - Checkout (auth required)
- `/wishlist` - Wishlist (auth required)

### Admin Routes
- `/admin/dashboard` - Admin dashboard
- `/admin/products` - Product management
- `/admin/categories` - Category management
- `/admin/orders` - Order management

## 🧪 Testing

Run tests with:
```bash
php artisan test
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👤 Author

**Ahmed**
- GitHub: [@ahmed12348](https://github.com/ahmed12348)

## 🙏 Acknowledgments

- Laravel Framework
- Bootstrap
- Font Awesome
- All contributors and users

---

## 📌 Recent Updates

### Latest Features
- ✅ Variant selection in cart before checkout
- ✅ Improved product page layout for Arabic
- ✅ Quantity selector with +/- buttons
- ✅ Enhanced RTL support
- ✅ Loyalty points system refinement
- ✅ Checkout process simplification
- ✅ Order notes support
- ✅ Dynamic cart count in header

---

**Note**: This is an active development project. Features and documentation are continuously updated.
