# Feature Specification: Staff Notices Page (notice / warning / termination)

**Feature Branch**: `004-staff-notices`

**Created**: 2026-11-27

**Status**: Draft

**Input**: User description: "Let's create the notices page (spec 004), try to deduce its purpose and functions from its current database schema and corresponding eloquent models. this new spec should be based on the git branch of the current one (003)."

**Context**: The `r_notices` table already exists (Ruby module) with an author contact, a
recipient contact, a type of `notice` / `warning` / `termination`, free-text content and an
optional link to a payroll payment — but no page, route, controller or view has ever been
built for it. The navbar already links a Notices entry under the Personnel menu (currently a
dead link), the member-removal retention check already counts notices, and the module's
shared components (list, filters, modal, language switcher) are in place. This feature
deduces the purpose — a formal personnel record trail — and delivers the page.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Review the Notices Record Trail (Priority: P1)

An HR user opens Personnel → Notices and sees every notice issued in the organization as a
dated record trail: who it concerns (recipient), who issued it (author), its type (notice,
warning or termination) and its content. They narrow the trail with filters — by type, by
the member it concerns (recipient), by the member who issued it (author), and by issue date
range — and clear them to see everything again. Records that concern deactivated members
remain visible, because the trail is the organization's formal history, not a view of
current staff.

**Why this priority**: the record trail is the core value even before anything can be issued
from the page itself; making existing data reviewable is the unblocker for trust in the
feature.

**Independent Test**: Can be fully tested by opening the notices page with existing records
present, verifying each column renders, applying each filter alone and combined, and
confirming records of deactivated members still appear.

**Acceptance Scenarios**:

1. **Given** one or more notices exist, **When** the user opens Personnel → Notices,
   **Then** the list shows one row per record — recipient, author, type, content, issue
   date — newest first.
2. **Given** the trail holds multiple types and members, **When** the user applies the
   recipient, author, type and/or date-range filters (alone or combined), **Then** only
   matching records remain, and combined filters narrow together.
3. **Given** a notice concerns a deactivated member, **When** the user opens the list,
   **Then** the record is still visible with the member's name.
4. **Given** no notices exist yet, **When** the user opens the page, **Then** a clear
   empty state is shown instead of a blank table.

---

### User Story 2 - Issue and Manage Notices (Priority: P2)

