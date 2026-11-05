<!-- List view template -->
<template id="tpl-list">
    <div class="ti-card ti-filters">
        <div class="ti-card-b">
            <form id="ti-filter-form" class="row g-2 align-items-end" autocomplete="off">
                <div class="col-lg-3 col-md-6">
                    <label for="f-q">Search</label>
                    <input type="text" class="form-control form-control-sm" id="f-q" name="q">
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="requests,client-requests">
                    <label for="f-method">Method</label>
                    <select class="form-control form-control-sm ti-picker" id="f-method" name="method" multiple
                        data-none-selected-text="Any">
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
                    <select class="form-control form-control-sm ti-picker" id="f-status" name="status" multiple
                        data-none-selected-text="Any">
                        <option value="2">2xx</option>
                        <option value="3">3xx</option>
                        <option value="4">4xx</option>
                        <option value="5">5xx</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="requests">
                    <label for="f-user">User</label>
                    <input type="text" class="form-control form-control-sm" id="f-user" name="user"
                        placeholder="Name, email or user id">
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="jobs">
                    <label for="f-job-status">Job status</label>
                    <select class="form-control form-control-sm ti-picker" id="f-job-status" name="jobStatus"
                        multiple data-none-selected-text="Any">
                        <option value="pending">Pending</option>
                        <option value="processed">Processed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-6" data-types="logs">
                    <label for="f-level">Level</label>
                    <select class="form-control form-control-sm ti-picker" id="f-level" name="level" multiple
                        data-none-selected-text="Any">
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
                    <input type="number" min="0" class="form-control form-control-sm" id="f-min-ms" name="minMs">
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label for="f-tag">Tag (server side)</label>
                    <input type="text" class="form-control form-control-sm" id="f-tag" name="tag"
                        placeholder="e.g. Auth:199, slow">
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label for="f-from">From</label>
                    <input type="text" class="form-control form-control-sm ti-date" id="f-from" name="from">
                </div>
                <div class="col-lg-2 col-md-3 col-6">
                    <label for="f-to">To</label>
                    <input type="text" class="form-control form-control-sm ti-date" id="f-to" name="to">
                </div>
                <div class="col-lg-1 col-md-3 col-6">
                    <label for="f-take">Fetch size</label>
                    <select class="form-control form-control-sm ti-picker" id="f-take" name="take">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-ti"><i class="ri-search-line"></i> Search</button>
                    <button type="button" class="btn btn-sm btn-ti-outline" id="ti-reset">Reset</button>
                </div>
            </form>
            <div class="ti-note mt-2">
                Search and the other filters run in your browser over the entries fetched so far, newest first, a
                small batch at a time.
                Only <em>Tag</em>, and <em>User</em> when it is a numeric user id, are filtered by Telescope itself.
                Large entries are never rendered in the list.
            </div>
        </div>
    </div>
    <div class="ti-card">
        <div class="ti-card-h">
            <h6 id="ti-list-title">Entries</h6>
            <span class="ti-progress ms-auto" id="ti-progress"></span>
            <button type="button" class="btn btn-sm btn-ti-outline d-none" id="ti-stop"><i
                    class="ri-stop-circle-line"></i> Stop</button>
            <button type="button" class="btn btn-sm btn-ti-outline" id="ti-refresh" title="Fetch newest"><i
                    class="ri-refresh-line"></i></button>
        </div>
        <div class="table-responsive">
            <table class="ti-table">
                <thead id="ti-thead"></thead>
                <tbody id="ti-tbody"></tbody>
            </table>
        </div>
        <div id="ti-list-footer" class="p-3 text-center"></div>
    </div>
</template>
