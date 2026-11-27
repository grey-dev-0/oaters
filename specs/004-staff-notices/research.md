# Research: Staff Notices Page

**Feature**: 004-staff-notices | **Date**: 2026-11-27 | **Branch**: `004-staff-notices` (cut from `003-language-switcher`)

## Deduction basis (schema → purpose)

`r_notices` (ruby_base migration) reads as a formal personnel record:
- `author_id` → `lc_contacts`, cascade — the issuing HR member; record dies with the author's
  hard deletion (contacts are soft-deleted in practice, so history persists)
- `contact_id` → `lc_contacts`, nullable, SET NULL — the recipient; detachment (not
  deletion) keeps the record if the member is ever hard-removed
- `payroll_payment_id` → `r_payroll_payments`, nullable, cascade — optional anchor to a
  payroll payment event (e.g., a termination tied to the final settlement)
- `type` enum(`notice`, `warning`, `termination`), `content` text, timestamps

`ContactController::hasHistory()` already refuses permanent removal for members holding
notices (as author **or** recipient) — the retention rule exists and goes live with this
feature. The navbar Personnel → Notices link exists and is dead. `ruby::words.notices`
ships in both languages. The `Notice` model is a bare stub (no relations, guarded).

## Decision: page anatomy mirrors the attendance page

- **Decision**: one master-rendered page (breadcrumb card + `vue-datafilter` +
  `vue-datatable` + action column), one create/edit modal, bootbox-confirmed delete.
- **Rationale**: identical list-page shape (filters + server-side DataTables + row actions)
  is the module's established pattern; reusing it means zero new generic components
  (Principle II) and free RTL/bilingual from spec 003.
- **Alternatives**: a timeline-based page (the profile modal uses timelines for leaves) —
  rejected: no filtering/pagination story for a formal record trail.

## Decision: routing and permission gating

- **Decision**: `notices` prefix inside the module's `auth + can:ruby` group:
  `GET/POST /` (list + data) open to module access; `create`/`update`/`delete` behind
  per-route `can:manage-notices` — exactly how attendance settings routes are gated.
  The blade issues/edits controls render inside `@can('manage-notices')` (attendance.blade
  precedent).
- **Rationale**: user decision 1B at specify time; matches `manage-attendance-settings`
  in routes, blade and tests.
- **Alternatives**: `can:ruby` for everything (rejected by user); author-scoped edit rules
  (rejected — provenance already preserved via immutable author column).
- **Seeding**: `PermissionSeeder` already seeds a legacy `view-notices` (unused by current
  routes — the module gates by `can:ruby`); `manage-notices` is added there and granted to
  the issuing roles (`general-manager`, `hr-manager`, `hr-assistant` — mirroring how
  `manage-attendance-settings` excludes `financial-manager` and below).

## Decision: author resolution

- **Decision**: the controller resolves the author as the signed-in user's linked contact
  (`auth()->user()->contact->id`); it is never a form input. A signed-in user without a
  linked contact gets a clear validation-style failure (the author column is NOT NULL).
- **Rationale**: FR-005 (formal record provenance); same identity the navbar renders.
- **Alternatives**: a pickable author (rejected — falsifiable provenance).

## Decision: filters

- **Decision**: server-side `filterNotices` using `DataTablesHelper::getColumns()` +
  `filterByDate` on `r_notices.created_at` (attendance `filterAttendance` pattern):
  type → `whereIn('type')`; recipient → text-search filter matching the recipient's name
  (`whereHas('recipient', …name like…)`), author → text-search filter matching the
  author's name the same way — both matching **all** members, including deactivated ones.
- **Rationale**: FR-003 (two separate member filters, clarification 2026-11-27) plus
  FR-010's "remain listed and filterable" — text search keeps deactivated members
  filterable (analysis remediation I1), mirrors the attendance page's name filter, and
  needs no picker payload. The issue form's recipient select2 stays active-only per the
  spec's assumption.
- **Alternatives**: active-only select2 pickers (rejected — records become un-filterable
  once a member deactivates); all-members select2 (rejected — heavier payload, no
  precedent).

## Decision: form + delete flows

- **Decision**: `ruby::modals.notice-form` blade (vue-form + vue-field: select2 recipient
  from active members, select type, textarea content) validated by `NoticeRequest`
  (rules + trans-based messages, `SettingRequest` pattern); edit mode reuses the modal with
  the recipient/type/content prefilled — author and created date never accepted from input.
  Delete: bootbox.confirm with a page-pushed locale string → `$.post` → toast →
  `dataTable.draw()` (contacts.js pattern).
- **Rationale**: FR-006/FR-007/FR-008; every existing form/delete in the module works this
  way.
- **Alternatives**: inline table editing (rejected — no precedent, worse RTL/bilingual).

## Decision: type rendering and payroll link

- **Decision**: type column renders the localized label with a semantic color class
  (punctuality-column precedent: `text-primary`/`text-warning`/`text-danger` via a
  `renderType` function fed from the page's locale push). The payroll payment link, when
  present, renders read-only in its own column (payment date); nothing on the page can set
  it.
- **Rationale**: FR-004/FR-011.
- **Alternatives**: icons instead of colors (colors suffice and match the module).

## Localization & RTL (inherited from 003)

- New `Modules/Ruby/resources/lang/{en,ar}/notices.php` (page strings, type labels,
  confirmation and validation messages); member names are plain string data (bidi-safe
  via the 003 `unicode-bidi: plaintext` cell rule).
- Page bundle `resources/js/ruby/notices.js` is auto-discovered by Vite's glob; strings
  reach it through the page's `locale` push (`uiLocale` carries nothing page-specific).
- The RTL sheets and switcher need zero page work (verified by the 003 walkthrough).

## Validation & tests

- New feature tests follow the staff/attendance harness (`StaffTestCase`):
  list+filters matrix, permission gating (403 for unauthorized issue/edit/delete),
  issue (author auto-set; contactless user refused), edit (author/created_at immutable),
  delete (confirmed, gone), retention regression (member with notices refuses permanent
  removal — exercises the existing `hasHistory` branch), bilingual strings present under
  Arabic.
- No export tests (no export in scope).

## Gaps this feature closes

1. `Notice` model: relations (`author`, `recipient`, `payrollPayment`), unguarded.
2. Routes + `NoticeController` + `NoticeRequest`.
3. `notices.blade.php` + `notice-form` modal + `notices.js` bundle.
4. `PermissionSeeder`: `manage-notices` + role grants.
5. `{en,ar}/notices.php` catalogs.
6. Tests + tenant re-seed note (`module:tenants-seed` for the permission) + e2e RTL pass.
