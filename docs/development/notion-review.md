# Notion Review — Status & Backlog

::: warning Read first
This page is a **snapshot review** of the private Notion workspace "Diamond"
(`https://app.notion.com/p/Diamond-b9e9094e33b74a3e95e5bf3892a69ea1`), exported and
cross-referenced against the codebase on **2026-10-30**.

The Notion pages were written as **exploratory scribbles** to find the best way forward —
they are not decisions. Where code and Notion disagree, **the code is the source of truth**.
Several Notion items listed as "required modifications" were already implemented at export
time. Use this page as the working backlog when picking up each area.
:::

## Status legend

| Mark | Meaning |
| --- | --- |
| ✅ | Implemented (verified in code / git history) |
| 🔄 | Partially implemented — verify gaps against the spec below |
| 📋 | Not started (schema/models may already exist) |
| ❌ | Explicitly not implemented |
| ⚠️ | Contradiction or open decision — settle before building |

## Diamond root

Container page only. One stub line per module, matching the module map in the project
constitution. Nothing actionable.

## Onyx / Amethyst / Topaz / Emerald

One-line scope statements only (manufacturing/CRM/inventory; e-commerce; accounting &
finance; project management). No details to act on.

## Ruby — Human Resources

### Organization

| Area | Notion spec | Status |
| --- | --- | --- |
| Departments | Filterable table: name, manager, members count, vacancies count, created at, actions (edit / staff / delete with confirmation); create & edit modal: localized name (AR/EN), manager, staff — same fields for both | ✅ Implemented (`departments.blade.php`, `DepartmentController`, department-form & department-contacts modals) |
| Staff — The Table | Filterable table: name, default email, default phone, role, department(s), recruited at, actions (profile / edit / delete with confirmation) | 🔄 Exists today as the **contacts** page (`ContactController`, contacts table, profile + contact-form modals) — verify columns against spec |
| Staff — Member Profile | Profile modal: avatar/initials, name, emails, phones, addresses, job, role, tenure, department(s), recruited at, degree, attachments (credentials), **latest 5 approved leaves** | 🔄 Profile modal exists; tenure / degree / leaves timeline unverified — leaves timeline is blocked until Leaves exists |
| Staff — Add / Edit Member | Modal, 3 groups: **User Account** (profile image, username, password + confirmation), **Contact Details** (name, emails, phones, addresses), **Employment Details** (role, managed departments, member of department, highest degree, attachments w/ add / rename / delete) | 🔄/📋 See flag ⚠️-3: user accounts cross the Ruby ↔ Sapphire boundary |
| (Organizational) Structure | A member who belongs to a department must not appear as a manager on members of *other* departments **in the grouping logic only** (diagram rendering) | 🔄 Page exists (`structure.blade.php`, JointJS org chart); grouping rule not yet encoded |

### Personnel

| Area | Notion spec | Status |
| --- | --- | --- |
| Attendance table | Filterable: name, department(s), type (in/out), created at; **exportable to Excel/CSV per selected filters** | 🔄 Table + filters exist (incl. punch-type filter); ❌ export not built |
| Attendance — punctuality column | "Punctuality" column showing "on time" / "X minutes late" / "X minutes early", color coded: late-Ins & early-Outs **red** over a configurable threshold, **blue** at/below it; on-time **green**; early-Ins & late-Outs **bold green** | ❌ Not built (no punctuality/latency rendering in views or controllers). Threshold "set in settings" — see flag ⚠️-5 |
| Attendance — DB modifications | `r_shifts` (start/end strings, 24h); pivot `r_contact_shifts` w/ weekday tinyint → `lc_contacts`; `r_punches.latency` smallint + closest-shift lookup | ✅ Implemented (`create_shifts_table` + `add_latency_to_punches`: `latency` smallint, nullable `shift_id`) — **this "required" list is already done, do not redo** |
| Notices | Listed only | 📋 `Notice` model exists; no controller/views |
| Leaves | Listed only | 📋 `Leave` model exists; no controller/views |
| Visas | Listed only | ⚠️ **No `Visa` model exists at all** — forgotten from schema or dropped? (flag ⚠️-4) |

### Payroll

Components / Salaries / Payments — listed only. 📋 Models exist
(`PayrollComponent` + locale, `Salary`, `SalaryComponent`, `PayrollPayment`, `PaymentComponent`);
no controllers or views.

### Recruitment

Vacancies / Applicants / Documents — listed only. 📋 Models exist (`Vacancy` + locale,
`Applicant`, `Degree` + locale, `Document`); only `DocumentController` exists.

Notion notes (verbatim intent):

- All employees are handled via / linked with **contacts**; applicants hold the *initial*
  employee data captured when they applied or were recruited.
- Applicant documents (employment credentials, certificates) keep being updated after
  recruitment, so they end up **more current** than the rest of the applicant record.
