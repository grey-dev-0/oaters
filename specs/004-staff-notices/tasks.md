# Tasks: Staff Notices Page

**Input**: Design documents from `/specs/004-staff-notices/`

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md,
data-model.md, quickstart.md

**Tests**: Included per the project constitution (Development Workflow): new backend
behavior ships with PHPUnit tests.

**Organization**: Tasks grouped by user story. `[P]` = parallelizable (different files, no
dependencies with siblings in the same phase). `[US*]` maps to the spec's user stories.

---

## Phase 1: Setup (Shared Infrastructure)

- [ ] T001 [P] Extend `Modules/Ruby/Database/Seeders/PermissionSeeder.php`: add the
      `manage-notices` permission (guard `web`) and grant it to the issuing roles
      (`general-manager`, `hr-manager`, `hr-assistant` — mirroring how
      `manage-attendance-settings` excludes `financial-manager`); leave the legacy unused
      `view-notices` row untouched.
- [ ] T002 [P] Author page string catalogs `Modules/Ruby/resources/lang/en/notices.php`
      and `Modules/Ruby/resources/lang/ar/notices.php`: page title, new/issue button,
      modal titles, type labels (notice/warning/termination), delete confirmation,
      save/created/updated/deleted messages, see-more/see-less content-expansion
      labels, validation messages (`contact_id`, `type`, `content`), the
      no-author-profile error, and the payroll-payment column label.
- [ ] T003 [P] Complete `Modules/Ruby/App/Models/Notice.php`: `author()` and
      `recipient()` (belongsTo `Contact`) and `payrollPayment()` (belongsTo) relations;
      unguard the model (`$guarded = []`); no fillable restriction of author/timestamps
      is needed there — writes go through the request layer (data-model.md).

## Phase 2: Foundational (Blocking Prerequisites)

**⚠️ No user story work can begin until this phase is complete.**

- [ ] T004 Add the notices route group to `Modules/Ruby/routes/routes.php` inside the
      `auth + can:ruby` group: `GET /notices` + `POST /notices` (list + data, named
      `notices.index`), and `POST /notices/create`, `POST /notices/update`,
      `POST /notices/delete` (named `notices.create/update/delete`) each behind
      `->middleware('can:manage-notices')` — the attendance-settings pattern.
- [ ] T005 Create `Modules/Ruby/App/Http/Requests/NoticeRequest.php` with the
      data-model rules: `contact_id` required/integer/exists among active recruited
      members, `type` required in `notice|warning|termination`, `content`
      required/string; localized messages from `ruby::notices` (the `SettingRequest`
      pattern); authorization delegated to the route's `can:` middleware.

**Checkpoint**: routes live (page 404-free), validation contract in place.

## Phase 3: User Story 1 — Review the Notices Record Trail (P1) 🎯 MVP

**Goal**: the filterable, paginated record trail — recipient, author, type, content,
issue date (newest first) — reachable from the Personnel → Notices navbar entry for
every Ruby user.

**Independent Test**: with notices seeded, open `/r/notices`, verify every column and
each filter (type, recipient, author, date range — alone and combined, then cleared),
and confirm records of deactivated members stay listed.

- [ ] T006 [P] [US1] Implement `Modules/Ruby/App/Http/Controllers/NoticeController.php`
      `getIndex`/`postIndex`: page data (active recruited members for the issue form's
      recipient picker), server-side DataTables with `author`/`recipient`/`payrollPayment`
      eager loads, newest-first order, and `filterNotices` — type `whereIn`,
      recipient and author as text-search name filters (`whereHas` on the
      respective contact relation, matching all members including deactivated ones),
      date range via `\DataTablesHelper::filterByDate` on `r_notices.created_at` —
      following `AttendanceController::filterAttendance`.
- [ ] T007 [US1] Create `Modules/Ruby/resources/views/notices.blade.php` (attendance
      page anatomy): master/breadcrumb/card, `vue-datafilter` with four `dt-filter`s
      (type select with localized values, recipient text search, author text search,
      date range), `vue-datatable` columns (recipient, author, type, content, payment,
      created_at, actions) with the content cell eliding long text behind a see-more
      control, the page `locale` push for JS strings, and the empty state.
- [ ] T008 [US1] Create `resources/js/ruby/notices.js` page bundle (Vite auto-discovers
      it): `renderType` (localized label + `text-primary`/`text-warning`/`text-danger`
      color class from the locale push), `renderContent` (elides long text with a
      see-more/see-less toggle using the locale-pushed labels), `renderActions`
      (view-only page renders no edit/delete), payment column rendering.
- [ ] T009 [US1] Tests `tests/Feature/Ruby/NoticePageTest.php`: columns render newest
      first; each filter alone and combined narrows exactly (type, recipient name,
      author name, date range); clear restores the trail; records concerning
      deactivated members remain listed **and reachable through the name filters**; a
      record with a `payroll_payment_id` renders the payment link read-only; list
      routes stay 200 for users without `manage-notices`.

