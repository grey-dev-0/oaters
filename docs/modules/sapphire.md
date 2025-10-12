# Sapphire Module

## Overview

**Sapphire** is the foundational module of the OATERS ERP system, providing centralized authentication, authorization, multi-tenancy, and user management capabilities. As the security and access control hub, Sapphire manages all tenant organizations, their subscriptions to other modules, and user permissions across the entire system.

## Business Functionality

### Core Features

#### 1. Multi-Tenancy Management
- **Tenant Registration**: Onboard new business organizations to the OATERS platform
- **Tenant Isolation**: Complete data separation between different organizations
- **Tenant Configuration**: Customize settings and preferences per tenant
- **Tenant Dashboard**: Super admin overview of all registered tenants
- **Tenant Authentication**: Tenants can authenticate as super-admin users to manage their organization

#### 2. Subscription Management
- **Module Subscriptions**: Tenants can subscribe to specific modules (Onyx, Amethyst, Topaz, Emerald, Ruby)
- **Subscription Plans**: Different pricing tiers and feature sets
- **Purchase Tracking**: Record and manage module purchases
- **Subscription Lifecycle**: Handle activations, renewals, and cancellations

#### 3. User Management
- **User Registration**: Create user accounts within tenant organizations
- **User Profiles**: Manage user information and preferences
- **Contact Integration**: Users link to Contact records from Common module for extended information
- **User Status**: Active, inactive, suspended user states

#### 4. Role-Based Access Control (RBAC)
- **Role Definition**: Create custom roles with specific permissions
- **Multi-language Support**: Role names in multiple languages via translation system
- **Permission Assignment**: Granular permission control using Spatie Permission
- **Role Assignment**: Assign roles to users and tenants for access control

#### 5. Authentication & Authorization
- **Dual Authentication**: Separate authentication for super-admin tenants and tenant-level users
- **Secure Login**: Tenant-aware authentication system
- **Session Management**: Secure session handling across modules
- **Password Management**: Reset, recovery, and security policies
- **API Authentication**: Token-based authentication for API access

## Technical Architecture

### Technology Stack

- **Framework**: Laravel 10+
- **Module System**: nwidart/laravel-modules (^10.0)
- **Multi-tenancy**: stancl/tenancy (^3.8)
- **Permission System**: spatie/laravel-permission (^6.4)
- **Localization**: astrotomic/laravel-translatable (^11.12)
- **Database**: MySQL/PostgreSQL with tenant-specific databases
- **Authentication**: Laravel Sanctum/Passport

### Database Schema

#### Core Models

**Tenant Model** (`Tenant.php`)

```php
// Represents a super-admin tenant organization
// Extends Stancl\Tenancy\Database\Models\Tenant
// Implements: Authenticatable, Authorizable, CanResetPassword
```

Key Features:
- Tenants are authenticatable users (can login as super-admin)
- Uses traits: `Authenticatable`, `Authorizable`, `CanResetPassword`, `MustVerifyEmail`, `HasDatabase`, `HasDomains`, `HasRoles`
- Custom columns: `id`, `user_id`, `name`, `email`, `password`, `hash`

Schema:
- id: Primary key (auto-increment)
- user_id: Associated user ID
- name: Organization name
- email: Contact email
- password: Hashed password for authentication
- hash: Security hash
- created_at, updated_at

Relationships:
- `subscriptions()`: hasMany(Subscription)

**User Model** (`User.php`)

```php
// Tenant-level users who access the system
// Table: s_users
```

Schema:
- id: Primary key (auto-increment)
- username: User login name
- password: Hashed password (100 chars)
- image: Profile image path (nullable)
- created_at, updated_at

Relationships:
- `contact()`: hasOne(Contact) - Links to Common module Contact for extended info

Features:
- Uses `HasFactory` and `HasRoles` traits
- Integrates with Spatie Permission for role management
- Links to Common module Contact for additional user information

**Role Model** (`Role.php`)

```php
// User roles for permission management
// Extends Spatie\Permission\Models\Role
// Table: roles (from Spatie Permission)
```

Features:
- Uses `Translatable` trait from Astrotomic
- Translated attribute: `title`
- Inherits all Spatie Permission functionality

Schema (from Spatie Permission migration):
- id: Primary key
- name: Role identifier (slug)
- guard_name: Authentication guard
- team_foreign_key: For team support (nullable)
- created_at, updated_at

Relationships:
- `translations()`: hasMany(RoleLocale) - via Astrotomic Translatable
- Plus all Spatie Permission relationships (permissions, users, etc.)

**RoleLocale Model** (`RoleLocale.php`)

```php
// Localized role information
// Table: role_locales (singular: role_locales)
```

Schema:
- id: Primary key
- role_id: Foreign key to roles
- locale: Language code (en, ar, etc.) - 2 chars
- title: Translated role name

Relationships:
- `role()`: belongsTo(Role)

**Module Model** (`Module.php`)

```php
// Available OATERS modules for subscription
```

Schema:
- id: Primary key
- name: Module name (Onyx, Amethyst, Topaz, Emerald, Ruby)
- slug: Module identifier
- description: Module description
- price: Base subscription price
- status: active/inactive
- No timestamps

Relationships:
- `subscriptions()`: belongsToMany(Subscription) via `tenant_modules` pivot

**Subscription Model** (`Subscription.php`)