An HR user authorized to manage notices (a dedicated permission) issues a new record from
the page: they pick the member it concerns, choose the type (notice, warning, termination)
and write the content. The system records the author automatically — the signed-in
member's own profile — because a formal record must state who issued it, not who filled the
form. The record appears in the trail immediately. The user can later correct a record's
recipient, type or content (the original author and issue date are preserved as the
record's provenance), or delete a wrongly issued record after an explicit confirmation.
Users without the permission can view the trail but see no issuing or editing controls.

**Why this priority**: issuing from the page is what turns the dead schema into a working
HR tool; without it the trail can only be fed by external means that do not exist.

**Independent Test**: Can be fully tested by issuing a notice, a warning and a termination
to different members, verifying each lands in the trail with the signed-in member as
author, then editing one and deleting another with confirmation.

**Acceptance Scenarios**:

1. **Given** the user holds the manage-notices permission and has an HR profile, **When**
   they issue a record choosing a member, type and content, **Then** the record is stored
   with them as author and appears in the list immediately.
2. **Given** a record was issued, **When** the user edits its recipient, type or content,
   **Then** the change is saved while the original author and issue date remain unchanged.
3. **Given** a record was issued in error, **When** the user chooses to delete it and
   confirms, **Then** the record is permanently removed from the trail.
4. **Given** the user submits an incomplete form (missing member, type or content),
   **When** the form is validated, **Then** field-level errors render in the active
   language and nothing is stored.

---

### User Story 3 - Notices Anchor the Member's Permanent Record (Priority: P3)

A member who has notices — issued by them or concerning them — cannot be permanently
removed from the organization, because their record trail must be retained; the removal
flow offers deactivation instead, exactly like attendance history. This guarantee already
exists in the removal logic; this feature makes it reachable in practice by allowing
notices to exist at all.

**Why this priority**: the guarantee protects the integrity of the formal record; it
matters once the first notice is issued, not before.

**Independent Test**: Can be fully tested by issuing a notice to a member, then attempting
permanent removal of that member and confirming it is refused while deactivation remains
available and the notice stays visible.

**Acceptance Scenarios**:

1. **Given** a member has at least one notice (as recipient or author), **When** an
   attempt is made to permanently remove the member, **Then** removal is refused with the
   established feedback and deactivation is offered instead.
2. **Given** a member was deactivated after notices were issued, **When** any user opens
   the notices page, **Then** all of that member's records remain visible.

---

### Edge Cases

- What happens when the signed-in user has no linked HR profile (no author identity)?
  Issuing cannot proceed — the system must say why clearly rather than fail silently.
- What happens to a record whose recipient was hard-removed through any future path? The
  schema detaches the recipient while keeping the record; the list then shows it without a
  recipient (unassigned), never a broken name.
- How does long or mixed-direction content (English sentences inside an Arabic page)
  behave in the list? It must remain readable, per the established bidi handling.
- What happens when editing a record whose recipient was deactivated? The current
  recipient stays a valid prefilled value; only changing the recipient restricts the
  choices to active members.
- What happens when a notice carries a payroll payment link? The linkage is displayed
  read-only; this feature provides no way to create or change it.
- Termination type: issuing it is a record only — no automatic member status change is
  implied by this feature.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The notices page MUST be reachable from the existing Personnel → Notices
  navbar entry for signed-in users with Ruby access; the entry must no longer be a dead
  link.
- **FR-002**: The list MUST show one row per record with recipient name, author name,
  type, content and issue date, ordered newest first. Long content MUST render elided
  with a see-more control that reveals the full text in place.
- **FR-003**: The list MUST be filterable by type, by recipient, by author and by issue
  date range — recipient and author being two separate text-search filters by member
  name, matching all members including deactivated ones — with filters combinable and a
  one-action clear that restores the unfiltered trail.
- **FR-004**: The three types MUST render as localized labels and MUST be visually
  distinguishable from each other at a glance.
- **FR-005**: Issuing MUST require a recipient (an active member), a type and non-empty
  content; the author MUST be recorded automatically as the signed-in member's profile and
  MUST NOT be a form input.
- **FR-006**: Editing MUST allow changing recipient, type and content while preserving the
  original author and issue date.
- **FR-007**: Deletion MUST require an explicit confirmation and MUST remove the record
  permanently.
- **FR-008**: Form validation errors MUST render in the active interface language.
- **FR-009**: Every user-facing string this page introduces MUST ship in both English and
  Arabic, and the page MUST mirror (right-to-left) under Arabic by inheriting the
  established language mechanism.
- **FR-010**: Records MUST survive member deactivation and remain listed and filterable;
  a member holding notices as author or recipient MUST be refused permanent removal per
  the existing retention rule.
- **FR-011**: A record's payroll payment link, when present, MUST be displayed read-only;
  the feature MUST NOT offer manual assignment of that link.
- **FR-012**: Issuing, editing and deleting records MUST be limited to users holding a
  dedicated manage-notices permission, following the attendance-settings precedent; viewing
  the record trail stays open to every user with Ruby module access.

### Key Entities *(include if feature involves data)*

- **Notice**: A formal personnel record — a type (`notice` / `warning` / `termination`)
  and free-text content, issued on a date by an author (the HR member who issued it) about
  a recipient (the staff member it concerns), optionally anchored to a payroll payment.
  The record is permanent until explicitly deleted; it anchors the member's history.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A user can issue a correctly filled record in under one minute (pick member,
  pick type, write content, save).
- **SC-002**: On a trail of hundreds of records, filtering by each dimension alone and
  combined returns exactly the matching records with no mismatched rows.
- **SC-003**: 100% of the page's user-facing strings render in the active language, and
  the page mirrors correctly under Arabic with no clipped or dead controls.
- **SC-004**: Permanent removal of a member holding notices is refused 100% of the time,
  while deactivation remains available and the records stay visible.
- **SC-005**: Editing a record changes only recipient, type and content — author and issue
  date remain byte-stable across edits.

## Assumptions

- Purpose is deduced from the schema: notices are formal HR records (author, recipient,
  typed, payroll link), not system notifications — no email/push delivery is included.
- Privilege model (user decision, 2026-11-27): issuing/editing/deleting require a dedicated
  manage-notices permission granted through the module's permission system, mirroring the
  attendance-settings pattern; viewing requires only Ruby access.
- The signed-in user's linked contact is the author; users without a linked profile cannot
  issue (clear feedback), consistent with the author column being mandatory.
- The existing `r_notices` schema and `Notice` model are consumed as-is (no migration);
  the schema is the contract per the constitution.
- The recipient picker offers active members only; existing records concerning deactivated
  members remain visible.
- No list export in this feature (unlike attendance) — the schema shows no export-shaped
  signals; it can be added later without schema change.
- Surfacing notices on the member profile modal is out of scope for this feature (page
  only); the profile already has a leave timeline that could host them later.
- The page reuses the module's shared list, filter and modal building blocks, and the
  language switcher from spec 003 is inherited with no per-page work.

## Clarifications

### Session 2026-11-27

- Q: When filtering by member, whose involvement does the filter match — recipient, author
  or both? → A: Two separate filters — one for the recipient (the member the record
  concerns), one for the author (the member who issued it).
