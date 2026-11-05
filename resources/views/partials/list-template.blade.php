<!-- List view template -->
<template id="tpl-list">
    <section class="ti-card ti-filters" aria-label="Filters">
        <form id="ti-filter-form" class="ti-card-b" autocomplete="off">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label for="f-q">Search</label>
                    <div class="ti-search">
                        <i class="ri-search-line" aria-hidden="true"></i>
                        <input type="text" class="form-control" id="f-q" name="q">
                        <kbd title="Press / to search">/</kbd>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="requests,client-requests">
                    <label for="f-method">Method</label>
                    <select class="form-control ti-picker" id="f-method" name="method" multiple data-none-selected-text="Any">
                        <option>GET</option>
                        <option>POST</option>
                        <option>PUT</option>
                        <option>PATCH</option>
                        <option>DELETE</option>
                        <option>OPTIONS</option>
                        <option>HEAD</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="requests,client-requests">
                    <label for="f-status">Status</label>
                    <select class="form-control ti-picker" id="f-status" name="status" multiple data-none-selected-text="Any">
                        <option value="2">2xx Success</option>
                        <option value="3">3xx Redirection</option>
                        <option value="4">4xx Client error</option>
                        <option value="5">5xx Server error</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="requests">
                    <label for="f-user">User</label>
                    <input type="text" class="form-control" id="f-user" name="user" placeholder="Name, email or ID">
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="jobs">
                    <label for="f-job-status">Job status</label>
                    <select class="form-control ti-picker" id="f-job-status" name="jobStatus" multiple data-none-selected-text="Any">
                        <option value="pending">Pending</option>
                        <option value="processed">Processed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="logs">
                    <label for="f-level">Level</label>
                    <select class="form-control ti-picker" id="f-level" name="level" multiple data-none-selected-text="Any">
                        <option>debug</option>
                        <option>info</option>
                        <option>notice</option>
                        <option>warning</option>
                        <option>error</option>
                        <option>critical</option>
                        <option>alert</option>
                        <option>emergency</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-3 col-6" data-types="requests,client-requests,queries,redis">
                    <label for="f-min-ms">Min ms</label>
                    <input type="number" min="0" class="form-control" id="f-min-ms" name="minMs" placeholder="200">
                </div>
                <div class="col-lg-1 col-md-3 col-6">
                    <label for="f-tag" title="Filtered by Telescope itself">Tag</label>
                    <input type="text" class="form-control" id="f-tag" name="tag" placeholder="e.g. Auth:1">
                </div>
            </div>
            <div class="ti-filter-row2">
                <div class="ti-inline">
                    <div class="ti-field">
                        <label class="ti-inline-label" for="f-from">From:</label>
                        <input type="text" class="form-control ti-date ti-date-input" id="f-from" name="from" placeholder="YYYY-MM-DD HH:mm">
                    </div>
                    <div class="ti-field">
                        <label class="ti-inline-label" for="f-to">To:</label>
                        <input type="text" class="form-control ti-date ti-date-input" id="f-to" name="to" placeholder="YYYY-MM-DD HH:mm">
                    </div>
                    <div class="ti-field">
                        <label class="ti-inline-label" for="f-take">Fetch:</label>
                        <div class="ti-take">
                            <select class="form-control ti-picker" id="f-take" name="take">
                                <option value="10">10</option>
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-ti"><i class="ri-search-line"></i> Search</button>
                    <button type="button" class="btn btn-ti-ghost" id="ti-reset">Reset</button>
                </div>
                <div class="ti-filter-note">
                    Filters run in your browser over the fetched batches, newest first. Tag, and a numeric user ID, are filtered by Telescope.
                </div>
            </div>
        </form>
    </section>
    <section class="ti-card">
        <div class="ti-card-h">
            <h6 id="ti-list-title">Entries</h6>
            <span class="ti-pill" id="ti-list-count">0 fetched</span>
            <span class="ms-auto"></span>
            <button type="button" class="ti-icon-btn d-none" id="ti-stop" title="Stop searching"><i class="ri-stop-circle-line"></i> Stop</button>
            <button type="button" class="ti-icon-btn" id="ti-refresh" title="Fetch newest"><i class="ri-refresh-line"></i></button>
        </div>
        <div class="table-responsive">
            <table class="ti-table">
                <thead id="ti-thead"></thead>
                <tbody id="ti-tbody"></tbody>
            </table>
        </div>
        <div id="ti-list-state" class="ti-footer-state"></div>
        <div class="ti-list-footer">
            <div class="ti-footer-meta" id="ti-footer-meta"></div>
            <div class="ti-footer-actions" id="ti-list-footer"></div>
        </div>
    </section>
</template>
