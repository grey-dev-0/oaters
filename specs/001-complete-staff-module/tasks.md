# Tasks: Complete Staff Module

**Input**: Design documents from `/specs/001-complete-staff-module/`

**Prerequisites**: plan.md (required), spec.md (required for user stories),
research.md, data-model.md, contracts/account-management.md

**Tests**: Included per the project constitution (Development Workflow): new backend
behavior ships with PHPUnit tests; bug fixes include regression tests.

**Organization**: Tasks are grouped by user story (spec.md) so each story can be
implemented, tested and delivered independently. `[P]` = parallelizable (no file
dependencies with siblings in the same phase).

---

## Phase 1: Setup (Shared Infrastructure)

- [x] T001 [P] Enable sqlite in-memory test DB in `phpunit.xml` (uncomment the
      `DB_CONNECTION`/`DB_DATABASE` env lines) and verify `php artisan test` runs
      module migrations (adjust `tests/TestCase.php` only if needed).
- [x] T002 [P] Common migration `Modules/Common/Database/migrations/`: add
      `softDeletes()` to `lc_contacts`; add `SoftDeletes` trait to
      `Modules/Common/App/Models/Contact.php` (verify `activeRecruit` list still
      excludes nothing unintended — soft-delete scope composes automatically).
- [x] T003 [P] Sapphire migration `Modules/Sapphire/Database/migrations/`: add
      `active boolean default true` to `s_users`; cast in
      `Modules/Sapphire/App/Models/User.php`.
- [x] T004 [P] Add EN/AR lang keys for all new UI strings:
      `Modules/Ruby/resources/lang/{en,ar}/contacts.php` (form groups, defaults,
      applicant picker/inline, inactive filter, reactivate, confirm messages,
      toasts) — reuse existing `applicants.php`/`words.php` keys where present.

## Phase 2: Foundational (Blocking Prerequisites)

**⚠️ No user story work can begin until this phase is complete.**

- [x] T005 Create `Modules/Sapphire/App/Services/AccountManager.php` per
      `contracts/account-management.md`: `provision/update/suspend/restore`,
      username uniqueness (`AccountException`), password hashing, role assignment,
      `lc_contacts.user_id` linking. Sapphire-owned; no Ruby code here.
- [x] T006 Enforce suspension at the auth guard: reject inactive users in
      `Modules/Ruby/App/Http/Controllers/ModuleController.php@postLogin`
      (and the Sapphire login path if separate) with the existing failure response.
- [x] T007 Extend `ContactController@getIndex` (`Modules/Ruby/App/Http/Controllers/ContactController.php`)
      to also pass localized degrees (`r_degrees`) and nationalities (`lc_countries`)
      for the inline applicant pane.
- [x] T008 Add routes in `Modules/Ruby/routes/routes.php` (inside the existing
      `auth + can:ruby` contacts group): `POST contacts/create`,
      `POST contacts/update`, `POST contacts/delete`, `POST contacts/restore`,
      `POST contacts/applicants` (picker search) — names `contacts.*`.

**Checkpoint**: migrations + contract + routes ready; user story work can proceed.

## Phase 3: User Story 1 — Add a New Staff Member (P1) 🎯 MVP

- [x] T009 Create `Modules/Ruby/App/Http/Requests/ContactRequest.php` (pattern:
      `DepartmentRequest`): rules for name/job/`emails[]`/`phones[]`/`addresses[]`
      (+ single default each), `image` file, `roles[]`, `member_departments[]`,
      `managed_departments[]`, applicant (`applicant_id` XOR inline
      `nationality/degree/degree_date/recruited_at/documents[]`) and account
      (`username`, `password`+confirmation, required on create);
      update-mode rules (`id` exists) per route name.
- [x] T010 Implement `ContactRequest::handle()` create path inside
      `DB::transaction`: contact → emails/phones/addresses (normalize exactly one
      default) → contact roles → `r_subordinates` (member + managed) →
      applicant row (`recruited_at` set) → documents stored to `r_documents/`
      disk → `AccountManager::provision(...)`; catch `AccountException` →
      validation error, transaction rolls back (all-or-nothing, FR-010).
- [x] T011 Implement `ContactController@postApplicants` picker search: contacts
      having `r_applicants` with `recruited_at NULL`, not soft-deleted, name-like
      filter, exclude already-linked (structural — see data-model.md); JSON
      id/text pairs (pattern: existing `postSearch`).
- [x] T012 Complete the Contact pane in
      `Modules/Ruby/resources/views/components/modals/contact-form.blade.php`
      (replaces `TBD`): name, job, dynamic emails/phones/addresses rows
      (add/remove + default selection), city select2 for addresses — generic
      `vue-field`/select2 only, business markup stays in Blade.
