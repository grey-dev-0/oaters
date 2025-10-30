# Amethyst Module

## Overview

**Amethyst** is the shopping cart and e-commerce module of the OATERS ERP system. It provides essential functionality for managing customer shopping carts, handling product selection, and tracking cart contents. Amethyst integrates seamlessly with the Article module for product management and the Sapphire module for user authentication, enabling a complete e-commerce experience.

## Business Functionality

### Core Features

#### 1. Shopping Cart Management
- **User Carts**: Create and maintain individual shopping carts for authenticated users
- **Guest Carts**: Support for anonymous shopping via token-based cart identification
- **Cart Persistence**: Store cart items persistently with user or token association
- **Multiple Cart Support**: Users can maintain separate shopping sessions via tokens

#### 2. Product Selection & Management
- **Add Products**: Select and add articles to shopping carts
- **Quantity Tracking**: Manage product quantities with support for various measurement units
- **Item Removal**: Remove individual items or entire articles from cart
- **Cart Updates**: Modify quantities and product selections

#### 3. Cart Organization
- **Cart Items**: Detailed line items showing product, quantity, and unit
- **Unit Support**: Handle various measurement units (kg, liters, pieces, etc.)
- **Item Association**: Clear linking between cart items and articles

## Technical Architecture

### Technology Stack

- **Framework**: Laravel 10+
- **Module System**: nwidart/laravel-modules (^10.0)
- **Database**: MySQL/PostgreSQL (tenant-specific)
- **Authentication**: Integrated with Sapphire module
- **Multi-tenancy**: Tenant-scoped data via Sapphire module

## Database Schema

### Core Models

**Cart Model** (`Cart.php`)

```php
// Shopping carts for users and guests
// Table: a_carts
```

Schema:
- id: Primary key (increments)
- user_id: Foreign key to users (nullable, unique - one cart per user)
- token: Guest cart identifier (40 chars, nullable, unique)
- created_at, updated_at: Timestamps

Features:
- User-based carts: Authenticated users have unique carts linked via user_id
- Guest carts: Anonymous users identified via token for cart persistence
- Either user_id or token can be set, but at least one must exist

Relationships:
- `user()`: belongsTo(User) from Sapphire module
- `items()`: hasMany(CartArticle) - cart contents
- `articles()`: belongsToMany(Article) via CartArticle

**CartArticle Model** (`CartArticle.php`)

```php
// Individual items in a shopping cart
// Table: a_cart_articles
```

Schema:
- cart_id: Part of composite primary key
- article_id: Part of composite primary key (foreign key to articles)
- quantity: Item quantity (float)
- quantity_unit: Unit of measurement (10 chars - kg, l, pieces, etc.)

Relationships:
- `cart()`: belongsTo(Cart)
- `article()`: belongsTo(Article) from Article module

## Directory Structure

```
Modules/Amethyst/
├── App/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── CartController.php         # Cart management
│   │       └── [Other e-commerce controllers]
│   ├── Models/                   # Eloquent models (2 models)
│   │   ├── Cart.php
│   │   └── CartArticle.php
│   └── Providers/
│       └── ModuleServiceProvider.php
├── Database/
│   ├── Factories/               # Model factories for testing
│   ├── migrations/              # Database migrations
│   │   └── 2024_04_02_102150_amethyst_base.php
│   └── Seeders/                # Database seeders
├── resources/
│   ├── views/                  # Blade templates
│   │   └── cart/               # Cart display templates
│   └── lang/                   # Translations
│       └── en/
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes (cart operations)
├── config/
│   └── config.php              # Module configuration
└── module.json                 # Module metadata
```

## Key Relationships

```
User (Sapphire) (1) ─────── (1) Cart
                            ↓
                        CartArticle (N)
                            ↓
                        Article (Article module)

Cart (1) ─────── (N) CartArticles
CartArticle (N) ─────── (1) Article
```

## Integration with Other Modules

### Article Module Integration
Amethyst integrates with the Article module for product management:
- References articles in cart items
- Retrieves article details for cart display
- Links to article inventory and pricing

### Sapphire Module Integration
- User authentication and authorization
- User identification for cart management
- Tenant-scoped cart data

## Shopping Cart Workflows

