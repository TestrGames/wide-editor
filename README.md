# Wide File Editor

Pelican Panel plugin that makes the server file editor (Monaco) use the
full width and height of the browser window, toggleable via a floating
button (bottom-right, ⛶). State is remembered per-browser in
`localStorage`.

## How it works

The plugin injects CSS that, when `html` has the `wide-editor-on` class,
overrides `max-width` on `main.fi-main` and forces the height of the
`.fme-wrapper` / `.fme-container` / `.fme-element-wrapper` / `.fme-element`
chain (Pelican's own Monaco field markup, as of panel `main`/beta37,
Filament v5) with `!important`. All levels have to be overridden together
— overriding just one leaves an inner level at its default `height: 100%`
while an outer level with `overflow: hidden` clips it back down.

The render hook is registered with
`scopes: App\Filament\Server\Resources\Files\Pages\EditFiles::class`, so
it only ever renders on the file editor page — not the login screen, not
the admin panel, not other server pages. This matters because plugin
service providers are otherwise loaded globally regardless of the
`panels` field in `plugin.json` (that field only restricts the main
`Plugin` class, not raw `ServiceProvider::boot()` hooks).

**We deliberately do *not* use Pelican's own built-in (but unexposed)
`fullScreenModeEnabled` / `toggleFullScreenMode()` / `.fme-full-screen`
mechanism** — it exists in the blade but its button is commented out,
and its CSS hardcodes `position: fixed; left: 300px; top: 64px`, which
doesn't line up with every layout/sidebar state and renders visibly
broken. Not worth depending on an unfinished upstream feature.

The CSS selectors target Pelican's own `.fme-*` Monaco field markup, not
a stable public API — a future panel release could rename them. If the
button stops toggling the size correctly, check
`resources/views/filament/components/monaco-editor.blade.php` and
`resources/css/monaco-editor.css` in the panel repo for what changed.

## Install

1. Download the latest `wide-editor.zip` from
   [Releases](https://github.com/TestrGames/wide-editor/releases) and
   upload it via the panel's plugin import screen (Admin → Plugins →
   Import). Or `wget`/`curl` it directly into
   `/var/www/pelican/plugins/wide-editor` on the panel server and unzip.
2. `php artisan p:plugin:install` and pick `wide-editor` (skip if
   installed via the UI).
3. `php artisan optimize:clear`

## Updating

Once installed from a release that has `update_url` set in `plugin.json`
(everything from v1.0.0 onward), the panel checks
[`update.json`](update.json) for a newer version and shows an **Update**
button in Admin → Plugins when one exists — no manual re-upload needed.

To publish a new version:

1. Bump `"version"` in `plugin.json`.
2. Commit, then tag and push: `git tag vX.Y.Z && git push origin vX.Y.Z`
   (the tag must match `plugin.json`'s version exactly, e.g. `1.2.0` →
   `v1.2.0` — the release workflow checks this and fails otherwise).
3. [`.github/workflows/release.yml`](.github/workflows/release.yml) takes
   it from there: builds the zip, creates the GitHub Release with it
   attached, and rewrites `update.json` to point at it — all pushed back
   to `main` automatically.

`raw.githubusercontent.com` caches for a few minutes, so the panel might
not see a brand-new release immediately.

## Development

The CSS/JS lives inline in
[src/Providers/WideEditorPluginProvider.php](src/Providers/WideEditorPluginProvider.php).
After editing it on the panel server, run `php artisan view:clear` (and
`optimize:clear` if the change doesn't show up) and reload the page.
