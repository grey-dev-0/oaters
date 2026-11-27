# Implementation Plan: Language Switcher (EN/AR + RTL)

**Branch**: `003-language-switcher` | **Date**: 2026-11-19 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `/specs/003-language-switcher/spec.md`

## Summary

Add the missing request-time locale mechanism: a Common-owned middleware resolves the active
language from the session (seeding once per session from the account-stored preference via the
Sapphire account boundary), an authenticated switch endpoint updates session + account
together, and a generic navbar dropdown — rendered at the start of the navbar's right slot —
puts the control directly before the username dropdown on every module page. Arabic activates
full right-to-left presentation: dynamic `lang`/`dir` on layouts, an rtlcss-generated RTL
stylesheet loaded conditionally, a hand-authored `[dir="rtl"]` override layer for
component-level directional rules, and language configuration for DataTables chrome plus the
daterangepicker locale. Localized record data and bilingual exports already follow the active
locale and gain test coverage only.

## Technical Context

**Language/Version**: PHP 8.1+ (Laravel 10), Vue 3.5, Blade, Vite (rolldown), SCSS (Bootstrap 4.6 source)

**Primary Dependencies**: nwidart/laravel-modules, stancl/tenancy (bypassed for the switch route — locale is tenant-agnostic), yajra DataTables 2, select2, daterangepicker, bootbox; **new devDependency**: `rtlcss`

**Storage**: `s_users.locale` (Sapphire migration) + session key `locale`; CSS artifacts `public/build/**` + `.rtl.css` siblings

**Testing**: PHPUnit feature tests (existing harness: MariaDB via `.env.testing`, module-schema bootstrap, tenancy middlewares bypassed, AJAX post helper)

**Target Platform**: Laravel server-rendered pages + Vue-enhanced admin UI (tenant module pages)

**Project Type**: Modular monolith — Common (mechanism), Sapphire (storage + boundary), Ruby (first adopting layout)

**Performance Goals**: zero extra queries on ordinary requests (session-only resolution); RTL stylesheet loaded only when Arabic is active

**Constraints**: constitution Principles I–VI; login page stays English (Q3=B); visualizations container-mirrored only (Q2=A); session copy wins cross-session (Q5=A)

**Scale/Scope**: 1 migration, 1 middleware, 1 route+controller, 1 Vue component + navbar integration, 1 blade helper + layout adoption, 1 build script + npm wiring, ~1 RTL override sheet, 3 test classes

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | How |
|-----------|--------|-----|
| I. Bounded module ownership | ✅ | Common owns the mechanism (middleware, route, switcher UI, RTL assets); Sapphire owns the column and its only accessors (`AccountManager::locale/setLocale`); Ruby adopts the layout attributes |
| II. Business Blade over generic Vue | ✅ | The switcher is a generic, business-agnostic Vue component (language names + icon only); no business vocabulary leaks in; navbar integration is global, not per-module |
| III. Lava-first | ✅ | The shared mechanism lives in Common so every current/future module inherits it (FR-009); reuses existing locale tables and catalogs |
| IV. Tenant isolation & Sapphire auth | ✅ | Switch route authenticated; preference is per-account; no cross-tenant surface; tenant DB untouched by the mechanism itself |
| V. Schema as contract | ✅ | Single Sapphire-owned migration for `s_users.locale`; models updated in same change; runtime schema untouched |
| VI. Simplicity / YAGNI | ✅ | No cross-session propagation (Q5=A), no URL locale segments, no login-page switcher (Q3=B), visualizations LTR-internal (Q2=A) |

## Project Structure

### Documentation (this feature)

```text
specs/003-language-switcher/
├── spec.md                        # requirements (5 clarified decisions)
├── checklists/requirements.md     # 16/16
├── plan.md                        # this file
├── research.md                    # codebase findings
├── data-model.md                  # 1 migration + resolution flow
├── contracts/account-locale.md    # Common → Sapphire locale boundary
├── quickstart.md                  # manual + automated verification
└── tasks.md                       # /speckit.tasks output
```

