# Tasks: Attendance Punctuality & Export

**Input**: Design documents from `/specs/002-attendance-punctuality-export/`

**Prerequisites**: plan.md (required), spec.md (required for user stories)

**Tests**: Included per the project constitution (Development Workflow): new backend
behavior ships with PHPUnit tests; suite runs on MariaDB `oaters_test` inside
`docker exec -w /home/projects/theultragrey/oaters lemp-php-1 php vendor/bin/phpunit ...`
(sqlite cannot work — `uuid_short()` in module migrations).

**Organization**: Tasks are grouped by user story (spec.md) so each story can be
implemented, tested and delivered independently. `[P]` = parallelizable (no file
dependencies with siblings in the same phase).

---

## Phase 1: Setup & Foundational (Blocking Prerequisites)

**⚠️ No user story work can begin until this phase is complete.**

- [x] T001 [P] Add the export writer dependency inside the container:
      `docker exec -w /home/projects/theultragrey/oaters lemp-php-1 composer require
      openspout/openspout` — commit `composer.json` + `composer.lock`.
- [x] T002 [P] Common settings mechanism (Lava-owned, spec FR-003): migration
      `Modules/Common/Database/migrations/*_create_lc_settings_table.php`
      (`id`, `key` string unique, `value` nullable string, timestamps) + model
      `Modules/Common/App/Models/Setting.php` (`$table = 'lc_settings'`,
      `$guarded = ['id']`) + service `Modules/Common/App/Services/Settings.php`
      (`get(string $key, $default = null)`, `set(string $key, $value)` via
      `updateOrCreate`). Register facade `app/Facades/Settings.php` and the `Settings`
      alias following the `DataTablesHelper` precedent (facade +
      `app/Providers/DataTablesHelperServiceProvider.php` + `config/app.php` aliases).
- [x] T003 [P] Add permission `manage-attendance-settings` to
      `Modules/Ruby/Database/Seeders/PermissionSeeder.php`; grant to `general-manager`,
      `hr-manager`, `hr-assistant` ONLY — widen the `financial-manager` exclusion
      (currently "all except view-recruitment") so it does NOT receive the new
      permission; `director`, `team-lead`, `accountant`, `employee` unchanged
      (view-only). Spec clarification 2026-11-02.
- [x] T004 [P] EN/AR lang file `Modules/Ruby/resources/lang/{en,ar}/attendance.php`:
      `punctuality`, punctuality values with `:minutes` placeholder using
      trans-choice for singular/plural (`on time`, `:m minute late|:m minutes late`,
      `:m minute early|:m minutes early`), settings modal strings (title, label, hint,
      save), export strings (excel, csv, button title), toast messages. Reuse
      `common::words.*` keys where present (e.g., `unassigned` for the neutral
      marker).
- [x] T005 Create `tests/Feature/Ruby/AttendanceTestCase.php` extending
      `StaffTestCase` (reuses MariaDB bootstrap, tenancy bypass, `postAjax`):
      helpers `makeShift($start, $end)` (factory), `makePunch($type, $latency,
      $shift, $overrides = [])` and a DataTables-shaped `postIndex` helper returning
      decoded JSON rows.

**Checkpoint**: settings mechanism + permission + lang + test bootstrap ready; user
story work can proceed.

---

## Phase 2: User Story 1 — Punctuality Column (P1) 🎯 MVP

**Goal**: Every punch shows a color-coded punctuality reading derived from its
recorded latency; threshold comes from the settings mechanism with the 15-minute
default.

**Independent Test**: seed one punch per rule cell + one shift-less punch; `postIndex`
returns the correct text + CSS class for each (spec US1 scenarios 1–8).

### Tests for User Story 1 (write first, must fail)

- [x] T006 [P] [US1] `tests/Feature/Ruby/PunctualityColumnTest.php`: six rule cells
      (In 12/20 min late, In 10 early, Out 8/25 early, Out 30 late, Out 0 on time at
      default threshold 15) assert rendered `<span>` classes (`text-primary`,
      `text-danger`, `text-success font-weight-bold`, `text-success`) and texts;
      shift-less punch (`shift_id`/`latency` null) renders the `text-muted`
      unassigned marker; `\Settings::set('ruby.attendance.punctularity_threshold', 5)`
      flips a 6-minute late In from `text-primary` to `text-danger` on the next
      request (FR-005); ordering by column name `r_punches.latency` sorts rows by
      latency (FR-011); texts localize via `app()->setLocale('ar')` spot-check.

