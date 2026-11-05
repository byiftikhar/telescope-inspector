<?php

namespace ByIftikhar\TelescopeInspector\Tests\Feature;

use ByIftikhar\TelescopeInspector\TelescopeInspectorServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeServiceProvider;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PruneTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [TelescopeServiceProvider::class, TelescopeInspectorServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('k', 32)));
        $app['config']->set('app.timezone', 'Asia/Dubai');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing.foreign_key_constraints', true);
        $app['config']->set('telescope.driver', 'database');
        $app['config']->set('telescope.storage.database.connection', 'testing');
        // The tests' own queries and requests must not be recorded as entries.
        $app['config']->set('telescope.watchers', []);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../vendor/laravel/telescope/database/migrations');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Telescope::auth(fn (): bool => true);
    }

    protected function tearDown(): void
    {
        Telescope::auth(null);

        parent::tearDown();
    }

    #[Test]
    public function it_counts_the_entries_a_prune_would_delete(): void
    {
        $this->seedEntries();

        $this->assertSame(4, $this->previewCount(['from' => '2026-11-05 10:00', 'to' => '2026-11-05 11:00']));
        $this->assertSame(2, $this->previewCount(['from' => '2026-11-05 10:00', 'to' => '2026-11-05 11:00', 'types' => ['request']]));
        $this->assertSame(3, $this->previewCount(['from' => '2026-11-05 10:00', 'to' => '2026-11-05 11:00', 'keep_exceptions' => true]));
        $this->assertSame(5, $this->previewCount(['to' => '2026-11-05 11:00']));
    }

    #[Test]
    public function the_to_minute_is_included_in_full(): void
    {
        $this->seedEntries();

        // 11:00:30 belongs to the 11:00 minute; 10:59 leaves it out.
        $this->assertSame(4, $this->previewCount(['from' => '2026-11-05 10:00', 'to' => '2026-11-05 11:00']));
        $this->assertSame(3, $this->previewCount(['from' => '2026-11-05 10:00', 'to' => '2026-11-05 10:59']));
    }

    #[Test]
    public function it_deletes_matching_entries_with_their_tags_and_keeps_the_rest(): void
    {
        config()->set('telescope-inspector.prune.chunk_size', 2);
        $entries = $this->seedEntries();

        $this->postJson('/telescope-inspector/prune', ['from' => '2026-11-05 10:00', 'to' => '2026-11-05 11:00', 'keep_exceptions' => true])
            ->assertOk()
            ->assertExactJson(['deleted' => 3]);

        $remaining = DB::table('telescope_entries')->pluck('uuid')->sort()->values()->all();
        $expected = collect([$entries['early'], $entries['exception'], $entries['late']])->sort()->values()->all();
        $this->assertSame($expected, $remaining);
        $this->assertSame(0, DB::table('telescope_entries_tags')->where('entry_uuid', $entries['tagged'])->count());
        $this->assertSame(1, DB::table('telescope_entries_tags')->where('entry_uuid', $entries['early'])->count());
    }

    #[Test]
    public function it_rejects_invalid_ranges_and_types(): void
    {
        $this->postJson('/telescope-inspector/prune/preview', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['to' => 'Pick the "To" date.']);

        $this->postJson('/telescope-inspector/prune/preview', ['from' => '2026-11-05 12:00', 'to' => '2026-11-05 11:00'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['from']);

        $this->postJson('/telescope-inspector/prune/preview', ['to' => '2026-11-05 11:00', 'types' => ['not-a-type']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['types.0']);

        $this->postJson('/telescope-inspector/prune', ['to' => '05/11/2026'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['to']);
    }

    #[Test]
    public function a_prune_telescope_gate_can_forbid_pruning(): void
    {
        $this->seedEntries();
        Gate::define('pruneTelescope', fn (?Authenticatable $user = null): bool => false);

        $this->postJson('/telescope-inspector/prune', ['to' => '2026-11-05 23:59'])->assertForbidden();
        $this->assertSame(6, DB::table('telescope_entries')->count());

        Gate::define('pruneTelescope', fn (?Authenticatable $user = null): bool => true);

        $this->postJson('/telescope-inspector/prune', ['to' => '2026-11-05 23:59'])->assertExactJson(['deleted' => 6]);
    }

    #[Test]
    public function telescope_access_rules_apply_to_pruning(): void
    {
        Telescope::auth(fn (): bool => false);

        $this->postJson('/telescope-inspector/prune', ['to' => '2026-11-05 23:59'])->assertForbidden();
    }

    #[Test]
    public function it_needs_the_database_driver(): void
    {
        config()->set('telescope.driver', 'redis');

        $this->postJson('/telescope-inspector/prune/preview', ['to' => '2026-11-05 11:00'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['to']);
    }

    #[Test]
    public function the_page_offers_pruning_and_the_theme_switch(): void
    {
        $response = $this->get('/telescope-inspector');

        $response->assertOk();
        $response->assertSee('id="ti-prune-open"', false);
        $response->assertSee('id="ti-prune-modal"', false);
        $response->assertSee('data-hours="0">All entries</button>', false);
        $response->assertSee('id="ti-theme"', false);
        $response->assertSee('"previewUrl":"\/telescope-inspector\/prune\/preview"', false);
        $response->assertSee('"url":"\/telescope-inspector\/prune"', false);
    }

    #[Test]
    #[DefineEnvironment('disablePruning')]
    public function pruning_can_be_switched_off(): void
    {
        $this->seedEntries();

        // The path still exists as a page (GET), so a POST there is refused as "method not allowed".
        $this->postJson('/telescope-inspector/prune', ['to' => '2026-11-05 23:59'])->assertMethodNotAllowed();
        $this->assertSame(6, DB::table('telescope_entries')->count());

        $response = $this->get('/telescope-inspector');

        $response->assertOk();
        $response->assertSee('"prune":null', false);
        $response->assertDontSee('id="ti-prune-open"', false);
        $response->assertDontSee('id="ti-prune-modal"', false);
    }

    protected function disablePruning($app): void
    {
        $app['config']->set('telescope-inspector.prune.enabled', false);
    }

    /**
     * @param  array<string, mixed>  $criteria
     */
    private function previewCount(array $criteria): int
    {
        return $this->postJson('/telescope-inspector/prune/preview', $criteria)->assertOk()->json('count');
    }

    /**
     * Six entries on 2026-11-05 (server time): one before the 10:00-11:00 window,
     * four inside it (one of them an exception, one at 11:00:30) and one after it.
     *
     * @return array<string, string>
     */
    private function seedEntries(): array
    {
        return [
            'early' => $this->entry('request', '2026-11-05 09:00:00', ['Auth:1']),
            'tagged' => $this->entry('request', '2026-11-05 10:30:00', ['Auth:2', 'slow']),
            'query' => $this->entry('query', '2026-11-05 10:30:00'),
            'exception' => $this->entry('exception', '2026-11-05 10:45:00'),
            'edge' => $this->entry('request', '2026-11-05 11:00:30'),
            'late' => $this->entry('request', '2026-11-05 12:00:00'),
        ];
    }

    /**
     * @param  list<string>  $tags
     */
    private function entry(string $type, string $createdAt, array $tags = []): string
    {
        $uuid = (string) Str::uuid();

        DB::table('telescope_entries')->insert([
            'uuid' => $uuid,
            'batch_id' => (string) Str::uuid(),
            'family_hash' => null,
            'should_display_on_index' => true,
            'type' => $type,
            'content' => '{}',
            'created_at' => $createdAt,
        ]);

        foreach ($tags as $tag) {
            DB::table('telescope_entries_tags')->insert(['entry_uuid' => $uuid, 'tag' => $tag]);
        }

        return $uuid;
    }
}