### Source Code (repository root)

```text
Modules/Sapphire/
├── App/Models/User.php                     # locale cast
├── App/Services/AccountManager.php         # +locale()/setLocale()
└── Database/migrations/                    # +add_locale_to_users_table

Modules/Common/
├── App/Http/Middleware/SetAppLocale.php    # NEW — session-first resolution
├── App/Http/Controllers/LocaleController.php # NEW — switch endpoint
├── App/Providers/RouteServiceProvider.php  # NEW — POST /locale (auth)
├── routes/routes.php                       # +route
├── resources/views/
│   ├── html-attrs.blade.php                # NEW — shared lang/dir partial
│   └── resources.blade.php                 # +locale.lang/dir/switchUrl globals
└── resources/lang/{en,ar}/navbar.php       # NEW — switcher strings

Modules/Ruby/
└── resources/views/master.blade.php        # dynamic html attrs + conditional RTL css

resources/components/navbar/navbar.vue      # switcher at right-slot start
resources/components/navbar/language-switcher.vue  # NEW (generic)

resources/scss/_rtl-overrides.scss         # NEW — [dir=rtl] component overrides
scripts/build-rtl.mjs                      # NEW — manifest-driven rtlcss step
package.json                                # +rtlcss devDep, npm script wiring
app/Http/Kernel.php                         # +SetAppLocale in web group (1 line)

tests/Feature/Locale/
├── LocaleMiddlewareTest.php
├── LocaleSwitchTest.php
└── LocaleExportTest.php
```

**Structure Decision**: follow established module patterns exactly; the only core-app touch is the one-line Kernel registration (mechanism code itself stays in Common). The switch route skips the subdomain-tenancy middlewares — locale switching must work regardless of how the page is addressed.

## Complexity Tracking

No constitution violations. (RTL is delivered by build-tool transformation + one explicit
override layer rather than a parallel RTL framework — rejected alternatives: per-request
runtime flipping (flash of misaligned layout) and community Bootstrap-RTL packages (drift
from the themed build).)

## Milestones

**M1 — Mechanism (EN⇄AR switching, no RTL yet)**: migration + boundary accessors; middleware +
Kernel registration; switch endpoint; navbar switcher component + globals; layout lang/dir
partial (Ruby adopts); tests for the resolution matrix and endpoint.

**M2 — RTL presentation**: rtlcss build step + npm wiring; `_rtl-overrides.scss` for
component-level direction; conditional stylesheet load in layouts; widget alignment QA
(select2, daterangepicker locale, modals/tabs/forms, DataTables scroll/sort chrome).

**M3 — Polish & verification**: DataTables language object (chrome strings via
`locale` global); AR export coverage test; fallback test; EN-regression sweep; full suite
green; Pint; docs update on the `docs` branch.

## Testing Strategy

- **Feature tests**: middleware matrix (session value wins / session-seed from account once /
  anonymous default EN); switch endpoint (auth required, valid values only, session+account
  updated together, redirect back); rendered `<html lang/dir>` attributes per locale; exports
  honor Arabic (headers + punctuality); English fallback for missing translations.
- **Manual**: quickstart.md flow (switch, persistence incl. cross-device re-login, RTL walk of
  staff/attendance/departments/structure + modal, exports, EN regression, session semantics).
- **Regression guards**: existing suites stay green; EN pages byte-identical (no conditional
  markup beyond the html attributes + stylesheet link).

## Rollout

Single branch/PR (stacked on `002-…`), reviewable as M1→M3 commits. Deployment to live tenants:
`composer install` (no PHP deps), `npm run production` (emits RTL assets), Sapphire tenant
migration (`module:tenants-migrate`). No data backfill — column defaults to `en`. Docs branch
update included (constitution gate).
