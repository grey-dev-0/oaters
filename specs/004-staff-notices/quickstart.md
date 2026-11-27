# Quickstart: Staff Notices Page

**Feature**: 004-staff-notices | **Date**: 2026-11-27

## Prerequisites

```bash
composer install && npm install && npm run development
php artisan module:tenants-migrate --tenants=<key>   # no-op for schema; keeps parity
php artisan module:tenants-seed --tenants=<key>      # grants manage-notices to HR roles
```

Sign in at `/r/login` with an HR-manager account (issuing rights); a non-issuing module
account (e.g. team-lead) exercises the view-only surface.

## Manual verification flow

1. **Trail (US1)**: open Personnel → Notices (the formerly dead navbar link). Rows render
   recipient, author, type, content, issue date — newest first. Filter by type alone, by
   recipient alone, by author alone, by date range, then combined; "Clear Filters" restores
   the full trail. A record concerning a deactivated member stays visible.
2. **Issue (US2)**: with an HR-manager account press the issue control → pick a member,
   type and content → save. The row appears immediately with you as author. Try an empty
   submit → field errors in the active language. As a view-only user the issuing controls
   are absent and direct POSTs are refused.
3. **Edit (US2)**: edit a record's member/type/content → author and issue date unchanged.
4. **Delete (US2)**: delete demands a confirmation; the record disappears.
5. **Retention (US3)**: issue a notice to a member, then attempt permanent removal of that
   member — refused, deactivation offered, the record remains listed.
6. **Bilingual/RTL**: switch to العربية — the whole page mirrors and renders Arabic
   (labels, type colors, empty state, validation errors); mixed-direction content stays
   readable. Switch back — English is unchanged.
7. **Payroll link**: a record anchored to a payroll payment shows the link read-only; the
   form never offers to set it.

## Automated tests

```bash
php artisan test tests/Feature/Ruby/NoticePageTest.php       # list, filters, gating
php artisan test tests/Feature/Ruby/NoticeLifecycleTest.php  # issue/edit/delete/retention
php artisan test                                             # full suite green
```
