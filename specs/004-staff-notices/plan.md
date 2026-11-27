# Implementation Plan: Staff Notices Page

**Branch**: `004-staff-notices` | **Date**: 2026-11-27 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `/specs/004-staff-notices/spec.md`

## Summary

Deliver the Personnel → Notices page on the existing (never-consumed) `r_notices` schema:
a filterable, paginated record trail (recipient, author, type, content, issue date —
newest first) with issue/edit/delete flows gated by a dedicated `manage-notices`
permission. The author is auto-recorded from the signed-in member's profile and edits
preserve provenance; deletes are bootbox-confirmed and hard. Records survive member
deactivation and anchor the existing permanent-removal retention rule. The page mirrors
the attendance list anatomy (filters + server-side DataTables + modal + toast), so it
inherits bilingual EN/AR strings and RTL presentation from spec 003 with no new generic
components and no schema change.

## Technical Context

**Language/Version**: PHP 8.1+ (Laravel 10), Vue 3.5, Blade, Vite (rolldown), SCSS (Bootstrap 4.6)

**Primary Dependencies**: nwidart/laravel-modules, stancl/tenancy (module routes keep the
subdomain middlewares), spatie/laravel-permission (`manage-notices`), yajra DataTables 2,
select2, bootbox

**Storage**: existing `r_notices` (+ `lc_contacts`, `r_payroll_payments` read-only joins) — **no migration**

**Testing**: PHPUnit feature tests on the staff harness (`StaffTestCase`: module schema
bootstrap, tenancy bypass, DatabaseTransactions, AJAX post helper)

**Target Platform**: Laravel server-rendered page + Vue-enhanced admin UI (tenant module page)

**Project Type**: Modular monolith — Ruby-owned page; Common entities consumed; Sapphire permission layer

**Performance Goals**: server-side pagination/filtering on the existing DataTables flow (no new targets)

**Constraints**: constitution Principles I–VI; schema consumed as-is (Principle V); `manage-notices` gate per user decision 1B; two separate member filters per clarification 2026-11-27

**Scale/Scope**: 0 migrations; 1 controller + 1 request + 1 route group; 1 view + 1 modal + 1 JS bundle; 2 lang catalogs; 1 seeder edit; 2 test classes

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | How |
|-----------|--------|-----|
| I. Bounded module ownership | ✅ | Ruby owns the page, controller, request, views and strings; `Contact`/`lc_contacts` consumed through existing relations (Lava entity); permissions via Sapphire's spatie layer (the established `can:` route pattern) |
| II. Business Blade over generic Vue | ✅ | Business markup in `ruby::notices` + `ruby::modals.notice-form`; generic components (`vue-datatable`, `dt-filter`, `vue-form`, `select2`, bootbox) reused unchanged; page JS is the established page-bundle pattern |
| III. Lava-first | ✅ | No new shared entity — `r_notices` already belongs to Ruby; no Lava changes at all |
| IV. Tenant isolation & Sapphire auth | ✅ | Routes under the module's tenancy + `auth` + `can:ruby` group; privileged actions behind `can:manage-notices`; no cross-tenant surface |
| V. Schema as contract | ✅ | Zero migrations; the pre-built schema is consumed as designed (the feature's premise); model gains relations + unguarding only |
| VI. Simplicity / YAGNI | ✅ | No export, no notification delivery, no profile-modal timeline, no author picker; `view-notices` legacy permission left untouched |

## Project Structure

### Documentation (this feature)

```text
specs/004-staff-notices/
├── spec.md                        # requirements (1 specify-time + 1 clarify decision)
├── checklists/requirements.md     # 16/16
├── plan.md                        # this file
├── research.md                    # deduction + pattern decisions
├── data-model.md                  # consumed schema, rules, state transitions
├── quickstart.md                  # manual + automated verification
└── tasks.md                       # /speckit.tasks output
```

### Source Code (repository root)

```text
Modules/Ruby/
├── App/Http/Controllers/NoticeController.php    # NEW — index, data, create/update, delete
├── App/Http/Requests/NoticeRequest.php          # NEW — rules + localized messages
├── App/Models/Notice.php                        # +author()/recipient()/payrollPayment(), unguard
├── Database/Seeders/PermissionSeeder.php        # +manage-notices + role grants
├── routes/routes.php                            # +notices group (can:manage-notices on writes)
└── resources/
    ├── views/notices.blade.php                  # NEW — trail + filters + issue button (@can)
    ├── views/components/modals/notice-form.blade.php  # NEW — create/edit modal
    └── lang/{en,ar}/notices.php                 # NEW — page strings both languages

resources/js/ruby/notices.js                     # NEW — page bundle (Vite auto-discovers)

tests/Feature/Ruby/
├── NoticePageTest.php                           # list, filters, permission gating
└── NoticeLifecycleTest.php                      # issue/edit/delete + retention regression
```

**Structure Decision**: identical to the attendance page's shape (controller + request +
view + modal + page bundle + lang pair + seeder edit). No new generic components, no
Core-app touches, no shared-resource changes.

## Complexity Tracking

No constitution violations. (Rejected alternatives: timeline-based page — no
filter/pagination story; pickable author — falsifiable provenance; client-side filtering —
breaks the server-side DataTables contract; soft deletes — schema has no
`deleted_at` and the record class is explicitly deletable-with-confirmation.)

## Milestones

**M1 — The record trail (P1)**: Notice model relations; routes + controller index/data;
notices view with filters (type, recipient, author, date range); lang catalogs; view-only
page live behind `can:ruby`; NoticePageTest.

**M2 — Issue and manage (P2)**: `manage-notices` seeding; notice-form modal +
NoticeRequest + create/update/delete flows; provenance-preserving edit; bootbox delete;
view-only users see no controls; NoticeLifecycleTest.

**M3 — Verify & close**: retention regression (permanent removal refused); bilingual +
RTL e2e pass (Orca browser on the tenant domain); full suite + Pint; docs branch note.

## Testing Strategy

- **Feature tests** (`StaffTestCase` harness): list renders all columns newest-first;
  each filter alone and combined (type, recipient, author, date range) narrows exactly;
  issue stores author from the signed-in contact and rejects contactless users; edit
  changes recipient/type/content only (author + created_at byte-stable); delete removes
  after the confirmed POST; unauthorized POSTs to create/update/delete get 403 while the
  list stays 200; member with notices refuses permanent removal (the live `hasHistory`
  branch).
- **Manual/e2e**: quickstart.md flow on the dockerized LEMP + Orca browser (EN + AR/RTL
  walk, mixed-direction content, payroll link read-only, permission surfaces).
- **Regression guards**: full suite stays green; EN pages byte-identical (no Core or
  shared-resource changes beyond one seeder row).

## Rollout

Single stacked PR (`004-staff-notices` → `003-language-switcher`, after PR #4). Deploy:
`composer install` (no deps), `npm run production`, tenant re-seed
(`module:tenants-seed`) to grant `manage-notices` to the HR roles. No data backfill —
the table starts empty and grows from use. Docs branch update included (constitution
documentation gate).
