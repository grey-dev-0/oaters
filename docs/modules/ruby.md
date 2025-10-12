# Ruby Module

## Overview

**Ruby** is the Human Resource management module of the OATERS ERP system. It provides comprehensive HR functionality including employee management, payroll processing, attendance tracking, leave management, recruitment, and organizational structure management. Ruby heavily integrates with the **Common module** for contact management and employee information.

## Business Functionality

### Core Features

#### 1. Employee Management
- **Employee Profiles**: Complete employee information via Common module Contact integration
- **Department Organization**: Organize employees by departments with hierarchical structure
- **Document Management**: Store and manage employee/applicant documents
- **Reporting Relationships**: Define manager-subordinate hierarchies within departments

#### 2. Payroll Management
- **Salary Structure**: Define salary types (hourly, monthly, annual)
- **Payment Components**: Manage earnings, allowances, and incentives
- **Payroll Processing**: Calculate and process payroll payments
- **Component Tracking**: Track individual salary components per employee
- **Multi-language Support**: Payroll component names in multiple languages

#### 3. Attendance & Time Tracking
- **Punch In/Out**: Record employee check-in and check-out times
- **Attendance Records**: Track employee presence with timestamp tracking
- **Simple Time Tracking**: Streamlined punch system with in/out types

#### 4. Leave Management
- **Leave Applications**: Submit and manage leave requests
- **Leave Types**: Support for various leave categories (sick, annual, casual)
- **Leave Status**: Approve or reject leave applications
- **Date Range Tracking**: Track leave start and end dates

#### 5. Recruitment Management
- **Job Vacancies**: Post and manage job openings by department
- **Vacancy Localization**: Multi-language job titles and descriptions
- **Applicant Tracking**: Manage job applications with contact integration
- **Applicant Documents**: Store resumes, certificates, and related files
- **Recruitment Pipeline**: Track candidates through hiring process

#### 6. Organizational Structure
- **Department Management**: Create and manage organizational departments
- **Department Localization**: Multi-language department names
- **Degree/Position Management**: Define job titles and educational requirements
- **Hierarchical Structure**: Build complex organizational charts with manager-employee relationships
- **Department Heads**: Automatically identify department leadership

#### 7. Notices & Communications
- **Company Notices**: Broadcast announcements to employees
- **Notice Types**: Support for notices, warnings, and terminations
- **Targeted Communications**: Send notices to specific employees
- **Payroll-Linked Notices**: Associate notices with payroll payments

## Technical Architecture

### Technology Stack

- **Framework**: Laravel 10+
- **Module System**: nwidart/laravel-modules (^10.0)
- **Localization**: astrotomic/laravel-translatable (^11.12)
- **Permission System**: spatie/laravel-permission (^6.4) - via Contact model
- **Multi-tenancy**: Tenant-scoped data via Sapphire module
- **Database**: MySQL/PostgreSQL (tenant-specific)
- **Authentication**: Integrated with Sapphire module

## Database Schema

### Core Models

**Department Model** (`Department.php`)

```php
// Organizational departments with hierarchical support
// Table: r_departments
// Uses Translatable trait from Astrotomic
```

Schema:
- id: Primary key
- created_at, updated_at

Translated Attributes (via r_department_locales):
- name: Department name (localized)

Relationships:
- `managers()`: belongsToMany(Contact) via `r_subordinates` pivot
- `employees()`: belongsToMany(Contact) via `r_subordinates` pivot
- `head()`: Complex query to find department heads (managers with no superiors)
- `staff()`: hasMany(Subordinate) - for counting members
- `subordinates()`: hasMany(Subordinate) - direct pivot access
- `applicants()`: morphToMany(Applicant) via `r_applicables`
- `vacancies()`: hasMany(Vacancy)

Features:
- Hierarchical department structure
- Automatic department head detection
- Staff member aggregation including managers and employees

**DepartmentLocale Model** (`DepartmentLocale.php`)

```php
// Localized department information
// Table: r_department_locales
```

Schema:
- id: Primary key
- department_id: Foreign key to departments
- locale: Language code (2 chars)
- name: Translated department name

**Degree Model** (`Degree.php`)

```php
// Job positions/titles and educational qualifications
// Table: r_degrees
// Uses Translatable trait
```

Schema:
- id: Primary key
- No timestamps

Translated Attributes (via r_degree_locales):
- name: Position/degree name (localized)

Relationships:
- `applicants()`: hasMany(Applicant)

**DegreeLocale Model** (`DegreeLocale.php`)

```php
// Localized degree/position information
// Table: r_degree_locales
```

Schema:
- id: Primary key
- degree_id: Foreign key to degrees
- locale: Language code (2 chars)
- name: Translated degree/position name

**Salary Model** (`Salary.php`)

