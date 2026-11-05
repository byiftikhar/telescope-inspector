<?php

namespace ByIftikhar\TelescopeInspector\Tests\Feature;

use ByIftikhar\TelescopeInspector\TelescopeInspectorServiceProvider;
use Illuminate\Http\Request;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeServiceProvider;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;

class TelescopeIgnoresInspectorTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TelescopeServiceProvider::class, TelescopeInspectorServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('telescope.ignore_paths', ['nova-api*']);
    }

    #[Test]
    public function the_inspector_routes_are_added_to_telescope_ignore_paths(): void
    {
        $this->assertSame(['nova-api*', 'telescope-inspector', 'telescope-inspector/*'], config('telescope.ignore_paths'));
        $this->assertFalse($this->telescopeWouldRecord('/telescope-inspector'));
        $this->assertFalse($this->telescopeWouldRecord('/telescope-inspector/prune/preview'));
        $this->assertTrue($this->telescopeWouldRecord('/orders'));
    }

    #[Test]
    #[DefineEnvironment('useCustomPaths')]
    public function a_custom_inspector_path_is_ignored_too(): void
    {
        // Neither path starts with the other, so Telescope's own "tools/telescope*" does not cover it.
        $this->assertFalse($this->telescopeWouldRecord('/debug/inspector'));
        $this->assertFalse($this->telescopeWouldRecord('/debug/inspector/prune'));
        $this->assertTrue($this->telescopeWouldRecord('/debug/other'));
    }

    #[Test]
    #[DefineEnvironment('disableInspector')]
    public function nothing_is_added_when_the_inspector_is_disabled(): void
    {
        $this->assertSame(['nova-api*'], config('telescope.ignore_paths'));
    }

    protected function useCustomPaths($app): void
    {
        $app['config']->set('telescope.path', 'tools/telescope');
        $app['config']->set('telescope-inspector.path', 'debug/inspector');
    }

    protected function disableInspector($app): void
    {
        $app['config']->set('telescope-inspector.enabled', false);
    }

    /**
     * Telescope's own check, the one it runs before recording a request.
     */
    private function telescopeWouldRecord(string $uri): bool
    {
        $method = new ReflectionMethod(Telescope::class, 'requestIsToApprovedUri');

        return $method->invoke(null, Request::create($uri));
    }
}
