# Changelog

All notable changes to `byiftikhar/telescope-inspector` are documented here.

## v1.1.0 (unreleased)

### Added
- Developer-tool design with light and dark theme (Auto / Light / Dark switch, remembered per browser).
- Prune entries by date range, entry type and "keep exceptions", with quick ranges, a live count and a
  confirmation. Optional `pruneTelescope` gate; `TELESCOPE_INSPECTOR_PRUNE=false` turns it off.
- Clean URLs (`/telescope-inspector/requests/<uuid>/<batch>`); old `#/...` links still open.
- The inspector's own routes are added to `telescope.ignore_paths` automatically.
- Copy-URI button, coloured method badges, durations coloured by speed, `/` focuses search.
- Version badge in the header and the server timezone in the subtitle.

### Changed
- The URI column shows only the path; the full URI stays in the tooltip, copy button and detail page.
- Licensed under MIT.

### Fixed
- "Recording" shows "watcher off" when only the current tab's Telescope watcher is disabled.
- A link to an entry that no longer exists shows a clear message instead of "HTTP 404".

## v1.0.2

### Added
- Spinner while older entries load.

## v1.0.1

### Added
- Favicon.

### Fixed
- Underlined links in the header and card titles.

## v1.0.0

- First release: searchable Telescope viewer that reads Telescope's own API in small batches.