```php
// Employee salary records
// Table: r_salaries
// Primary key is the Contact ID (shared primary key pattern)
```

Schema:
- id: Primary key (references lc_contacts.id)
- type: enum('hourly', 'monthly', 'annual')
- created_at, updated_at

Foreign Keys:
- id → lc_contacts.id (cascade on update/delete)

Note: Currently a minimal implementation - ready for expansion

**SalaryComponent Model** (`SalaryComponent.php`)

```php
// Individual salary component values per employee
// Table: r_salary_components
```

Schema:
- salary_id: Part of composite primary key
- payroll_component_id: Part of composite primary key
- amount: Component monetary value (float)

Relationships:
- `salary()`: belongsTo(Salary)
- `payrollComponent()`: belongsTo(PayrollComponent)

**PayrollComponent Model** (`PayrollComponent.php`)

```php
// Salary component definitions (allowances, deductions, incentives)
// Table: r_payroll_components
```

Schema:
- id: Primary key
- type: enum('basic', 'allowance', 'incentive')
- No timestamps

Translated Attributes (via r_payroll_component_locales):
- title: Component name (localized)

**PayrollComponentLocale Model** (`PayrollComponentLocale.php`)

```php
// Localized payroll component information
// Table: r_payroll_component_locales
```

Schema:
- id: Primary key
- payroll_component_id: Foreign key
- locale: Language code (2 chars)
- title: Translated component name

**PaymentComponent Model** (`PaymentComponent.php`)

```php
// Payment processing components for payroll
// Table: r_payment_components
```

Schema:
- payroll_payment_id: Part of composite primary key
- payroll_component_id: Part of composite primary key
- amount: Payment amount (float)

Relationships:
- `payrollPayment()`: belongsTo(PayrollPayment)
- `payrollComponent()`: belongsTo(PayrollComponent)

**PayrollPayment Model** (`PayrollPayment.php`)

```php
// Payroll payment records and disbursements
// Table: r_payroll_payments
```

Schema:
- id: Primary key
- salary_id: Foreign key to salaries (nullable, set null on delete)
- bank_account_id: Foreign key to bank accounts (nullable)
- units: Number of payment units (default 1, tinyint)
- paid_at: Payment timestamp (nullable)
- created_at, updated_at

Relationships:
- `salary()`: belongsTo(Salary)
- `bankAccount()`: belongsTo(BankAccount) - from financial module
- `paymentComponents()`: hasMany(PaymentComponent)

**Punch Model** (`Punch.php`)

```php
// Attendance punch records (check-in/check-out)
// Table: r_punches
```

Schema:
- id: Primary key (bigint)
- contact_id: Foreign key to contacts
- type: enum('in', 'out')
- created_at: Timestamp (no updated_at)

Relationships:
- `contact()`: belongsTo(Contact) from Common module

Features:
- Simplified punch tracking
- Records timestamp on creation
- Type indicates check-in or check-out

**Leave Model** (`Leave.php`)

```php
// Leave applications and tracking
// Table: r_leaves
```

Schema:
- id: Primary key
- contact_id: Foreign key to contacts
- type: enum('sick', 'casual', 'annual')
- status: boolean (nullable) - true=approved, false=rejected, null=pending
- starts_at: Leave start date
- ends_at: Leave end date
- created_at, updated_at

Casts:
- starts_at, ends_at: datetime

Relationships:
- `contact()`: belongsTo(Contact) from Common module

**Vacancy Model** (`Vacancy.php`)

```php
// Job openings and positions
// Table: r_vacancies
```

Schema:
- id: Primary key
- department_id: Foreign key to departments
- active: boolean (default false)
- created_at, updated_at

Translated Attributes (via r_vacancy_locales):
- title: Job title (localized)
- description: Job description (localized)

Relationships:
- `department()`: belongsTo(Department)
- `applicants()`: morphToMany(Applicant) via `r_applicables`

**VacancyLocale Model** (`VacancyLocale.php`)

```php
// Localized vacancy information
// Table: r_vacancy_locales
```

Schema:
- id: Primary key
- vacancy_id: Foreign key to vacancies
- locale: Language code (2 chars)
- title: Translated job title
- description: Translated job description (text)

**Applicant Model** (`Applicant.php`)

```php
// Job applicants and recruitment candidates
// Table: r_applicants
// Primary key is the Contact ID (shared primary key pattern)
```

Schema:
- id: Primary key (references lc_contacts.id, non-incrementing)
- country_id: Foreign key to countries (nationality)
- degree_id: Foreign key to degrees (educational qualification)
- degree_date: Year of degree completion (unsigned int)
- tenure: Years of experience (tinyint)
- recruited_at: Recruitment timestamp (nullable)
- created_at, updated_at

Casts:
- recruited_at: datetime

