# Feature Specification: Language Switcher (EN/AR + RTL)

**Feature Branch**: `003-language-switcher`

**Created**: 2026-11-19

**Status**: Implemented

**Input**: User description: "Starting from currently checked out branch let's start spec 003 that adds the missing language switcher to the project with the Common (Lava) module and, the navbar switcher icon - preferably right before the username dropdown toggle. Some vue components might need updating to accommodate Arabic localization beside the RTL styling for both the blade views as a whole and the vue components themselves."

**Context**: Arabic string catalogs, localized entity data (roles, departments, degrees, cities),
and bilingual exports already exist — but nothing in the web application ever changes the active
language, so Arabic is unreachable by real users. This feature adds the missing switching
mechanism, owned by the Common (Lava) module, and makes the interface genuinely usable in Arabic
by mirroring the layout (right-to-left) across Blade views and the shared Vue components.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Switch the Interface Language from the Navbar (Priority: P1)

From any page, a user opens the language control in the navbar — placed directly before the
username dropdown toggle — and picks English or Arabic. The page immediately re-renders in the
chosen language: navigation labels, page titles, table headers, filters, buttons, validation
messages, confirmations and toasts all switch. Localized record data (role, department, degree
and city names) renders in the chosen language too. The choice persists as the user navigates
between pages.

**Why this priority**: The switching mechanism is the single unblocker for every Arabic
capability the product already paid for; without it the entire Arabic layer is dead weight.

**Independent Test**: Can be fully tested by switching to Arabic on the staff page and verifying
the navbar, table headers, filters and one row's department name all render in Arabic, then
navigating to attendance and confirming it stays Arabic.

**Acceptance Scenarios**:

1. **Given** any authenticated page, **When** the user chooses Arabic in the navbar language
   control, **Then** the whole visible interface (navigation, headings, tables, filters, actions)
   renders in Arabic without a manual reload misbehaving and with the choice still applied on the
   next page.
2. **Given** Arabic is active, **When** the user triggers a validation error, confirmation dialog
   or toast, **Then** that message renders in Arabic.
3. **Given** a record whose Arabic translation is missing, **When** Arabic is active, **Then**
   the record's value falls back to English rather than showing a blank or a raw key.
4. **Given** Arabic is active, **When** the user exports the attendance table, **Then** the
   exported file's headers and punctuality readings are in Arabic.

---

### User Story 2 - Fully Mirrored (RTL) Layout in Arabic (Priority: P2)

When Arabic is active, the entire layout mirrors: the navbar (brand right, menus flowing
right-to-left, language control and username dropdown at the mirrored end), breadcrumbs, cards,
data tables (including sorting controls and pagination), forms with their labels and inputs,
modals, timeline and filter widgets. English keeps the current left-to-right layout. No clipped,
overlapping or misaligned content appears on the core surfaces.

**Why this priority**: Arabic text in a left-to-right layout is poor UX; mirroring is what makes
the switcher deliver real value rather than a novelty.

**Independent Test**: Can be tested by switching to Arabic and walking the staff, attendance,
departments and structure pages plus one create/edit modal, checking each surface renders
mirrored and remains usable.

**Acceptance Scenarios**:

1. **Given** Arabic is active, **When** the user opens the staff page, **Then** the navbar,
   breadcrumb, filters, table and actions column all mirror and remain fully interactive.
2. **Given** Arabic is active, **When** the user opens a create/edit modal, **Then** labels and
   inputs flow right-to-left, validation errors align with their fields, and tab navigation
   reads right-to-left.
3. **Given** English is active, **Then** the layout is byte-for-byte the familiar left-to-right
   experience (no regression).
4. **Given** Arabic is active, **When** interactive widgets are used — multi-select dropdowns,
   the date range picker, data-table sorting and pagination, the timeline — **Then** each opens,
   reads and behaves correctly in the mirrored layout.

---

### User Story 3 - Language Preference Follows the User (Priority: P3)

The chosen language persists beyond the current page and session navigation behavior — the
preference lives on the user's record with a working copy in the current session — so the user
is not forced back to English on their next visit.

**Why this priority**: Persistence quality differentiates a demo from a usable feature, but the
switcher and mirroring deliver value even with the simplest persistence model.

**Independent Test**: Can be tested by switching to Arabic, signing out and back in — including
from another browser — and confirming Arabic greets the user each time.

**Acceptance Scenarios**:

1. **Given** the user has chosen Arabic, **When** they navigate between pages within the module,
   **Then** Arabic remains active.
2. **Given** the user has chosen Arabic, **When** they sign out and sign back in — including
   from a different device or browser — **Then** Arabic is active immediately, because the
   preference lives on their platform account while the current session carries the working
   value.
3. **Given** a signed-in user switches language mid-session, **When** any later page or request
   renders, **Then** the new choice applies at once and sticks — ordinary page loads resolve the
   language from the session without re-reading the stored record.

---

### Edge Cases

- What happens when a string has no Arabic translation (new feature, missed key)? — English
  fallback must render, never a raw key.
- How does mixed-direction data behave — English names, email addresses, phone numbers and dates
  inside Arabic (RTL) table cells?
- What does the pre-login page show? — Resolved: it stays English only (Q3); switching starts
  after sign-in.
