# Feature Specification: Complete Staff Module

**Feature Branch**: `001-complete-staff-module`

**Created**: 2026-10-30

**Status**: Draft

**Input**: User description: "Let's start by completing the staff related features and functions."

**Context**: The Ruby HR module already has a Staff page (staff list, filters, profile modal
with latest approved leaves, and a skeleton add/edit modal whose contact and employment
panes are placeholders). The edit/delete row actions and the create/update/delete
operations behind them do not exist yet. This feature completes that staff management
experience end to end.

## Clarifications

### Session 2026-10-30

- Q: Must every staff member have a platform user account, or can a member be recorded
  without one? → A: Required — every staff member gets an account on creation, since
  members use the platform at least for their own attendance.
- Q: Can a single applicant record be linked to more than one staff member, and once
  linked can its details still be edited from the staff form? → A: One member per
  applicant; already-linked applicants are hidden from the picker; linked details stay
  editable from the staff form.
- Q: If a new staff member's data is valid but the account creation fails through the
  auth contract, what should happen to the staff record? → A: All-or-nothing — the staff
  record is not saved, the form stays open with the entered data, and the failure is
  clearly reported.
- Q: After a staff member with history is deactivated (soft-removed), should they be
  viewable or reactivate-able anywhere in this feature? → A: Both — a staff list filter
  includes deactivated members, and a reactivate action restores them.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Add a New Staff Member (Priority: P1)

An HR manager opens the Staff page, clicks "New", and fills in a single form organised in
three groups — Contact Details (name, one or more emails / phones / addresses with a
default of each), Employment Details (job title, role(s), managed departments, departments
of membership, profile image, and a link to an applicant record — picked from existing
applicants or created inline with recruitment date, highest educational degree and its
date, and employment documents), and User Account (username, password with confirmation) —
then saves. The new member appears in the staff list with their default email, default
phone, role, department(s) and recruitment date.

**Why this priority**: Without the ability to create staff records, the module cannot
onboard employees and every other staff capability (edit, deactivate, profile) operates
only on seeded or manually inserted data.

**Independent Test**: Can be fully tested by creating one staff member through the form and
finding them in the staff list with the expected columns populated — delivers the core
"employee onboarding" value on its own.

**Acceptance Scenarios**:

1. **Given** the staff form with all required fields completed, **When** the HR manager
   saves, **Then** the record persists and the staff list shows the member with default
   email, default phone, role, departments and recruitment date.
2. **Given** a staff form missing a required field (e.g., name), **When** saving is
   attempted, **Then** the form is rejected with a clear, actionable message and nothing
   is saved.
3. **Given** multiple emails or phones entered, **When** one is marked as default,
   **Then** only the default appears in the staff list and is emphasised in the profile.
4. **Given** the form is submitted in Arabic, **When** the member is saved and viewed,
   **Then** all labels, messages and localized values render correctly in Arabic.
5. **Given** an existing applicant record, **When** it is linked while creating the
   member, **Then** the member's recruitment date, degree and employment documents are
   drawn from that record.

---

### User Story 2 - Edit an Existing Staff Member (Priority: P2)

An HR manager clicks the edit action on a staff row; the same form opens prefilled with
the member's current contact, employment and account data. Changing any value (adding an
email, moving department membership, updating the degree, replacing the profile image)
and saving updates the record; the list and profile immediately reflect the changes.

**Why this priority**: Staff data changes constantly (transfers, promotions, new phone
numbers); editing is the day-to-day operation after initial onboarding, and it reuses the
same form built for US1.

**Independent Test**: Can be tested by editing any seeded member's job title and default
phone and verifying both the list and profile modal show the new values.

**Acceptance Scenarios**:

1. **Given** an existing staff member, **When** the edit form opens, **Then** it is
   prefilled with all current values, including defaults, memberships and documents.
2. **Given** a change of department membership or managed departments, **When** saved,
   **Then** the staff list department column and the profile reflect the new assignments.
3. **Given** an uploaded employment document, **When** its display name is renamed in the
   form, **Then** the profile document list shows the new name.

---

