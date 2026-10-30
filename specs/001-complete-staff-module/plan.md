# Implementation Plan: Complete Staff Module

**Branch**: `001-complete-staff-module` | **Date**: 2026-10-30 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `/specs/001-complete-staff-module/spec.md`

## Summary

Complete the Ruby HR staff experience: finish the skeleton add/edit modal (contact +
applicant panes are `TBD`), add the missing create/update/remove/reactivate endpoints
behind the existing form action URLs, wire the inert edit/delete row actions, and
provision mandatory platform accounts through a new Ruby → Sapphire `AccountManager`
contract. Deactivation uses soft deletes on `lc_contacts` (Lava) with account
suspension via an `active` flag on `s_users` (Sapphire); members without history are
force-deleted. Everything reuses existing entities — staff = contacts with a recruited
applicant record, per the 1:1 `r_applicants` extension table.

## Technical Context

**Language/Version**: PHP 8.1+ (Laravel 10+), Vue 3.5, Blade, Vite (rolldown)

**Primary Dependencies**: nwidart/laravel-modules, spatie/laravel-permission,
yajra/laravel-datatables-oracle, select2, daterangepicker, bootbox, laravel/pint,
phpunit 10

**Storage**: MySQL/PostgreSQL (tenant DB); profile images & employment documents on the
`local` disk (`r_documents/`)

**Testing**: PHPUnit (feature tests; sqlite in-memory — currently commented out in
`phpunit.xml`, must be enabled)

**Target Platform**: Laravel server-rendered pages + Vue-enhanced admin UI

**Project Type**: Modular monolith (web app) — Modules/Ruby (consumer), Modules/Sapphire
(contract owner), Modules/Common (schema owner)

**Performance Goals**: staff list filters < 2 s @ 1,000 members (SC-003); no full-page
reloads on CRUD (FR-007)

**Constraints**: constitution Principles I–VI (below); bilingual EN/AR UI; all schema
changes via owning-module migrations

**Scale/Scope**: ~2 migrations, 1 service class, 1 form request, 5 endpoints,
2 Blade panes, 1 JS file update, ~4 feature test classes

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | How |
|-----------|--------|-----|
| I. Bounded module ownership | ✅ | Ruby owns staff flows; account CRUD only via Sapphire's `AccountManager`; no module writes another's tables |
| II. Business Blade over generic Vue | ✅ | New UI = business Blade components (`modals/contact-form` panes) composing generic `vue-field`, select2, daterange, file inputs; zero business logic in Vue components |
| III. Lava-first | ✅ | Shared entities untouched except a Common-owned soft-delete migration on `lc_contacts`; emails/phones/addresses/degrees/countries reused |
| IV. Tenant isolation & Sapphire auth | ✅ | All routes inside `auth + can:ruby` group; credentials never leave Sapphire; suspension enforced at the auth guard |
| V. Schema as contract | ✅ | 2 migrations in owning modules (Common, Sapphire); models updated in same change; no runtime schema changes |
| VI. Simplicity / YAGNI | ✅ | Inline applicant creation is minimal (no recruitment pipeline); reactivation is `restore()`, not a new lifecycle engine |

## Project Structure

### Documentation (this feature)

```text
specs/001-complete-staff-module/
├── spec.md                        # requirements (4 clarified decisions)
├── checklists/requirements.md     # quality checklist (16/16)
├── plan.md                        # this file
├── research.md                    # codebase findings
├── data-model.md                  # 2 migrations + field mappings
├── contracts/account-management.md# Ruby → Sapphire account contract
├── quickstart.md                  # manual + automated verification
└── tasks.md                       # /speckit.tasks output
```

### Source Code (repository root)

```text
Modules/Common/
├── App/Models/Contact.php               # +SoftDeletes trait
└── Database/migrations/                 # +add_soft_deletes_to_contacts

Modules/Sapphire/
├── App/Models/User.php                  # active cast/handling
├── App/Services/AccountManager.php      # NEW contract implementation
└── Database/migrations/                 # +add_active_to_users

Modules/Ruby/
├── App/Http/Controllers/ContactController.php   # +create/update/delete/restore/applicants
├── App/Http/Requests/ContactRequest.php         # NEW validation + transactional handle()
├── routes/routes.php                            # +5 routes
└── resources/views/
    ├── contacts.blade.php                       # +inactive filter, edit/delete modals
    └── components/modals/contact-form.blade.php # contact + applicant panes (was TBD)

resources/js/ruby/contacts.js            # CRUD wiring (pattern: departments.js)

resources/components/                    # UNTOUCHED (generic, business-agnostic)

tests/Feature/Ruby/
├── ContactCreateTest.php
├── ContactUpdateTest.php
├── ContactRemovalTest.php
└── AccountContractTest.php
```

**Structure Decision**: follow the established module layout exactly; the only new
architectural piece is `AccountManager` (service class, in-process call — no HTTP hop,
per modular-monolith Principle VI).

## Complexity Tracking

No constitution violations. (No 4th project, no repository layer — direct Eloquent +
FormRequest `handle()` matches the codebase pattern.)

## Milestones

**M1 — Foundations** (blocks everything): the two owning-module migrations;
`AccountManager` + auth-guard suspension; test DB enabled; lang keys.

**M2 — US1 Create (P1, MVP)**: `ContactRequest::handle()` transaction (contact →
info rows → roles/departments → applicant(+documents) → account via contract);
applicant picker endpoint; Blade panes + JS; profile image upload.

**M3 — US2 Edit (P2)**: prefill payload (`getContact` extended with account username
+ editable collections), update path with collection syncing (add/update/remove, single
default), password-optional account update.

**M4 — US3 Remove/Reactivate (P3)**: history check → soft vs force; clear
`r_subordinates` + shifts; suspend account; inactive filter + reactivate action.

**M5 — US4 hardening**: duplicate-username UX, cross-module failure test, login tests
(active/inactive), Pint + full suite green, docs update on the `docs` branch.

## Testing Strategy

- **Feature tests (PHPUnit)** per constitution workflow: create happy path + every
  rejection scenario; atomicity incl. `AccountException` rollback; edit sync; removal
  mode matrix (with/without history × manager/non-manager); reactivate; login with
  provisioned + suspended accounts.
- **Test DB**: enable sqlite `:memory:` in `phpunit.xml` (already scaffolded, commented).
- **Manual**: quickstart.md flow (bilingual pass included).
- **Regression guard**: existing departments flow untouched — `DepartmentTest`-style
  checks optional but departments.js pattern is copied, not modified.

## Rollout

Single PR or story-by-story PRs (M2→M5 are independently shippable per spec user
stories). No data backfill: `deleted_at NULL` / `active TRUE` defaults match current
behavior. Docs branch update included (constitution workflow gate).
