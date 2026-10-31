# Contract: Staff Account Management (Ruby → Sapphire)

**Feature**: 001-complete-staff-module | **Date**: 2026-10-30
**Owner**: Sapphire (implementation) / Ruby (consumer)

Constitution Principles I & IV: user accounts belong to Sapphire; Ruby staff flows may
provision accounts **only** through this contract. Ruby never reads/writes `s_users`.

## Interface

`Modules\Sapphire\App\Services\AccountManager` (class, invoked in-process — same
monolith):

```php
AccountManager::provision(int $contactId, string $username, ?string $password,
                          array $roleNames): void        // create + link
AccountManager::update(int $contactId, string $username, ?string $password,
                       array $roleNames): void           // password null = keep
AccountManager::suspend(int $contactId): void
AccountManager::restore(int $contactId): void
AccountManager::delete(int $contactId): void             // permanent removal
```

## Semantics

| Rule | Detail |
|------|--------|
| Provision | Creates `s_users` row (hashed password), sets `lc_contacts.user_id`, assigns the given role names to the **user** (module access layer). Throws `AccountException` on duplicate username. |
| Update | Username change allowed (uniqueness checked); null password keeps the current hash; roles synced. |
| Suspend | Sets `active = false`. Auth guard (`attempt` / middleware) MUST reject inactive users. |
| Restore | Sets `active = true`. |
| Delete | Deletes the `s_users` row (and its role assignments); called by Ruby's force-removal branch. **Required**: `lc_contacts.user_id`'s FK is `on delete set null`, so deleting a contact cascades nothing to the user — without this call an active orphaned login survives the staff record. |
| Atomicity | Caller (Ruby) wraps the whole staff save in a DB transaction; `AccountException` aborts it → **all-or-nothing** per FR-010 (no staff record persists, form data preserved client-side). |
| Credentials | Password handling (hashing, never returning values) is entirely Sapphire-side. Ruby validates only shape (min length, confirmation match). |

## Failure modes surfaced to the UI

- Duplicate username → field-level error on the username field, nothing persisted.
- Unexpected contract failure → generic "account could not be provisioned" error,
  nothing persisted (US4 acceptance scenario 3).

## Open integration note

Contact roles (HR positions shown in the staff UI) remain assigned directly by Ruby to
the contact (`s_model_has_roles`, model = Contact) — the existing pattern. The contract
carries the same role names to the user layer so `can('ruby')` style access checks work
at login. If Sapphire later needs a different mapping (subscription-driven), only the
contract implementation changes.