Relationships:
- `contact()`: belongsTo(Contact, 'id') - one-to-one with Contact
- `documents()`: hasMany(Document)
- `nationality()`: belongsTo(Country) from Common module
- `degree()`: belongsTo(Degree)
- `vacancies()`: morphedByMany(Vacancy) via `r_applicables`
- `departments()`: morphedByMany(Department) via `r_applicables`

Features:
- Applicant shares ID with Contact (no separate user needed)
- Can be recruited (recruited_at timestamp)
- Links to multiple vacancies and departments via polymorphic relation

**Document Model** (`Document.php`)

```php
// Employee and applicant documents (resumes, certificates, etc.)
// Table: r_documents
```

Schema:
- id: Primary key
- applicant_id: Foreign key to applicants
- title: Document title/name
- filename: Stored filename
- created_at, updated_at

Relationships:
- `applicant()`: belongsTo(Applicant)

Accessors:
- `download_url`: Generates route for document download

**Subordinate Model** (`Subordinate.php`)

```php
// Employee reporting relationships within departments
// Table: r_subordinates
// Connects managers, employees, and departments
```

Schema:
- id: Primary key (string UUID from uuid_short(), non-incrementing)
- manager_id: Foreign key to contacts (nullable, set null on delete)
- contact_id: Foreign key to contacts (nullable, set null on delete)
- department_id: Foreign key to departments (required)
- Unique constraint on (manager_id, contact_id)
- Timestamps

Relationships:
- `manager()`: belongsTo(Contact, 'manager_id')
- `contact()`: belongsTo(Contact, 'contact_id')
- `department()`: belongsTo(Department)

Features:
- **Three-way relationship**: Links manager, employee, and department
- Either manager_id or contact_id (or both) can be null
- Allows flexible organizational hierarchies
- Used to build department staff lists and reporting chains

**Notice Model** (`Notice.php`)

```php
// Company announcements and HR notices
// Table: r_notices
```

Schema:
- id: Primary key
- author_id: Foreign key to contacts (who created the notice)
- contact_id: Foreign key to contacts (recipient, nullable)
- payroll_payment_id: Foreign key to payroll payments (nullable)
- type: enum('notice', 'warning', 'termination')
- content: Notice text (text field)
- created_at, updated_at

Relationships:
- `author()`: belongsTo(Contact, 'author_id')
- `contact()`: belongsTo(Contact, 'contact_id') - recipient
- `payrollPayment()`: belongsTo(PayrollPayment)

## Directory Structure

```
Modules/Ruby/
├── App/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AttendanceController.php
│   │       ├── ContactController.php
│   │       ├── DashboardController.php
│   │       ├── DepartmentController.php
│   │       ├── DocumentController.php
│   │       └── ModuleController.php
│   ├── Models/                   # Eloquent models (19 models)
│   │   ├── Applicant.php
│   │   ├── Degree.php
│   │   ├── DegreeLocale.php
│   │   ├── Department.php
│   │   ├── DepartmentLocale.php
│   │   ├── Document.php
│   │   ├── Leave.php
│   │   ├── Notice.php
│   │   ├── PaymentComponent.php
│   │   ├── PayrollComponent.php
│   │   ├── PayrollComponentLocale.php
│   │   ├── PayrollPayment.php
│   │   ├── Punch.php
│   │   ├── Salary.php
│   │   ├── SalaryComponent.php
│   │   ├── Subordinate.php
│   │   ├── Vacancy.php
│   │   └── VacancyLocale.php
│   └── Providers/
│       └── ModuleServiceProvider.php
├── Database/
│   ├── Factories/               # Model factories for testing
│   ├── migrations/              # Database migrations
│   │   └── 2024_04_02_104117_ruby_base.php
│   └── Seeders/                # Database seeders
├── resources/
│   ├── views/
│   │   └── components/         # Reusable UI components
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
Contact (Common) (1) ─────── (1) Salary
Contact (1) ─────── (N) Punch (Attendance)
Contact (1) ─────── (N) Leave
Contact (1) ─────── (N) Notices (as author)
Contact (1) ─────── (N) Notices (as recipient)
Contact (1) ─────── (1) Applicant (shared primary key)

Department (1) ─────── (N) Vacancies
Department (1) ─────── (N) Subordinates
Department (1) ─────── (N) DepartmentLocale
Department (N) ─────── (N) Contacts (via r_subordinates) - managers
Department (N) ─────── (N) Contacts (via r_subordinates) - employees
Department (N) ─────── (N) Applicants (via r_applicables polymorphic)

Degree (1) ─────── (N) Applicants
Degree (1) ─────── (N) DegreeLocale

Salary (1) ─────── (N) SalaryComponent
Salary (1) ─────── (N) PayrollPayment

PayrollComponent (1) ─────── (N) SalaryComponent
PayrollComponent (1) ─────── (N) PaymentComponent
PayrollComponent (1) ─────── (N) PayrollComponentLocale

PayrollPayment (1) ─────── (N) PaymentComponent
PayrollPayment (1) ─────── (N) Notices

Vacancy (1) ─────── (N) VacancyLocale
Vacancy (N) ─────── (N) Applicants (via r_applicables polymorphic)

Applicant (1) ─────── (N) Documents
Applicant (N) ─────── (1) Contact (shared ID)
Applicant (N) ─────── (1) Country (nationality)
Applicant (N) ─────── (1) Degree

Subordinate (N) ─────── (1) Contact (as manager)
Subordinate (N) ─────── (1) Contact (as employee)
Subordinate (N) ─────── (1) Department
```

