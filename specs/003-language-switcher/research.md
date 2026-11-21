# Research: Language Switcher (EN/AR + RTL)

**Feature**: 003-language-switcher | **Date**: 2026-11-19 | **Branch**: cut from `002-attendance-punctuality-export`

## Existing anatomy (verified in code)

### Navbar — where the switcher lands
- `resources/components/navbar/navbar.vue`: brand + collapsible + `<ul class="navbar-nav">` (slot)
  and `<ul class="navbar-nav ml-auto"><slot name="right">`. Module masters place the username
  dropdown (a `nav-item dropdown`) into the `right` slot — Ruby's master is currently the only
  navbar'd layout; Sapphire's admin master is central/out of scope, no other tenant navbar exists.
- **Placement decision**: render the switcher inside `navbar.vue` at the *start* of the right
  slot — it appears on every navbar'd page automatically, directly before whatever the module
  puts there (the username dropdown), satisfying FR-001 without per-module edits. The component
  stays business-agnostic (generic Vue per Principle II).
- `nav-item`/dropdown pattern (`navbar/item.vue`) gives the dropdown styling precedent.

### Locale state today
- `config('app.locale') = 'en'`, fallback `en`. Nothing sets it per-request; only model scopes
  read it (`app()->getLocale()` in Contact/Department/… locale filters) — so once the middleware
  sets the app locale, localized record data follows automatically.
- Arabic catalogs exist for Ruby/Common strings (incl. attendance plurals); `s_role_locales`,
  `r_department_locales`, `r_degree_locales`, `lc_city locales` carry `ar` rows; exports build
  headers/values through `trans()`/locale-aware renderers — they honor the active locale with no
  code change (needs test coverage only).
- Blade passes JS state via a `locale` global (`var locale = {}` in
  `Modules/Common/resources/views/resources.blade.php`) — extend it with the current language,
  direction, and switch endpoint for client components.

### HTTP pipeline
- `app/Http/Kernel.php` `web` group: EncryptCookies → AddQueuedCookies → **StartSession** →
  ShareErrors → VerifyCsrf → SubstituteBindings. A locale middleware must sit **after
  StartSession**; registration is one line in the core Kernel pointing at a Common-owned
  middleware class (mechanism ownership stays in Lava per FR-009).
- `Modules/Common` has **no RouteServiceProvider** today (empty routes file) — the switch
  endpoint needs one, following the Ruby/Sapphire provider pattern (no subdomain-tenancy
  middleware needed: locale switching is tenant-agnostic and works on any authenticated page).

### Account boundary (where the preference lives — clarification Q4)
- Preference column goes on **`s_users`** (Sapphire-owned): every account has a contact, not
  every contact has an account (user's rationale). Reads/writes route through the existing
  `AccountManager` contract (`Modules/Sapphire/App/Services/AccountManager.php` — already
  exposes `username(int $contactId)`; add `locale()`/`setLocale()` alongside). The middleware
  resolves the contact id once per session-seed via `auth()->user()->contact->id`.

### Styles / RTL pipeline
- Single themed SCSS entry `resources/scss/bootstrap.scss` (Bootstrap 4.6 source + custom theme
  colors); Vite auto-discovers `resources/scss/*.scss` and `resources/js/**` as inputs;
  per-component styles live inside Vue SFCs (async-loaded, injected at runtime).
- Bootstrap 4 has **no official RTL**. Standard approach: **rtlcss** post-processing of the
  compiled stylesheet → a `.rtl.css` sibling; load it conditionally when the page direction is
  RTL. Precedent for non-manifest CSS: fontawesome is served via `asset('css/…')` outside Vite.
- A small build script (`scripts/build-rtl.mjs`) reads `public/build/.vite/manifest.json`,
  rtlcss-transforms every emitted CSS asset next to its source, so names stay manifest-derived;
  the layout helper derives the RTL URL from the LTR asset URL (`.css` → `.rtl.css`).
- SFC-specific directional rules (navbar `ml-auto`, breadcrumb separators, timeline connectors,
  modal footer order, form label columns) are **not covered by transforming the SCSS bundle** —
  they need a hand-authored `[dir="rtl"]` override layer appended to the RTL build.
- `rtlcss` is not installed yet (devDependency to add). `npm run development`/`production`
  scripts gain the RTL step.

### Widgets under RTL (QA targets)
- DataTables (`datatable.vue`): no language config today — DT chrome ("Search", "Showing x to y",
  pagination labels) is English-only; needs a language object fed from the `locale` global for
  FR-002 completeness. Column sorting arrows + scroll-X under RTL need visual QA.
- select2 (bootstrap4 theme), daterangepicker (has locale support — feed from global), bootbox
  confirmations (rendered with page direction), modals/tabs/forms (Bootstrap grid direction),
  JointJS org chart + chart.js (container-level RTL only per Q2=A — inner drawing stays LTR).

### Layouts
- `<html lang="en">` exists in: `ruby::master`, `sapphire::admin.master` (out of scope),
  `common::login` + `sapphire::login` (stay English per Q3=B). Ruby's master gains dynamic
  `lang`/`dir` attributes via a shared Common blade helper so future masters reuse it.

## Gaps this feature closes

1. `s_users.locale` column (Sapphire migration) + `AccountManager::locale()/setLocale()`.
2. `SetAppLocale` middleware (Common) registered in the web group: session value wins; account
   read only when the session has none (per Q1=C refinement); persists on switch (session +
   account updated together).
3. Switch endpoint `POST /locale` (Common RouteServiceProvider + controller, authenticated).
4. `language-switcher.vue` generic component rendered at the right-slot start of `navbar.vue`.
5. Dynamic `lang`/`dir` on layouts + conditional RTL stylesheet.
6. RTL build step (rtlcss + `[dir="rtl"]` override layer) wired into npm scripts.
7. DataTables language config + daterangepicker locale; widget RTL QA fixes.
8. Tests: middleware/persistence matrix, switch endpoint, direction attributes, AR export
   honor, fallback behavior.
