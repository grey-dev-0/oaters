# Database Structure Documentation

This section provides comprehensive visual documentation of the database structure for all modules in the Oaters system. Each diagram is automatically generated from the Laravel migrations and updated regularly.

## Overview

The Oaters system uses a **modular architecture** powered by [Laravel Modules](https://nwidart.com/laravel-modules/) and implements **multi-tenancy** through [Stancl Tenancy](https://tenancyforlaravel.com/). The database structure reflects this organization, with each module maintaining its own isolated data models while core tables support cross-module functionality.

## Complete Database Schema

View the complete database structure that includes all modules and their relationships:

[Complete Database Diagram →](./database-diagram.md)

## Module-Specific Schemas

### SaaS Management Layer

- **[Core Database](./core-diagram.md)** - Super admin management system for the SaaS platform, handling tenant registration, subscription management, module licensing, and multi-tenant data isolation

### Core Infrastructure

- **[Sapphire Module](./sapphire-diagram.md)** - Centralized tenant and user management system providing authentication, authorization, multi-tenancy, and access control
- **[Common Module](./common-diagram.md)** - Shared entities including countries, cities, contacts, timezones, and currencies used across all modules

### Business Modules

- **[Ruby Module](./ruby-diagram.md)** - Human Resource management system handling employee records, payroll, attendance, leave management, and recruitment workflows
- **[Onyx Module](./onyx-diagram.md)** - Production and product line management for manufacturing operations, inventory tracking, and production workflow optimization
- **[Amethyst Module](./amethyst-diagram.md)** - E-commerce platform and online store for selling products and services with integrated shopping cart, payment processing, and order management
- **[Emerald Module](./emerald-diagram.md)** - Project management for task assignment, progress tracking, team coordination, and project delivery oversight
- **[Article Module](./article-diagram.md)** - Content management system with articles, categories, and properties

#### Modules In Development
- **Topaz Module** - Financial management and analysis system featuring accounting, budgeting, financial reporting, and comprehensive business analytics (database schema coming soon)

## Database Design Principles

### Multi-Tenancy Architecture

Each module respects the Stancl Tenancy configuration:
- **Tenant-specific tables** are prefixed by module codes (e.g., `lc_*` for Common, `la_*` for Article)
- **Core tables** (prefixed with `s_*`) are shared across all tenants
- **Isolation** ensures data privacy and security for different clients

### Naming Conventions

Tables follow a consistent naming pattern to indicate their module:
- `lc_*` - Common module tables
- `la_*` - Article module tables
- `le_*` - Commerce module tables
- `s_*` - Sapphire/Core system tables

### Relationship Types

The diagrams show foreign key relationships using Mermaid ERD notation:
- **||--o{** indicates a one-to-many relationship
- Relationship labels show the foreign key column name for clarity

## How to Update Diagrams

To regenerate database diagrams after adding new migrations:

```bash
php artisan db:generate-diagrams
```

This command will:
1. Scan all migrations in `Modules/*/Database/migrations/` and `database/migrations/`
2. Parse `Schema::create` and foreign key definitions
3. Generate Mermaid ERD diagrams for each module
4. Create a comprehensive diagram of all tables

**Output Directory:** `docs/database/`

## Database Statistics

| Module | Tables | Key Features |
|--------|--------|---------------|
| Sapphire | 7 tables | User management, authentication, roles, permissions |
| Common | 13 tables | Countries, cities, contacts, timezones, currencies |
| Ruby | 15+ tables | Employees, payroll, attendance, leaves, departments |
| Onyx | Varies | Production management, inventory, workflows |
| Amethyst | Varies | Shopping cart, orders, product management |
| Topaz | Varies | Accounting, budgeting, financial reporting |
| Emerald | Varies | Projects, tasks, team coordination |
| Article | 8 tables | Articles, categories, properties, options |

## Related Documentation

- [Frontend Architecture](../development/frontend-architecture.md) - How the frontend interacts with the database layer
- [Getting Started - Frontend](../development/getting-started-frontend.md) - Frontend development guide