- ⚠️ Notion says "Sapphire's contacts module" — contacts actually live in **Lava Common**
  (`lc_contacts`). Terminology slip or intended move? (flag ⚠️-2)

## Sapphire

All features below are 📋 **unbuilt** (models ready: `Tenant`, `User`, `Role`, `RoleLocale`,
`Subscription`, `Module`, `Purchase`):

- **Users management for tenants** (CRUD for business owners to manage their business
  users) + a users list for the super admin.
- **Tenant dashboard**: current subscription (expiration / remaining days & months),
  covered modules, total users, total contacts.
- **User dashboard** (manager role): everything from the tenant dashboard **except**
  subscription details.
- **Tenant impersonation** by the main tenant (super admin).
- **User impersonation** by the main tenant (super admin).
- Subscription-gated module access via impersonating the tenant's corresponding user
  accounts, with an explicit warning that the main tenant account is **different** from the
  modules user account (security measure).
- Submodules to consider: **Admin** (main admin), **Tenant** (business owners),
  **User** (platform standard users).
- ❌ Diagram change: replace the phone/email **polymorphic contactable** with a direct
  `contact_id` FK to the standard common contacts table — keeps main-database entities from
  referencing tenant-database tables. Not implemented in code.

## Lava

| Item | Status |
| --- | --- |
| Rewire `lc_addresses` → `lc_cities` (instead of `lc_countries`) + relationship remapping | ✅ Done (`fix_addresses_city_relationship`) |
| `lc_addresses`: `line_1`, `line_2` text nullable; `lat` decimal(10,8); `long` decimal(11,8) | ✅ Done — column types match the scribble exactly |
| `lc_measurement_units` (type enum: length/area/volume/weight/data/box/piece; `base_id` nullable; `factor` decimal(20,10) = 1 for roots) + `lc_measurement_unit_locales` (locale, name, symbol); only custom units editable | ✅ Done — note the scribble said `base_id` "unsigned tinyint", implemented as unsigned integer FK (correct; tinyint would overflow). System units seeded as non-editable |
| Restructure all "quantity unit" string columns to FKs on measurement units | ✅ Done |
| Cities data pipeline (OpenStreetMap) | 📋 External pipeline, not in repo — see appendix for the exact scribbled commands |

## Open flags — settle before building

1. **Stale TODOs**: Lava's and Attendance's "required modifications" look pending in Notion
   but are already implemented. Treat Notion as historical brainstorm; never re-do them.
2. **"Sapphire's contacts module"** — contacts live in Lava Common (`lc_contacts`).
   Terminology slip, or an intent to move them?
3. **Staff ↔ user accounts**: the Add/Edit Member spec creates usernames/passwords, which is
   Sapphire territory (constitution Principles I & IV). Needs a Ruby ↔ Sapphire contract
   decision before implementation.
4. **No Visa model** despite "Visas" being listed under Personnel — schema gap or dropped
   feature?
5. **Punctuality threshold "set in settings"** — Ruby has no settings infrastructure yet;
   decide where settings live before building the punctuality column.
6. **Sapphire impersonation + subscription gating** is architecturally heavy (tenancy and
   module access control) — deserves a dedicated spec, not a side task.

## Appendix — verbatim Notion export

::: details Diamond — full tree (2026-10-30)
```text
# Diamond
├── Onyx: CRM, Inventory and, Production
├── Amethyst: E-commerce and online store.
├── Topaz: Accounting and Financial Management.
├── Emerald: Project Management
├── Ruby: Human Resources Module
│   ├── Organization
│   │   ├── Departments
│   │   ├── Staff
│   │   │   ├── The Table
│   │   │   ├── Member Profile
│   │   │   └── Add / Edit Member
│   │   └── (Organizational) Structure
│   ├── Personnel
│   │   ├── Attendance
│   │   ├── Notices
│   │   ├── Leaves
│   │   └── Visas
│   ├── Payroll
│   │   ├── Components
│   │   ├── Salaries
│   │   └── Payments
│   └── Recruitment
│       ├── Vacancies
│       ├── Applicants (recruits + employment credentials)
│       └── Documents
├── Sapphire: Tenants, users and, privileges.
└── Lava: Common resources and entities between the mentioned modules
```
:::

::: details Ruby — Departments (verbatim)
A filterable table that consists of: Name, Manager, Members count, Vacancies count,
Created At, Actions (Edit, Staff, Delete with confirmation). And an option to create a new
department with fields: Name (localized in Arabic and English), Manager, Staff
(subordinates). Department creation fields should be the same as department edit.
:::

::: details Ruby — Staff (verbatim)
**The Table**: a filterable table consists of Name, Default email, Default phone, Role,
Department(s), Recruited At, Actions (Profile, Edit, Delete with confirmation).

