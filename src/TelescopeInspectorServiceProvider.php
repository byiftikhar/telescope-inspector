<?php

namespace ByIftikhar\TelescopeInspector;

use ByIftikhar\TelescopeInspector\Http\Controllers\InspectorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TelescopeInspectorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/telescope-inspector.php', 'telescope-inspector');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'telescope-inspector');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telescope-inspector.php' => config_path('telescope-inspector.php'),
            ], 'telescope-inspector-config');
        }

        if (config('telescope-inspector.enabled')) {
            $this->registerRoute();
        }
    }

    private function registerRoute(): void
    {
        Route::middleware($this->middleware())
            ->domain(config('telescope.domain'))
            ->get(config('telescope-inspector.path'), InspectorController::class)
            ->name('telescope-inspector');
    }

    /**
     * The inspector's own middleware, or Telescope's when none is configured.
     *
     * @return array<int, string>
     */
    private function middleware(): array
    {
        return config('telescope-inspector.middleware') ?? config('telescope.middleware', ['web']);
    }
}
