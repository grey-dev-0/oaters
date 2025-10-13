# OATERS ERP

<div align="center">

**A comprehensive, modular Enterprise Resource Planning system built with Laravel**

[![Laravel](https://img.shields.io/badge/Laravel-10+-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Development Status](https://img.shields.io/badge/status-In%20Development-orange.svg)](#development-status)

</div>

## Overview

**OATERS** is a modern, modular ERP (Enterprise Resource Planning) system designed to streamline business operations across multiple domains. Built on Laravel with a focus on multi-tenancy, scalability, and modularity, OATERS provides a comprehensive suite of business management tools.

### What does OATERS stand for?

Each letter represents a core business module:

- **🏭 O**nyx - Production & Manufacturing
- **🛍️ A**methyst - E-commerce & Sales  
- **💰 T**opaz - Financial Management
- **📋 E**merald - Project Management
- **👥 R**uby - Human Resources
- **🔐 S**apphire - Authentication & Tenant Management

## Key Features

- **🔧 Modular Architecture** - Each module operates independently while integrating seamlessly
- **🏢 Multi-Tenant** - Complete data isolation between organizations using Stancl Tenancy
- **🔒 Enterprise Security** - Role-based access control with Spatie Permission
- **🌍 Multi-Language** - Full localization support via Astrotomic Translatable
- **🚀 Scalable** - Built on Laravel for performance and growth
- **🔌 API-First** - RESTful APIs for third-party integrations
- **📊 Analytics Ready** - Comprehensive reporting across all modules

## Technology Stack

| Component | Technology |
|-----------|------------|
| **Backend** | Laravel 10+ (PHP 8.1+) |
| **Module System** | nwidart/laravel-modules |
| **Database** | MySQL/PostgreSQL |
| **Multi-Tenancy** | stancl/tenancy |
| **Permissions** | spatie/laravel-permission |
| **Localization** | astrotomic/laravel-translatable |
| **Frontend** | Blade Templates / Vue.js 3 |
| **Build Tool** | Vite (Rolldown) |
| **Documentation** | VitePress |

## Architecture

OATERS is built on three foundational layers:

### Core Layer
- **Common Module** - Shared entities (Contacts, Addresses, Countries, etc.)
- **Sapphire Module** - Authentication, authorization, and multi-tenancy

### Business Layer
- **Ruby Module** - Human Resources ✅ *Active Development*
- **Onyx Module** - Production & Manufacturing 🚧 *Planned*
- **Amethyst Module** - E-commerce & Sales 🚧 *Planned*
- **Topaz Module** - Financial Management 🚧 *Planned*
- **Emerald Module** - Project Management 🚧 *Planned*

## Development Status

OATERS is currently under **active development**. Here's what's available:

### ✅ Completed
- **Sapphire Module** - Multi-tenant authentication and user management
- **Ruby Module** - HR management foundation with employee, department, and recruitment features
- **Common Module** - Shared entities and contact management
- **Documentation Infrastructure** - VitePress-based documentation system

### 🔄 In Progress
- Ruby Module enhancements (payroll system, leave approval workflows)
- API documentation
- Installation and deployment guides

### 📋 Planned
- Onyx, Amethyst, Topaz, and Emerald modules
- Comprehensive testing suite
- Docker containerization
- CI/CD pipeline

## Module Integration

The system is designed with deep integration:

```
Common Module (Shared Entities)
    ↓
Sapphire Module (Auth & Tenancy)
    ↓
Business Modules (Ruby, Onyx, Amethyst, etc.)
```
- **Common** provides shared entities used across all modules
- **Sapphire** handles authentication and ensures tenant data isolation  
- **Ruby** integrates with Common for employee contact management
- Future modules will integrate for seamless data flow (e.g., Ruby payroll → Topaz financial reports)

## Installation

> **Note**: OATERS is in active development. Installation instructions will be provided as the system stabilizes.

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & npm
- MySQL or PostgreSQL
- Redis (recommended)

### Quick Start
```bash
# Clone the repository
git clone https://github.com/yourusername/oaters.git
cd oaters

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup (configure your .env first)
php artisan migrate
php artisan db:seed

# Build assets
npm run production

# Serve the application
php artisan serve
```
## Documentation

Comprehensive documentation is available and growing:

- **[Full Documentation](https://grey-dev-0.github.io/oaters/)** - Complete module documentation
- **[Sapphire Module](https://grey-dev-0.github.io/oaters/modules/sapphire.md)** - Authentication & multi-tenancy
- **[Ruby Module](https://grey-dev-0.github.io/oaters/modules/ruby.md)** - Human resources management

To build and view documentation locally:
```bash
npm run docs:dev
```
## Contributing

We welcome contributions! As the project evolves, detailed contribution guidelines will be established.

### Current Development Priorities
1. Complete Ruby module payroll system
2. Develop remaining business modules (Onyx, Amethyst, Topaz, Emerald)
3. Comprehensive testing
4. API documentation
5. Installation and deployment automation

## Security

OATERS implements enterprise-level security:

- **Multi-tenant data isolation** via separate databases per tenant
- **Role-based access control** with granular permissions
- **Secure authentication** with password hashing and token-based API access
- **CSRF protection** and secure session handling

## License

OATERS is open-source software licensed under the [MIT License](LICENSE).

## Roadmap

### Phase 1: Foundation (Current)
- ✅ Core architecture and multi-tenancy
- 🔄 Sapphire and Ruby modules  
- 🔄 Documentation system

### Phase 2: Business Modules
- 📋 Onyx (Manufacturing)
- 📋 Amethyst (E-commerce)
- 📋 Topaz (Finance)
- 📋 Emerald (Project Management)

### Phase 3: Enterprise Features
- 📋 Advanced reporting and analytics
- 📋 API marketplace and integrations
- 📋 Mobile applications
- 📋 Enterprise deployment tools

---

<div align="center">

**Built with ❤️ using Laravel**

*Version 1.0.0 - Under Active Development*

</div>
