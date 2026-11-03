# Feature Specification: Attendance Punctuality & Export

**Feature Branch**: `002-attendance-punctuality-export`

**Created**: 2026-11-02

**Status**: Draft

**Input**: User description: "Add a 'Punctuality' column to the Ruby attendance table showing
values like 'on time', 'X minutes late' or 'X minutes early', computed from each punch's
recorded latency and its closest shift. The column is color coded by punch type […]. The
threshold lives in a settings mechanism (currently absent from Ruby) — propose one. Also
make the attendance table exportable to Excel/CSV according to the currently selected
filters." (plus environment and governance notes)

**Context**: The Ruby module's Attendance page already shows a filterable punch table —
Name, Role(s), Department(s), Type (in / out), Created At — and punches already record,
at capture time, the latency in signed minutes relative to the employee's closest shift,
together with the shift that was matched (the shift model, the contact–shift assignment
and the latency capture were delivered by a previous change and are the contract of
record). What is missing is the human presentation of that data: a punctuality reading
per punch with policy-driven color coding, a way for the organisation to tune what counts
as "late", and the ability to take the filtered view out of the system for reporting.
Source: verbatim notes under "Ruby — Attendance" in
`docs/development/notion-review.md` (docs branch), items not already implemented.

## Clarifications

### Session 2026-11-02

- Q: Which user roles should be allowed to change the punctuality threshold in the
  attendance settings? → A: General manager, HR manager and HR assistant — the roles
  already holding all Ruby privileges; other roles remain view-only.
- Q: What should the maximum accepted value be for the punctuality threshold setting,
  above which a save is rejected? → A: 4 hours (240 minutes).
- Q: What default punctuality threshold should the system ship with for organisations
  that never configure one? → A: 15 minutes.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - See Each Punch's Punctuality (Priority: P1)

An HR manager opens the Attendance page. The table now carries a "Punctuality" column
next to the punch data: every punch with a recorded latency reads "on time", "X minutes
late" or "X minutes early" according to its signed minutes versus the shift that was
matched at capture time. The reading is color coded by punch type: an In punch that is
late, or an Out punch that is early, shows in red when it exceeds the configured
threshold and in blue when it is at or below it; an exactly on-time punch shows in
green; an early In punch or a late Out punch shows in bold green. Punches with no
matched shift (no latency recorded) show a neutral marker instead of a value.

**Why this priority**: The latency data is already captured but invisible; turning it
into an at-a-glance compliance signal is the core value of this feature and works
immediately with the built-in default threshold, requiring no other story to deliver
benefit.

**Independent Test**: Can be fully tested by seeding punches across the six color rule
cells (late/early/on-time × in/out) plus one shift-less punch and verifying each row's
text and color in the table — delivers immediate attendance-compliance visibility.

**Acceptance Scenarios**:

1. **Given** an In punch recorded 12 minutes after its shift start and a threshold of
   15 minutes, **When** the attendance table is viewed, **Then** the Punctuality column
   reads "12 minutes late" in blue.
2. **Given** an In punch recorded 20 minutes after its shift start and a threshold of
   15 minutes, **When** the attendance table is viewed, **Then** the column reads
   "20 minutes late" in red.
3. **Given** an In punch recorded 10 minutes before its shift start, **When** the
   attendance table is viewed, **Then** the column reads "10 minutes early" in bold
   green.
4. **Given** an Out punch recorded 25 minutes before its shift end and a threshold of
   15 minutes, **When** the attendance table is viewed, **Then** the column reads
   "25 minutes early" in red.
5. **Given** an Out punch recorded exactly at its shift end, **When** the attendance
   table is viewed, **Then** the column reads "on time" in green.
6. **Given** an Out punch recorded 30 minutes after its shift end, **When** the
   attendance table is viewed, **Then** the column reads "30 minutes late" in bold
   green.
7. **Given** an Out punch recorded 8 minutes before its shift end and a threshold of
   15 minutes, **When** the attendance table is viewed, **Then** the column reads
   "8 minutes early" in blue.
