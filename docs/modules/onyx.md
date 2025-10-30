# Onyx Module

## Overview

**Onyx** is the inventory and procurement management module of the OATERS ERP system. It provides comprehensive functionality for managing inventory, tracking purchases, planning production, and monitoring consumption of materials and products. Onyx enables organizations to maintain optimal stock levels, plan procurement efficiently, and execute production plans with detailed tracking.

## Business Functionality

### Core Features

#### 1. Purchase Management
- **Purchase Orders**: Create and manage procurement orders from suppliers
- **Purchase Tracking**: Monitor purchase status and payment tracking
- **Payment Management**: Integrate with financial module for bank account recording
- **Purchase History**: Audit trail of all purchase events and status changes
- **Line Items**: Detailed tracking of articles/products per purchase with quantity and pricing

#### 2. Inventory Planning
- **Product Plans**: Define material requirements and production plans
- **Quantity Planning**: Specify required quantities with measurement units
- **Plan Tracking**: Monitor planned vs. actual consumption
- **Resource Allocation**: Assign plans to specific users for execution
- **Article Integration**: Link plans to specific articles for precise tracking

#### 3. Production Execution
- **Plan Execution**: Execute production/consumption plans with real-time tracking
- **Execution Status**: Track execution progress (pending, in-progress, completed, failed)
- **Consumption Tracking**: Record actual material consumption against planned quantities
- **Execution Logs**: Detailed audit trail of all execution activities by user
- **Notes & Comments**: Attach notes to executions for documentation

#### 4. Consumption Monitoring
- **Planned Consumption**: Track consumption items defined in production plans
- **Actual Consumption**: Record real material consumption during execution
- **Consumption History**: Timeline of all consumption events with timestamps
- **Variance Analysis**: Compare planned vs. actual consumption
- **Quantity Unit Support**: Handle various measurement units (kg, liters, units, etc.)

## Technical Architecture

### Technology Stack

- **Framework**: Laravel 10+
- **Module System**: nwidart/laravel-modules (^10.0)
- **Database**: MySQL/PostgreSQL (tenant-specific)
- **Authentication**: Integrated with Sapphire module
- **Multi-tenancy**: Tenant-scoped data via Sapphire module

## Database Schema

### Core Models

**Purchase Model** (`Purchase.php`)

```php
// Procurement orders and purchase records
// Table: o_purchases
```

Schema:
- id: Primary key (increments)
- contact_id: Foreign key to contacts (supplier)
- bank_account_id: Foreign key to bank accounts (nullable - payment method)
- amount: Total purchase amount (float)
- paid: Payment status (boolean, default false)
- received_at: Timestamp when goods were received (nullable)
- created_at, updated_at: Timestamps

Relationships:
- `contact()`: belongsTo(Contact) from Common module
- `bankAccount()`: belongsTo(BankAccount) from financial module
- `lines()`: hasMany(PurchaseLine)
- `history()`: hasMany(PurchaseHistory)

**PurchaseLine Model** (`PurchaseLine.php`)

```php
// Individual line items in a purchase order
// Table: o_purchase_lines
```

Schema:
- id: Primary key
- purchase_id: Foreign key to purchases
- article_id: Foreign key to articles (from Article module)
- quantity: Ordered quantity (float)
- quantity_unit: Unit of measurement (10 chars - kg, l, units, etc.)
- price: Unit price (float)

Relationships:
- `purchase()`: belongsTo(Purchase)
- `article()`: belongsTo(Article)

**PurchaseHistory Model** (`PurchaseHistory.php`)

```php
// Audit trail of purchase events and status changes
// Table: o_purchase_histories
```

Schema:
- id: Primary key
- purchase_id: Foreign key to purchases
- event: Description of the event (text)
- time: When the event occurred (timestamp)
- created_at: Record creation time (nullable)

Relationships:
- `purchase()`: belongsTo(Purchase)

**ProductPlan Model** (`ProductPlan.php`)

```php
// Production/consumption plans defining material requirements
// Table: o_product_plans
```

Schema:
- id: Primary key
- user_id: Foreign key to users (plan creator/owner)
- article_id: Foreign key to articles (primary planned article)
- quantity: Planned quantity (float)
- quantity_unit: Unit of measurement (10 chars)

Relationships:
- `user()`: belongsTo(User) from Sapphire module
- `article()`: belongsTo(Article) from Article module
- `consumptions()`: hasMany(PlanConsumption)
- `executions()`: hasMany(PlanExecution)

**PlanConsumption Model** (`PlanConsumption.php`)

```php
// Materials planned to be consumed for a production plan
// Table: o_plan_consumptions
```

Schema:
- id: Primary key
- product_plan_id: Foreign key to product plans
- article_id: Foreign key to articles (consumption item)
- quantity: Consumption quantity (float)
- quantity_unit: Unit of measurement (10 chars)
- created_at, updated_at: Timestamps

Relationships:
- `productPlan()`: belongsTo(ProductPlan)
- `article()`: belongsTo(Article)

**PlanExecution Model** (`PlanExecution.php`)

```php
// Execution records of production plans with status tracking
// Table: o_plan_executions
```

Schema:
- id: Primary key
- user_id: Foreign key to users (executor)
- product_plan_id: Foreign key to product plans (nullable - plan may be deleted)
- plan_log_id: External plan log identifier (nullable string)
- status: Execution status (tiny int: 0=pending, 1=in-progress, 2=completed, 3=failed)
- note: Execution notes (text, nullable)
- created_at, updated_at: Timestamps

Relationships:
- `user()`: belongsTo(User)
- `productPlan()`: belongsTo(ProductPlan)
- `consumptions()`: hasMany(ExecutionConsumption)
- `logs()`: hasMany(ExecutionLog)

