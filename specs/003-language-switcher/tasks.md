# Tasks: Language Switcher (EN/AR + RTL)

**Input**: Design documents from `/specs/003-language-switcher/`

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md,
data-model.md, contracts/account-locale.md

**Tests**: Included per the project constitution (Development Workflow): new backend behavior
ships with PHPUnit tests.

**Organization**: Tasks grouped by user story / milestone. `[P]` = parallelizable (no file
dependencies with siblings in the same phase).

---

## Phase 1: Setup (Shared Infrastructure)

- [x] T001 [P] Sapphire migration `Modules/Sapphire/Database/migrations/`: add
      `locale enum('en','ar') not null default 'en'` to `s_users`; add the cast in
      `Modules/Sapphire/App/Models/User.php`.
- [x] T002 [P] Extend `Modules/Sapphire/App/Services/AccountManager.php` per
      `contracts/account-locale.md`: `locale(int $contactId): string` (defensive `'en'`
      fallback) and `setLocale(int $contactId, string $locale): void` (accepts `en|ar` only).
- [x] T003 [P] Add switcher strings `Modules/Common/resources/lang/{en,ar}/navbar.php`
      (language names, menu aria-labels, active marker).

## Phase 2: Foundational (Blocking Prerequisites)

**⚠️ No user story work can begin until this phase is complete.**

- [x] T004 Create `Modules/Common/App/Http/Middleware/SetAppLocale.php`: session value wins;
      when absent and authenticated, seed once from `AccountManager::locale()` (contact id via
      `auth()->user()->contact->id`) and store into the session; anonymous default `en`.
      Register it in `app/Http/Kernel.php`'s `web` group immediately after `StartSession`.
- [x] T005 Create `Modules/Common/App/Providers/RouteServiceProvider.php` + route in
      `Modules/Common/routes/routes.php`: `POST /locale` (`LocaleController@switch`) behind
      `auth`; prefixed by no module URL segment (locale switching works from every module's
      pages) — tenancy IS initialized from the subdomain, since the account-stored
      preference lives in the tenant database (found during e2e QA). Register the provider
      in Common's `ModuleServiceProvider` (the established pattern).
- [x] T006 Create `Modules/Common/App/Http/Controllers/LocaleController.php`: validate
      `locale ∈ {en, ar}` (422 otherwise), write `session(['locale' => …])` +
      `AccountManager::setLocale(…)` together, redirect back.
- [x] T007 Shared layout partial `Modules/Common/resources/views/html-attrs.blade.php`
      emitting `lang="{{app()->getLocale()}}" dir="{{…}}"`; adopt it in
      `Modules/Ruby/resources/views/master.blade.php`'s `<html>` tag (other layouts remain
      English per Q3=B).
- [x] T008 Extend `Modules/Common/resources/views/resources.blade.php` globals:
      `locale.lang`, `locale.dir`, `locale.switchUrl` (`route('common::locale.switch')`).

**Checkpoint**: language switchable server-side; pages carry correct `lang`/`dir`.

## Phase 3: User Story 1 — Switch the Interface Language (P1) 🎯 MVP

- [x] T009 Create `resources/components/navbar/language-switcher.vue` (generic): dropdown
      offering English/العربية with the active language indicated; posts to
      `locale.switchUrl` (CSRF via the global header) and reloads on success; reads current
      language from `locale.lang`. No business vocabulary.
- [x] T010 Render the switcher in `resources/components/navbar/navbar.vue` at the **start of
      the right slot** so it precedes the username dropdown on every navbar'd page (FR-001);
      register the component wherever the navbar bundle loads.
- [x] T011 DataTables language: extend `resources/components/datatable/datatable.vue` with a
      `language` option fed from the `locale` global (search/info/pagination/empty-table
      strings; Arabic translations in `navbar.php` or a dedicated `datatable.php` lang file)
      so table chrome honors FR-002.
- [x] T012 Set the daterangepicker locale from the `locale` global in
      `resources/components/form/field.vue` (initDatePicker) — Arabic month/day labels and
      RTL-friendly popup position.
- [x] T013 Tests `tests/Feature/Locale/LocaleMiddlewareTest.php`: session value wins over
      account; session-seed reads account exactly once then caches (no per-request read);
      anonymous default English; `lang`/`dir` attributes correct per locale.
- [x] T014 Tests `tests/Feature/Locale/LocaleSwitchTest.php`: endpoint requires auth; invalid
      locale 422; valid switch updates session + account together and redirects back;
      localized record data (role/department names) renders Arabic with English fallback for
      a missing translation.

## Phase 4: User Story 2 — Mirrored (RTL) Layout (P2)

- [x] T015 Add `rtlcss` devDependency + `scripts/build-rtl.mjs` (reads
      `public/build/.vite/manifest.json`, rtlcss-transforms every emitted CSS asset to a
      `.rtl.css` sibling); wire into both npm scripts after the Vite build.
- [x] T016 Author `resources/scss/_rtl-overrides.scss` (appended to the RTL build):
      `[dir="rtl"]` rules for component-level direction the SCSS transform can't reach —
      navbar `ml-auto` flip, breadcrumb separator + order, timeline connector direction,
      modal footer button order, form label/input column order, alert/toast icon spacing,
      select2 alignment, DataTables scroll/sort-icon alignment — plus bidirectional text
      isolation for data values (English names, emails, phone numbers and dates inside RTL
      cells) via `unicode-bidi` isolation so embedded left-to-right runs stay readable.
- [x] T017 Conditional RTL load: in `Modules/Ruby/resources/views/master.blade.php` (via the
      shared helper), emit a stylesheet link to the `.rtl.css` variant of the theme asset only
      when direction is RTL; keep the LTR link unconditional (EN stays byte-identical).
- [x] T018 Widget RTL QA fixes pass: select2 dropdown alignment, daterangepicker popup
      position, bootbox confirmation buttons, tabs order, table scroll direction, org-chart
      and chart containers mirrored with LTR inner drawing (Q2=A) — adjust the override sheet,
      not the widgets' business logic.
- [x] T019 Manual RTL walkthrough per quickstart.md (staff list + filters, create/edit modal,
      attendance + settings modal, departments, structure) — record defects and fix in the
      override layer; zero blocking defects on those surfaces (SC-003). Include a
      mixed-direction check: rows whose name/email/phone/date values are English-only must
      read correctly inside the RTL table cells (U1).

## Phase 5: User Story 3 — Preference Follows the User (P3)

- [x] T020 Tests for persistence semantics: switch persists across navigation (session);
      re-login adopts the account value including cross-device (fresh session seeds from
      account); a second live session keeps its working language until re-login (Q5=A).
- [x] T021 Tests `tests/Feature/Locale/LocaleExportTest.php`: attendance CSV + XLSX under
      Arabic carry Arabic headers and punctuality readings; UTF-8 BOM intact; English export
      unchanged.

## Phase 6: Finalization

- [x] T022 Run `./vendor/bin/pint` over touched PHP files; full `php artisan test` green;
      `npm run development` emits both LTR and RTL assets; manual quickstart pass EN + AR.
- [x] T023 English-regression check: with `en` active, pages are byte-identical to
      pre-feature (only `lang`/`dir` attributes and no RTL link differ) — SC-004.
- [ ] T024 Update VitePress docs on the `docs` branch: language switcher page (usage,
      persistence model, RTL build step) + Ruby module page note (constitution gate).
- [ ] T025 Mark spec.md **Status: Implemented** and check off this task list.
