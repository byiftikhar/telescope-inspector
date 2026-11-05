<?php

namespace ByIftikhar\TelescopeInspector\Http\Controllers;

use ByIftikhar\TelescopeInspector\Http\Requests\PruneRequest;
use ByIftikhar\TelescopeInspector\Pruning\EntryPruner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PruneController
{
    public function __construct(private readonly EntryPruner $pruner) {}

    /**
     * How many entries a prune with these settings would delete.
     */
    public function preview(PruneRequest $request): JsonResponse
    {
        return response()->json(['count' => $this->pruner->count($request->filter())]);
    }

    public function destroy(PruneRequest $request): JsonResponse
    {
        $filter = $request->filter();
        $deleted = $this->pruner->prune($filter);

        Log::info('Telescope Inspector: entries pruned', [
            'user_id' => $request->user()?->getAuthIdentifier(),
            'deleted' => $deleted,
            ...$filter->toArray(),
        ]);

        return response()->json(['deleted' => $deleted]);
    }
}