- [x] T013 Complete the Applicant pane (replaces `TBD 2`): toggle between
      "existing applicant" (autocomplete/select2 fed by `contacts.applicants`)
      and "new applicant" inline fields (nationality select, degree select2,
      degree date, recruited_at daterange, documents multi-file with editable
      titles); when an existing applicant is picked, show its (editable)
      recruitment details.
- [x] T014 Add profile image `file` field to the User pane; store under the
      existing contact image convention (`lc_contacts.image`), wire into avatar.
- [x] T015 Wire `resources/js/ruby/contacts.js`: `addContact` → modal →
      `submit('createContact')` (pattern: `departments.js` submit: form.submit →
      toast → `dataTable.draw()`), dynamic collection field management, applicant
      mode toggle, file inputs, error display per field.
- [x] T016 Tests `tests/Feature/Ruby/ContactCreateTest.php`: happy path persists
      contact + collections + applicant + account + documents; missing required
      field rejects (nothing persisted); duplicate username aborts all (no
      `lc_contacts` row); inline vs picked applicant paths; two defaults sent →
      handler normalizes to one.

## Phase 4: User Story 2 — Edit an Existing Staff Member (P2)

- [x] T017 Extend `ContactController@getContact` to return editable payload:
      all collections (not only defaults), roles ids, member/managed department
      ids, applicant fields + documents, account `username` (never password).
- [x] T018 Implement `ContactRequest::handle()` update path: sync collections
      (add/update/remove by id), re-normalize defaults, sync roles +
      `r_subordinates`, update applicant (degree/dates) + documents
      (upload/rename/delete), optional image replace, `AccountManager::update`
      with null password = keep current; all inside one transaction.
- [x] T019 Wire edit in `resources/js/ruby/contacts.js` + row action: ✎ opens
      the create modal in edit mode prefilled via `form.reset({...})` (pattern:
      `departments.js editDepartment`); reuse `contacts.blade.php` edit modal
      instance of `contact-form` (`edit` prop already supported).
- [x] T020 Tests `tests/Feature/Ruby/ContactUpdateTest.php`: prefill payload
      shape; edit persists changes atomically; empty password keeps hash;
      collection sync (remove email, change default, rename document title);
      username change enforces uniqueness.

## Phase 5: User Story 3 — Remove / Reactivate a Staff Member (P3)

- [x] T021 Implement `ContactController@postDelete` per data-model.md history
      check (`r_punches`, `r_leaves`, `r_documents`, `r_notices`,
      `r_salaries`/`r_payroll_payments`): with history → `delete()` (soft) +
      `AccountManager::suspend`; without → `forceDelete()` (documents removed
      from disk) + `AccountManager::delete` (the `user_id` FK does not cascade
      from the contact side — without this an orphaned login survives); both
      detach `r_subordinates` (memberships + management) and `r_contact_shifts`;
      departments untouched; distinct toast per mode.
- [x] T022 Implement `ContactController@postRestore`: `restore()` +
      `AccountManager::restore` (memberships intentionally NOT reattached —
      documented behavior); list default scope excludes deactivated members.
- [x] T023 Add the inactive-members filter (`dt-filter` on
      `contacts.blade.php`, `withTrashed` branch in `postIndex` datatable) and
      Reactivate row action for trashed rows (Blade actions view + JS handler).
- [x] T024 Wire delete confirm (bootbox) + reactivate in
      `resources/js/ruby/contacts.js` on the existing inert icons in
      `Modules/Ruby/resources/views/actions/contacts.blade.php`; cancel aborts.
- [x] T025 Tests `tests/Feature/Ruby/ContactRemovalTest.php`: member with
      attendance → soft (row has `deleted_at`, account suspended, login
      rejected); member without history → gone from DB; manager removal leaves
      department intact managerless (no `r_subordinates` rows remain);
      reactivate restores row + account; inactive filter includes only
      deactivated.

## Phase 6: User Story 4 — Account Handling Hardening (P4)

- [x] T026 Tests `tests/Feature/Ruby/AccountContractTest.php`: provisioned
      member can log in via `/r/login` and passes `can('ruby')`; suspended
      member rejected by guard (T006 regression); `AccountManager` duplicate
      username throws; update keeps password when null.
- [x] T027 Surface duplicate username as a field-level error pre-submit or on
      response (keep UX consistent with FR-005) in the staff form.

## Phase 7: Finalization

- [ ] T28 Run `./vendor/bin/pint` over touched files; full `php artisan test`
      green; manual quickstart.md pass in EN + AR.
      *(pint + suite green 2026-11-01 — 22 tests, 91 assertions; the manual
      EN/AR quickstart pass in a browser is pending the maintainer.)*
- [x] T29 Update VitePress docs on the `docs` branch: Ruby module page gains
      staff CRUD section (constitution documentation gate).
- [x] T30 Mark spec.md **Status: Implemented** and check off this task list.