8. **Given** a punch with no matched shift (no latency recorded), **When** the
   attendance table is viewed, **Then** the Punctuality column shows a neutral
   unassigned-style marker with no color coding and no minutes value.
9. **Given** the interface language is Arabic, **When** the attendance table is viewed,
   **Then** the column header and all punctuality values (including singular/plural
   minute forms) render correctly in Arabic.
10. **Given** the Punctuality column header, **When** it is used for sorting, **Then**
   rows order by the punch's recorded latency.

---

### User Story 2 - Tune the Punctuality Threshold in Settings (Priority: P2)

A managerial HR user opens the attendance settings surface from the Attendance page and
sees the current punctuality threshold in whole minutes. The value starts from a
sensible default when the organisation has never configured it. Changing it (e.g. from
15 to 5 minutes) and saving takes effect on the next table load: a 6-minute late In
punch flips from blue to red. The threshold applies equally to late Ins and early Outs.
Invalid entries are rejected with an actionable message and the stored value is left
unchanged; users without the managerial privilege cannot change it at all. Each
organisation (tenant) has its own threshold — one tenant's change never affects
another's.

**Why this priority**: The punctuality column is already useful with the default
threshold; letting the organisation define its own lateness policy is the refinement
that makes the color coding trustworthy for their context. It also introduces the
project's first settings mechanism, which later features can build on.

**Independent Test**: Can be tested by changing the threshold as a privileged user,
reloading the attendance table and verifying the color boundary moves exactly as
configured — delivers "policy ownership" value on its own.

**Acceptance Scenarios**:

1. **Given** an organisation that never configured a threshold, **When** punctuality is
   evaluated, **Then** the default (15 minutes) applies.
2. **Given** a threshold of 15 minutes, **When** a managerial user changes it to 5
   minutes and saves, **Then** the change succeeds and a subsequent table load colors a
   6-minute late In punch red and a 4-minute late In punch blue.
3. **Given** the settings field, **When** an invalid value is submitted (empty,
   non-integer, negative, or greater than 240 minutes), **Then** it is rejected with a
   clear, actionable message and the previously stored value is retained.
4. **Given** a user who can view the attendance table but lacks the threshold-editing
   privilege, **When** they attempt to reach or submit the threshold setting, **Then**
   editing is denied and the stored value cannot be changed.
5. **Given** two different organisations with different thresholds, **When** either
   views its attendance table, **Then** each sees color coding driven solely by its own
   threshold.
6. **Given** the settings surface in Arabic, **When** opened, **Then** its labels,
   hints and validation messages render correctly in Arabic.
7. **Given** a threshold change, **When** any punch is evaluated, **Then** the same
   threshold governs late Ins and early Outs alike.

---

### User Story 3 - Export the Filtered Attendance Table (Priority: P3)

An HR user applies any combination of the table's filters — name search, role(s),
department(s), creation date range, punch type — then chooses to export the table as
Excel (.xlsx) or CSV. The downloaded file contains every punch matching the currently
applied filters in the table's current order — not just the visible page — with the
same data columns as the table (Name, Role, Departments, Type, Punctuality as text,
Created At) and localized headers. The exported file opens cleanly in mainstream
spreadsheet applications in both English and Arabic.

**Why this priority**: Export extends the value of the punctuality view into payroll
review, audits and offline reporting, but depends on the table (US1) being worth
exporting; it is the outermost layer of the feature.

**Independent Test**: Can be tested by filtering to a known department and date range,
exporting in both formats, and verifying the file's rows match the filtered table
row-for-row — delivers take-the-data-with-you value on its own.

**Acceptance Scenarios**:

1. **Given** filters selecting one department and a date range, **When** the user
   exports, **Then** the file contains exactly the punches the filtered table reports,
   in the same order, with the table's columns Name, Role, Departments, Type,
   Punctuality (as text, e.g. "20 minutes late") and Created At.
2. **Given** no filters applied, **When** the user exports, **Then** the file contains
   the entire attendance register, and an export of a month of punches (thousands of
   rows) completes promptly without silent truncation.
