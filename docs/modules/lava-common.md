# Lava Common Modules

## Overview

The **Lava** framework comprises three foundational common modules that integrate and provide shared functionality across the OATERS system. These modules are designed to work together, providing essential entities and utilities used by all business modules (Ruby, Onyx, Amethyst, Topaz, and Emerald).

**Lava Modules:**
- **Article** (LA) - Content management and product catalog
- **Commerce** (LE) - E-commerce transactions and financial operations
- **Contact** (LC) - Contact management and geographic information

> **Note**: "Lava" is a framework module that precedes and integrates with the gemstone-based business modules.

## Module Architecture

```
┌───────────────────────────────────────────────────┐
│          LAVA COMMON FRAMEWORK                    │
├───────────────────────────────────────────────────┤
│  Article (LA)    │  Commerce (LE)  │  Contact(LC) │
└───────────────────────────────────────────────────┘
         ↓                ↓                 ↓
┌───────────────────────────────────────────────────┐
│    BUSINESS MODULES (Built on Lava)               │
├───────────────────────────────────────────────────┤
│ Ruby (R)  │ Onyx (O)  │ Amethyst (A)  │ Topaz (T) │
│ Emerald (E)  │ Sapphire (S) Authentication        │
└───────────────────────────────────────────────────┘
```

## Article Module (LA) - Content & Catalog

### Purpose
Manages articles, products, and their properties. Provides a flexible system for storing and organizing content with multi-language support and customizable attributes.

### Core Features

1. **Articles** (`la_articles`)
   - Base article/product records
   - Type classification (type field)
   - Timestamps for created/updated tracking

2. **Article Locales** (`la_article_locales`)
   - Multi-language support for articles
   - Title and description in multiple locales
   - Locale-specific content management

3. **Categories** (`la_categories`)
   - Hierarchical organization of articles
   - Multi-language category names

4. **Properties & Options**
   - Dynamic properties system for articles
   - Type-based property classification
   - Public/system property flags
   - Multi-language property names

5. **Article-Category Mapping** (`la_article_categories`)
   - Many-to-many relationship
   - Articles can belong to multiple categories

### Database Schema

#### Core Models

**Article Model** (`Article.php`)

```php
// Base articles/products with type classification
// Table: la_articles
```

Schema:
- id: Primary key (increments)
- type: Article type classification (tinyInteger)
- created_at, updated_at: Timestamps

Relationships:
- `translations()`: hasMany(ArticleLocale)
- `categories()`: belongsToMany(Category, 'la_article_categories')

**ArticleLocale Model** (`ArticleLocale.php`)

```php
// Multi-language article content
// Table: la_article_locales
```

Schema:
- id: Primary key (increments)
- article_id: Foreign key to articles
- title: Article title (string)
- description: Article description (text)
- locale: Language code (2 chars)

Relationships:
- `article()`: belongsTo(Article)

**Category Model** (`Category.php`)

```php
// Article categorization system
// Table: la_categories
```

Schema:
- id: Primary key (increments)
- created_at, updated_at: Timestamps

Relationships:
- `translations()`: hasMany(CategoryLocale)
- `articles()`: belongsToMany(Article, 'la_article_categories')

**CategoryLocale Model** (`CategoryLocale.php`)

```php
// Multi-language category names
// Table: la_category_locales
```

Schema:
- id: Primary key (increments)
- category_id: Foreign key to categories
- title: Category title (string)
- description: Category description (text)
- locale: Language code (2 chars)

Relationships:
- `category()`: belongsTo(Category)

**Property Model** (`Property.php`)

```php
// Dynamic article properties system
// Table: la_properties
```

Schema:
- id: Primary key (increments)
- type: Property type (tinyInteger)
- public: Public visibility flag (boolean, default true)
- system: System property flag (boolean, default false)

Relationships:
- `translations()`: hasMany(PropertyLocale)
- `options()`: belongsToMany(Option, 'la_property_options')

**PropertyLocale Model** (`PropertyLocale.php`)

```php
// Multi-language property names
// Table: la_property_locales
```

