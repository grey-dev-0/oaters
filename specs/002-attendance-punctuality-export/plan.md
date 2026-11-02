# Implementation Plan: Attendance Punctuality & Export

**Branch**: `002-attendance-punctuality-export` | **Date**: 2026-11-02 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `/specs/002-attendance-punctuality-export/spec.md`

## Summary

Turn the already-captured punch latency (`r_punches.latency`, signed minutes vs the
punch's matched `r_shifts` row) into a visible, color-coded "Punctuality" column on the
Ruby attendance DataTable, tuned by a tenant-scoped threshold that lives in the project's
first settings mechanism — a Common-owned (Lava) `lc_settings` key/value store read
through a `\Settings` service, with Ruby declaring the key + default (15 min, bounds
0–240). Add Excel (.xlsx) / CSV export of the attendance table that reuses the exact
DataTables server-side filter pipeline so the file always mirrors what the filtered
table shows. XLSX/CSV streaming via `openspout/openspout` (zip + xmlwriter confirmed in
the LEMP container); CSV with UTF-8 BOM for Arabic. All PHP/Composer/Node work runs
inside `docker exec lemp-php-1`; tests run on MariaDB `oaters_test` following the
`StaffTestCase` bootstrap (sqlite cannot work — `uuid_short()` in module migrations).

## Technical Context

**Language/Version**: PHP 8.1.34 (Laravel 10+), Vue 3.5, Blade, Vite (rolldown-vite),
Bootstrap 4, DataTables.net (server-side via yajra)

**Primary Dependencies**: nwidart/laravel-modules, yajra/laravel-datatables-oracle,
spatie/laravel-permission, select2, daterangepicker, bootbox, laravel/pint, phpunit 10,
**NEW:** openspout/openspout (XLSX + CSV streaming; installed via `docker exec
lemp-php-1 composer require openspout/openspout`)

**Storage**: MariaDB tenant DB (new `lc_settings` table, Common-owned); streamed
download responses (no files persisted)

**Testing**: PHPUnit feature tests against MariaDB `oaters_test` (`.env.testing`),
bootstrap per `tests/Feature/Ruby/StaffTestCase.php` (`migrate:fresh` with fixtures +
`module:migrate` in `setUpBeforeClass`, `DatabaseTransactions`,
`withoutMiddleware` tenancy bypass, `postAjax` helper). Run inside the container:
`docker exec -w /home/projects/theultragrey/oaters lemp-php-1 php vendor/bin/phpunit
tests/Feature/Ruby/...`.

**Target Platform**: Laravel server-rendered pages + Vue-enhanced admin UI (LEMP stack,
everything executed inside the `lemp-php-1` container)

**Project Type**: Modular monolith — Modules/Common (settings mechanism owner),
Modules/Ruby (feature owner), Modules/Sapphire (permission enforcement)

**Performance Goals**: export of ~5,000 punches (one month) < 10 s, streaming (constant
memory) per SC-003; punctuality column adds no extra queries (computed from already
selected `latency`/`type` columns)

**Constraints**: constitution Principles I–VI (below); bilingual EN/AR strings
(`Modules/Ruby/resources/lang/{en,ar}/`); schema changes only via owning-module
migrations; generic Vue components stay business-agnostic; no runtime schema changes;
uploads N/A

**Scale/Scope**: 1 migration, 1 model, 1 service (+ facade/alias), 1 Ruby support
class, 4 endpoints (2 settings + 1 export + reuse postIndex), 2 Blade files touched,
1 new modal component, 1 JS entry update, 3 new test classes + 1 test bootstrap,
1 composer dependency, seeder + lang files

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | How |
|-----------|--------|-----|
| I. Bounded module ownership | ✅ | Ruby owns punctuality rendering, settings surface and export; the generic key/value settings *mechanism* is owned by Lava Common (shared foundation, like measurement units); Ruby never touches `lc_settings` directly — only via the Common service contract |
| II. Business Blade over generic Vue | ✅ | New UI = business Blade (attendance page column, settings modal in `Modules/Ruby/resources/views/components/modals/`) using generic `vue-datatable`/`dt-column`/field components; `resources/components/` stays untouched; `resources/js/ruby/attendance.js` only wires page behavior |
| III. Lava-first | ✅ | Before creating a settings entity in Ruby, Lava was checked (no settings entity exists anywhere) — the new `lc_settings` is created exactly once, in Common, reusable by every module (spec FR-003) |
| IV. Tenant isolation & Sapphire auth | ✅ | `lc_settings` is tenant-schema (module migration, migrated per tenant); settings + export routes sit inside the existing `auth + can:ruby` group; threshold editing additionally gated by `can:manage-attendance-settings`; no cross-tenant query paths |
| V. Schema as contract | ✅ | 1 migration in `Modules/Common/Database/migrations/` + `Setting` model in the same change; zero changes to `r_punches`/`r_shifts` (latency is already the contract of record) |
| VI. Simplicity / YAGNI | ✅ | Key/value settings with code-declared defaults — no settings UI framework, no audit trail, no per-user scope (spec assumptions); punctuality is a server-side rendered derived column (no new client dataset); export reuses the existing DataTables filter pipeline instead of a parallel query DSL |

## Project Structure

### Documentation (this feature)

```text
specs/002-attendance-punctuality-export/
├── spec.md                        # requirements (3 clarified decisions)
├── checklists/requirements.md     # quality checklist (16/16)
├── plan.md                        # this file
└── tasks.md                       # /speckit.tasks output
```

### Source Code (repository root)

```text
composer.json / composer.lock          # +openspout/openspout

app/
├── Facades/Settings.php               # NEW (pattern: Facades/DataTablesHelper.php)
└── Providers/DataTablesHelperServiceProvider.php  # alias registration (Settings)

Modules/Common/
├── App/Models/Setting.php             # NEW — lc_settings
├── App/Services/Settings.php          # NEW — get(key, default) / set(key, value)
└── Database/migrations/               # NEW create_lc_settings_table

Modules/Ruby/
├── App/Http/Controllers/AttendanceController.php  # +punctuality column, settings, export
├── App/Http/Requests/SettingRequest.php           # NEW threshold validation (int, 0–240)
├── App/Support/Punctuality.php                    # NEW — threshold resolution + label/class matrix
├── Database/Seeders/PermissionSeeder.php          # +manage-attendance-settings grants
├── routes/routes.php                              # +3 routes (settings x2, export)
└── resources/
    ├── lang/{en,ar}/attendance.php                # NEW punctuality/settings/export strings
    └── views/
        ├── attendance.blade.php                   # +punctuality dt-column, export + settings buttons
        └── components/modals/attendance-settings.blade.php  # NEW threshold modal

resources/js/ruby/attendance.js        # export triggers (blob download), settings modal wiring
resources/components/                  # UNTOUCHED (generic, business-agnostic)

tests/Feature/Ruby/
├── AttendanceTestCase.php             # NEW bootstrap: shifts/punches factories, postAjax reuse
├── PunctualityColumnTest.php          # US1 — 6 rule cells, neutral marker, threshold effects, sorting
├── AttendanceSettingsTest.php         # US2 — default, roundtrip, rejections, permissions
└── AttendanceExportTest.php           # US3 — filter fidelity, formats, empty set, permissions
```

**Structure Decision**: follow the established module layout exactly. Two deliberate
placements: (1) the settings service follows the in-repo shared-helper precedent
(`DataTablesHelper`: class in module `App/Services`, facade + alias) so every module
reads settings as `\Settings::get(...)`; (2) the punctuality rule matrix lives in a
pure `Support` class (`Punctuality::reading(Punch): array{text, class}`) so the
DataTables column and the exporter share one implementation of FR-001/FR-002.

## Key Design Decisions

1. **Settings mechanism (spec FR-003/FR-004)** — `lc_settings(key unique, value
   nullable string, timestamps)` + `Setting` model + `Settings::get/set` service
   (updateOrCreate). Module config plumbing is deliberately NOT introduced (module
   service providers here don't load config): Ruby declares its key and default as
   constants on `Punctuality` (`KEY = 'ruby.attendance.punctuality_threshold'`,
   `DEFAULT = 15`). Absent value → code default (FR default path).
2. **Punctuality rendering is server-side** — `postIndex` gains
   `addColumn('punctuality')` returning a styled `<span>` (translated, incl.
   trans-choice singular/plural), added to `rawColumns`. Rationale: one code path for
   EN/AR + export text reuse; the threshold is re-read per request so FR-005
   (freshness) holds with zero client state. Column is orderable by
   `r_punches.latency` (dt-column `name`), not searchable. Bootstrap 4 mapping:
   red → `text-danger`, blue → `text-primary`, green → `text-success`, bold green →
   `text-success font-weight-bold`, no-latency → `text-muted` unassigned marker.
3. **Export reuses the DataTables pipeline** — the browser sends
   `table.ajax.params()` (current filters, search, order) + `format` to
   `POST attendance/export`; the endpoint extracts the filter closure already used by
   `postIndex` (refactor to a shared private builder) and renders ALL matching rows via
   yajra with paging skipped — the export can never diverge from the table (FR-007).
   Punctuality cell = same `Punctuality::reading()` text without markup (FR-008).
4. **Writer** — openspout/openspout streams both formats (constant memory, SC-003 at
   ~5k rows); CSV written with UTF-8 BOM so Excel renders Arabic (SC-004); XLSX
   requires zip+xmlwriter (verified present). ASCII-safe filename
   `attendance_YYYY-MM-DD.xlsx|.csv`; localized headers via `trans()` at generation
   time.
5. **Permissions** — new `manage-attendance-settings` permission; per clarified
   decision granted to `general-manager`, `hr-manager`, `hr-assistant` only. Care in
   `PermissionSeeder`: `financial-manager` currently receives
   `all-except-view-recruitment` — the exclusion must be widened so it does NOT
   silently receive the new settings permission. Export inherits the attendance
   group's existing `auth + can:ruby` gate (identical access to viewing the table).
6. **Column placement / default sort** — Punctuality is inserted between Type and
   Created At; the page's default sort `[[4,'desc']]` shifts to the new Created At
   index so behavior is unchanged.

## Complexity Tracking

No constitution violations. (No new projects, no repository layer — the one new shared
service mirrors the existing `DataTablesHelper` precedent; one composer dependency is
the minimum for real .xlsx output — rejected alternatives: `maatwebsite/excel` (heavy,
Laravel-backed), hand-rolled SpreadsheetML (fragile), CSV-only (violates spec FR-007).)

## Milestones

**M1 — Foundations** (blocks everything): `lc_settings` migration + model +
`Settings` service/facade/alias; permission + seeder grants; EN/AR lang files;
openspout dependency; `AttendanceTestCase` bootstrap.

**M2 — US1 Punctuality column (P1, MVP)**: `Punctuality` support class (threshold
resolution + label/class matrix); `postIndex` column + Blade `dt-column` + sort index
fix; `PunctualityColumnTest`.

**M3 — US2 Settings surface (P2)**: settings routes + `SettingRequest` (int, 0–240,
nullable-keeps-default semantics) + `getSettings`/`postSettings`; attendance-settings
modal Blade + gear button (`@can`) + `attendance.js` wiring + table redraw on save;
`AttendanceSettingsTest` (incl. permission denials for a non-granted role).

**M4 — US3 Export (P3)**: filter closure extraction; `postExport` (xlsx|csv, streamed,
localized headers, filename header); export buttons + blob download in
`attendance.js`; `AttendanceExportTest` (filter fidelity, empty set, BOM, no Actions
column).

**M5 — Hardening**: `npm run development` build; pint + full phpunit suite green
(MariaDB, in container); docs branch VitePress update (attendance: punctuality,
settings, export); spec status review.

## Testing Strategy

- **Feature tests (PHPUnit, MariaDB in container)** per constitution workflow:
  - `PunctualityColumnTest`: one punch per rule cell (late In ≤/> threshold, early In,
    early Out ≤/> threshold, late Out, on-time) + shift-less punch → assert rendered
    HTML per cell from `postIndex` JSON; threshold change via `\Settings::set` flips
    red/blue; sorting by `r_punches.latency` column name orders correctly.
  - `AttendanceSettingsTest`: default 15 when unset (FR-004); set/get roundtrip;
    rejections (empty, non-integer, negative, 241) persist nothing and keep prior
    value (FR-006, SC-005); `hr-manager` (granted) succeeds, `team-lead` (not
    granted) gets 403 (FR-010, SC-006).
  - `AttendanceExportTest`: seeded punches + department/date/type filters → CSV rows
    match the filtered `postIndex` data set exactly (row-for-row, same order) with
    localized headers, leading UTF-8 BOM, punctuality text, no Actions column; XLSX
    opens (zip signature; sheet content contains expected values via openspout
    reader); zero-match export → headers only; unfiltered export includes all rows.
- **Tenant isolation**: `lc_settings` lives in the tenant schema and the suite runs
  tenancy-bypassed on a dedicated test DB; cross-tenant leakage is structurally
  impossible (single keyed table, no tenant column to get wrong) — verified by
  schema placement review, not by a runtime test.
- **Manual**: bilingual EN/AR browser pass on the LEMP stack (column colors, settings
  modal, export open in LibreOffice/Excel) before marking the spec implemented.

## Rollout

Single PR stacked on `001-complete-staff-module` (branch `002-attendance-punctuality-export`,
PR #2 depending on PR #1). No data backfill: absent `lc_settings` row ⇒ code default
(15) — existing tenants see the column working immediately. Docs branch update is a
linked follow-up commit on `docs` (constitution documentation gate). Rollback = revert
the feature commit; no destructive migration (one CREATE TABLE).
