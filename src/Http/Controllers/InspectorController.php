<?php

namespace ByIftikhar\TelescopeInspector\Http\Controllers;

use Composer\InstalledVersions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use OutOfBoundsException;

class InspectorController
{
    private const PACKAGE = 'byiftikhar/telescope-inspector';

    public function __invoke(): View
    {
        return view('telescope-inspector::inspector', [
            'settings' => [
                // Relative URLs, so the page works whatever host it is opened on.
                'baseUrl' => rtrim(route('telescope-inspector', [], false), '/'),
                'telescopeUrl' => Route::has('telescope') ? rtrim(route('telescope', [], false), '/') : null,
                'loginUrl' => Route::has('login') ? route('login', [], false) : null,
                'defaultTake' => (int) config('telescope-inspector.default_take', 25),
                'matchTarget' => (int) config('telescope-inspector.match_target', 50),
                // Telescope stores server time; the page uses this clock for prune presets.
                'serverNow' => now()->format('Y-m-d H:i:s'),
                'timezone' => config('app.timezone'),
                'version' => $this->packageVersion(),
                'prune' => $this->pruneSettings(),
            ],
        ]);
    }

    /**
     * The installed release (e.g. "v1.1.0"); null for a dev checkout without a tag.
     */
    private function packageVersion(): ?string
    {
        try {
            $version = InstalledVersions::getPrettyVersion(self::PACKAGE);
        } catch (OutOfBoundsException) {
            return null;
        }

        return $version !== null && preg_match('/^v?\d/', $version) === 1 ? $version : null;
    }

    /**
     * @return array{previewUrl: string, url: string}|null
     */
    private function pruneSettings(): ?array
    {
        if (! Route::has('telescope-inspector.prune')) {
            return null;
        }

        return [
            'previewUrl' => route('telescope-inspector.prune.preview', [], false),
            'url' => route('telescope-inspector.prune', [], false),
        ];
    }
}
