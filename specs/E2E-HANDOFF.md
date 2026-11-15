# E2E Handoff — oaters specs 001 & 002 (remote agent runbook)

**Purpose**: everything a remote cloud agent (Orca server, same setup as the local machine) needs to
continue e2e testing and implement fixes for `001-complete-staff-module` and
`002-attendance-punctuality-export` on branch `002-attendance-punctuality-export`.

- Detailed findings: `specs/001-complete-staff-module/e2e-findings.md` (F-001..F-006),
  `specs/002-attendance-punctuality-export/e2e-findings.md` (F-101..F-104).
- **Read both before testing again** — they list what already PASSED and should not be repeated.

## Fix backlog (priority order)

| ID | Sev | Fix | Files |
| --- | --- | --- | --- |
| F-001 | P1 | Cast role IDs to int before spatie sync/assign (or send names) | `Modules/Ruby/App/Http/Requests/ContactRequest.php` (`handle()`), check `Modules/Sapphire/App/Services/AccountManager.php` |
| F-101 | P1 | Trashed-contact punches crash Attendance UI — `withTrashed` on the eager load or exclude them; null-guard `row.contact` in JS | `Modules/Ruby/App/Models/Punch.php`, `Modules/Ruby/App/Http/Controllers/AttendanceController.php::attendanceQuery()`, `resources/js/ruby/attendance.js` (`renderDepartments`, `renderActions`) |
| F-102 | P1 | Register the Form bundle/components on the attendance page so the settings modal renders its input | `resources/js/ruby/attendance.js` (compare `resources/js/ruby/contacts.js`) |
| F-002 | P2 | Wire the staff "Inactive" filter to `request('status')` (hidden status column or ajaxData) | `Modules/Ruby/resources/views/contacts.blade.php`, `ContactController::postIndex()`, `resources/components/datatable/*` |
| F-103 | P2 | Add orderColumn mapping for `r_punches.latency` | `AttendanceController::postIndex()` |
| F-003 | P2 | Offer the member's current roles in the edit select (legacy roles like `supplier` otherwise make edits 422) | `ContactController::getIndex()/getContact()` + contact-form blade |
| F-006 | P3 | Surface save errors (toast/inline) instead of `.catch(() => {})`; close or keep modal deterministically | `resources/js/ruby/contacts.js::submit()` |
| F-005 | P3 | Remove dead `/r/logout` route (or implement `getLogout`) | `Modules/Ruby/routes/routes.php`, `ModuleController.php` |
| F-104 | P3 | Guard missing column keys in `filterByDate()` | `app/Services/DataTablesHelper.php` |
| — | — | Decide: web locale switcher (Arabic UI is currently unreachable) | product decision + new middleware/route if approved |

