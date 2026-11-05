@verbatim
<style>
    /*
     * Telescope Inspector (byiftikhar/telescope-inspector): a read-only viewer
     * over Telescope's own JSON API ({telescope path}/telescope-api/*).
     */
    :root {
        --ti-espresso: #372216;
        --ti-coffee: #5b3f31;
        --ti-sand: #d0b3a0;
        --ti-cream: #f6f0eb;
        --ti-line: #e7ddd5;
        --ti-ink: #2b211b;
        --ti-muted: #7b6a5f;
    }

    body {
        background: var(--ti-cream);
        color: var(--ti-ink);
        font-size: 13px;
    }

    .mono {
        font-family: SFMono-Regular, Menlo, Consolas, monospace;
        font-size: 12px;
    }

    .ti-band {
        background: var(--ti-espresso);
        color: #fff;
        padding: 14px 24px;
    }

    .ti-band h1 {
        font-size: 18px;
        margin: 0;
        color: #fff;
        font-weight: 600;
    }

    .ti-band .sub {
        color: var(--ti-sand);
        font-size: 12px;
    }

    .ti-band a {
    .ti-band a, .ti-card-h a { text-decoration: none; }
        color: var(--ti-sand);
    }

    .ti-tabs {
        background: var(--ti-coffee);
        padding: 0 16px;
        overflow-x: auto;
        white-space: nowrap;
    }

    .ti-tabs a {
        display: inline-block;
        color: #eadbd0;
        padding: 10px 14px;
        text-decoration: none;
        border-bottom: 3px solid transparent;
    }

    .ti-tabs a:hover {
        color: #fff;
    }

    .ti-tabs a.active {
        color: #fff;
        border-bottom-color: var(--ti-sand);
        font-weight: 600;
    }

    .ti-wrap {
        padding: 16px 24px 48px;
    }

    .ti-card {
        background: #fff;
        border: 1px solid var(--ti-line);
        border-radius: 8px;
    }

    .ti-card+.ti-card {
        margin-top: 14px;
    }

    .ti-card-h {
        padding: 10px 14px;
        border-bottom: 1px solid var(--ti-line);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ti-card-h h6 {
        margin: 0;
        font-weight: 600;
        font-size: 13px;
    }

    .ti-card-b {
        padding: 12px 14px;
    }

    .ti-filters label {
        font-size: 11px;
        color: var(--ti-muted);
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .ti-filters .form-control {
        border: 1px solid #d9cec5;
        background: #fff;
        color: var(--ti-ink);
        min-height: 31px;
        border-radius: 4px;
    }

    .ti-filters .form-control:focus {
        border-color: var(--ti-coffee);
        box-shadow: 0 0 0 .15rem rgba(91, 63, 49, .15);
    }

    .ti-query {
        color: var(--ti-muted);
    }

    .ti-table {
        width: 100%;
        margin: 0;
    }

    .ti-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--ti-muted);
        font-weight: 600;
        background: #fbf8f5;
        border-bottom: 1px solid var(--ti-line);
        padding: 8px 10px;
        white-space: nowrap;
    }

    .ti-table td {
        padding: 7px 10px;
        border-bottom: 1px solid #f1ebe6;
        vertical-align: top;
    }

    .ti-table tbody tr {
        cursor: pointer;
    }

    .ti-table tbody tr:hover {
        background: #fbf6f2;
    }

    .ti-table .wrap {
        word-break: break-all;
    }

    .ti-table .clip {
        max-width: 520px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ti-badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.4;
    }

    .ti-b-ok {
        background: #e6efe2;
        color: #3f5a34;
    }

    .ti-b-info {
        background: #e5ecf2;
        color: #34506a;
    }

    .ti-b-warn {
        background: #f6ead7;
        color: #7a5520;
    }

    .ti-b-bad {
        background: #f3dfd8;
        color: #8a3a22;
    }

    .ti-b-mute {
        background: #efe9e4;
        color: var(--ti-coffee);
    }

    .ti-progress {
        color: var(--ti-muted);
        font-size: 12px;
    }

    .ti-empty {
        padding: 36px;
        text-align: center;
        color: var(--ti-muted);
    }

    .ti-kv {
        width: 100%;
    }

    .ti-kv th {
        width: 200px;
        color: var(--ti-muted);
        font-weight: 500;
        padding: 5px 10px 5px 0;
        vertical-align: top;
    }

    .ti-kv td {
        padding: 5px 0;
        word-break: break-all;
    }

    pre.ti-pre {
        background: #fbf8f5;
        border: 1px solid var(--ti-line);
        border-radius: 6px;
        padding: 10px;
        margin: 0;
        max-height: 520px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-all;
        font-size: 12px;
    }

    .ti-sec-h {
        cursor: pointer;
        user-select: none;
    }

    .ti-sec-h .size {
        color: var(--ti-muted);
        font-size: 11px;
    }

    .ti-related-tabs .nav-link {
        color: var(--ti-coffee);
        padding: 6px 12px;
        font-size: 12px;
    }

    .ti-related-tabs .nav-link.active {
        background: var(--ti-coffee);
        color: #fff;
    }

    .ti-line-hl {
        background: #f6ead7;
        display: block;
    }

    .btn-ti {
        background: var(--ti-coffee);
        border-color: var(--ti-coffee);
        color: #fff;
    }

    .btn-ti:hover,
    .btn-ti:focus {
        background: var(--ti-espresso);
        border-color: var(--ti-espresso);
        color: #fff;
    }

    .btn-ti-outline {
        border-color: var(--ti-sand);
        color: var(--ti-coffee);
        background: #fff;
    }

    .btn-ti-outline:hover {
        background: var(--ti-cream);
        color: var(--ti-espresso);
    }

    .ti-alert {
        background: #f3dfd8;
        color: #8a3a22;
        border-radius: 6px;
        padding: 10px 14px;
    }

    .ti-note {
        color: var(--ti-muted);
        font-size: 12px;
    }

    .bootstrap-select .dropdown-toggle {
        background: #fff;
        border: 1px solid #ced4da;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .ti-wrap {
            padding: 12px 16px 40px;
        }

        .ti-band {
            padding: 12px 16px;
        }

        .ti-kv th {
            width: 120px;
        }
    }
</style>
@endverbatim
