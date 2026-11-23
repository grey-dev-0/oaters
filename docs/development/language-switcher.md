# Language Switcher (EN / العربية)

The interface language mechanism is owned by the **Common (Lava)** module so every
business module inherits it without per-module work. The durable preference is
stored on the platform account (Sapphire-owned `s_users.locale`), reached only
through the account boundary.

## Usage

Every navbar-rendered page shows a language dropdown at the **start of the right
slot** — directly before the username dropdown. It offers English / العربية,
marks the active language, posts to `POST /locale`, and reloads the page.

The login page stays English by design: no switcher is rendered there and guest
sessions carry no preference.

## Persistence model

| Store | Role |
|-------|------|
| `s_users.locale` | Durable per-account preference; applied at the next sign-in of every session |
| `session('locale')` | Working copy for the live session; wins for every request once set |

Request resolution (`Modules\Common\App\Http\Middleware\SetAppLocale`, registered
in the `web` group right after `StartSession`):

1. Guest request → any leftover working copy is dropped, default English.
2. Session has a value → that value wins (no account read).
3. Session has none and the user is authenticated → seeded **once** from the
   account preference through the `LocalePreference` contract and cached in the
   session.

Switching (`Modules\Common\App\Http\Controllers\LocaleController`) validates
`locale ∈ {en, ar}` and updates **both** stores together. A live session is
never disturbed by switches on other devices: it keeps its working language
until the next sign-in.

### Constitution note

Common never imports Sapphire (Principle III). The middleware and controller
depend on the Common-owned `LocalePreference` interface; Sapphire binds its
`AccountLocalePreference` adapter (which delegates to `AccountManager`) in its
module service provider.

## Blade / client integration

`common::resources` injects a `uiLocale` global next to the page-level `locale`
string bundles:

```javascript
uiLocale = { lang, dir, label, switchUrl, datatable: {...}, picker: {...} }
```

Generic components read only `uiLocale`: DataTables chrome language, the
clear-filters label, and daterangepicker localization (Arabic months/days,
RTL popup side). Layouts adopt `@include('common::html-attrs')` on the
`<html>` tag for dynamic `lang`/`dir` attributes (Ruby's master does).

## RTL build step

`npm run development` / `npm run production` run `scripts/build-rtl.mjs` after
the Vite build: every stylesheet emitted into `public/build/assets` is mirrored
by **rtlcss** into a `.rtl.css` sibling. When the active direction is RTL,
`common::rtl-styles` loads the theme's mirrored variant plus the
`rtl-overrides` entry; English pages load neither (byte-identical to before
the feature).

`resources/scss/rtl-overrides.scss` carries hand-authored `[dir="rtl"]` rules
for what the mirror cannot reach — margin-utility neutralizers, DataTables
chrome floats, select2 alignment, breadcrumb spacing, LTR direction for
email/phone inputs, and `unicode-bidi: plaintext` isolation so English values
inside RTL table cells stay readable.

Adding a new navbar-rendered module needs nothing extra; adding new page
strings means adding the Arabic catalog keys under
`Modules/{Module}/resources/lang/ar/`.