3. **Given** filters that match zero punches, **When** the user exports, **Then** a
   valid file with headers only is produced (or a clear explanation is shown), never a
   corrupt or empty-of-headers file.
4. **Given** the interface language is Arabic, **When** the user exports, **Then**
   headers and punctuality values are in Arabic and the file opens in mainstream
   spreadsheet applications without garbled characters.
5. **Given** any export, **When** the file is opened, **Then** it does not contain the
   row Actions column.
6. **Given** a user without access to the attendance page, **When** they attempt to
   export, **Then** the action is denied, exactly as viewing the table is.

---

### Edge Cases

- What happens when a punch has no matched shift (latency not recorded) — in the table,
  in sorting, and in exports?
- How does the system behave when latency is exactly 0 (on time, green) or exactly equal
  to the threshold (at or below it, blue — never red)?
- What happens when the threshold is set to 0 — every late In and early Out is red.
- How are latencies beyond an hour displayed — as plain minutes ("90 minutes late"),
  with no hours-and-minutes reformatting?
- How does the singular "1 minute" form render versus plural forms in both English and
  Arabic?
- What happens when an employee's shift assignment changes after a punch was recorded —
  the recorded latency stands; this feature displays captured data and never
  recomputes it.
- How are two managerial users editing the threshold concurrently handled — last saved
  value wins, with no corrupted state?
- What happens if punches are being captured while an export runs — the export reflects
  a consistent snapshot, with no partially-written rows?
- How does the table behave when the default threshold has never been configured (pure
  default path) or was configured then reset?
- What happens when an export is very large — it completes promptly or reports failure
  clearly; it is never silently truncated.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The attendance table MUST add a "Punctuality" column presenting, for every
  punch with a recorded latency, a reading derived from the punch's type and recorded
  signed minutes versus its matched shift: zero minutes → "on time"; positive minutes →
  "X minutes late"; negative minutes → "X minutes early".
- **FR-002**: The Punctuality column MUST be color coded by punch type and policy:
  late Ins and early Outs in red when their minutes are strictly greater than the
  configured threshold, and in blue when at or below it; on-time punches in green;
  early Ins and late Outs in bold green.
- **FR-003**: The system MUST provide a tenant-scoped settings mechanism, owned by the
  Lava shared foundation (Common), storing named settings per tenant with values that
  are read through a single declared access path; when a tenant has no stored value for
  a setting, the consuming module's declared default applies. The mechanism MUST be
  reusable by future modules without schema duplication.
- **FR-004**: The Ruby module MUST register, in that mechanism, a "punctularity
  threshold" setting: a whole number of minutes, zero or greater and at most 240
  minutes (4 hours), defaulting to 15. One threshold MUST govern late Ins and early
  Outs alike.
- **FR-005**: The attendance table MUST evaluate punctuality coloring against the
  current threshold on every load, so a saved threshold change is reflected the next
  time the table is viewed.
- **FR-006**: Users holding the threshold-editing privilege defined in FR-010 MUST be
  able to view and change the punctuality threshold from a settings surface within the
  Ruby module; invalid submissions MUST be rejected with clear, actionable messages and
  MUST leave the stored value unchanged.
- **FR-007**: The attendance table MUST offer export to Excel (.xlsx) and CSV that
  includes every punch matching the currently applied filters and searches, in the
  table's current ordering — never limited to the visible page — with the same columns
  and order as the table (Name, Role, Departments, Type, Punctuality, Created At), and
  with headers localized to the user's language.
- **FR-008**: Exports MUST carry the punctuality reading as text (color coding is not
  required in the file) and MUST open correctly in mainstream spreadsheet applications
  in both English and Arabic, with no garbled characters.
- **FR-009**: All new user-facing strings — column header, punctuality values (including
  singular/plural minute forms), settings labels, validation messages and export
  affordances — MUST be available in both English and Arabic.
- **FR-010**: Access MUST follow the project's privilege model: viewing and exporting
  the attendance table share the access control that governs the attendance page
  today; editing the punctuality threshold is gated by a dedicated privilege granted
  to the general manager, HR manager and HR assistant roles, enforced by the Sapphire
  authorization layer.
