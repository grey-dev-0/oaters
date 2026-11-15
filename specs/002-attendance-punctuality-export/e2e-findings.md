# E2E Findings: Attendance Punctuality & Export

**Tested on**: branch `002-attendance-punctuality-export` · **Date**: 2026-11-14/15
**Surface**: web (tenant `maxwell` @ `https://maxwell.oaters.docker/r/attendance`) via Orca browser + API
**Tester**: local agent (OpenCode) — see `specs/E2E-HANDOFF.md` for environment and how to continue

## Verdict summary

| Area | Result |
| --- | --- |
| US1 Punctuality column: text + colors (six rule cells, on-time, neutral) | ✅ PASS (verified at default 15 and via renderer at threshold 5 incl. exact 5=blue / 6=red boundary; plain minutes "90 minutes late") |
| US1 Sorting by punctuality (FR-011) | ❌ **P2 broken** — sort requests ignored, both directions return arbitrary order |
| US2 Threshold settings endpoint (default, save, validation, permissions) | ✅ PASS (API-level) |
| US2 Settings surface in the UI (FR-006) | ❌ **P1 broken** — modal renders no input; Save throws |
| US3 Export CSV/XLSX (full, filtered, zero-match) | ✅ PASS |
| Attendance page stability with deactivated members | ❌ **P1** — page dead for all users while any punch's contact is soft-deleted |
| Arabic (FR-009) | ⚠️ Strings exist (incl. singular/plural) but unreachable in the UI — no locale switcher in the web app |
| Automated feature tests | ✅ 48 passed / 223 assertions |

## Findings

### F-101 (P1) — Attendance page breaks for every user when any member with punches is deactivated

- **Repro**: deactivate a staff member that has punches (normal spec-001 US3 flow!) → open
  `/r/attendance` → table stuck on "Loading…" + `#attendance-table_processing` overlay forever; no
  console error surfaces. Happened with Dr. Kaycee Dooley (contact 4, 40 punches).
- **Root cause**: `Modules/Ruby/App/Models/Punch::contact()` is a plain `belongsTo(Contact::class)` —
  soft-deleted contacts drop out of the eager load, but `postDelete()` keeps their punches (history).
  Rows then ship with `contact: null` and the client renderers
  (`resources/js/ruby/attendance.js::renderDepartments` → `row.contact.departments`,
  `renderActions` → `.data().contact`) throw, killing the DataTables draw. Server response itself is a
  healthy 200 JSON.
- **Verified**: restoring the contact (POST `/r/contacts/restore`) instantly revives the table
  (25 rows, processing cleared, first row = her punch with neutral "Unassigned" marker).
- **Fix suggestions** (either side, ideally both):
  - server: eager-load trashed contacts (`'contact' => fn ($q) => $q->withRoles()->withDepartments()->withTrashed()`)
    and/or exclude punches of trashed contacts from the default query;
  - client: null-guard `row.contact` in `renderDepartments`/`renderActions`.

### F-102 (P1) — Attendance Settings modal is unusable: threshold input never renders

- **Where**: `resources/js/ruby/attendance.js` (page entry) vs `Modules/Ruby/resources/views/components/modals/attendance-settings.blade.php`.
- **Evidence**: modal opens with heading/label/hint/Cancel/Save but the body renders raw custom tags
  `<vue-form>`/`<vue-field>` (console: "Failed to resolve component: vue-form / vue-field") — **zero
  inputs in the DOM**. Clicking Save throws `Uncaught TypeError:
  this.$refs.attendanceSettingsForm.submit is not a function` and nothing is sent.
- **Root cause**: `attendance.js` registers only `bundles = [Datatable, Timeline, List]` and
  `components = {Modal: 'modal'}` — the **Form bundle is missing** (compare contacts.js:
  `[Datatable, Form, Timeline, List, Tab]` + `Autocomplete`). The settings modal is the only thing on
  this page that needs it, so nothing else breaks.
- **Fix**: add `Form` to the bundles (and whatever component `vue-field` lives in) in
  `resources/js/ruby/attendance.js`, rebuild assets. Then the full UI settings flow
  (view 15 → change to 5 → see colors flip) becomes testable — the backend already works (below).

### F-103 (P2) — Punctuality column sorting does nothing (FR-011)