**Member Profile**: the profile action opens a profile modal with following details: Avatar
(image or initials), Name, Emails, Phones, Addresses, Job, Role, Tenure, Department(s),
Recruited At, Degree, Attachments (employment credentials and documents), Approved leaves
(timeline of latest 5).

**Add / Edit Member**: opens a modal with the following fields — User Account (Profile
Image, username, password beside confirmation); Contact Details (Name, Emails, Phones,
Addresses); Employment Details (Role — affects member access to features, Managed
Departments, Member of Department, Highest Educational Degree, Attachments — add, edit
filename, delete).
:::

::: details Ruby — Structure (verbatim)
When a member belongs to a department he should not be included as a manager on other
members of other departments - in the department grouping logic only of the structure
diagram.
:::

::: details Ruby — Attendance (verbatim)
A filterable table that's consisted of: Name, Department(s), Type (in / out), Created At.
It's beneficial to make the table exportable in an excel or csv format, according to the
selected filters.

**Crucial Required Modifications** (already implemented — see status table):
- Creating r_shifts table and associated model in Ruby module.
  - Includes start and end columns of type string storing 24 hour format of shift timings.
  - Optionally related to zero or more of lc_contacts via a pivot r_contact_shifts
    including weekday as pivot data of type unsigned tinyint.
  - Attendance r_punches should include latency field of type smallint storing number of
    early / late minutes of a punch based on the closest r_shifts record.
- Attendance data table required changes:
  - Adding "Punctuality" column showing values like "on time", "X minutes late" or,
    "X minutes early", color coded based on whether the punch is "In" or "Out" e.g:
    - Late Ins OR Early Outs should be: Red if they're over a fixed threshold (can be set
      in settings); Blue If they're equal or below the mentioned threshold.
    - On time should be Green.
    - Early Ins OR Late Outs should be Bold Green.
:::

::: details Ruby — Notes (verbatim)
All employees are handled via and / or linked with Sapphire's contacts module, applicants
however are the employees initial data when they applied to join - or recruited by - the
organization.

Applicant's documents - employment credentials and certificates - might be updated after
recruitment nonetheless so, they'll be more current than other applicant's data.
:::

::: details Sapphire (verbatim)
Tenants, users and, privileges.

Features to Develop:
- Users Management for Tenants - with users list for super admin
  - Users CRUD to be used for tenants (business owners) to manager their business users.
- Tenant Dashboard, shows the following: Current subscription with expiration date and /
  or remaining days and months; Current subscription covered modules; Total Number of
  users; Total Number of contacts.
- User Dashboard (with manager role): Shows everything from Tenant's dashboard except for
  subscription details.
- Tenant Impersonation by main tenant (super admin)
- User impersonation by main tenant (super admin)
- Enable tenants to access modules they're subscribed in via impersonating their
  corresponding user accounts.
  - Warn tenants that their main tenant account is different from their modules user
    account as an extra security measure.

Main submodules to consider: Admin for main admin; Tenant for business owners subscribed
in the platform; User for platform standard users.

Update sapphire diagram changing phone and email polymorphic contactable to direct foreign
key "contact_id" referring to the standard common contacts table - to avoid relating main
database entities / tables to those in tenant's database.
:::

::: details Lava (verbatim)
- Rewire lc_addresses to lc_cities instead of lc_countries. Handle underlying
  relationships remapping between involved corresponding entities.
- Add the following fields to lc_addresses table: line_1 and line_2 of type text default
  null; lat of type decimal (10,8) default null; long of type decimal (11,8) default null.
- Create lc_measurement_units table and corresponding Eloquent model:
  - type enumeration of length, area, volume, weight, data, box and, piece.
  - base_id The ID of the base measurement unit - nullable unsigned tinyint (root unit).
  - factor of type decimal (20,10) with value of 1 for root units.
  - Include lc_measurement_unit_locales for translation support: locale, name, symbol.
  - The system should allow adding / editing only measurement units set as custom units.
- All Quantity Unit fields in the system should be restructured to use one of the
  measurement units provided by the lc_measurement_units table created.
  - The quantity unit fields are currently set as string columns.

Cities pipeline scribble (OpenStreetMap):

```bash
osmosis --read-pbf asia-latest.osm.pbf \
  --tf accept-nodes place=city,town,village \
  --tf accept-nodes name:en='*' name:ar='*' \
  --tf reject-ways --tf reject-relations \
  --write-xml asia-cities.osm

osmconvert asia-cities.osm --csv-headline \
  --csv='@id name:en name:ar short_name:en short_name:ar addr:country' \
  --csv-separator=',' -o=asia-cities.csv
```

Reference: [nominatim-docker OpenStreetMap data extracts]
(https://github.com/mediagis/nominatim-docker/tree/master/4.4#openstreetmap-data-extracts)
:::