## Integration with Common Module

Ruby module heavily depends on the **Common module** for contact management:

### Contact Model (from Common module)

The Contact model (`Modules\Common\App\Models\Contact`) is the central entity for:
- Employee information (names, personal details)
- Communication details (phones, emails)
- Addresses
- Role assignments (via Spatie Permission)

### Key Contact Relationships Used by Ruby:

```php
// From Common module Contact model
Contact::class
    ->departments()           // Employee's departments
    ->managed_departments()   // Departments they manage
    ->superiors()            // Their managers
    ->subordinates()         // Their direct reports
    ->applicant()            // Recruitment information
    ->leaves()               // Leave history
    ->hasRole()              // Permission checks
```

### Contact Scopes Used by Ruby:

```php
// Useful query scopes from Contact
Contact::withRoles()              // Load roles with translations
Contact::withDefaultInfo()        // Load primary phone/email
Contact::withAllInfo()            // Load all contact information
Contact::withRecruitmentDetails() // Load applicant info
Contact::activeRecruit()          // Only hired employees
Contact::withDepartments()        // Load department relationships
```

## Integration with Sapphire Module

Ruby integrates with Sapphire for:

1. **Authentication**: User authentication and session management
2. **Authorization**: Permission checks via Spatie Permission (inherited by Contact)
3. **Tenant Context**: All data is automatically scoped to current tenant

```php
use Modules\Sapphire\App\Models\User;

// Get authenticated user
$user = auth()->user();

// Get user's contact for HR operations
$contact = $user->contact;

// Check HR permissions
if ($contact->can('manage-employees')) {
    // HR management logic
}
```

## Organizational Hierarchy

The Ruby module implements a flexible three-tier hierarchy:

1. **Departments**: Organizational units
2. **Subordinates**: Pivot table linking managers, employees, and departments
3. **Contacts**: Individual people (managers and employees)

### How It Works:

```php
// Add an employee to a department with a manager
Subordinate::create([
    'department_id' => $dept->id,
    'contact_id' => $employee->id,
    'manager_id' => $manager->id
]);

// Add a department head (no manager)
Subordinate::create([
    'department_id' => $dept->id,
    'manager_id' => $head->id,
    'contact_id' => null  // Managers don't need contact_id
]);

// Get department staff
$department->staff;  // Returns all managers and employees

// Get department head
$department->head;   // Returns top-level managers
```

## Recruitment Workflow

1. **Create Vacancy**: Post job opening for a department
2. **Receive Applications**: Applicants (linked to Contacts) apply via polymorphic relation
3. **Document Upload**: Store resumes, certificates via Document model
4. **Recruitment**: Set `recruited_at` timestamp on Applicant
5. **Onboarding**: Recruited applicants become employees via Subordinate relationship

## API Endpoints

### Implemented Controllers:
- **AttendanceController**: Punch in/out functionality
- **ContactController**: Employee contact management
- **DashboardController**: HR dashboard and statistics
- **DepartmentController**: Department CRUD operations
- **DocumentController**: Document upload/download
- **ModuleController**: Module-specific settings

### Pending Implementation:
- Salary management
- Payroll processing
- Leave approval workflow
- Vacancy management
- Applicant tracking
- Notice distribution

## Development Notes

- **Priority**: 8
- **Alias**: ruby
- **Service Provider**: `Modules\Ruby\App\Providers\ModuleServiceProvider`
- **Dependencies**: 
  - **Common module** (primary - for Contact management)
  - **Sapphire module** (authentication/authorization)
- **Required Packages**: astrotomic/laravel-translatable, spatie/laravel-permission

## Future Enhancements

Based on the current minimal implementation, planned expansions include:

1. **Payroll System**: Full payroll calculation engine
2. **Leave Approval**: Multi-level approval workflow
3. **Performance Reviews**: Employee evaluation system
4. **Training Management**: Track employee training and certifications
5. **Shift Management**: Work schedule and shift assignments
6. **Time Off Requests**: Integrated leave request system
7. **Onboarding Workflow**: Automated new hire processes
8. **Exit Management**: Employee offboarding procedures