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

];