### User Story 3 - Remove a Staff Member (Priority: P3)

An HR manager clicks the delete action on a staff row and is asked to confirm; after
confirming, the member leaves the staff list — deactivated with all history retained if
they have any historical records, permanently removed if they have none — without breaking
departments they managed, their historical attendance, or their past leaves. Deactivated
members remain reachable through a staff list filter and can be reactivated with their
data and history intact.

**Why this priority**: Offboarding must exist for the staff register to stay accurate, but
it is less frequent than create/edit and depends on how history is preserved.

**Independent Test**: Can be tested by removing a member who manages a department and
verifying the department survives with no manager (or a flagged vacancy) and that no
orphaned assignments remain.

**Acceptance Scenarios**:

1. **Given** a staff row, **When** delete is clicked, **Then** a confirmation is required
   before anything happens, and cancelling aborts with no change.
2. **Given** a member who manages one or more departments, **When** their removal is
   confirmed, **Then** those departments remain intact without a manager, with no
   dangling assignments or broken structure diagram.
3. **Given** a member with no historical records (no attendance, leaves or documents),
   **When** their removal is confirmed, **Then** they are permanently removed and no
   trace remains in the staff register.
4. **Given** a deactivated member shown via the deactivated filter, **When** the
   reactivate action is confirmed, **Then** the member returns to the active staff list
   with all their data and history intact and their account access restored.

---

### User Story 4 - Staff User Account Handling (Priority: P4)

Every staff member holds a platform user account (username, password, profile image),
created together with the member and updated alongside them, so the member can sign in
with the access their role grants — at minimum to view their own attendance.

**Why this priority**: Account provisioning makes staff functional as system users, but it
crosses the HR/auth boundary and can be delivered after the HR data capabilities without
blocking them.

**Independent Test**: Can be tested by creating a member through the staff form and
verifying the created account can sign in with the access its role grants.

**Acceptance Scenarios**:

1. **Given** the user account group in the staff form, **When** a member is saved with
   account details, **Then** the platform account is created (new member) or updated
   (existing member) through a contract owned by the authentication module — staff
   management never handles accounts directly — and the member can sign in with the
   access their role grants.
2. **Given** a duplicate username, **When** the form is saved, **Then** it is rejected
   with a clear message and no partial record is created.
3. **Given** valid staff data but a failing account creation (e.g., auth service error),
   **When** the form is saved, **Then** nothing is persisted, the form stays open with
   the entered data, and a clear error explains the account failure.

---

### Edge Cases

- What happens when a member's only email or phone is removed, leaving no default to show
  in the staff list?
- How does the system handle two managers editing the same member at the same time?
- What happens when an uploaded document or profile image exceeds the allowed size or is
  of a disallowed type?
- How are passwords handled when an edit leaves the password fields empty (keep existing
  vs. require re-entry)?
- What happens when a staff member is deleted while their applicant record, documents or
  attendance history still reference them?
- What happens to a staff member when the applicant record linked to them is removed by
  another module or process?
- How does the staff list behave when an applicant picker search returns no matches?
- What happens when a role or department selected in the form is deactivated or removed
  elsewhere while the form is open?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST provide a single staff form for creating members, organised
  into contact details, employment details, and user account groups.
- **FR-002**: The contact group MUST capture name, one or more emails, phones and
  addresses, with exactly one default email and one default phone per member.
- **FR-003**: The employment group MUST capture job title, role(s), managed departments,
  departments of membership, and profile image. Recruitment date, highest educational
  degree and employment documents are handled through the applicant link as specified in
  FR-004 (entered when creating an applicant inline; displayed and editable through the
  linked record).
- **FR-004**: Every staff member MUST be linked to exactly one applicant record, and an
  applicant record MUST be linked to at most one member. The form MUST let the HR manager
  pick an existing applicant (excluding already-linked ones) or create a minimal applicant
  record inline (recruitment date, highest educational degree and its date, employment
  documents). All recruitment details displayed across the module MUST come from that
  record and remain editable from the staff form until the Recruitment feature exists.
- **FR-005**: The system MUST reject invalid submissions (missing required fields,
  malformed emails/phones, duplicate values) with field-level, actionable messages and
  persist nothing.
