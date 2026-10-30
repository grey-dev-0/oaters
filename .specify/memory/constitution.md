# OATERS ERP Constitution

## Core Principles

### I. Modular Monolith with Bounded Module Ownership (NON-NEGOTIABLE)
- The system is a single Laravel application (a modular monolith). It MUST NOT be split into
  separately deployed services while this version stands.
- Every business capability belongs to exactly one module under `Modules/{Module}`:
  - **Sapphire** — tenants, users, roles and privileges.
  - **Onyx** — CRM, inventory and production.
  - **Amethyst** — e-commerce and the online store.
  - **Topaz** — accounting and financial management.
  - **Emerald** — project management.
  - **Ruby** — human resources.
- A module MUST NOT write to another module's tables, call another module's internals,
  or reimplement another module's domain entities.
- Cross-module needs are met through Lava shared entities (Principle III) or through an
  explicit contract (service, event, or API) owned by the providing module.

Rationale: modules must stay independently understandable and replaceable while the monolith
keeps deployment and integration simple.

### II. Layered Frontend: Business Blade over Generic Vue (NON-NEGOTIABLE)
- Vue components MUST stay business-agnostic, reusable UI building blocks with no module or
  domain vocabulary baked in.
- Business-specific markup and data handling MUST live in Blade components inside the owning
  module (`Modules/{Module}/resources/views/components/`).
- Page views (`Modules/{Module}/resources/views/*.blade.php`) compose business Blade
  components, which in turn consume generic Vue components.
- A Vue component that names module entities, workflows, or permissions is a defect: it MUST
  be generalized or demoted into a Blade component.

Rationale: keeps Vue components reusable across all modules while business rules stay in the
module that owns them.

### III. Lava-First Shared Foundation
- Entities and resources used by more than one module MUST live in the Lava framework
  modules: **Common (LC)** general-purpose entities, **Article (LA)** content and catalog,
  **Commerce (LE)** commerce and financial primitives.
- Business modules MUST depend on Lava; Lava MUST NOT depend on business modules or on
  Sapphire.
- Before creating a new entity, the owning module MUST verify that Lava does not already
  provide it. Duplicating a shared entity inside a business module is forbidden.

Rationale: shared entities have exactly one home, preventing drift between modules.

### IV. Absolute Tenant Isolation (NON-NEGOTIABLE)
- Multi-tenancy (stancl/tenancy) keeps tenant data separated. Every query, seeder, job and
  command that touches tenant data MUST run inside an explicit tenant context.
- Cross-tenant data leakage is the highest-severity defect class in this system.
- Sapphire owns authentication, authorization (spatie/laravel-permission) and tenant
  lifecycle. No other module MUST manage tenants, users, roles or privileges.
- System records flagged as non-editable (for example seeded measurement units) MUST be
  protected from tenant-side modification.

Rationale: an ERP that leaks data between organizations is unusable and untrustworthy.

### V. Schema as Contract
- Each module's migrations and Eloquent models (already developed ahead of feature work)
  are the contract of record for that module's data.
- All schema changes MUST ship as migrations inside the owning module
  (`Modules/{Module}/Database/migrations/`) with the corresponding models updated in the
  same change.
- Application code MUST NOT alter schema at runtime, and manual database edits outside
  migrations are forbidden.
- Touching another module's schema MUST go through that module (or through Lava for shared
  entities), never by direct foreign migrations.

Rationale: the schema was designed up front across all modules; keeping it authoritative
prevents silent divergence between modules that share data.

### VI. Simplicity and YAGNI
- Build the simplest design that satisfies the current specification. Speculative
  abstractions, unused options and premature integrations MUST be justified or dropped.
- Development proceeds one module at a time (current focus: Ruby). Features of other modules
  MUST NOT be built speculatively.
- Any complexity beyond what the spec requires MUST be justified in the plan before
  implementation.

Rationale: an early-stage ERP dies from over-engineering more often than from missing
features.

## Additional Constraints

Technology stack (locked for this version):
- Backend: Laravel 10+ on PHP 8.1+; module system via nwidart/laravel-modules;
  multi-tenancy via stancl/tenancy; permissions via spatie/laravel-permission;
  translations via astrotomic/laravel-translatable; data tables via
  yajra/laravel-datatables-oracle.
- Frontend: Blade views plus Vue 3 generic components; Vite (rolldown-vite) build;
  Bootstrap 4 and DataTables.net styling; per-page JS entry points under
  `resources/js/{moduleName}/`.
- Database: MySQL or PostgreSQL.

Localization:
- All user-facing strings MUST be translatable; module strings live in
  `Modules/{Module}/resources/lang/`.
- Multi-language entity content MUST use translatable Eloquent models.

Security:
- Every route, page and action MUST declare its role/privilege requirement, enforced by
  Sapphire's authorization layer.
- Laravel security defaults (CSRF protection, secure sessions, hashed passwords, token
  auth via Sanctum) MUST remain enabled.

## Development Workflow

Spec-driven delivery:
- Feature work starts with the Spec Kit flow (`/speckit.specify`, then clarify, plan and
  tasks); implementation follows the accepted specification.
- Cross-module specifications MUST name the owning module of every entity and contract they
  touch.
- The Topaz integration approach is unresolved; any Topaz work starts with a dedicated
  design specification rather than direct implementation.

Quality gates:
- PHP code MUST pass laravel/pint formatting and the phpunit suite before merge.
- New backend behavior (services, controllers, models) MUST ship with PHPUnit tests; bug
  fixes MUST include a regression test.
- Frontend changes MUST be checked against the layered-frontend rule (Principle II).

Documentation:
- Changes to module behavior or database structure MUST be reflected in the VitePress docs
  on the `docs` branch, in the same change or a linked follow-up commit.

## Governance

- This constitution supersedes ad-hoc practices wherever they conflict with it.
- Amendments require: a documented proposal with rationale, an impact assessment across
  affected modules, a version bump, and a migration note for work in flight.
- Versioning follows semantic versioning: MAJOR for principle removals or incompatible
  redefinitions, MINOR for new or materially expanded principles and sections, PATCH for
  clarifications and wording fixes.
- Every specification, plan and code review MUST verify compliance with these principles;
  unexplained complexity MUST be challenged at review time.
- Runtime AI development guidance and project memory live under `.specify/memory/`.

**Version**: 1.0.0 | **Ratified**: 2026-10-30 | **Last Amended**: 2026-10-30
