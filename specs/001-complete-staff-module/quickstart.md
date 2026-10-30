# Quickstart: Complete Staff Module

**Feature**: 001-complete-staff-module | **Date**: 2026-10-30

## Environment

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
# configure DB, then:
php artisan migrate --seed
npm run development
php artisan serve
```

Sign in through Ruby (`/r/login`) with a seeded HR user (username/password from
`Modules/Ruby/Database/Seeders/`), then open **Staff** (`/r` → Staff).

## Manual verification flow

1. **Create (US1)**: Staff → New → fill Contact tab (name, 2 emails — mark one
   default, phone, address), Employment tab (job, roles, departments),
   Applicant tab → "New applicant" inline (nationality, degree, degree date,
   recruitment date, upload a document), User tab (username, password ×2) → Save.
   Row appears with default email/phone, role, departments, recruitment date.
2. **Negative (US1)**: submit without name → field errors, nothing saved.
   Duplicate username → username field error, **no** staff row created.
3. **Edit (US2)**: row action ✎ → modal prefilled (incl. documents and account
   username, password fields empty) → change job + default phone → Save →
   list/profile reflect changes without reload.
4. **Remove (US3)**: delete a member with attendance history → confirm → row gone;
   filter "Inactive" shows them; Reactivate restores them (and their login).
   Delete a freshly created member (no history) → gone permanently.
   Remove a department manager → department intact, managerless.
5. **Login (US4)**: sign out, sign in as the member created in step 1 → reaches
   Ruby. Deactivated member's login is rejected.
6. **Arabic**: switch locale → labels/messages/values render in Arabic.

## Automated tests

```bash
# phpunit.xml: enable the commented sqlite in-memory lines first
php artisan test --filter=Contact    # ContactCreateTest, ContactUpdateTest,
                                     # ContactRemovalTest, AccountContractTest
```
