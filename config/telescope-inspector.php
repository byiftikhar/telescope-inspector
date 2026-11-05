<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Turns the inspector page on or off. The page also needs Telescope itself
    | to be enabled, because all data comes from Telescope's own API.
    |
    */

    'enabled' => env('TELESCOPE_INSPECTOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Path
    |--------------------------------------------------------------------------
    |
    | The URI the inspector page is served from.
    |
    */

    'path' => env('TELESCOPE_INSPECTOR_PATH', 'telescope-inspector'),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | null reuses config('telescope.middleware'), so the page is protected by
    | exactly the same rules as Telescope (the viewTelescope gate).
    |
    */

    'middleware' => null,

    /*
    |--------------------------------------------------------------------------
    | Batch sizes
    |--------------------------------------------------------------------------
    |
    | default_take: entries fetched per request (10, 25 or 50).
    | match_target: matches collected per Search / "keep searching" click.
    |
    */

    'default_take' => 25,

    'match_target' => 50,

    /*
    |--------------------------------------------------------------------------
    | Prune
    |--------------------------------------------------------------------------
    |
    | The "Prune" button deletes entries in a date range (server time). It is
    | protected by the same middleware as the page. To limit it further, define
    | a "pruneTelescope" gate; when that gate exists it must also allow the user.
    | chunk_size: rows deleted per statement, like Telescope's own prune.
    |
    */

    'prune' => [
        'enabled' => env('TELESCOPE_INSPECTOR_PRUNE', true),
        'chunk_size' => 1000,
    ],

];
