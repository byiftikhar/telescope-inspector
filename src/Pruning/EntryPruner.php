<?php

namespace ByIftikhar\TelescopeInspector\Pruning;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Telescope\EntryType;

/**
 * Counts and deletes Telescope entries in a time range, straight on Telescope's
 * own table. Tags go with their entries (telescope_entries_tags cascades on delete),
 * exactly as with Telescope's own telescope:prune command.
 */
class EntryPruner
{
    public function __construct(private readonly int $chunkSize = 1000) {}

    public function count(PruneFilter $filter): int
    {
        return $this->query($filter)->count();
    }

    /**
     * Deletes in chunks so one prune never holds a long lock on a big table.
     *
     * @return int Number of entries deleted.
     */
    public function prune(PruneFilter $filter): int
    {
        $query = $this->query($filter)->orderBy('sequence');
        $total = 0;

        do {
            $deleted = (clone $query)->take($this->chunkSize)->delete();
            $total += $deleted;
        } while ($deleted > 0);

        return $total;
    }

    private function query(PruneFilter $filter): Builder
    {
        return DB::connection(config('telescope.storage.database.connection'))
            ->table('telescope_entries')
            // "To" is a whole minute, so everything recorded during that minute is included.
            ->where('created_at', '<', $filter->to->addMinute())
            ->when($filter->from, fn (Builder $query) => $query->where('created_at', '>=', $filter->from))
            ->when($filter->types, fn (Builder $query) => $query->whereIn('type', $filter->types))
            ->when($filter->keepExceptions, fn (Builder $query) => $query->where('type', '!=', EntryType::EXCEPTION));
    }
}