### User Cart Workflow
1. **User Authentication**: User logs in to the system
2. **Create/Retrieve Cart**: System retrieves or creates user's cart
3. **Add Items**: User selects articles and adds to cart with quantities
4. **Update Quantities**: User can modify item quantities
5. **Remove Items**: User can delete items from cart
6. **Cart Persistence**: Cart data automatically saved with user

### Guest Cart Workflow
1. **Generate Token**: Create unique token for anonymous user
2. **Identify Cart**: Use token for guest cart identification
3. **Add Items**: Anonymous user adds products to cart
4. **Maintain State**: Cart persists via token across sessions
5. **Convert to User**: Optional - guest can register and claim cart

## Usage Examples

### Retrieve User Cart
```php
// Get current user's cart
$user = auth()->user();
$cart = $user->cart;

// Access cart items
$items = $cart->items;

// Get associated articles
$articles = $cart->articles;
```

### Manage Cart Items
```php
// Add item to cart
CartArticle::create([
    'cart_id' => $cart->id,
    'article_id' => $article->id,
    'quantity' => 5,
    'quantity_unit' => 'kg'
]);

// Update quantity
$cartItem = CartArticle::find(['cart_id', 'article_id']);
$cartItem->update(['quantity' => 10]);

// Remove item
CartArticle::where('cart_id', $cart->id)
    ->where('article_id', $article->id)
    ->delete();
```

### Guest Cart Management
```php
// Create guest cart
$token = Str::random(40);
$cart = Cart::create(['token' => $token]);

// Later retrieve by token
$cart = Cart::where('token', $token)->first();
```

## API Endpoints

### Cart Operations
- GET `/api/cart` - Retrieve current cart
- POST `/api/cart/items` - Add item to cart
- PUT `/api/cart/items/{id}` - Update cart item quantity
- DELETE `/api/cart/items/{id}` - Remove item from cart
- GET `/api/cart/summary` - Get cart summary (total items, etc.)

## Best Practices

### Cart Management
- Always validate article existence before adding to cart
- Check article availability before processing orders
- Handle quantity units consistently across system
- Consider cart cleanup for abandoned guest carts (periodic cleanup)

### Data Integrity
- Use transactions when updating multiple cart items
- Validate quantity values (positive, numeric)
- Ensure unit consistency with article definitions

### Performance Optimization
- Cache cart item counts for dashboard display
- Use eager loading when retrieving full cart with articles
- Consider pagination for carts with many items

## Troubleshooting

### Cart Not Appearing
- Verify user is authenticated (for user-based carts)
- Check database for cart record existence
- Confirm user_id foreign key relationship

### Items Not Showing
- Verify CartArticle records exist
- Check article_id references valid articles
- Ensure quantity and quantity_unit are properly set

### Guest Cart Issues
- Verify token is valid and stored in cookie/session
- Check token uniqueness in database
- Confirm token is being retrieved correctly on subsequent requests

## Codebase Integration Patterns

The module follows OATERS patterns:

```php
// Module Service Provider
public function register(): void
{
    // Register configurations
    $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'amethyst');
}

public function boot(): void
{
    // Load migrations
    $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
    
    // Load routes
    $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    
    // Load views
    $this->loadViewsFrom(__DIR__ . '/../resources/views', 'amethyst');
}

// Cart scopes for querying
Cart::query()
    ->where('user_id', $userId)
    ->with('items.article')
    ->first();
```

## Development Notes

- **Priority**: 5
- **Alias**: amethyst
- **Service Provider**: `Modules\\Amethyst\\App\\Providers\\ModuleServiceProvider`
- **Dependencies**:
  - **Article module** (for product/article references)
  - **Sapphire module** (for user authentication)

## Future Enhancements

Planned expansions include:

1. **Saved Carts**: Allow users to save multiple named carts
2. **Cart Sharing**: Share carts with other users or via link
3. **Recommendations**: AI-based product recommendations
4. **Wishlists**: Save items for later purchase
5. **Cart Analytics**: Track abandoned carts and user behavior
6. **Bulk Operations**: Import/export cart contents
7. **Promotions Integration**: Apply discounts and coupon codes
8. **Checkout Flow**: Complete order placement from cart
9. **Cart Recovery**: Automated emails for abandoned carts
10. **Mobile Optimization**: PWA support for mobile shopping
