<?php

namespace ByIftikhar\TelescopeInspector\Tests\Feature;

use ByIftikhar\TelescopeInspector\TelescopeInspectorServiceProvider;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeServiceProvider;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class InspectorPageTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [TelescopeServiceProvider::class, TelescopeInspectorServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('k', 32)));
    }

    protected function tearDown(): void
    {
        Telescope::auth(null);

        parent::tearDown();
    }

    #[Test]
    public function it_renders_the_page_with_its_settings_for_users_telescope_allows(): void
    {
        Telescope::auth(fn (): bool => true);

        $response = $this->get('/telescope-inspector');

        $response->assertOk();
        $response->assertSee('<title>Telescope Inspector</title>', false);
        $response->assertSee('<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,', false);
        $response->assertSee('"telescopeUrl":"\/telescope"', false);
        $response->assertSee('"defaultTake":25', false);
        $response->assertSee('"matchTarget":50', false);
        $response->assertDontSee('@verbatim', false);
        $response->assertSee('@media (max-width: 768px)', false);
    }

    #[Test]
    public function it_is_forbidden_when_telescope_denies_the_user(): void
    {
        Telescope::auth(fn (): bool => false);

        $this->get('/telescope-inspector')->assertForbidden();
    }

    #[Test]
    #[DefineEnvironment('useCustomSettings')]
    public function it_honours_the_configured_path_and_batch_sizes(): void
    {
        Telescope::auth(fn (): bool => true);

        $this->get('/telescope-inspector')->assertNotFound();

        $response = $this->get('/debug/inspector');

        $response->assertOk();
        $response->assertSee('"telescopeUrl":"\/tools\/telescope"', false);
        $response->assertSee('"defaultTake":10', false);
    }

    #[Test]
    #[DefineEnvironment('disableInspector')]
    public function it_registers_no_route_when_disabled(): void
    {
        Telescope::auth(fn (): bool => true);

        $this->get('/telescope-inspector')->assertNotFound();
    }

    #[Test]
    #[DefineEnvironment('disableTelescope')]
    public function it_tells_the_user_when_telescope_itself_is_disabled(): void
    {
        Telescope::auth(fn (): bool => true);

        $response = $this->get('/telescope-inspector');

        $response->assertOk();
        $response->assertSee('"telescopeUrl":null', false);
    }

    protected function useCustomSettings($app): void
    {
        $app['config']->set('telescope.path', 'tools/telescope');
        $app['config']->set('telescope-inspector.path', 'debug/inspector');
        $app['config']->set('telescope-inspector.default_take', 10);
    }

    protected function disableInspector($app): void
    {
        $app['config']->set('telescope-inspector.enabled', false);
    }

    protected function disableTelescope($app): void
    {
        $app['config']->set('telescope.enabled', false);
    }
}
