# Research: Complete Staff Module

**Feature**: 001-complete-staff-module | **Date**: 2026-10-30

## What already exists (verified in code)

### Backend
- `ContactController` (`Modules/Ruby/App/Http/Controllers/ContactController.php`):
  search, list (DataTables + filters), profile fetch (`getContact` loads roles, all
  contact info, departments, recruitment details, latest 5 approved leaves, document
  download URLs), structure. **No create/update/delete.**
- Routes (`Modules/Ruby/routes/routes.php`): `contacts` group has index, datatable,
  `{contact}`, search, structure. The form already posts to `r/contacts/create` and
  `r/contacts/update` — **routes don't exist yet** (404s).
- `DepartmentRequest` (`Modules/Ruby/App/Http/Requests/DepartmentRequest.php`) is the
  reference pattern: `rules()` + `handle()` doing create-or-update in one action.
- `DocumentController@getDocument` streams downloads from the `local` disk,
  `r_documents/{filename}`.
- Login: `ModuleController@postLogin` uses `auth()->attempt(['username','password'])`
  then `auth()->user()->can('ruby')`.

### Models & schema (all migrations already exist except two flagged in data-model.md)
- `lc_contacts` (Lava Common): `user_id` nullable FK → `s_users`, `name`, `job`,
  `image`, gender/marital/birthdate, timestamps. **No soft deletes.**
- `lc_phones` / `lc_emails` / `lc_addresses`: `contact_id` FK + `default` boolean
  (addresses also country/city; cities rewiring done).
- `r_applicants` (Ruby): **PK = contact id (1:1 extension table)**, `country_id`
  (nationality, NOT NULL), `degree_id` (NOT NULL), `degree_date`, `tenure`,
  `recruited_at` nullable, timestamps. `activeRecruit` scope = `recruited_at` set →
  this is the staff membership test.
- `r_subordinates`: `manager_id` + `contact_id` + `department_id`, unique
  (manager, contact) — department membership and management both live here.
- `r_documents`: `applicant_id`, `title`, `filename`.
- `s_users` (Sapphire): `username`, `password` (hashed), `image`, timestamps.
  `User` is the Authenticatable; `lc_contacts.user_id` is the link
  (`User->contact()` hasOne).
- **Two role layers** (both via Spatie, shared `s_model_has_roles`):
  contact roles = HR position roles (rendered in staff UI), user roles = module
  access (`can('ruby')`).

### Frontend
- Staff page (`Modules/Ruby/resources/views/contacts.blade.php`): filters
  (name/email/phone/roles/job/departments/recruited date), table
  (`components/tables/contacts.blade.php` — name, default email/phone, role, job,
  departments, recruited_at, actions), profile modal (complete per spec),
  `contact-form` modal skeleton: 3 tabs exist (user / contact / applicant);
  user tab has username/password/confirmation fields; contact + applicant panes
  are `TBD` placeholders.
- Actions (`actions/contacts.blade.php`): profile wired; **edit + delete icons have
  no handlers**.
- `resources/js/ruby/contacts.js`: profile modal flow, `renderVueTemplate` for
  action buttons, tenure computed props. No add/edit/delete logic.
- `resources/js/ruby/departments.js` is the **reference CRUD implementation**:
  `addDepartment` (modal + form reset), `submit` (form.submit → toast →
  `dataTable.draw()`), `editDepartment` (fetch → `form.reset({...})` prefill),
  `showContacts` (nested table in modal).
- Generic components: `vue-form` (`resources/components/form/form.vue`) submits via
  ajax, tracks `hasFiles` (multipart); `vue-field` supports text/number/password/
  email/tel/daterange/**file**/select/select2/checkbox/radio/autocomplete;
  select2 + daterangepicker are the established widgets; `vue-datafilter` for table
  filters; bootbox available for confirms.

## Gaps this feature must close

1. `ContactRequest` (create/update validation + transactional handler).
2. `contacts/create`, `contacts/update`, `contacts/delete`, `contacts/restore`,
   applicant-picker search endpoint.
3. Contact + applicant form panes in Blade; dynamic emails/phones/addresses rows;
   applicant picker vs inline creation; file uploads (profile image, documents).
4. Edit/delete/reactivate row actions + JS wiring; inactive-members filter.
5. Account contract (Ruby → Sapphire): create/update/suspend/restore with username
   uniqueness and all-or-nothing semantics.
6. Soft deletes on `lc_contacts` (deactivation) and active flag on `s_users`
   (account suspension) — owned by Common and Sapphire respectively.
7. PHPUnit feature tests (constitution mandate); test DB currently disabled
   (sqlite lines commented out in `phpunit.xml`).

## Risks / watchpoints

- `r_applicants.country_id` and `degree_id` are NOT NULL — inline applicant
  creation must capture nationality and degree (spec's "minimal" record grows by
  nationality; degree was already listed).
- Removal must clear `r_subordinates` rows for the member (memberships AND
  management) while keeping departments intact; restore does NOT reattach
  memberships (documented behavior).
- The staff list `activeRecruit` scope and the new soft-delete scope must compose
  (deactivated members excluded by default, included via filter).
- Contact roles vs user roles: contract passes the selected HR role names to
  Sapphire; Sapphire owns what the account can actually access.
