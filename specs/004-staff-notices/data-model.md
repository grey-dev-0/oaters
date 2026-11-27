# Data Model: Staff Notices Page

**Feature**: 004-staff-notices | **Date**: 2026-11-27

Principle V (Schema as Contract): **no migration**. The `r_notices` schema is the contract
of record and is consumed exactly as designed (that consumption is the point of the
feature — the schema was built ahead of it).

## Consumed entity: Notice (`r_notices`, Ruby-owned)

| Field | Type | Meaning | Page usage |
|-------|------|---------|-----------|
| `id` | PK | Record identity | row key |
| `author_id` | FK → `lc_contacts`, NOT NULL, cascade | Member who issued the record | auto-set from the signed-in user's contact; rendered in list; author filter |
| `contact_id` | FK → `lc_contacts`, NULL, set-null | Member the record concerns | form input (create/edit); rendered in list; recipient filter |
| `payroll_payment_id` | FK → `r_payroll_payments`, NULL, cascade | Optional payroll payment anchor | read-only display when present |
| `type` | enum `notice` / `warning` / `termination` | Record severity class | form input (create/edit); filter; colored localized label |
| `content` | text | Free-text record body | form input (create/edit); list cell |
| `created_at` | timestamp | Issue date | list order (newest first); date-range filter; immutable on edit |

## Model changes (no schema change)

- `Modules\Ruby\App\Models\Notice`: `author()`, `recipient()`, `payrollPayment()`
  relations; unguarded for form mass-assignment (recipient, type, content only — author
  and timestamps are never accepted from input).

## Validation rules (NoticeRequest)

| Field | Rules | Failure message |
|-------|-------|-----------------|
| `contact_id` | required, integer, exists on active recruited members | localized `notices.validation_*` |
| `type` | required, `in:notice,warning,termination` | localized |
| `content` | required, string | localized |
| author presence (server-side) | signed-in user must have a linked contact | localized "no author profile" error |

The request is authorized by the route's `can:manage-notices` middleware (the
`SettingRequest` pattern).

## State transitions

```text
(nonexistent) ──issue──▶ [record: author, recipient, type, content, issued_at]
      ▲                        │
      │ edit (recipient/type/content only;     │ delete (bootbox-confirmed,
      │ author + issued_at immutable)          │      hard delete — no soft-delete column)
      └──────────── (re-issue is a new record) ◀┘
```

Lifecycle anchors (existing rules, exercised by this feature):

- Member holding notices (author **or** recipient) → permanent removal refused
  (`ContactController::hasHistory`), deactivation offered; records stay listed.
- Recipient hard-removed through any future path → `contact_id` set null → record survives,
  recipient renders as unassigned.
- Payroll payment deleted → anchored records cascade away with their payment (schema
  decision; no page behavior required).

## Derived page values

| Value | Source |
|-------|--------|
| Recipient filter | text search on the recipient's name — all members, including deactivated |
| Author filter | text search on the author's name — all members, including deactivated |
| Issue-form recipient picker | active recruited members (`Contact::activeRecruit()`), select2 |
| Type labels + colors | `ruby::notices` catalogs; `text-primary` / `text-warning` / `text-danger` |
| Content cell | elided long text with a see-more control revealing the full text in place |
| Payment column | linked payment's date, or empty cell |
| Issue date format | module timestamp format (d/m/Y h:i:s A) |