Schema:
- id: Primary key (increments)
- property_id: Foreign key to properties
- name: Property name (string)
- locale: Language code (2 chars)

Relationships:
- `property()`: belongsTo(Property)

**Option Model** (`Option.php`)

```php
// Property value options
// Table: la_options
```

Schema:
- id: Primary key (increments)

Relationships:
- `translations()`: hasMany(OptionLocale)
- `properties()`: belongsToMany(Property, 'la_property_options')

**OptionLocale Model** (`OptionLocale.php`)

```php
// Multi-language option names
// Table: la_option_locales
```

Schema:
- id: Primary key (increments)
- option_id: Foreign key to options
- name: Option name (string)
- locale: Language code (2 chars)

Relationships:
- `option()`: belongsTo(Option)

**ArticleCategory Model** (`ArticleCategory.php`)

```php
// Article-Category pivot table
// Table: la_article_categories
```

Schema:
- article_id: Primary key, foreign key to articles
- category_id: Primary key, foreign key to categories

**PropertyOption Model** (`PropertyOption.php`)

```php
// Property-Option pivot table
// Table: la_property_options
```

Schema:
- property_id: Primary key, foreign key to properties
- option_id: Primary key, foreign key to options

### Use Cases

- Product catalog management
- Blog articles and documentation
- Dynamic product specifications
- Multi-language content organization

## Commerce Module (LE) - Transactions & Commerce

### Purpose
Handles e-commerce operations including orders, payments, refunds, and financial transactions. Integrates with Contact module for customer information and Article module for product references.

### Core Features

1. **Bank Accounts** (`le_bank_accounts`)
   - Contact-associated bank account information
   - IBAN, SWIFT codes, account numbers
   - Multiple accounts per contact

2. **Orders** (`le_orders`)
   - Customer orders with contact reference
   - Shipping and billing addresses
   - Order amount and payment status
   - Delivery tracking

3. **Order Lines** (`le_order_lines`)
   - Individual items within orders
   - Links to articles (from Article module)
   - Quantity, units, and pricing
   - Discount support (fixed or percentage)

4. **Refunds** (`le_refunds`)
   - Polymorphic refund tracking
   - Refund amount and status
   - Return date tracking

5. **Order History** (`le_order_histories`)
   - Event-based order tracking
   - Timeline of order changes

6. **Transactions** (`le_transactions`)
   - Financial transaction records
   - Debit/credit tracking
   - Polymorphic transaction support

### Database Schema

#### Core Models

**BankAccount Model** (`BankAccount.php`)

```php
// Bank account information for contacts
// Table: le_bank_accounts
```

Schema:
- id: Primary key (increments)
- contact_id: Foreign key to contacts
- bank: Bank name (string)
- name: Account holder name (string)
- number: Account number (string)
- iban: IBAN code (string, nullable)
- swift: SWIFT code (string)
- created_at, updated_at: Timestamps

Relationships:
- `contact()`: belongsTo(Contact) from Common module

**Order Model** (`Order.php`)

```php
// Customer orders and purchase records
// Table: le_orders
```

Schema:
- id: Primary key (increments)
- contact_id: Foreign key to contacts (nullable - guest orders)
- shipping_address_id: Foreign key to addresses (nullable)
- billing_address_id: Foreign key to addresses (nullable)
- amount: Total order amount (float)
- paid: Payment status (boolean, default false)
- delivered_at: Delivery timestamp (nullable)
- created_at, updated_at: Timestamps

Relationships:
- `contact()`: belongsTo(Contact)
- `shippingAddress()`: belongsTo(Address)
- `billingAddress()`: belongsTo(Address)
- `lines()`: hasMany(OrderLine)
- `history()`: hasMany(OrderHistory)

**OrderLine Model** (`OrderLine.php`)

```php
// Individual items within orders
// Table: le_order_lines
```

Schema:
- id: Primary key (increments)
- order_id: Foreign key to orders
- article_id: Foreign key to articles (from Article module)
- quantity: Ordered quantity (float)
- quantity_unit: Unit of measurement (10 chars - kg, l, units, etc.)
- price: Unit price (float)
- discount: Discount amount (float)
- discount_type: Discount type (enum: fixed|percent)

