# Data Model: Language Switcher (EN/AR + RTL)

**Feature**: 003-language-switcher | **Date**: 2026-11-19

Principle V (Schema as Contract): exactly one migration, owned by the module that owns the
table. Per clarification (2026-11-19), the durable preference lives on the **platform account**
— every account has a contact, not every contact has an account.

## New migration

### Sapphire — language preference on accounts

- **Module**: Sapphire (`Modules/Sapphire/Database/migrations/`)
- **Change**: add `locale enum('en','ar') not null default 'en'` to `s_users`; cast on
  `Modules\Sapphire\App\Models\User`.
- **Access rule**: no other module touches the column. The Common mechanism reads/writes it
  **through the account boundary only** (see `contracts/account-locale.md`), mirroring the
  established `AccountManager` pattern (`username()` precedent).

## No other schema changes

| Need | Satisfied by |
|------|--------------|
| Session working copy | existing sessions store (`session('locale')`) — no schema |
| Page direction / language attributes | runtime values derived from the active locale |
| Localized record data | existing locale tables (`s_role_locales`, `r_department_locales`, `r_degree_locales`, city locales) — consumed, not modified |
| Bilingual UI strings | existing `*/lang/ar` catalogs + new keys for this feature's own strings |

## Request-time resolution (per FR-006, Q1=C + Q5)

```text
request → SetAppLocale middleware (after StartSession)
  ├─ session has 'locale' → App::setLocale(session value)          # ordinary request: no account read
  └─ session has none, user signed in →
        account locale (via AccountManager, contact-id keyed) →
        App::setLocale + session(['locale' => value])              # one-time per session
  (anonymous request → default 'en')
switch action → POST /locale {locale} → session(['locale' => …]) + AccountManager::setLocale(…)
```

Cross-session (Q5=A): a live session keeps its working value; other signed-in sessions adopt
the stored preference at their next sign-in (their session already has a value).

## Derived values

| Value | Source |
|-------|--------|
| `<html lang>` | active locale (`en` / `ar`) |
| `<html dir>` | `rtl` when locale is `ar`, else `ltr` |
| RTL stylesheet URL | LTR asset URL with `.rtl.css` suffix, loaded only when `dir=rtl` |
| Client-side globals | `locale.lang`, `locale.dir`, `locale.switchUrl` (blade-injected) |
