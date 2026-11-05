# Telescope Inspector

A lightweight, searchable viewer for [Laravel Telescope](https://laravel.com/docs/telescope).

Telescope's own screen loads 50 entries at a time **with their full content** (request payload, headers,
session, response). One heavy request on a page can make that page several MB, and the screen stops
loading. It also cannot search by route, job, status or user.

This package adds one page, `/telescope-inspector`, that reads **Telescope's own API** in small
batches, keeps only a short summary of each entry, and lets you search and filter. It adds no tables,
migrations or jobs and changes nothing in how Telescope records.

## Features

- Tabs for Requests, HTTP Client, Jobs, Commands, Schedule, Exceptions, Logs, Queries, Mail,
  Notifications, Events, Cache and Redis.
- Fetches 10, 25 or 50 entries at a time; big entries are never drawn in the list.
- Search all words (route, controller, job class, SQL, message), plus filters: method, status
  (2xx to 5xx), user (name, email or id), job status, log level, minimum duration, from/to time and
  Telescope tag. Scans older entries until 50 matches are found, with a Stop button.
- Detail page: overview, collapsible sections (anything over 20 KB starts closed, over 50 KB shows a
  "Show all" button), copy buttons, and everything recorded in the same batch (queries with total time,
  slow count and repeated statements / possible N+1, logs, exceptions, jobs, HTTP calls...).
  For a job it also shows what ran inside the worker.
- Shareable links: every list and entry has its own clean URL (`/telescope-inspector/requests`,
  `/telescope-inspector/requests/<uuid>/<batch>`); refresh, Back/Forward and Ctrl-click work as usual.
- Light and dark theme: slate in light mode, a
  GitHub-style "night" palette in dark mode, sky accent, Inter + JetBrains Mono. The header switch cycles
  Auto (follows the operating system), Light and Dark, and is remembered per browser. Press `/` to jump
  to the search box.
- Its own page and endpoints are added to `telescope.ignore_paths` automatically, so using the
  inspector never fills Telescope with entries about the inspector.
- Prune: delete entries in a date range (server time), optionally only some entry types and keeping
  exceptions. Quick ranges (older than 1 hour / 6 hours / 1 day / 7 days, or all entries), a live count
  of what will be deleted, and a confirmation before anything is removed.

## Requirements

- PHP 8.2+
- Laravel 11, 12 or 13
- `laravel/telescope` 5.x, installed and enabled

## Installation

The package is private, so add its git repository to the project's `composer.json` once:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:byiftikhar/telescope-inspector.git"
    }
]
```

Colleagues without an SSH key on GitHub can use `https://github.com/byiftikhar/telescope-inspector.git`
instead. If the repository is private, each of them needs read access to it.

Then install it the same way Telescope is installed in that project:

```bash
# Telescope is a dev dependency (local only):
composer require byiftikhar/telescope-inspector --dev

# Telescope also runs on UAT / production:
composer require byiftikhar/telescope-inspector
```

The service provider is discovered automatically. Open `https://your-app/telescope-inspector`.

## Access

The page uses **the same middleware as Telescope** (`config('telescope.middleware')`), so whoever can
open `/telescope` can open the inspector, and nobody else. Outside the `local` environment that is
decided by the `viewTelescope` gate in `app/Providers/TelescopeServiceProvider.php`. Make sure that gate
does not simply `return true`, or every signed-in user can read every recorded request.

## Pruning

The Prune button calls two endpoints the package adds next to the page (with the same middleware):
`POST {path}/prune/preview` (how many entries match) and `POST {path}/prune` (delete them). It deletes
from Telescope's `telescope_entries` table in chunks, the same way `php artisan telescope:prune` does,
and the entries' tags go with them. Every prune is written to the application log
("Telescope Inspector: entries pruned", with the user id, range, types and count).

