---
layout: home

hero:
  name: "OATERS"
  text: "Integrated ERP System"
  tagline: A comprehensive, modular Enterprise Resource Planning system built with Laravel
  actions:
    - theme: brand
      text: View Modules
      link: /modules/sapphire
    - theme: alt
      text: View on GitHub
      link: https://github.com/grey-dev-0/oaters

features:
  - icon: 🏭
    title: Onyx (O)
    details: Production and product line management module for manufacturing operations, inventory tracking, and production workflow optimization.
    link: /modules/onyx
  
  - icon: 🛍️
    title: Amethyst (A)
    details: E-commerce platform and online store for selling products and services with integrated shopping cart, payment processing, and order management.
    link: /modules/amethyst
  
  - icon: 💰
    title: Topaz (T)
    details: Financial management and analysis system featuring accounting, budgeting, financial reporting, and comprehensive business analytics.
    link: /modules/topaz
  
  - icon: 📋
    title: Emerald (E)
    details: Project management module for task assignment, progress tracking, team coordination, and project delivery oversight.
    link: /modules/emerald
  
  - icon: 👥
    title: Ruby (R)
    details: Human Resource management system handling employee records, payroll, attendance, leave management, and recruitment workflows.
    link: /modules/ruby
  
  - icon: 🔐
    title: Sapphire (S)
    details: Centralized tenant and user management system providing authentication, authorization, multi-tenancy, and access control.
    link: /modules/sapphire
---

## About OATERS

**OATERS** is a modern, modular ERP (Enterprise Resource Planning) system built on Laravel, designed to streamline business operations across multiple domains. The system integrates six powerful modules, each represented by a precious gemstone, working together to provide a complete business management solution.

### 🎯 System Overview

OATERS stands for a comprehensive suite of business management tools:
- **O**nyx - Production & Manufacturing
- **A**methyst - E-commerce & Sales
- **T**opaz - Financial Management
- **E**merald - Project Management
- **R**uby - Human Resources
- **S**apphire - User & Tenant Management

### ✨ Key Features

- **🔧 Modular Architecture**: Each module operates independently while seamlessly integrating with others
- **🏢 Multi-Tenant**: Built-in support for multiple organizations with isolated data via Stancl Tenancy
- **🔒 Secure**: Enterprise-grade security with role-based access control using Spatie Permission
- **🌍 Multi-Language**: Full localization support via Astrotomic Translatable
- **📊 Analytics**: Comprehensive reporting and business intelligence across all modules
- **🚀 Scalable**: Built on Laravel for performance and scalability
- **🎨 Modern UI**: Clean, intuitive interface for enhanced user experience
- **📱 Responsive**: Works seamlessly across desktop, tablet, and mobile devices
- **🔌 API-First**: RESTful APIs for integration with third-party systems

### 🏗️ Technology Stack

- **Backend**: Laravel 10+ (PHP 8.1+)
- **Module System**: nwidart/laravel-modules (^10.0)
- **Database**: MySQL/PostgreSQL with tenant isolation
- **Multi-Tenancy**: stancl/tenancy (^3.8)
- **Permissions**: spatie/laravel-permission (^6.4)
- **Localization**: astrotomic/laravel-translatable (^11.12)
- **Frontend**: Blade Templates / Vue.js 3
- **Build Tool**: Vite (Rolldown)
- **Authentication**: Laravel Sanctum / Passport
- **Queue System**: Redis / Laravel Queue
- **Caching**: Redis / Memcached
- **Documentation**: VitePress

### 🏛️ Module Architecture

The OATERS system is built on three foundational layers:

#### 1. Core Layer
- **Common Module**: Shared entities (Contacts, Addresses, Countries, etc.)
- **Sapphire Module**: Authentication, authorization, and multi-tenancy

#### 2. Business Layer (Currently Documented)
- **Ruby Module**: Human Resources ✅ Documented
- Additional business modules in development

#### 3. Business Layer (In Development)
- **Onyx Module**: Production & Manufacturing 🚧 Coming soon
- **Amethyst Module**: E-commerce & Sales 🚧 Coming soon
- **Topaz Module**: Financial Management 🚧 Coming soon
- **Emerald Module**: Project Management 🚧 Coming soon

### 🎪 Module Integration

The OATERS system is designed with integration at its core:

- **Common** provides shared entities (Contacts, Addresses, etc.) used across all modules
- **Sapphire** provides centralized authentication and tenant management for all modules
- **Ruby** integrates with Common for employee contact management
- **Onyx** production data will feed into **Amethyst** for product availability
- **Amethyst** sales transactions will integrate with **Topaz** for financial tracking
- **Emerald** project costs and budgets will sync with **Topaz**
- **Ruby** payroll and HR expenses will integrate with **Topaz** financial reports
- Cross-module reporting provides unified business insights

### 📚 Available Documentation

#### Completed Modules
- [**Sapphire Module**](/modules/sapphire) - Authentication, multi-tenancy, and user management
- [**Ruby Module**](/modules/ruby) - Human resources and employee management

#### Modules In Development
Documentation will be added as modules are developed:
- **Onyx Module** - Production & Manufacturing (coming soon)
- **Amethyst Module** - E-commerce & Sales (coming soon)
- **Topaz Module** - Financial Management (coming soon)
- **Emerald Module** - Project Management (coming soon)

### 🔗 Key Integrations

#### Sapphire ↔ Common
- Users link to Contacts for extended information
- Contacts can have roles and permissions
- Shared authentication across modules

#### Ruby ↔ Common
- Employees are stored as Contacts
- Departments link to Contacts for managers and staff
- Applicants extend Contact information

#### Ruby ↔ Sapphire
- Authentication and session management
- Permission-based access control
- Tenant-scoped data isolation

### 🚧 Development Status

**OATERS is currently under active development.** The following components are available:

✅ **Completed:**
- Sapphire module (authentication, multi-tenancy)
- Ruby module (HR management foundation)
- Common module (shared entities)
- Documentation infrastructure (VitePress)

🔄 **In Progress:**
- Ruby module enhancements (payroll, leave approval)
- Additional business modules

📋 **Planned:**
- Onyx, Amethyst, Topaz, and Emerald modules
- API documentation
- Installation and deployment guides
- Developer documentation

### 🤝 Contributing

OATERS is under active development. We welcome contributions from the community! 

As documentation is completed, contribution guidelines will be added here.

### 📄 License

OATERS is open-source software. Please refer to the LICENSE file for more information.

---

<div style="text-align: center; margin-top: 2rem; color: #666;">
  <p>Built with ❤️ using Laravel and VitePress</p>
  <p>Version 1.0.0 (Under Development)</p>
</div>