Relationships:
- `order()`: belongsTo(Order)
- `article()`: belongsTo(Article) from Article module

**Refund Model** (`Refund.php`)

```php
// Polymorphic refund tracking system
// Table: le_refunds
```

Schema:
- id: Primary key (increments)
- refundable_id: Polymorphic foreign key
- refundable_type: Polymorphic type (30 chars)
- amount: Refund amount (float)
- paid: Refund payment status (boolean, default false)
- returned_at: Return timestamp (nullable)
- created_at, updated_at: Timestamps

Relationships:
- `refundable()`: morphTo() - Can refund orders, order lines, etc.

**OrderHistory Model** (`OrderHistory.php`)

```php
// Audit trail of order events and status changes
// Table: le_order_histories
```

Schema:
- id: Primary key (increments)
- order_id: Foreign key to orders
- event: Event description (text)
- time: Event timestamp
- created_at: Record creation time (nullable)

Relationships:
- `order()`: belongsTo(Order)

**Transaction Model** (`Transaction.php`)

```php
// Financial transaction records
// Table: le_transactions
```

Schema:
- id: Primary key (increments)
- transferable_id: Polymorphic foreign key
- transferable_type: Polymorphic type (30 chars)
- type: Transaction type (enum: debit|credit)
- amount: Transaction amount (float)
- created_at: Transaction timestamp (nullable)

Relationships:
- `transferable()`: morphTo() - Can be associated with orders, refunds, etc.

**Currency Model** (`Currency.php`)

```php
// Currency definitions for countries
// Table: le_currencies
```

Schema:
- id: Primary key (increments)
- name: Currency name (string)
- code: Currency code (10 chars)
- symbol: Currency symbol (25 chars)
- format: Display format (50 chars)
- exchange_rate: Exchange rate (string)
- active: Active status (boolean, default false)
- created_at, updated_at: Timestamps

Relationships:
- `countries()`: hasMany(Country) from Common module

### Use Cases

- E-commerce order management
- Payment and refund processing
- Customer financial records
- Transaction audit trails

## Contact Module (LC) - Contacts & Geography

### Purpose
Manages all contact-related entities and geographic information. Serves as the central repository for contacts, addresses, and location data used across all modules.

### Core Features

1. **Contacts** (`lc_contacts`)
   - Core contact entity
   - Links to Sapphire users
   - Personal information (name, job, image)
   - Gender, marital status, birthdate
   - Timezone association

2. **Phones** (`lc_phones`)
   - Multiple phone numbers per contact
   - Default phone flag
   - Up to 16-digit support

3. **Emails** (`lc_emails`)
   - Multiple email addresses per contact
   - Default email designation
   - Unique email tracking

4. **Addresses** (`lc_addresses`)
   - Multiple addresses per contact
   - Country and city association
   - Used for shipping/billing in Commerce module

5. **Geographic Data**
   - Countries with currency association
   - Cities with country hierarchy
   - Timezone information
   - Country-timezone mappings

6. **Colors** (`lc_colors`)
   - System color definitions
   - Multi-language color names
   - Used for UI theming and categorization

### Database Schema

#### Core Models

**Contact Model** (`Contact.php`)

```php
// Base contact entity for all person-related data
// Table: lc_contacts
// Uses Spatie Roles trait for permission management
```

Schema:
- id: Primary key (increments)
- user_id: Foreign key to Sapphire users (nullable)
- timezone_id: Foreign key to timezones (nullable)
- name: Contact name (string)
- job: Job title (string)
- image: Profile image (nullable)
- gender: Gender (enum: male|female|other, nullable)
- marital_status: Marital status code (tinyInteger, nullable)
- birthdate: Birth date (nullable)
- created_at, updated_at: Timestamps

Relationships:
- `phones()`: hasMany(Phone)
- `emails()`: hasMany(Email)
- `addresses()`: hasMany(Address)
- `user()`: belongsTo(User) from Sapphire module
- `timezone()`: belongsTo(Timezone)
- `roles()`: belongsToMany(Role) - Spatie Permission

