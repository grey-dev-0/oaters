# Contract: Account Locale (Common → Sapphire)

**Feature**: 003-language-switcher | **Date**: 2026-11-19
**Owner**: Sapphire (implementation) / Common (consumer)

Constitution Principles I & V: the language column belongs to Sapphire's account table; the
Common-owned mechanism may only reach it through this boundary — identical stance to the
existing staff-account contract.

## Interface

Extends `Modules\Sapphire\App\Services\AccountManager` (contact-id keyed, like `username()`):

```php
AccountManager::locale(int $contactId): string            // 'en' or 'ar'; 'en' fallback
AccountManager::setLocale(int $contactId, string $locale): void   // 'en'|'ar' only
```

## Semantics

| Rule | Detail |
|------|--------|
| Read | Returns the account's stored language; unknown/missing accounts resolve to `'en'` without throwing (defensive — used by the session-seed path). |
| Write | Accepts exactly `'en'` or `'ar'`; other values are ignored (the switch endpoint validates before calling). |
| Contact resolution | The middleware seeds from `auth()->user()->contact->id` once per session; accounts without a contact (none today) simply never persist. |
| No cross-module SQL | Common never touches `s_users` directly; `locale`/`setLocale` are the only accessors. |

## Consumers

- `Modules\Common\App\Http\Middleware\SetAppLocale` — session-seed read (once per session).
- `Modules\Common\App\Http\Controllers\LocaleController` — persists on switch alongside the
  session write.