### Implementation for User Story 1

- [x] T007 [US1] Create `Modules/Ruby/App/Support/Punctuality.php`: constants
      `KEY = 'ruby.attendance.punctularity_threshold'`, `DEFAULT = 15`, `MAX = 240`;
      `threshold(): int` (via `\Settings::get(KEY, DEFAULT)`, clamped to 0–MAX);
      `reading(Punch $punch): ?array{text, class}` implementing the full FR-001/FR-002
      matrix (sign × type; favorable = `text-success font-weight-bold`, on-time =
      `text-success`, adverse > threshold = `text-danger`, adverse ≤ threshold =
      `text-primary`, null latency = null) with trans-choice labels.
- [x] T008 [US1] `Modules/Ruby/App/Http/Controllers/AttendanceController@postIndex`:
      `addColumn('punctuality')` rendering `Punctuality::reading()` as a styled span
      (or the neutral marker); add `punctuality` to `rawColumns`; verify the shared
      filter/order path keeps working with the new column.
- [x] T009 [US1] `Modules/Ruby/resources/views/attendance.blade.php`: add
      `<dt-column name="r_punches.latency" data="punctuality" :searchable="false">`
      (label `ruby::attendance.punctuality`) between Type and Created At; shift the
      default `:sort` index so Created At stays the initial descending sort.

**Checkpoint**: punctuality column live in EN/AR with default threshold — MVP
deliverable on its own.

---

## Phase 3: User Story 2 — Threshold Settings Surface (P2)

**Goal**: Granted roles view/change the threshold (0–240 minutes); new value drives
coloring on the next table load; invalid input rejected with prior value intact.

**Independent Test**: as `hr-manager`, change 15 → 5 and verify a 6-minute late In
punch turns red after reload; as `team-lead` the save is a 403.

### Tests for User Story 2 (write first, must fail)

- [x] T010 [P] [US2] `tests/Feature/Ruby/AttendanceSettingsTest.php`: default 15
      returned when no row exists (FR-004); set/get roundtrip persists
      `lc_settings`; rejections (empty, non-integer, negative, 241) return 422 JSON
      and leave the stored value unchanged (FR-006, SC-005); `hr-manager` (granted)
      succeeds, `team-lead` (not granted) gets 403 on GET and POST (FR-010, SC-006);
      Arabic validation message spot-check.

### Implementation for User Story 2