After JS/PHP changes: `npm run development` (rebuild `public/build`) and hard-reload the page
(each page load resets the Orca browser's device/viewport emulation too, if used).

## Environment (mirror of local)

- **Repo**: `theultragrey/oaters`, work on branch `002-attendance-punctuality-export`
  (already contains 001 + fixes `20195b7`, `9a8f451`). Vendor matches (incl. `openspout/openspout`)
  — `composer install` only if you switch branches.
- **Stack**: docker LEMP (`lemp-nginx-1`, `lemp-php82-1`, `lemp-mariadb-1`, `lemp-dns-1`).
  Project is bind-mounted: host `<repo>` → container `/home/projects/theultragrey/oaters`.
  Run artisan inside PHP container:
  `docker exec -w /home/projects/theultragrey/oaters lemp-php82-1 php artisan ...`
- **URLs**: central `https://oaters.docker`, tenant **`https://maxwell.oaters.docker`**
  (nginx server_name, mkcert cert covering `*.oaters.docker`, regenerated 2026-11-14, valid →
  2029-02; root CA at `~/.local/share/mkcert/rootCA.pem`). If the browser shows
  `ERR_CERT_DATE_INVALID`/`ERR_CERT_AUTHORITY_INVALID`: reissue inside the dns container
  (`docker exec -w /root/.local/share/mkcert lemp-dns-1 mkcert oaters.docker "*.oaters.docker"`,
  then `docker exec lemp-nginx-1 nginx -s reload`) and make sure the root CA is trusted by the
  browser's NSS store (`certutil -d sql:$HOME/.pki/nssdb -A -t "C,," -n mkcert -i ~/.local/share/mkcert/rootCA.pem`).
- **Tenancy**: stancl/tenancy; tenant **maxwell = id 2**, hostname-resolved, DB **`maxwell`**
  (prefix-less table names: `lc_*` common, `r_*` ruby, `s_*` sapphire). Central DB: `oaters`;
  tests use `oaters_test` (`.env.testing`, root/123456, host `mariadb`).
- **Storage permissions** (the #1 cause of confusing 500s — Monolog can't write): if logs show
  "could not be opened in append mode: Permission denied", run
  `docker exec -u root lemp-php82-1 chmod -R a+rwX /home/projects/theultragrey/oaters/storage /home/projects/theultragrey/oaters/bootstrap/cache`.
- **Assets**: `npm run development` (Vite dev build → `public/build`). No dev server; nginx serves builds.
- **Feature tests**: `docker exec -w /home/projects/theultragrey/oaters lemp-php82-1 php artisan test`
  (baseline: 48 passed / 223 assertions as of 2026-11-15).

## Accounts (tenant maxwell; all passwords `test123`)

| Username | Role | Use |
| --- | --- | --- |
| `tom78` (Geraldine Hammes I, contact 3) | hr-manager | primary tester (has `ruby` + `manage-attendance-settings`) |
| `hansen.maybell` | general-manager | privileged settings editor |
| `milford17` | hr-assistant | privileged settings editor |
| `simonis.litzy` | employee | negative-permission tests (view attendance, 403 on settings) |
| `nigel71` (Buck Kirlin DDS, contact 9) | legacy `supplier` | login refused (`can('ruby')` false), edit-form edge case F-003 |
| `kris.assunta` (Dr. Kaycee Dooley, contact 4) | director | was deactivated + restored during tests (see state) |
| central: `george@maxwell.com` / `test123` | tenant owner | Sapphire central admin |

Login: `https://maxwell.oaters.docker/r/login` (username + password). Logout is `s/logout`
(POST-free GET). Sessions are file-based — they survive container restarts but not branch switches
of session config; just re-login when bounced.

## Database state left by the local run (before fixes)

- `lc_contacts`: 25 rows, **no test members remain** (fixture "Aaa E2E Fixture" was hard-deleted).
- Contact 15 (Prof. Marcel Lind, user `rippin.vidal`): was deactivated then **restored** — fully back
  (deleted_at NULL, account active).
- Contact 4 (Dr. Kaycee Dooley, user `kris.assunta`): deactivated then **restored**, BUT by design her
  department memberships, shift assignments and `r_subordinates` manager links (managed dept 6) were
  deleted on deactivation and NOT restored — department 6 itself still exists. Expect "Unassigned"
  departments on her rows. Re-link via the UI if needed for org-structure tests.
- `lc_settings`: **empty** (threshold test value was reset; default 15 applies again).
- `r_punches`/`r_applicants`/`s_users`: untouched (960 punches / 24 applicants / seeded users intact).

## Orca-browser mechanics that worked locally (time-savers)

- Tab: `ORCA tab create --url https://maxwell.oaters.docker/r/login --json`, then drive by
  `--page <browserPageId>`; refs (`@e…`) go stale after navigation/redraw — re-snapshot.
- jQuery is module-scoped (not on `window`) — use DOM APIs in `eval`, or real clicks for select2:
  mouse move/down/up on the select2 container, then click the rendered `.select2-results__option`.
- bootbox confirms: find `.modal.show` containing the message and JS-click its OK button.
- API replay needs `X-CSRF-TOKEN` from `<meta name="csrf-token">` plus the session cookie jar;
  DataTables sends multi-select filters as `columns[i][search][value][]=id` (array) — replicate that
  shape or you'll hit server-side array/type errors that the real UI never produces.
- Watch server truth via `storage/logs/laravel-<date>.log` (after fixing storage perms) and
  `docker exec lemp-nginx-1 sh -c 'tail -20 /var/log/nginx/*access*.log'`.

## Remaining test plan (after fixes)

1. **F-001 fixed** → full US1 create via UI (4 tabs, 2 emails w/ default, applicant + document,
   username/password) → row appears with default email/phone/role/recruited date; duplicate username →
   username-taken field error, no row; US2 edit (job + phone) persists without reload; US4 with the
   newly created member (login OK → deactivate → login rejected).
2. **F-101 fixed** → deactivate a punched member → attendance page still renders (their punches show
   or are excluded per chosen fix) → reactivate.
3. **F-102 fixed** → settings modal: input shows 15 (default), change to 5 → toast → table reload
   flips 6-min-late rows blue→red (boundary: 5 blue, 6 red); invalid entries show messages and keep
   stored value; employee sees no gear (or 403).
4. **F-103 fixed** → latency asc/desc ordering correct; export of a sorted+filtered view mirrors the
   order (FR-007).
5. Regression sweep: staff table filters (name/role/departments/date), departments & structure pages,
   profile modal, export buttons on both formats, `php artisan test`.
6. Optional: seed ~5k punches and time a full export (SC-003 < 10 s); Arabic if a locale switcher
   lands.
