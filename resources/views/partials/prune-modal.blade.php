<div class="modal fade ti-prune" id="ti-prune-modal" tabindex="-1" aria-labelledby="ti-prune-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content ti-filters" id="ti-prune-form" autocomplete="off" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="ti-prune-title"><i class="ri-delete-bin-6-line me-1"></i> Prune entries</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="ti-note mb-3">
                    Permanently deletes recorded entries in a time range (server time), together with their tags.
                </p>
                <div class="mb-3">
                    <label>Quick ranges</label>
                    <div class="d-flex flex-wrap gap-1" id="ti-prune-presets">
                        <button type="button" class="btn btn-sm btn-ti-outline" data-hours="1">Older than 1 hour</button>
                        <button type="button" class="btn btn-sm btn-ti-outline" data-hours="6">Older than 6 hours</button>
                        <button type="button" class="btn btn-sm btn-ti-outline" data-hours="24">Older than 1 day</button>
                        <button type="button" class="btn btn-sm btn-ti-outline" data-hours="168">Older than 7 days</button>
                        <button type="button" class="btn btn-sm btn-ti-outline" data-hours="0">All entries</button>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label for="p-from">From</label>
                        <input type="text" class="form-control form-control-sm" id="p-from" name="from" placeholder="The beginning">
                    </div>
                    <div class="col-6">
                        <label for="p-to">To (required)</label>
                        <input type="text" class="form-control form-control-sm" id="p-to" name="to" placeholder="Pick a date and time">
                    </div>
                    <div class="col-12">
                        <label for="p-types">Entry types</label>
                        <select class="form-control form-control-sm" id="p-types" name="types" multiple data-none-selected-text="All types"></select>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="p-keep" name="keep_exceptions">
                            <label class="form-check-label" for="p-keep">Keep exceptions</label>
                        </div>
                    </div>
                </div>
                <div class="ti-prune-count mt-3" id="ti-prune-count" aria-live="polite"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-ti-outline" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-ti-danger" id="ti-prune-submit" disabled>
                    <i class="ri-delete-bin-6-line"></i> Delete entries
                </button>
            </div>
        </form>
    </div>
</div>
