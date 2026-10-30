# Data Model: Complete Staff Module

**Feature**: 001-complete-staff-module | **Date**: 2026-10-30

Principle V (Schema as Contract): every schema change ships as a migration inside the
**owning** module. This feature adds exactly two migrations; everything else reuses
existing tables.

## New migrations

### 1. Lava Common — soft deletes on staff contacts

- **Module**: Common (`Modules/Common/Database/migrations/`)
- **Change**: add `deleted_at timestamp nullable` to `lc_contacts`
  (`$table->softDeletes()`); add `SoftDeletes` trait to
  `Modules/Common/App/Models/Contact`.
- **Why**: FR-008 deactivation = Laravel soft delete; permanent removal = force
  delete. Relations referencing contacts use `onDelete` rules already defined.
- **Effects**: default queries exclude deactivated members automatically;
  `withTrashed()` powers the inactive filter; `restore()` powers reactivation.

### 2. Sapphire — account suspension flag

- **Module**: Sapphire (`Modules/Sapphire/Database/migrations/`)
- **Change**: add `active boolean default true` to `s_users`.
- **Why**: FR-008 — deactivation suspends account access; reactivation restores it.
  Auth guard rejects inactive users at login.
- **Note**: Sapphire owns users (constitution Principle I/IV); Ruby never touches
  `s_users` directly.

## No other schema changes

| Need | Satisfied by |
|------|--------------|
| Staff ↔ applicant 1:1 | `r_applicants.id` **is** `lc_contacts.id` (extension table) — exclusivity is structural |
| "Already-linked" applicants | `r_applicants.recruited_at` set ⇒ staff member; picker filters `whereNull('recruited_at')` |
| Contact ↔ account link | existing `lc_contacts.user_id` → `s_users.id` |
| Emails / phones / addresses + defaults | existing `default` booleans |
| Department membership / management | existing `r_subordinates` pivot |
| Employment documents | existing `r_documents` + `r_documents/{filename}` local disk |
| Degrees / nationality | existing `r_degrees` (+ locales) and `lc_countries` |

## Write-path field mapping (create / update payload)

| Form group | Fields | Persistence |
|------------|--------|-------------|
| Contact | `name`, `job`, `image?` (file), `emails[]` + `emails_default`, `phones[]` + `phones_default`, `addresses[]` (city, lines) + default | `lc_contacts` + child rows; exactly one `default=true` per emails/phones enforced in handler |
| Employment | `roles[]` (contact roles), `managed_depart[]` + `member_departments[]` | `s_model_has_roles` (Contact) + `r_subordinates` |
| Applicant | `applicant_id` (existing) **or** inline `{nationality, degree, degree_date, recruited_at, documents[]}` | `r_applicants` (+ `r_documents`) |
| User account | `username`, `password` + confirmation (required on create; optional on edit) | `s_users` via Sapphire contract |

## Removal-mode history check (FR-008)

Member "has history" if **any** of:

- `r_punches` rows (attendance)
- `r_leaves` rows
- `r_documents` rows (via applicant)
- `r_notices` rows (authored or targeted)
- `r_salaries` row exists or any `r_payroll_payments` via salary

Otherwise permanent removal. In both modes `r_subordinates` rows for the member
(memberships **and** management) are detached; `r_contact_shifts` pivot rows are
detached; departments themselves untouched.