- **FR-006**: The edit form MUST open prefilled with all current member data and MUST
  save changes atomically (a failed update leaves the member unchanged).
- **FR-007**: The staff list MUST refresh to reflect created, edited or removed members
  without a full page reload.
- **FR-008**: Removal MUST require explicit confirmation. The system MUST determine the
  removal mode automatically: a member with historical records (attendance, leaves,
  documents or any other activity) is deactivated and fully retained; a member with no
  history is permanently removed. In both modes, the member's department memberships and
  managed departments are cleared with the departments themselves left intact. The staff
  list MUST offer a filter that includes deactivated members; deactivation MUST suspend
  the member's account access through the auth-owned contract, and a reactivate action
  MUST restore both the member and their account access.
- **FR-009**: All form labels, validation messages and display values MUST be available in
  both English and Arabic.
- **FR-010**: Every staff member MUST have a platform account. The user account group MUST
  create the account (username and password required at creation) or update it through a
  contract owned by the authentication module whenever the staff form is saved, and MUST
  prevent duplicate usernames. A failure on the account side MUST abort the entire save
  (all-or-nothing): no staff record is persisted, the form stays open with the entered
  data, and the failure is reported clearly.

### Key Entities *(include if feature involves data)*

- **Staff Member (Contact)**: A person working in the organisation — name, job title,
  profile image, default-flagged emails / phones / addresses, roles, department
  memberships, managed departments, recruitment details, and employment documents.
- **Role**: An access-granting position assigned to staff (e.g., HR manager, accountant);
  more than one may be assigned.
- **Department**: An organisational unit a member belongs to and/or manages.
- **Applicant / Recruitment Record**: The pre-employment record carrying recruitment date,
  degree and employment credentials that staff data draws from; linked to at most one
  staff member.
- **User Account**: The mandatory sign-in identity (username, password, profile image) of
  every staff member — members sign in at least to view their own attendance.
- **Employment Document**: An attachment (credential, certificate) with a display name,
  downloadable from the member profile.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: An HR manager can create a complete staff record — contact, employment and
  account data — in under 3 minutes using only the staff form.
- **SC-002**: 100% of staff data shown in the list and profile is captured by the single
  add/edit form, with no post-creation data patching required.
- **SC-003**: On a staff register of 1,000 members, every list filter returns results
  within 2 seconds.
- **SC-004**: After an edit is saved, the list and profile display the new values on the
  next view with zero stale data.
- **SC-005**: No removal leaves orphaned assignments — every department previously
  managed by a removed member remains intact and managerless (or flagged) 100% of the time.
- **SC-006**: Zero invalid or partial staff records are persisted across all rejection
  scenarios (required fields, duplicates, malformed values).
- **SC-007**: The full staff workflow (create, edit, remove) is operable end to end in
  both English and Arabic.

## Assumptions

- "Staff" is the existing Ruby staff page backed by contacts; this feature completes it
  in place rather than creating a new page.
- The staff role set, departments, degrees and contact entities already available to the
  module are reused; no new organisational vocabulary is introduced.
- Multiple roles may be assigned to one member; the profile shows the first as primary,
  as it does today.
- Leaves in the profile remain read-only; leave management is out of scope.
- The organisational structure diagram, attendance and payroll are out of scope. The full
  recruitment workflow (vacancies, application pipeline) is also out of scope — only the
  minimal applicant record needed to link a staff member (recruitment date, degree,
  documents) is created inline by this feature.
- Account credentials entered in the staff form are delegated to the authentication
  module through an explicit contract; the HR module does not store or manage
  credentials itself.
- Employment documents are uploaded, renamed, removed and downloaded via the existing
  documents mechanism; no versioning is required.
- The profile image belongs to the staff member's contact record and is optional
  (initials are shown when absent, as today).
- Staff management actions are gated to HR roles from the existing role vocabulary
  (HR manager, HR assistant); exact privileges are declared per the project constitution.
- Leaving the password fields empty on edit keeps the current password (credentials are
  never displayed or re-entered to view a member).
