# E2E Findings: Complete Staff Module

**Tested on**: branch `002-attendance-punctuality-export` (includes all of spec 001 + its fixes, per testing instruction)
**Date**: 2026-11-14/15 · **Surface**: web (tenant `maxwell` @ `https://maxwell.oaters.docker/r`) via Orca browser + API
**Tester**: local agent (OpenCode) — see `specs/E2E-HANDOFF.md` for environment and how to continue

> Spec 001 could not be browser-tested on its own branch: `/r/contacts` renders ~80 MB of HTML
> (787,588 `<option>` tags — unscoped selects) and wedges Chromium; `/r/contacts/structure` 500s.
> Both are fixed only on branch 002 (commit `20195b7`). All results below are from branch 002.

## Verdict summary

| Area | Result |
| --- | --- |
| US1 Create (browser) | ❌ **P1 broken** — 500 on every save with a role selected |
| US2 Edit (browser) | ❌ **P1 broken** (same root cause) + P2 for legacy roles |
| US3 Deactivate / Reactivate / Permanent delete / Manager removal | ✅ PASS (with one P2 gap: Inactive filter is dead) |
| US4 Member login / suspended login rejected | ✅ PASS |
| Arabic | ⚠️ Not verifiable in web UI — **no locale switcher exists anywhere** (strings exist, unreachable) |
| Automated feature tests | ✅ 48 passed / 223 assertions (mask the UI bugs — see F-001) |

## Findings

### F-001 (P1) — Staff create & update always 500 with a role selected (role IDs sent as strings)

- **Where**: `Modules/Ruby/App/Http/Requests/ContactRequest.php::handle()` → `$contact->syncRoles($this->roles)`
  and `AccountManager::provision()/update()` (same `$this->roles`).
- **Evidence**: real form POST `/r/contacts/create` (roles bound correctly, `roles[]=3` in FormData) →
  `500 RoleDoesNotExist: There is no role named '3' for guard 'web'` (spatie). Same on `/r/contacts/update`
  (`There is no role named '5'`).
- **Root cause**: HTML forms always serialize `roles[]` as **strings**. spatie/laravel-permission
  `HasRoles::getStoredRole()` resolves by ID only when `is_int($role)`; a numeric string is looked up
  **by name** and throws. The feature tests post PHP int arrays (`StaffTestCase::memberPayload()`:
  `'roles' => [$this->hrRole()->id]`), which is why `ContactCreateTest` etc. pass while the browser fails.
- **Fix suggestion**: cast in `handle()`: `$roles = array_map('intval', $this->roles);` and pass that to
  `syncRoles`/`AccountManager` (provision/update docs say `$roleNames` but actually receive IDs today —
  align names/usage). Regression-test with a string-typed payload.

### F-002 (P2) — "Inactive" staff filter is dead wiring; deactivated members are unreachable in the UI

- **Where**: `Modules/Ruby/resources/views/contacts.blade.php` (`<dt-filter name="status" ...>`),
  `resources/components/datatable/datafilter.vue` (`column('status:name').search()`), and
  `ContactController::postIndex()` (`request('status') == 'inactive'`).
- **Evidence**: selecting "Inactive" in the filter fires a table redraw whose payload contains **no**
  `status` param and no `status` column (table has no such column → `column('status:name')` never maps).
  Deactivated members never appear; Reactivate UI is therefore unreachable. The restore **endpoint**
  works (`POST /r/contacts/restore {id}` → 200, row restored, account `active=1`).
- **Fix suggestion**: add a hidden `status` column to the table and read it via
  `DataTablesHelper::getColumns()` in `postIndex`, or append `status` to the DataTables `ajaxData`
  from the filter component.

### F-003 (P2, data-dependent) — Members whose role is not one of the 8 offered roles cannot be edited

- Seeded member with legacy role `supplier` (Buck Kirlin DDS): the edit modal's role select can't
  represent his role → payload has no `roles[]` → 422 "The roles field is required." (also surfaced
  `applicant_id` empty on the same row, which may be the same stale-binding symptom — re-verify after F-001 fix).
- **Fix suggestion**: include the member's current roles among the select options on edit (server
  already has them), or relax `roles` on update when unchanged.

### F-004 (P2, cross-spec) — Deactivating a member with punch history breaks the whole Attendance page

See `specs/002-attendance-punctuality-export/e2e-findings.md` F-101. This pairs spec 001 US3 with
spec 002 US1 and must be fixed together (contact relation lacks `withTrashed`, punches of deactivated
members remain → `contact: null` → client renderer throws → table stuck "Loading…" for every user).

### F-005 (P3) — Dead route `/r/logout`

`Route::get('logout', 'ModuleController@getLogout')` references a method that does not exist
(`ModuleController` only has `getLogin`/`postLogin`). The navbar uses `s/logout`. Remove the dead route
or implement the method.

### F-006 (P3) — Silent failure UX on all staff form saves

`resources/js/ruby/contacts.js::submit()` does `.catch(() => {})`: 500s and 422s produce no toast, no
console-visible error for the user, and stale modals stay open (they later cover the page and block
filters — observed live). Note also: the create/update form carries **no `_token` input**; it relies on
the `X-CSRF-TOKEN` meta header.

### Note — Arabic (spec 001 step 6)

`app()->getLocale()` is always `en`: **no locale-switching mechanism exists in the web app** (no
middleware/route/UI; only tests call `app()->setLocale('ar')`). Arabic catalogs exist
(`Modules/Ruby/resources/lang/ar/*` + `s_role_locales`/`r_department_locales` carry Arabic rows) but
cannot be reached by a real user. If Arabic is a product requirement, a locale switcher must be built
first (spec 002's Arabic scenarios are blocked the same way).

## What passed (verified in browser/DB, do not re-test)

- Staff page renders on 002 (94 KB; was 80 MB on branch 001). Structure page 200 (was 500).
- Create modal: complete 3-tab UI (User Account / Contact Details incl. employment fields / Applicant),
  inline "New Applicant" (nationality/degree/degree-year/recruited-at), employment-document upload
  (title + file), +Add Email/Phone/Address, default email/phone radio logic, select2 role picker.
  422 validation with visible per-field errors (e.g. "The roles field is required.").
- Edit modal prefills correctly (name/job/username, 5 emails + 5 phones with defaults, applicant data).
- Deactivate (member WITH history): confirm dialog → soft delete (`deleted_at` set), linked account
  `active=0`, toast "Staff member deactivated. Their account access is suspended." Row disappears.
- Reactivate endpoint: restores row + account (`active=1`). Toast message verified via API.
- Permanent delete (member WITHOUT history — DB-fixture contact with recruited applicant): hard-deleted
  from `lc_contacts` **and** `r_applicants` cascade after UI confirm.
- Department-manager removal: deactivate a manager → `r_subordinates` links removed, department row
  intact (by design, memberships/shifts/manager links are not restored on reactivate).
- US4: linked active member (`simonis.litzy`/`test123`, role employee) logs into `/r/login` → reaches
  `/r`; suspended member (`kris.assunta`) rejected with "Wrong username and / or password submitted!".
  Ruby access requires `can('ruby')` — e.g. legacy `supplier` accounts are refused (message is generic
  by design).
- Feature suite: `php artisan test` → **48 passed, 223 assertions** (uses `.env.testing` → DB `oaters_test`).