- **FR-011**: The Punctuality column MUST be sortable by the punch's recorded latency
  and, being a derived reading, MUST NOT participate in text search.
- **FR-012**: Settings MUST be tenant-isolated: no user action may read or write another
  tenant's settings, and threshold changes MUST never leak across organisations.
- **FR-013**: Punches without a recorded latency MUST show a neutral unassigned-style
  marker in the table and an empty punctuality cell in exports, and MUST be excluded
  from color coding.

### Key Entities *(include if feature involves data)*

- **Attendance Punch** (existing, Ruby): a recorded in/out event with its capture time,
  matched shift and recorded latency in signed minutes. This feature adds only a derived
  punctuality presentation; it does not change how punches are captured or stored.
- **Shift** (existing, Ruby): the daily working window (start/end) an employee is
  assigned to per weekday; the reference against which latency was captured.
- **Tenant Setting** (new, Lava Common): a named, per-organisation preference with a
  code-declared default — the reusable mechanism this feature introduces. Keys are
  namespaced by the consuming module; values are typed and validated on write.
- **Punctuality Threshold** (new setting key, Ruby): the whole-minute grace boundary that
  separates red from blue punctuality readings; default 15 minutes.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% of punches with a recorded latency display the correct punctuality
  text and color for all six rule cells (late/early/on-time × in/out) in both English
  and Arabic.
- **SC-002**: A managerial user can complete a threshold change in under 1 minute, and
  the new value drives the table's color coding on the very next load 100% of the time.
- **SC-003**: Exporting one month of attendance (~5,000 punches) under any filter
  combination completes within 10 seconds and matches the on-screen filtered table
  row-for-row and column-for-column.
- **SC-004**: Exported Excel and CSV files open correctly in mainstream spreadsheet
  applications with 100% readable English and Arabic content (headers, names, punctuality
  values).
- **SC-005**: Zero invalid threshold values are persisted across all rejection scenarios
  (empty, non-integer, negative, out of bounds).
- **SC-006**: 100% of threshold-change attempts by non-privileged users are blocked, and
  zero cross-tenant settings reads or writes are possible.
- **SC-007**: Punches without a matched shift render a neutral marker in the table and
  export with an empty punctuality cell, 100% of the time — never a misleading value.

## Assumptions

- The latency capture, the shift model and the contact–shift assignments delivered by
  the previous change are correct and authoritative; this feature is a presentation and
  export layer and never recomputes latency. If a shift changes after a punch was
  recorded, the recorded latency stands.
- The default punctuality threshold is 15 minutes (confirmed 2026-11-02), a common
  organisational grace period; tenants can change it within the validated bounds
  (0–240 minutes).
- "Resetting" the threshold means saving the default value back manually; no separate
  reset-to-default control exists in this feature.
- "At or below" the threshold colors blue; strictly greater colors red (verbatim from
  the source notes). Exactly on-time (0 minutes) is the only "on time" reading.
- Minutes are displayed as plain minutes (e.g., "90 minutes late"); no
  hours-and-minutes reformatting in this feature.
- A single threshold serves late Ins and early Outs; separate per-direction thresholds
  are out of scope.
- Export ordering mirrors the table's current sort; the Actions column is excluded;
  colors are not reproduced in files (text only).
- Exports cover the full filtered result set regardless of pagination; no new filters
  are added (e.g., filtering by punctuality status is out of scope).
- The settings mechanism stays minimal — named keys with typed values and defaults, no
  per-user settings, no change history/audit trail in this feature — but is structured
  so other modules can register keys without duplicating the mechanism (Lava-first).
- The threshold editing surface lives inside the Ruby module (attendance settings),
  gated to the general manager, HR manager and HR assistant roles (clarified
  2026-11-02); a cross-module settings hub/page is out of scope.
- Viewing the punctuality column and exports follow the attendance page's existing
  access control; no new attendance roles are introduced beyond the dedicated
  threshold-editing privilege.
- Existing attendance filters, the profile modal and all other staff-module behavior are
  unchanged by this feature.
- Documentation on the docs branch is updated in the same change, per the project
  constitution.