```php
// Tenant subscriptions to modules
```

Schema:
- id: Primary key
- tenant_id: Foreign key to tenants
- status: active/expired/cancelled
- start_date: Subscription start
- end_date: Subscription expiry
- created_at, updated_at

Relationships:
- `tenant()`: belongsTo(Tenant)
- `modules()`: belongsToMany(Module) via `tenant_modules` pivot table

**Purchase Model** (`Purchase.php`)

```php
// Purchase transactions for subscriptions
```

Schema:
- id: Primary key
- subscription_id: Foreign key to subscriptions
- amount: Purchase amount
- payment_method: Payment type
- status: pending/completed/failed
- created_at, updated_at

Relationships:
- `subscription()`: belongsTo(Subscription)

## Directory Structure

```
Modules/Sapphire/
├── App/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ModuleController.php
│   │   │   │   ├── PurchaseController.php
│   │   │   │   └── TenantController.php
│   │   │   └── User/
│   │   │       └── ModuleController.php
│   │   └── Middleware/           # Module-specific middleware
│   ├── Models/                   # Eloquent models
│   │   ├── Tenant.php
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── RoleLocale.php
│   │   ├── Module.php
│   │   ├── Subscription.php
│   │   └── Purchase.php
│   └── Providers/                # Service providers
│       └── ModuleServiceProvider.php
├── Database/
│   ├── factories/                # Model factories
│   ├── migrations/               # Database migrations
│   │   ├── 2024_04_02_104130_sapphire_base.php
│   │   └── 2024_04_09_191538_sapphire_permissions.php
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/                    # Blade templates
│   └── lang/                     # Translations
│       └── en/
├── routes/
│   ├── web.php                   # Web routes
│   └── api.php                   # API routes
├── config/
│   └── config.php                # Module configuration
└── module.json                   # Module metadata
```

## Key Relationships

```
Tenant (1) ─────── (N) Subscriptions

Subscription (N) ─────── (N) Modules (via tenant_modules pivot)
Subscription (1) ─────── (N) Purchases

User (1) ─────── (1) Contact (Common module)
User (N) ─────── (N) Roles (via Spatie Permission)

Role (1) ─────── (N) RoleLocale
Role (N) ─────── (N) Permissions (via Spatie Permission)
Role (N) ─────── (N) Users (via Spatie Permission)

Tenant (1) ─────── (N) Roles (via Spatie Permission HasRoles trait)
```

## Tenancy Implementation

Sapphire uses `stancl/tenancy` for multi-tenancy:

1. **Tenant Identification**: By subdomain or domain
2. **Database Separation**: Each tenant has isolated database
3. **Automatic Tenant Context**: Middleware ensures tenant-specific data access
4. **Central Database**: Stores tenant information and global data
5. **Tenant Authentication**: Tenants can authenticate as super-admins to manage their organization

## Permission System

Uses **Spatie Permission** package for RBAC:

1. **Roles**: Defined with localized names via Astrotomic Translatable
2. **Permissions**: Granular permissions assigned to roles
3. **Guards**: Support for multiple authentication guards
4. **Teams**: Optional team-based permissions
5. **Direct Permissions**: Can assign permissions directly to users/tenants

## How Other Modules Use Sapphire

All other modules (Onyx, Amethyst, Topaz, Emerald, Ruby) depend on Sapphire for:

1. **Authentication**: User login and session management
2. **Authorization**: Permission checks via Spatie Permission
3. **Tenant Context**: Automatic data scoping to current tenant
4. **User Information**: Access to user profiles
5. **Contact Integration**: Extended user information via Common module Contact

### Example Integration

```php
// In any module controller
use Modules\Sapphire\App\Models\User;
use Modules\Sapphire\App\Models\Tenant;

// Get current tenant
$tenant = Tenant::current();

// Get current user
$user = auth()->user();

// Check permission (via Spatie Permission)
if ($user->can('manage-inventory')) {
    // Allow access to Onyx inventory features
}

// Get user's contact information
$contact = $user->contact;

// Check role
if ($user->hasRole('admin')) {
    // Admin-specific logic
}
```

## Integration with Common Module

Sapphire's User model integrates with the Common module's Contact model:

- Users have a one-to-one relationship with Contact
- Contact stores extended information (phones, emails, addresses, etc.)
- This separation allows for flexible user management
- Contact can be shared across multiple modules (e.g., Ruby for employees)

## API Endpoints

### Admin Routes
- Tenant management (CRUD)
- Module management
- Purchase tracking
- Dashboard statistics

### User Routes
- Module subscription browsing
- User profile management
- Role and permission viewing

## Security Considerations

1. **Password Hashing**: All passwords use Laravel's bcrypt hashing
2. **Password Reset**: Secure token-based password recovery
3. **Email Verification**: Optional email verification for users/tenants
4. **Role-Based Access**: Granular control over features and data
5. **Tenant Isolation**: Complete data separation between organizations
6. **Session Security**: Secure session handling with CSRF protection
7. **API Authentication**: Token-based auth for API access

## Development Notes

- **Priority**: 0 (highest - loads first)
- **Alias**: sapphire
- **Service Provider**: `Modules\Sapphire\App\Providers\ModuleServiceProvider`
- **Dependencies**: Common module (for Contact integration)
- **Required Packages**: stancl/tenancy, spatie/laravel-permission, astrotomic/laravel-translatable