**Checkpoint**: the trail is live and independently demoable — the dead navbar link
works.

## Phase 4: User Story 2 — Issue and Manage Notices (P2)

**Goal**: permitted users issue notice/warning/termination records (author auto-recorded),
edit recipient/type/content with provenance preserved, and delete after confirmation;
others see no controls and their direct POSTs are refused.

**Independent Test**: as an HR manager issue one record of each type, edit one, delete
one; as a view-only user confirm the controls are absent and write POSTs return 403.

- [ ] T010 [US2] Create `Modules/Ruby/resources/views/components/modals/notice-form.blade.php`
      (contact-form pattern): `vue-form` posting to `notices.create`/`notices.update`
      behind `can:manage-notices`, `vue-field` recipient select2 (active members), type
      select, content textarea; edit mode prefills recipient/type/content only —
      a deactivated current recipient stays a valid prefilled value, with only *new*
      selections restricted to active members (spec edge case).
- [ ] T011 [US2] Implement `postCreateOrUpdate` (+ `postDelete`) in
      `Modules/Ruby/App/Controllers/NoticeController.php`: author resolved from
      `auth()->user()->contact` and rejected with the localized no-author error for
      contactless users; create stores author+recipient+type+content; update changes
      recipient/type/content only (author and `created_at` byte-stable); delete removes
      the record; success messages localized.
- [ ] T012 [US2] Wire the flows in `resources/js/ruby/notices.js`: the issue button and
      modal open (blade `@can('manage-notices')`), submit with 422 field errors in the
      active language, edit prefill/submit, bootbox-confirmed delete with toast and
      `dataTable.draw()` (contacts.js pattern).
- [ ] T013 [US2] Tests `tests/Feature/Ruby/NoticeLifecycleTest.php`: issuing stores the
      signed-in contact as author; contactless users are refused; edit leaves author
      and `created_at` unchanged; confirmed delete removes the record; write POSTs
      return 403 without `manage-notices` (list stays 200); validation errors localize
      under an Arabic session.

**Checkpoint**: issuing works end-to-end; view-only surfaces unchanged.

## Phase 5: User Story 3 — Notices Anchor the Permanent Record (P3)

**Goal**: a member holding notices (as recipient or author) is refused permanent removal —
the existing retention rule goes live.

**Independent Test**: issue a notice to a member, attempt permanent removal, confirm it
is refused with the established feedback, deactivation still offered, the record listed.

- [ ] T014 [US3] Retention regression in
      `tests/Feature/Ruby/NoticeLifecycleTest.php` (or a dedicated
      `tests/Feature/Ruby/NoticeRetentionTest.php`): a member with a notice as
      recipient refuses permanent removal (deactivates instead); likewise a member
      holding one as author; a member with no notices remains fully removable. No
      production code change is expected — this exercises the existing
      `ContactController::hasHistory` branch.

**Checkpoint**: the record trail's guarantees are locked by tests.

## Phase 6: Polish & Finalization

- [ ] T015 Run `./vendor/bin/pint` over touched PHP files; full `php artisan test`
      green; `npm run development` emits the new bundle cleanly.
- [ ] T016 Manual/e2e pass per `specs/004-staff-notices/quickstart.md` on the tenant
      domain (Orca browser): full trail + filters, issue/edit/delete, retention, EN +
      Arabic/RTL walk (mirrored layout, localized strings, mixed-direction content
      readable, permission surfaces), payroll link read-only.
- [ ] T017 Update the VitePress docs on the `docs` branch: Ruby module page note for
      the notices surface (records, permission model, filters) — constitution
      documentation gate.
- [ ] T018 Mark `specs/004-staff-notices/spec.md` **Status: Implemented** and check off
      this task list.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: no dependencies — T001/T002/T003 run in parallel.
- **Foundational (Phase 2)**: after Phase 1 — blocks all user stories.
- **User Stories**: US1 after Phase 2; US2 after Phase 2 (integrates with US1's page
  surface, independently testable); US3 after US2 (needs records to exist).
- **Polish (Phase 6)**: after all stories.

### Parallel Opportunities

- Phase 1: all three tasks (seeder / catalogs / model — different files).
- Phase 3: T006 (controller) parallel with T007+T008 (view then bundle).
- Phase 4: T010 (modal blade) parallel with T011 (controller), T012 after both.

## Implementation Strategy

**MVP = Phase 3 (US1) alone** — the record trail is viewable and filterable, the dead
navbar link goes live. US2 adds issuing/management; US3 locks the retention guarantee;
Phase 6 closes out.

## Notes

- No migration tasks by design: `r_notices` is consumed as-is (Principle V).
- Deployment includes a tenant re-seed so `manage-notices` reaches the HR roles
  (`php artisan module:tenants-seed --tenants=<key>`).
- Reuse inventory (research.md): `vue-datatable`, `dt-filter` (text/select/select2/date),
  `vue-form`, `vue-field`, bootbox confirm, toast, `@can` blade guard,
  `DataTablesHelper` filters — no generic component changes needed.