**Phone Model** (`Phone.php`)

```php
// Contact phone numbers
// Table: lc_phones
```

Schema:
- id: Primary key (increments)
- contact_id: Foreign key to contacts
- number: Phone number (16 chars max)
- default: Default phone flag (boolean)

Relationships:
- `contact()`: belongsTo(Contact)

**Email Model** (`Email.php`)

```php
// Contact email addresses
// Table: lc_emails
```

Schema:
- id: Primary key (increments)
- contact_id: Foreign key to contacts
- address: Email address (string)
- default: Default email flag (boolean)

Relationships:
- `contact()`: belongsTo(Contact)

**Address Model** (`Address.php`)

```php
// Contact addresses with geographic location
// Table: lc_addresses
```

Schema:
- id: Primary key (increments)
- contact_id: Foreign key to contacts
- country_id: Foreign key to countries

Relationships:
- `contact()`: belongsTo(Contact)
- `country()`: belongsTo(Country)

**Country Model** (`Country.php`)

```php
// Country definitions with translations
// Table: lc_countries
// Uses Astrotomic Translatable
```

Schema:
- id: Primary key (increments)
- currency_id: Foreign key to currencies (nullable)
- code: Country code (3 chars)
- status: Active status (boolean)

Relationships:
- `translations()`: hasMany(CountryLocale)
- `currency()`: belongsTo(Currency)
- `cities()`: hasMany(City)
- `addresses()`: hasMany(Address)
- `timezones()`: belongsToMany(Timezone, 'lc_country_timezones')

**CountryLocale Model** (`CountryLocale.php`)

```php
// Multi-language country names
// Table: lc_country_locales
```

Schema:
- id: Primary key (increments)
- country_id: Foreign key to countries
- name: Country name (string)
- locale: Language code (2 chars)

Relationships:
- `country()`: belongsTo(Country)

**City Model** (`City.php`)

```php
// City definitions with translations
// Table: lc_cities
// Uses Astrotomic Translatable
```

Schema:
- id: Primary key (increments)
- country_id: Foreign key to countries
- status: Active status (boolean)

Relationships:
- `translations()`: hasMany(CityLocale)
- `country()`: belongsTo(Country)

**CityLocale Model** (`CityLocale.php`)

```php
// Multi-language city names
// Table: lc_city_locales
```

Schema:
- id: Primary key (increments)
- city_id: Foreign key to cities
- name: City name (string)
- locale: Language code (2 chars)

Relationships:
- `city()`: belongsTo(City)

**Timezone Model** (`Timezone.php`)

```php
// Timezone definitions
// Table: lc_timezones
```

Schema:
- id: Primary key (increments)
- identifier: Timezone identifier (string - e.g., "Europe/London")

Relationships:
- `countries()`: belongsToMany(Country, 'lc_country_timezones')
- `contacts()`: hasMany(Contact)

**Color Model** (`Color.php`)

```php
// System color definitions with translations
// Table: lc_colors
// Uses Astrotomic Translatable
```

Schema:
- id: Primary key (varchar 6 - hex color code)

Relationships:
- `translations()`: hasMany(ColorLocale)

**ColorLocale Model** (`ColorLocale.php`)

```php
// Multi-language color names
// Table: lc_color_locales
```

Schema:
- id: Primary key (increments)
- color_id: Foreign key to colors
- name: Color name (string)
- locale: Language code (2 chars)

Relationships:
- `color()`: belongsTo(Color)

## Key Relationships

