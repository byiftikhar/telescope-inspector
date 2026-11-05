<?php

namespace ByIftikhar\TelescopeInspector\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class InspectorController
{
    public function __invoke(): View
    {
        return view('telescope-inspector::inspector', [
            'settings' => [
                // Relative URLs, so the page works whatever host it is opened on.
                'telescopeUrl' => Route::has('telescope') ? rtrim(route('telescope', [], false), '/') : null,
                'loginUrl' => Route::has('login') ? route('login', [], false) : null,
                'defaultTake' => (int) config('telescope-inspector.default_take', 25),
                'matchTarget' => (int) config('telescope-inspector.match_target', 50),
            ],
        ]);
    }
}