**ExecutionConsumption Model** (`ExecutionConsumption.php`)

```php
// Actual material consumption recorded during plan execution
// Table: o_execution_consumptions
```

Schema:
- id: Primary key
- plan_execution_id: Foreign key to plan executions
- article_id: Foreign key to articles (nullable - article may be deleted)
- quantity: Consumed quantity (float)
- quantity_unit: Unit of measurement (10 chars)
- created_at, updated_at: Timestamps

Relationships:
- `planExecution()`: belongsTo(PlanExecution)
- `article()`: belongsTo(Article)

**ExecutionLog Model** (`ExecutionLog.php`)

```php
// Audit trail of execution activities and status changes
// Table: o_execution_logs
```

Schema:
- id: Primary key
- user_id: Foreign key to users (who performed the action)
- plan_execution_id: Foreign key to plan executions
- status: Status at this log point (tiny int)
- note: Log notes/comments (text, nullable)
- created_at: When the log was created (nullable)

Relationships:
- `user()`: belongsTo(User)
- `planExecution()`: belongsTo(PlanExecution)

## Directory Structure

```
Modules/Onyx/
├── App/
│   ├── Http/
│   │   └── Controllers/
│   │       └── [Controllers for purchase, plan, and execution management]
│   ├── Models/                   # Eloquent models (7 models)
│   │   ├── Purchase.php
│   │   ├── PurchaseLine.php
│   │   ├── PurchaseHistory.php
│   │   ├── ProductPlan.php
│   │   ├── PlanConsumption.php
│   │   ├── PlanExecution.php
│   │   ├── ExecutionConsumption.php
│   │   └── ExecutionLog.php
│   └── Providers/
│       └── ModuleServiceProvider.php
├── Database/
│   ├── Factories/               # Model factories for testing
│   ├── migrations/              # Database migrations
│   │   └── 2024_04_02_104108_onyx_base.php
│   └── Seeders/                # Database seeders
├── resources/
│   ├── views/                  # Blade templates
│   └── lang/                   # Translations
│       └── en/
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes
├── config/
│   └── config.php              # Module configuration
└── module.json                 # Module metadata
```

## Key Relationships

```
Contact (Common) (1) ─────── (N) Purchases (supplier)
BankAccount (Finance) (1) ─────── (N) Purchases (payment method)

Purchase (1) ─────── (N) PurchaseLines
Purchase (1) ─────── (N) PurchaseHistory

PurchaseLine (N) ─────── (1) Article (Common/Article)

User (Sapphire) (1) ─────── (N) ProductPlans
ProductPlan (1) ─────── (N) PlanConsumption
ProductPlan (1) ─────── (N) PlanExecution

Article (Common/Article) (1) ─────── (N) PlanConsumption
Article (1) ─────── (N) ExecutionConsumption

PlanExecution (1) ─────── (N) ExecutionConsumption
PlanExecution (1) ─────── (N) ExecutionLog

User (Sapphire) (1) ─────── (N) PlanExecution
User (1) ─────── (N) ExecutionLog
```

## Integration with Other Modules

### Article Module Integration
Onyx integrates with the Article module for product/article management:
- References articles in purchase line items
- Tracks consumption of articles in production plans
- Links planned and actual consumption to specific articles

### Financial Module Integration
Onyx connects to Financial module for payment tracking:
- Associates purchases with bank accounts
- Tracks payment method for procurement
- Enables financial reconciliation

### Common Module Integration
- Uses Contact records for supplier management
- Integrates with User model for execution tracking

### Sapphire Module Integration
- User authentication for plan creators and executors
- Tenant-scoped data isolation

## Business Workflows

### Procurement Workflow
1. **Create Purchase Order**: Define required articles and quantities
2. **Record Supplier**: Link to contact (supplier)
3. **Specify Payment**: Associate with bank account if pre-defined
4. **Track Purchase**: Monitor purchase through system
5. **Record Receipt**: Update received_at when goods arrive
6. **Mark Paid**: Update payment status

### Production Planning Workflow
1. **Create Product Plan**: Define primary article and quantity
2. **Add Consumables**: Link consumption articles and quantities
3. **Assign Executor**: Associate with user responsible for execution
4. **Execute Plan**: Create execution record and track consumption
5. **Record Consumption**: Log actual material consumed
6. **Complete Execution**: Mark as completed/failed with notes

## Consumption Tracking

The module tracks consumption at two levels:

1. **Planned Level**: What you expect to consume (PlanConsumption)
2. **Actual Level**: What was actually consumed (ExecutionConsumption)

This enables:
- Variance analysis between planned and actual
- Inventory adjustment
- Cost tracking and analysis
- Efficiency metrics

## Development Notes

- **Priority**: 4
- **Alias**: onyx
- **Service Provider**: `Modules\\Onyx\\App\\Providers\\ModuleServiceProvider`
- **Dependencies**:
  - **Article module** (for article/product references)
  - **Common module** (for Contact/supplier management)
  - **Sapphire module** (authentication/authorization)
  - **Financial module** (optional - for bank account integration)

## Future Enhancements

Planned expansions include:

1. **Advanced Planning**: AI-based inventory level recommendations
2. **Supply Chain**: Multi-warehouse inventory distribution
3. **Quality Control**: Defect tracking and quality metrics
4. **Forecasting**: Predictive inventory requirements
5. **Vendor Management**: Supplier performance tracking and ratings
6. **Order Automation**: Automatic reorder when stock falls below threshold
7. **Cost Analysis**: Detailed procurement cost analysis and reporting