```
// Article Module Relationships
Article (1) ─────── (N) ArticleLocale
Article (N) ─────── (N) Category (via ArticleCategory pivot)
Category (1) ─────── (N) CategoryLocale
Property (1) ─────── (N) PropertyLocale
Property (N) ─────── (N) Option (via PropertyOption pivot)
Option (1) ─────── (N) OptionLocale

// Commerce Module Relationships
Contact (Common) (1) ─────── (N) BankAccount
Contact (1) ─────── (N) Order
Address (Common) (1) ─────── (N) Order (shipping/billing addresses)
Order (1) ─────── (N) OrderLine
Order (1) ─────── (N) OrderHistory
Article (Article) (1) ─────── (N) OrderLine
Currency (1) ─────── (N) Country (Common)

// Polymorphic Relationships (Commerce)
Refund (N) ─────── (1) Order|OrderLine (polymorphic: refundable)
Transaction (N) ─────── (1) Order|Refund (polymorphic: transferable)

// Contact Module Relationships
User (Sapphire) (1) ─────── (1) Contact (nullable)
Timezone (1) ─────── (N) Contact
Contact (1) ─────── (N) Phone
Contact (1) ─────── (N) Email
Contact (1) ─────── (N) Address
Country (1) ─────── (N) Address
Country (1) ─────── (N) City
Country (N) ─────── (N) Timezone (via CountryTimezone pivot)
Country (1) ─────── (N) CountryLocale
City (1) ─────── (N) CityLocale
Color (1) ─────── (N) ColorLocale

// Cross-Module Integration
Contact (Common) (N) ─────── (N) Role (Spatie Permission)
Contact ─────── Ruby Module (Employee relationships)
Contact ─────── Commerce Module (Customer relationships)
User (Sapphire) ─────── Contact (Common) (profile extension)
```

### Use Cases

- User profile management
- Customer and vendor contact databases
- Geographic location tracking
- Multi-language support for locations
- Communication channel management

## Inter-Module Integration

### Article ↔ Commerce
- **Relationship**: Orders reference Articles through order lines
- **Purpose**: Track which products are sold in orders
- **Flow**: Products defined in Article → Sold through Commerce orders

### Commerce ↔ Contact
- **Relationship**: Orders linked to Contacts; addresses from Contact used for billing/shipping
- **Purpose**: Customer order management and delivery
- **Flow**: Customers (Contacts) → Place orders → Shipped to Contact addresses

### All Modules ↔ Contact
- **Relationship**: Contacts serve as central entity for all person-related data
- **Purpose**: Unified contact repository
- **Integration Points**:
  - Ruby: Employees are Contacts
  - Sapphire: Users linked to Contacts
  - Commerce: Customers are Contacts

## Key Characteristics

### Multi-Language Support
- All text-based entities have locale-specific tables
- Supports multiple languages without data duplication
- Used in Article categories, properties, countries, cities, and colors

### Polymorphic Relationships
- Refunds can apply to multiple entities
- Transactions can be associated with various transferable types
- Provides flexibility for future integrations

### Geographic Hierarchy
- Countries → Cities hierarchy
- Timezone associations at country level
- Currency support per country

### Extensibility
- Dynamic properties system in Article module
- Type-based classification for articles and properties
- Flag-based system/public distinction for properties

## Development Guidelines

### When to Use Each Module

**Article Module:**
- Product/content storage and organization
- Dynamic product specifications
- Multi-language content management

**Commerce Module:**
- E-commerce transactions
- Order and refund management
- Financial transaction tracking

**Contact Module:**
- Contact information storage
- Geographic data management
- Address and communication tracking

### Best Practices

1. **Always use Contact for person-related data** - Don't duplicate contact information
2. **Leverage multi-language support** - Use locale tables for multi-language content
3. **Use polymorphic relationships** - Provide flexibility for future feature additions
4. **Maintain geographic hierarchy** - Validate city-country relationships
5. **Track financial transactions** - Use transaction tables for audit trails

## Database Setup

The Lava common modules are automatically migrated when the `CentralAppSeeder` runs:

```bash
php artisan db:seed --class=CentralAppSeeder
```

This creates all necessary tables for Article, Commerce, and Contact modules in the central database.

## Future Enhancements

- **Article Module**: Media attachment support, versioning system
- **Commerce Module**: Advanced inventory management, subscription support
- **Contact Module**: Enhanced geographic search, bulk import/export tools

## See Also

- [Ruby Module](/modules/ruby) - HR management using Contact entities
- [Sapphire Module](/modules/sapphire) - User management linking to Contacts
- [Frontend Guide](/development/getting-started-frontend) - Building interfaces with these modules