- **Evidence**: API replay with `order[0][column]=4` (latency column) returns the **identical
  arbitrary sequence for both `asc` and `desc`**: `[null×4, 52, -19, 25, -14, 45, 55, 33, 24, null,
  null, -21, 37, ...]`. The initial view that "looked" sorted was the default `created_at desc`
  ordering coinciding with seed clusters.
- **Root cause**: no `orderColumn` mapping exists in `AttendanceController::postIndex()` for the
  latency column (its `data` is the virtual `punctuality` column, so yajra has no SQL to order by).
- **Fix**: add `->orderColumn('r_punches.latency', 'r_punches.latency $1')` (or equivalent) — and keep
  nulls positioning consistent. Until fixed, "export mirrors current sort" (FR-007) is also untestable.

### F-104 (P3, defensive) — Date-filter helper crashes when a request omits the created_at column

`DataTablesHelper::filterByDate()` does `$columns['r_punches.created_at']` without isset →
`Undefined array key` exception (error JSON + empty data). Only reachable by non-standard clients
today (the real table always sends all columns), but a one-line guard makes it safe.

### Note — Arabic (FR-009)

`Modules/Ruby/resources/lang/ar/attendance.php` contains the full set incl. correct singular/plural
(`'minutes_late' => '{1}دقيقة واحدة متأخر|{2,*}:m دقائق متأخر'`) and CSV exports ship UTF-8 with BOM.
But the web app has **no locale switcher at all** (nothing sets the app locale from a request; only
tests do), so every "interface language is Arabic" scenario is unreachable in the browser. Needs a
product decision (add switcher / accept test-only coverage).

## What passed (verified, do not re-test)

- **Punctuality rendering** (browser at threshold 15): in-late 6/7 → blue `text-primary`; in-late
  26/36 → red `text-danger`; in-early 3 → bold green; out-late 21/23 → bold green; latency null →
  gray neutral "Unassigned" (`text-muted`), no minutes value.
- **Renderer boundary probe** (tinker, threshold 5 live from `lc_settings`): in 4→blue, in 5→blue,
  in 6→red, out −5→blue, out −6→red; 0 → green "on time"; in −3 / out +23 → bold green; 90 →
  "90 minutes late" (plain minutes). One threshold governs both directions.
- **Settings endpoint**: `GET /r/attendance/settings` → `{"threshold":15}` default (table empty);
  `POST threshold=5` → saves to `lc_settings` key `ruby.attendance.punctuality_threshold` and drives
  the very next table load (FR-005). Invalid values — empty / −1 / 241 / `abc` / `3.7` — all 422 with
  actionable messages ("must be a whole number of minutes", "may not be greater than 240 minutes",
  "at least 0 minutes"), stored value untouched.
- **Permissions (FR-010)**: hr-manager (tom78) — page + settings OK; employee (simonis.litzy) —
  attendance page 200, `GET`/`POST /r/attendance/settings` → **403** (route middleware
  `can:manage-attendance-settings`). Roles holding it: general-manager, hr-manager, accountant,
  hr-assistant.
- **Export (FR-007/FR-008/FR-013, US3)**:
  - Full CSV (browser-real params): 200, `text/csv; charset=UTF-8` with BOM, **960 rows + header**,
    columns exactly Name, Role, Departments, Type, Punctuality, Created At — **no Actions column**;
    punctuality as text; null-latency rows export an empty cell.
  - Filtered (departments = Finance, real UI filter → browser download): file contains exactly the
    filtered table's 80 rows, Finance-only, row-for-row.
  - XLSX: valid Excel-2007+ zip, correct MIME, 81 sheet rows (80 + header), same columns.
  - Zero-match (date range with no punches): clean headers-only CSV, no corruption.
- **Feature suite**: `php artisan test` → 48 passed / 223 assertions (incl. Punctuality/Settings/AR
  assertions via `app()->setLocale('ar')`).

## Not yet exercised (for the remote agent)

- Settings modal UI flow after F-102 fix (view/change/toast/live flip in-browser).
- Sorting + "export mirrors current sort" after F-103 fix.
- Arabic end-to-end in the UI (blocked on a locale switcher existing).
- Large export timing gate (SC-003: ~5,000 punches < 10 s) — current DB has 960 punches; seed more if gating.
- Concurrent threshold edits (last-write-wins) — edge case, low priority.
- Cross-tenant settings isolation (FR-012) — structurally sound (setting lives in the tenant DB);
  only one usable tenant (`maxwell`) exists locally, so the negative path wasn't exercised.
