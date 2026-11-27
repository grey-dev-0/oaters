# Quickstart: Language Switcher (EN/AR + RTL)

**Feature**: 003-language-switcher | **Date**: 2026-11-19

## Environment

```bash
composer install
npm install            # adds rtlcss (devDependency)
npm run development    # build now also emits *.rtl.css siblings
php artisan module:tenants-migrate --tenants=<tenant>   # applies s_users.locale
php artisan serve      # or the LEMP stack
```

Sign in at `/r/login` (`<hr-user>/test123`), then use the language control in the navbar —
directly left of the username dropdown.

## Manual verification flow

1. **Switch (US1)**: on Staff, open the language control → Arabic. Navbar labels, breadcrumb,
   card title, table headers, filters, "New Member" button all render Arabic; one row's
   department/role names render Arabic (localized data).
2. **Persistence (US3)**: navigate Attendance → still Arabic. Sign out → login page is English
   (Q3=B); sign back in → Arabic immediately (account-stored preference).
3. **RTL (US2)**: layout mirrors — brand right, menus flow RTL, control before the username
   dropdown at the mirrored end. Walk: staff filters + table (sort/pagination chrome in Arabic),
   create/edit modal (labels/inputs/errors RTL, tabs read RTL), attendance (settings modal,
   date picker opens sensibly), departments, structure (diagram container mirrored, drawing
   LTR per Q2=A). No clipping/overlap/dead controls.
4. **Exports (FR-007)**: with Arabic active, export attendance CSV + XLSX → headers and
   punctuality readings in Arabic; file opens correctly.
5. **Fallback (FR-003)**: a record lacking an Arabic value shows its English value.
6. **English regression (SC-004)**: switch back → byte-identical familiar layout and strings.
7. **Session semantics (Q5=A)**: second browser signed in switches to English → the first
   browser keeps Arabic (no propagation); after re-login it adopts the stored value.

## Automated tests

```bash
php artisan test --filter=Locale    # LocaleMiddlewareTest, LocaleSwitchTest, LocaleExportTest
php artisan test                    # full suite green (incl. previous specs)
```