- [x] T011 [US2] Routes in `Modules/Ruby/routes/routes.php` (attendance group):
      `GET attendance/settings` → `AttendanceController@getSettings`,
      `POST attendance/settings` → `postSettings`, both behind
      `can:manage-attendance-settings` (in addition to the group's `auth + can:ruby`).
- [x] T012 [US2] `Modules/Ruby/App/Http/Requests/SettingRequest.php`:
      `threshold` required, integer, min 0, max 240; `AttendanceController`:
      `getSettings` returns `{threshold: Punctuality::threshold()}`;
      `postSettings` validates via `SettingRequest` (AJAX 422 JSON via `postAjax`
      semantics), stores `\Settings::set(Punctuality::KEY, $threshold)`, returns the
      saved value.
- [x] T013 [US2] `Modules/Ruby/resources/views/components/modals/attendance-settings.blade.php`
      (business Blade, pattern: `department-form`): numeric field, current value
      loaded from `attendance/settings`, save via the existing form-submit pattern
      with per-field error display; gear button in the attendance card header
      (`attendance.blade.php`) wrapped in `@can('manage-attendance-settings')`.
- [x] T014 [US2] Wire `resources/js/ruby/attendance.js`: settings modal open/submit
      (pattern: `departments.js` submit: form.submit → toast → `dataTable.draw()` so
      colors refresh immediately, FR-005).

**Checkpoint**: threshold is tenant-tunable and permission-gated; US1 + US2 both
independently functional.

---

## Phase 4: User Story 3 — Filtered Export (P3)

**Goal**: Excel (.xlsx) and CSV export containing every punch matching the current
table filters/search/order — never just the visible page — with localized headers and
punctuality as text.

**Independent Test**: filter to one department + date range, export both formats; the
file rows equal the filtered `postIndex` data row-for-row, same order (spec US3
scenario 1).

### Tests for User Story 3 (write first, must fail)

- [x] T015 [P] [US3] `tests/Feature/Ruby/AttendanceExportTest.php`: seeded punches +
      department/date/type filter payload → CSV body matches the filtered `postIndex`
      row set exactly (order included; cells serialized per plan §Key Decisions #7)
      with localized headers, leading UTF-8 BOM, punctuality text column, NO Actions
      column (FR-007/FR-008, SC-003/SC-004);
      XLSX response is a valid zip and sheet rows match via openspout reader;
      zero-match filters → headers-only file; unfiltered → all rows; user without
      `ruby` access is redirected/denied.

### Implementation for User Story 3

- [x] T016 [US3] Refactor `AttendanceController@postIndex`: extract the
      filter/order/with-relationships query into a private `attendanceQuery()` used by
      both the DataTables feed and the exporter (single source of filter truth).
- [x] T017 [US3] Route `POST attendance/export` (inside the attendance group — same
      access as viewing the table) → `AttendanceController@postExport`: accept the
      DataTables request shape (the JS sends `table.ajax.params()`) + `format`
      (`xlsx|csv`); render ALL matching rows (yajra over `attendanceQuery()` with
      paging skipped or equivalent chunked retrieval); stream via openspout — CSV
      with UTF-8 BOM, XLSX plain; headers and cells per plan §Key Decisions #7
      (columns mirror the table; departments comma-joined, first role, table
      timestamp format, empty punctuality cell for shift-less punches) via
      `trans()` in the requesting locale; filename
      `attendance_YYYY-MM-DD.xlsx|.csv` in `Content-Disposition`.
- [x] T018 [US3] `attendance.blade.php`: Excel + CSV buttons in the attendance card
      header; `resources/js/ruby/attendance.js`: `exportTable(format)` posts
      `dataTable.ajax.params()` + format as a blob, triggers browser download from
      the response `Content-Disposition` filename.

**Checkpoint**: all three stories independently functional.

---

## Phase 5: Finalization

- [x] T019 Build assets in the container:
      `docker exec -w /home/projects/theultragrey/oaters lemp-php-1 npm run development`
      and verify the attendance page in the browser (EN + AR pass: column colors,
      settings modal, exports open in LibreOffice/Excel); time an export of one
      month of punches (~5,000 rows) in both formats against SC-003 (< 10 s each).
- [x] T020 `docker exec -w /home/projects/theultragrey/oaters lemp-php-1
      ./vendor/bin/pint` over touched files; full
      `php vendor/bin/phpunit` green (existing staff suite + new tests).
- [x] T021 Update VitePress docs on the `docs` branch (linked follow-up commit,
      constitution documentation gate): Ruby attendance page — punctuality column,
      threshold setting (roles, bounds, default), export behavior.
- [x] T022 Mark spec.md **Status: Implemented** and check off this task list.

---

## Dependencies & Execution Order

- **Phase 1** blocks all story work (settings mechanism T002 + lang T004 are needed
  by US1; permission T003 by US2; writer T001 by US3; T005 by every test).
- **US1 (Phase 2)** → no story dependencies. **US2 (Phase 3)** depends on T002/T003
  and reuses `Punctuality` from T007 for default display. **US3 (Phase 4)** depends
  on T001 and reuses `Punctuality` (text) from T007; T016 refactors the controller
  shared with US1 — do US3 after US1 to avoid churn.
- **Phase 5** after all desired stories complete.
- Suggested sequence: T001–T005 (parallelizable) → T006 → T007–T009 → T010 →
  T011–T014 → T015 → T016–T018 → T019–T022.

## Notes

- All PHP/Composer/Node/test commands run inside the LEMP container (host has none).
- Tests run against MariaDB `oaters_test` via `.env.testing`; never enable sqlite.
- Commit after each task or logical group; the branch stacks on
  `001-complete-staff-module` and targets PR #2.