- Needs Telescope's `database` storage driver.
- "To" is a whole minute and is included: `To 10:30` removes everything up to 10:30:59.
- To let only some people prune, define a `pruneTelescope` gate in the project, for example in
  `app/Providers/TelescopeServiceProvider.php`:

  ```php
  Gate::define('pruneTelescope', fn ($user) => $user->hasRole('Super Admin'));
  ```

  When that gate exists the user must pass it as well as the Telescope gate. Without it, anyone who can
  open Telescope can prune (Telescope's own screen can already clear everything).
- To remove the feature entirely, set `TELESCOPE_INSPECTOR_PRUNE=false`.

## Configuration

Optional. Publish the config file only if you need to change something:

```bash
php artisan vendor:publish --tag=telescope-inspector-config
```

| Key | Env | Default | Meaning |
| --- | --- | --- | --- |
| `enabled` | `TELESCOPE_INSPECTOR_ENABLED` | `true` | Register the page at all. |
| `path` | `TELESCOPE_INSPECTOR_PATH` | `telescope-inspector` | URL of the page. |
| `middleware` | | `null` | `null` reuses Telescope's middleware; or give your own array. |
| `default_take` | | `25` | Entries fetched per request (10, 25 or 50). |
| `match_target` | | `50` | Matches collected per Search / "Keep searching" click. |
| `prune.enabled` | `TELESCOPE_INSPECTOR_PRUNE` | `true` | Show the Prune button and register its endpoints. |
| `prune.chunk_size` | | `1000` | Rows deleted per statement while pruning. |

Telescope's own `path` and `domain` settings are picked up automatically.

## How it stays fast, and its limits

- Lists call `POST {telescope}/telescope-api/{type}` with a small `take` and a `before` cursor.
- Telescope's detail endpoint also returns **every** entry of the batch with no limit (thousands of
  queries for a heavy request), so the page asks the list endpoint filtered by `batch_id` instead and
  loads related entries 25 at a time.
- Telescope's API can only filter by **tag** on the server. A numeric user id is sent as Telescope's
  `Auth:<id>` tag; every other filter runs in the browser over the fetched batches, newest first. A
  search for something rare may page through many entries before it finds a match.
- An entry is still downloaded in full when it is part of a fetched batch; it is just never rendered.
- Times are the server's time, as Telescope stores them.

## Front-end assets

Bootstrap 5.3.3, Remix Icon 4.5.0, bootstrap-select 1.14.0-beta3, flatpickr 4.6.13 (plus its dark theme),
SweetAlert2 11.22.5 and jQuery 3.7.1 (plus the Inter and JetBrains Mono fonts from Google Fonts) are
loaded from jsDelivr / code.jquery.com with exact versions and SRI hashes. Nothing needs to be
published or built. The viewer's browser needs internet access.

All recorded data is rendered as text (`textContent`), never as HTML.

## Development

Where things are (`resources/views/`):

| File | Contents |
| --- | --- |
| `inspector.blade.php` | Page shell: CDN assets, settings JSON, includes the partials below. |
| `partials/styles.blade.php` | All CSS (brand colours as `--ti-*` variables). |
| `partials/theme-boot.blade.php` | Applies the saved theme before first paint. |
| `partials/header.blade.php` | Top band (Prune and theme buttons), type tabs, main container. |
| `partials/prune-modal.blade.php` | Prune dialog markup. |
| `partials/list-template.blade.php` | Filter form and results table (`<template>`). |
| `partials/scripts/config.blade.php` | Reads the settings JSON, constants. |
| `partials/scripts/helpers.blade.php` | DOM builder, badges, formatting, API client, errors. |
| `partials/scripts/entry-types.blade.php` | One definition per Telescope type: summary fields and columns. |
| `partials/scripts/list-state.blade.php` | Filters, matching, the batch scanner. |
| `partials/scripts/list-view.blade.php` | Filter form wiring and list rendering. |
| `partials/scripts/detail-view.blade.php` | Single entry page and its sections. |
| `partials/scripts/related.blade.php` | "Recorded in the same batch" tabs. |
| `partials/scripts/theme.blade.php` | Theme switch (auto / light / dark). |
| `partials/scripts/prune.blade.php` | Prune dialog: presets, live count, confirm, delete. |
| `partials/scripts/router.blade.php` | Clean URLs via the History API (`{path}/type`, `{path}/type/uuid/batch`). |

PHP: `src/Http/Controllers` (page and prune endpoints), `src/Http/Requests/PruneRequest.php`
(validation and the optional `pruneTelescope` gate), `src/Pruning` (`PruneFilter`, `EntryPruner`).

All script partials run inside one function scope opened in `inspector.blade.php`, so they share
helpers without globals. Each CSS/JS partial is wrapped in `@verbatim` so Blade never touches `{{ }}`
or `@` inside it; keep that when adding a partial.


```bash
composer install
composer test
```

Tests run on Orchestra Testbench (checked on Laravel 13 / Telescope 5.27 and, with
`composer update --prefer-lowest`, Laravel 12 / Telescope 5.5).

## Updating a project

Tag a release in this repository (`git tag v1.1.0 && git push --tags`), then in each project:

```bash
composer update byiftikhar/telescope-inspector
```