- Widget internals: does the date picker's calendar and the multi-select's dropdown open in a
  sensible position under RTL?
- Does switching mid-form discard entered data (page reload) or preserve it?
- URLs: language is viewer-bound (session), not URL-bound — shared links open in the viewer's
  own language.
- Numeric date/time formats under Arabic (keep current numeric formats or localize)?
- Exports downloaded while Arabic is active — file naming and content language consistency.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST provide a language control in the authenticated navbar on every
  module page, placed directly before the username dropdown toggle, offering English and Arabic
  with the active language indicated.
- **FR-002**: Choosing a language MUST apply it to the entire page — navigation, titles, table
  headers, filters, buttons, validation messages, confirmations and toasts — without requiring
  more than one user action.
- **FR-003**: Localized entity data (roles, departments, degrees, cities, and similar translated
  records) MUST render in the active language, falling back to English when a translation is
  missing.
- **FR-004**: The interface layout MUST mirror for Arabic (right-to-left) across navbar,
  breadcrumbs, cards, tables (including sort and pagination controls), forms, modals and filter
  widgets, and MUST remain left-to-right for English with zero regression.
- **FR-005**: Shared interactive components (multi-selects, date range pickers, data tables,
  timelines, modals, tabs) MUST remain fully usable and visually correct under right-to-left
  rendering. Complex visual renderings (the organizational structure diagram, analytics charts)
  MUST mirror their surrounding container while their internal drawing stays left-to-right.
- **FR-006**: The chosen language MUST persist across in-module navigation and future sessions:
  the preference is stored on the user's platform account — the sign-in identity, which every
  user has (unlike a contact, which may have no account) — and is read and written through the
  auth module's account boundary. The active session carries the working value; ordinary
  requests resolve the language from the session, and the account is read only when the session
  holds no preference (first request of a session). Switching languages updates both. A live
  session's working value wins until that session ends: switching on one device does not
  disturb other signed-in sessions, which adopt the stored preference at their next sign-in.
- **FR-007**: Exports MUST honor the active language for headers and language-dependent values
  (e.g., punctuality readings).
- **FR-008**: Every user-facing string introduced by this feature MUST ship in both English and
  Arabic.
- **FR-009**: The switching mechanism (control, request handling, layout direction) MUST be
  owned by the Common (Lava) module so every business module inherits it without per-module
  work; only the stored preference itself lives with the platform account, accessed through the
  auth module's account boundary.
- **FR-010**: The pre-login page MUST remain English only; the language control appears only in
  the authenticated navbar.

### Key Entities *(include if feature involves data)*

- **Language Preference**: The user's chosen interface language (English/Arabic) — a durable
  value stored on the platform account (the sign-in identity) plus a working copy in the
  current session; the account is consulted only when the session has no value yet. Records
  without an account have no preference — they never see the interface.
- **Localized Record**: Existing translated business data (roles, departments, degrees, cities)
  consumed — not modified — by this feature.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A user can switch languages from any page in one action (max 2 interactions with
  the control).
- **SC-002**: On every module page, 100% of navigation and page-chrome strings render in the
  active language.
- **SC-003**: A RTL visual QA pass over the core surfaces (navbar, staff list + filters,
  create/edit modal, attendance table + settings modal, departments, structure) shows zero
  blocking layout defects (no clipping, overlap or dead controls).
- **SC-004**: Switching to Arabic and back to English is lossless — the English experience is
  identical to before the feature.
- **SC-005**: An export downloaded under Arabic contains Arabic headers and values and opens
  correctly in mainstream spreadsheet applications.
- **SC-006**: Record data spot-checks (role, department, degree, city names) render in the active
  language with English fallback on both the list and profile surfaces.

## Assumptions

- English remains the fallback and the default for first-time visitors.
- Existing Arabic catalogs cover current surfaces; only strings this feature introduces need new
  translations.
- Numeric date/time formats stay as-is under Arabic (no calendar system change).
- The language control shows both languages with the active one indicated (icon and/or code).
- Central (Sapphire admin) pre-login surfaces beyond the login page itself are out of scope; the
  tenant-facing module navbar is the target.
- Language switching may reload the page; entered-but-unsaved form data is not required to
  survive a language switch (documented behavior).
- No new translated data is created — the feature consumes the existing bilingual records.

## Clarifications

### Session 2026-11-19

- Q: Where does the chosen language live? → A: Per-user with session override — the preference
  is stored on the user's record (durable across devices/sessions), and the current session
  carries the working value; requests read from the session, and the record is read only when
  the session has no preference yet, avoiding a record read on every request.
- Q: Do the org chart and charts get full RTL treatment? → A: Container-level RTL — the page
  chrome mirrors around them, their internal drawing stays left-to-right.
- Q: Should the login page offer a switcher? → A: No — the login page stays English; the
  switcher appears only in the authenticated navbar.
- Q: Which record durably stores the language preference — the staff contact or the platform
  account? → A: The platform account (the sign-in identity): every account has a contact, but
  not every contact has an account, so storage belongs with the account, accessed through the
  auth module's account boundary.
- Q: When one device switches language, when do other signed-in sessions pick it up? → A:
  Session copy wins — other live sessions keep their working language until they sign in
  again; no cross-session propagation.
