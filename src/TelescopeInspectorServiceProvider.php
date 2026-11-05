<?php

namespace ByIftikhar\TelescopeInspector;

use ByIftikhar\TelescopeInspector\Http\Controllers\InspectorController;
use ByIftikhar\TelescopeInspector\Http\Controllers\PruneController;
use ByIftikhar\TelescopeInspector\Pruning\EntryPruner;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TelescopeInspectorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/telescope-inspector.php', 'telescope-inspector');

        $this->app->bind(EntryPruner::class, fn (): EntryPruner => new EntryPruner(
            (int) config('telescope-inspector.prune.chunk_size', 1000),
        ));

        // Runs after every package has merged its config but before Telescope's own
        // boot decides whether to record the current request.
        $this->app->booting(fn () => $this->hideInspectorFromTelescope());
    }

    /**
     * The inspector's own page and endpoints are tooling, not application traffic:
     * add them to Telescope's ignore_paths so they never show up as entries.
     */
    private function hideInspectorFromTelescope(): void
    {
        if (! config('telescope-inspector.enabled')) {
            return;
        }

        $path = trim(config('telescope-inspector.path'), '/');
        $ignored = config('telescope.ignore_paths', []);

        config(['telescope.ignore_paths' => array_values(array_unique([...$ignored, $path, $path.'/*']))]);
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
            $this->registerRoutes();
        }
    }

    private function registerRoutes(): void
    {
        $path = trim(config('telescope-inspector.path'), '/');

        Route::middleware($this->middleware())
            ->domain(config('telescope.domain'))
            ->group(function () use ($path): void {
                // One page; {view} is the client-side route (e.g. requests/<uuid>/<batch>) so links can be refreshed or shared.
                Route::get($path.'/{view?}', InspectorController::class)->where('view', '.*')->name('telescope-inspector');

                if (config('telescope-inspector.prune.enabled')) {
                    Route::post($path.'/prune/preview', [PruneController::class, 'preview'])->name('telescope-inspector.prune.preview');
                    Route::post($path.'/prune', [PruneController::class, 'destroy'])->name('telescope-inspector.prune');
                }
            });
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
