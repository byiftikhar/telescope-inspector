@verbatim
<style>
    /*
     * Telescope Inspector (byiftikhar/telescope-inspector).
     * Developer-tool design: slate scale in light mode, the
     * "night" scale in dark mode, sky as the accent, Inter + JetBrains Mono.
     * Every colour is a token: light on :root, dark on :root[data-theme="dark"].
     */
    :root {
        --ti-font: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        --ti-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;

        --ti-bg: #f8fafc;
        --ti-surface: #fff;
        --ti-surface-2: #f8fafc;
        --ti-thead: #f1f5f9;
        --ti-hover: #f1f5f9;
        --ti-line: #e2e8f0;
        --ti-line-soft: #e2e8f0;
        --ti-input-bg: #f8fafc;
        --ti-input-border: #cbd5e1;
        --ti-chip-bg: #f1f5f9;
        --ti-chip-border: #e2e8f0;
        --ti-ink: #1e293b;
        --ti-strong: #0f172a;
        --ti-soft: #334155;
        --ti-muted: #64748b;
        --ti-faint: #94a3b8;

        --ti-accent: #0284c7;
        --ti-accent-hover: #0ea5e9;
        --ti-accent-text: #0284c7;
        --ti-focus: rgba(14, 165, 233, .35);
        --ti-btn-bg: #0284c7;
        --ti-btn-hover: #0ea5e9;
        --ti-danger: #e11d48;
        --ti-danger-hover: #f43f5e;

        --ti-ok-bg: #d1fae5;
        --ti-ok-fg: #047857;
        --ti-ok-border: #6ee7b7;
        --ti-ok-text: #059669;
        --ti-info-bg: #f0f9ff;
        --ti-info-fg: #0284c7;
        --ti-info-border: #bae6fd;
        --ti-indigo-bg: #eef2ff;
        --ti-indigo-fg: #4f46e5;
        --ti-indigo-border: #c7d2fe;
        --ti-warn-bg: #fef3c7;
        --ti-warn-fg: #b45309;
        --ti-warn-border: #fcd34d;
        --ti-warn-text: #d97706;
        --ti-bad-bg: #ffe4e6;
        --ti-bad-fg: #be123c;
        --ti-bad-border: #fda4af;
        --ti-bad-text: #e11d48;
        --ti-mute-bg: #f1f5f9;
        --ti-mute-fg: #475569;
        --ti-mute-border: #e2e8f0;
        --ti-highlight: #fef3c7;

        --ti-scroll-track: #f1f5f9;
        --ti-scroll-thumb: #cbd5e1;
        --ti-scroll-thumb-hover: #94a3b8;
    }

    :root[data-theme="dark"] {
        --ti-bg: #090d13;
        --ti-surface: #0d1117;
        --ti-surface-2: #12171f;
        --ti-thead: rgba(13, 17, 23, .9);
        --ti-hover: rgba(22, 27, 34, .8);
        --ti-line: rgba(48, 54, 61, .7);
        --ti-line-soft: rgba(33, 38, 45, .6);
        --ti-input-bg: #090d13;
        --ti-input-border: #30363d;
        --ti-chip-bg: #161b22;
        --ti-chip-border: #30363d;
        --ti-ink: #e6edf3;
        --ti-strong: #f0f6fc;
        --ti-soft: #c9d1d9;
        --ti-muted: #8b949e;
        --ti-faint: #484f58;

        --ti-accent-text: #38bdf8;
        --ti-focus: rgba(56, 189, 248, .35);

        --ti-ok-bg: rgba(16, 185, 129, .15);
        --ti-ok-fg: #34d399;
        --ti-ok-border: rgba(16, 185, 129, .3);
        --ti-ok-text: #34d399;
        --ti-info-bg: rgba(14, 165, 233, .15);
        --ti-info-fg: #38bdf8;
        --ti-info-border: rgba(14, 165, 233, .3);
        --ti-indigo-bg: rgba(99, 102, 241, .15);
        --ti-indigo-fg: #818cf8;
        --ti-indigo-border: rgba(99, 102, 241, .3);
        --ti-warn-bg: rgba(245, 158, 11, .15);
        --ti-warn-fg: #fbbf24;
        --ti-warn-border: rgba(245, 158, 11, .3);
        --ti-warn-text: #fbbf24;
        --ti-bad-bg: rgba(244, 63, 94, .15);
        --ti-bad-fg: #fb7185;
        --ti-bad-border: rgba(244, 63, 94, .3);
        --ti-bad-text: #fb7185;
        --ti-mute-bg: #161b22;
        --ti-mute-fg: #8b949e;
        --ti-mute-border: #21262d;
        --ti-highlight: rgba(245, 158, 11, .15);

        --ti-scroll-track: #0d1117;
        --ti-scroll-thumb: #30363d;
        --ti-scroll-thumb-hover: #484f58;
    }

    /* ---------------------------------------------------------------- base */

    body {
        background: var(--ti-bg);
        color: var(--ti-ink);
        font-family: var(--ti-font);
        font-size: 13px;
        -webkit-font-smoothing: antialiased;
        transition: background-color .2s, color .2s;
    }

    ::selection {
        background: rgba(99, 102, 241, .3);
    }

    ::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }

    ::-webkit-scrollbar-track {
        background: var(--ti-scroll-track);
    }

    ::-webkit-scrollbar-thumb {
        background: var(--ti-scroll-thumb);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--ti-scroll-thumb-hover);
    }

    .mono {
        font-family: var(--ti-mono);
        font-size: 12px;
    }

    a {
        color: var(--ti-accent-text);
        text-decoration: none;
    }

    /* -------------------------------------------------------------- header */

    .ti-header {
        position: sticky;
        top: 0;
        z-index: 1030;
        background: var(--ti-surface);
        border-bottom: 1px solid var(--ti-line);
    }

    .ti-header-row {
        min-height: 56px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 8px 32px;
    }

    .ti-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .ti-logo {
        width: 32px;
        height: 32px;
        flex: none;
        border-radius: 8px;
        padding: 1px;
        background: linear-gradient(45deg, #4f46e5, #38bdf8);
        box-shadow: 0 1px 2px rgba(99, 102, 241, .2);
    }

    .ti-logo span {
        width: 100%;
        height: 100%;
        border-radius: 7px;
        background: var(--ti-surface);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0ea5e9;
    }

    :root[data-theme="dark"] .ti-logo span {
        color: #38bdf8;
    }

    .ti-brand h1 {
        font-size: 14px;
        font-weight: 600;
        letter-spacing: -.01em;
        margin: 0;
        color: var(--ti-strong);
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .ti-version {
        font-family: var(--ti-mono);
        font-size: 10px;
        font-weight: 500;
        padding: 1px 6px;
        border-radius: 4px;
        background: var(--ti-chip-bg);
        color: var(--ti-muted);
        border: 1px solid var(--ti-chip-border);
    }

    .ti-sub {
        font-size: 12px;
        color: var(--ti-muted);
    }

    .ti-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ti-recording {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 3px 10px;
        border-radius: 999px;
        font-family: var(--ti-mono);
        font-size: 12px;
        font-weight: 500;
        border: 1px solid var(--ti-mute-border);
        background: var(--ti-mute-bg);
        color: var(--ti-mute-fg);
    }

    .ti-recording .dot {
        position: relative;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
    }

    .ti-recording.is-ok {
        background: var(--ti-ok-bg);
        color: var(--ti-ok-fg);
        border-color: var(--ti-ok-border);
    }

    .ti-recording.is-ok .dot::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: currentColor;
        animation: ti-ping 1.2s cubic-bezier(0, 0, .2, 1) infinite;
    }

    .ti-recording.is-warn {
        background: var(--ti-warn-bg);
        color: var(--ti-warn-fg);
        border-color: var(--ti-warn-border);
    }

    .ti-recording.is-bad {
        background: var(--ti-bad-bg);
        color: var(--ti-bad-fg);
        border-color: var(--ti-bad-border);
    }

    @keyframes ti-ping {
        75%, 100% {
            transform: scale(2.2);
            opacity: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ti-recording.is-ok .dot::after {
            animation: none;
        }
    }

    .ti-head-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        color: var(--ti-soft);
        background: var(--ti-chip-bg);
        border: 1px solid var(--ti-chip-border);
        box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
        transition: background-color .15s, color .15s;
    }

    .ti-head-btn:hover,
    .ti-head-btn:focus-visible {
        color: var(--ti-strong);
        background: var(--ti-hover);
    }

    .ti-head-btn:focus-visible {
        outline: 2px solid var(--ti-focus);
        outline-offset: 1px;
    }

    .ti-head-btn .label-mono {
        font-family: var(--ti-mono);
        font-size: 11px;
    }

    .ti-head-btn .ri-moon-line {
        color: #38bdf8;
    }

    .ti-head-btn .ri-sun-line {
        color: #f59e0b;
    }

    .ti-head-btn .ri-external-link-line {
        color: var(--ti-faint);
    }

    .ti-tabs {
        display: flex;
        align-items: center;
        gap: 4px;
        overflow-x: auto;
        scrollbar-width: none;
        padding: 4px 32px 0;
        margin-bottom: -1px;
        border-top: 1px solid var(--ti-line-soft);
        font-size: 12px;
        font-weight: 500;
    }

    .ti-tabs::-webkit-scrollbar {
        display: none;
    }

    .ti-tabs a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        white-space: nowrap;
        color: var(--ti-muted);
        border-bottom: 2px solid transparent;
        transition: color .15s, border-color .15s;
    }

    .ti-tabs a:hover {
        color: var(--ti-ink);
        border-bottom-color: var(--ti-faint);
    }

    .ti-tabs a.active {
        color: var(--ti-accent-text);
        border-bottom-color: var(--ti-accent-text);
        font-weight: 600;
    }

    .ti-tab-count {
        padding: 0 6px;
        border-radius: 999px;
        font-size: 10px;
        line-height: 16px;
        background: var(--ti-info-bg);
        color: var(--ti-info-fg);
        border: 1px solid var(--ti-info-border);
    }

    .ti-tab-count:empty {
        display: none;
    }

    /* ---------------------------------------------------------------- page */

    .ti-wrap {
        padding: 20px 32px 48px;
    }

    .ti-card {
        background: var(--ti-surface);
        border: 1px solid var(--ti-line);
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    .ti-card+.ti-card {
        margin-top: 16px;
    }

    .ti-card-h {
        padding: 12px 16px;
        border-bottom: 1px solid var(--ti-line);
        background: var(--ti-surface-2);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ti-card-h h6 {
        margin: 0;
        font-weight: 600;
        font-size: 14px;
        color: var(--ti-strong);
    }

    .ti-card-b {
        padding: 16px;
    }

    .ti-pill {
        padding: 1px 8px;
        border-radius: 999px;
        font-family: var(--ti-mono);
        font-size: 11px;
        background: var(--ti-chip-bg);
        color: var(--ti-soft);
        border: 1px solid var(--ti-chip-border);
    }

    .ti-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 5px 7px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: var(--ti-muted);
        font-size: 12px;
    }

    .ti-icon-btn:hover {
        color: var(--ti-strong);
        background: var(--ti-hover);
    }

    /* ------------------------------------------------------------- filters */

    .ti-filters label,
    .ti-inline-label {
        display: block;
        font-family: var(--ti-mono);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ti-muted);
        margin-bottom: 4px;
    }

    .ti-inline-label {
        display: inline;
        margin: 0 6px 0 0;
    }

    .ti-filters .form-control {
        background: var(--ti-input-bg);
        border: 1px solid var(--ti-input-border);
        border-radius: 8px;
        color: var(--ti-strong);
        font-family: var(--ti-mono);
        font-size: 12px;
        min-height: 31px;
        padding: 5px 10px;
    }

    .ti-filters .form-control::placeholder {
        color: var(--ti-faint);
    }

    .ti-filters .form-control:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 1px #0ea5e9;
    }

    .ti-search {
        position: relative;
    }

    .ti-search .form-control {
        padding-left: 30px;
        padding-right: 36px;
    }

    .ti-search .ri-search-line {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ti-faint);
        font-size: 13px;
    }

    .ti-search kbd {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-family: var(--ti-mono);
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 4px;
        color: var(--ti-muted);
        background: var(--ti-chip-bg);
        border: 1px solid var(--ti-input-border);
        box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
    }

    .ti-filter-row2 {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--ti-line-soft);
    }

    .ti-filter-row2 .ti-inline {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }

    .ti-filter-row2 .ti-field {
        display: flex;
        align-items: center;
    }

    .ti-filter-row2 .ti-date-input {
        width: 150px;
    }

    .ti-filter-row2 .ti-take {
        width: 72px;
    }

    .ti-filter-note {
        font-size: 11px;
        font-style: italic;
        color: var(--ti-muted);
    }

    /* --------------------------------------------------------------- table */

    .ti-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        font-family: var(--ti-mono);
        font-size: 12px;
        color: var(--ti-soft);
    }

    .ti-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ti-muted);
        font-weight: 600;
        background: var(--ti-thead);
        border-bottom: 1px solid var(--ti-line);
        padding: 10px 16px;
        white-space: nowrap;
        user-select: none;
    }

    .ti-table td {
        padding: 10px 16px;
        border-bottom: 1px solid var(--ti-line-soft);
        vertical-align: middle;
    }

    .ti-table tbody tr {
        cursor: pointer;
        transition: background-color .15s;
    }

    .ti-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .ti-table tbody tr:hover {
        background: var(--ti-hover);
    }

    .ti-table .muted {
        color: var(--ti-muted);
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

    .ti-uri {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        color: var(--ti-strong);
        font-weight: 500;
    }

    .ti-uri-text {
        max-width: 560px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ti-uri:hover .ti-uri-text {
        color: var(--ti-accent-text);
    }

    .ti-copy {
        flex: none;
        opacity: 0;
        border: 0;
        background: transparent;
        color: var(--ti-faint);
        padding: 2px 4px;
        border-radius: 4px;
        font-size: 12px;
        line-height: 1;
    }

    .ti-table tr:hover .ti-copy,
    .ti-copy:focus-visible {
        opacity: 1;
    }

    .ti-copy:hover {
        color: var(--ti-strong);
    }

    .ti-closure {
        color: var(--ti-muted);
        font-style: italic;
    }

    .ti-user {
        font-family: var(--ti-font);
        font-weight: 500;
        color: var(--ti-soft);
    }

    .ti-guest {
        display: inline-block;
        padding: 1px 6px;
        border-radius: 4px;
        font-size: 11px;
        background: var(--ti-chip-bg);
        color: var(--ti-muted);
        border: 1px solid var(--ti-chip-border);
    }

    .ti-t-ok {
        color: var(--ti-ok-text);
        font-weight: 600;
    }

    .ti-t-warn {
        color: var(--ti-warn-text);
        font-weight: 600;
    }

    .ti-t-bad {
        color: var(--ti-bad-text);
        font-weight: 600;
    }

    .ti-num {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .ti-list-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        background: var(--ti-surface-2);
        border-top: 1px solid var(--ti-line);
        font-family: var(--ti-mono);
        font-size: 12px;
        color: var(--ti-muted);
    }

    .ti-list-footer strong {
        color: var(--ti-ink);
    }

    .ti-footer-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
    }

    .ti-sep {
        color: var(--ti-faint);
    }

    .ti-live-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--ti-ok-text);
        margin-right: 6px;
    }

    .ti-footer-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ti-footer-state {
        padding: 0 16px;
    }

    /* -------------------------------------------------------------- badges */

    .ti-badge {
        display: inline-flex;
        align-items: center;
        padding: 1px 8px;
        border-radius: 4px;
        font-family: var(--ti-mono);
        font-size: 11px;
        font-weight: 600;
        line-height: 1.6;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .ti-badge.is-pill {
        border-radius: 999px;
    }

    .ti-b-ok {
        background: var(--ti-ok-bg);
        color: var(--ti-ok-fg);
        border-color: var(--ti-ok-border);
    }

    .ti-b-info {
        background: var(--ti-info-bg);
        color: var(--ti-info-fg);
        border-color: var(--ti-info-border);
    }

    .ti-b-indigo {
        background: var(--ti-indigo-bg);
        color: var(--ti-indigo-fg);
        border-color: var(--ti-indigo-border);
    }

    .ti-b-warn {
        background: var(--ti-warn-bg);
        color: var(--ti-warn-fg);
        border-color: var(--ti-warn-border);
    }

    .ti-b-bad {
        background: var(--ti-bad-bg);
        color: var(--ti-bad-fg);
        border-color: var(--ti-bad-border);
    }

    .ti-b-mute {
        background: var(--ti-mute-bg);
        color: var(--ti-mute-fg);
        border-color: var(--ti-mute-border);
    }

    /* ------------------------------------------------------ states / notes */

    .ti-empty {
        padding: 36px;
        text-align: center;
        color: var(--ti-muted);
    }

    .ti-loader {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 0;
        color: var(--ti-muted);
        font-size: 12px;
    }

    .ti-loader .spinner-border {
        color: var(--ti-accent-text);
    }

    .ti-alert {
        background: var(--ti-bad-bg);
        color: var(--ti-bad-fg);
        border: 1px solid var(--ti-bad-border);
        border-radius: 8px;
        padding: 10px 14px;
    }

    .ti-note {
        color: var(--ti-muted);
        font-size: 12px;
    }

    /* -------------------------------------------------------------- detail */

    .ti-kv {
        width: 100%;
        color: var(--ti-ink);
    }

    .ti-kv th {
        width: 200px;
        color: var(--ti-muted);
        font-family: var(--ti-mono);
        font-size: 12px;
        font-weight: 500;
        padding: 6px 12px 6px 0;
        vertical-align: top;
    }

    .ti-kv td {
        padding: 6px 0;
        word-break: break-all;
        color: var(--ti-strong);
    }

    pre.ti-pre {
        background: var(--ti-input-bg);
        color: var(--ti-soft);
        border: 1px solid var(--ti-line);
        border-radius: 8px;
        padding: 12px;
        margin: 0;
        max-height: 520px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-all;
        font-family: var(--ti-mono);
        font-size: 12px;
    }

    .ti-sec-h {
        cursor: pointer;
        user-select: none;
    }

    .ti-sec-h .size {
        color: var(--ti-muted);
        font-family: var(--ti-mono);
        font-size: 11px;
    }

    .ti-related-tabs .nav-link {
        color: var(--ti-muted);
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 8px;
    }

    .ti-related-tabs .nav-link:hover {
        color: var(--ti-ink);
        background: var(--ti-hover);
    }

    .ti-related-tabs .nav-link.active {
        background: var(--ti-info-bg);
        color: var(--ti-info-fg);
        box-shadow: inset 0 0 0 1px var(--ti-info-border);
    }

    .ti-line-hl {
        background: var(--ti-highlight);
        display: block;
    }

    /* ------------------------------------------------------------- buttons */

    .btn-ti {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--ti-btn-bg);
        border: 1px solid var(--ti-btn-bg);
        color: #fff;
        font-weight: 500;
        font-size: 12px;
        border-radius: 8px;
        padding: 5px 14px;
        box-shadow: 0 1px 2px rgba(12, 74, 110, .3);
    }

    .btn-ti:hover,
    .btn-ti:focus {
        background: var(--ti-btn-hover);
        border-color: var(--ti-btn-hover);
        color: #fff;
    }

    .btn-ti-ghost {
        background: transparent;
        border: 0;
        color: var(--ti-muted);
        font-size: 12px;
        border-radius: 6px;
        padding: 5px 12px;
    }

    .btn-ti-ghost:hover,
    .btn-ti-ghost:focus {
        color: var(--ti-ink);
        background: var(--ti-hover);
    }

    .btn-ti-outline {
        background: var(--ti-chip-bg);
        border: 1px solid var(--ti-chip-border);
        color: var(--ti-soft);
        font-family: var(--ti-mono);
        font-size: 12px;
        border-radius: 6px;
        padding: 4px 10px;
    }

    .btn-ti-outline:hover,
    .btn-ti-outline:focus {
        background: var(--ti-hover);
        border-color: var(--ti-chip-border);
        color: var(--ti-strong);
    }

    .btn-ti-more {
        color: var(--ti-accent-text);
    }

    .btn-ti-danger {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--ti-danger);
        border: 1px solid var(--ti-danger);
        color: #fff;
        font-weight: 500;
        font-size: 12px;
        border-radius: 8px;
        padding: 5px 14px;
    }

    .btn-ti-danger:hover,
    .btn-ti-danger:focus {
        background: var(--ti-danger-hover);
        border-color: var(--ti-danger-hover);
        color: #fff;
    }

    .btn-ti-danger:disabled {
        background: var(--ti-danger);
        border-color: var(--ti-danger);
        color: #fff;
        opacity: .5;
    }

    /* ------------------------------------------------------ bootstrap-select */

    /* bootstrap-select copies .form-control onto its wrapper; only the inner button is the field. */
    .ti-filters .bootstrap-select.form-control {
        border: 0;
        padding: 0;
        min-height: 0;
        background: transparent;
        box-shadow: none;
    }

    .bootstrap-select .dropdown-toggle {
        background: var(--ti-input-bg);
        border: 1px solid var(--ti-input-border);
        border-radius: 8px;
        color: var(--ti-ink);
        font-family: var(--ti-mono);
        font-size: 12px;
        min-height: 31px;
    }

    .bootstrap-select .dropdown-toggle:hover,
    .bootstrap-select .dropdown-toggle:focus,
    .bootstrap-select.show .dropdown-toggle {
        background: var(--ti-input-bg);
        color: var(--ti-ink);
        border-color: #0ea5e9;
        outline: 0 !important;
    }

    .bootstrap-select .dropdown-toggle .filter-option-inner-inner {
        color: var(--ti-ink);
    }

    .bootstrap-select .dropdown-menu {
        font-family: var(--ti-mono);
        font-size: 12px;
        border-color: var(--ti-line);
        border-radius: 8px;
    }

    .bootstrap-select .dropdown-item.active,
    .bootstrap-select .dropdown-item:active {
        background: var(--ti-info-bg);
        color: var(--ti-info-fg);
    }

    /* --------------------------------------------------------- prune modal */

    .ti-prune .modal-content {
        background: var(--ti-surface);
        color: var(--ti-ink);
        border: 1px solid var(--ti-line);
        border-radius: 12px;
    }

    .ti-prune .modal-header,
    .ti-prune .modal-footer {
        border-color: var(--ti-line);
    }

    .ti-prune .modal-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--ti-strong);
    }

    .ti-prune .form-check-label {
        font-family: var(--ti-font);
        font-size: 13px;
        color: var(--ti-ink);
        text-transform: none;
        letter-spacing: 0;
    }

    .ti-prune .form-check-input:checked {
        background-color: var(--ti-accent);
        border-color: var(--ti-accent);
    }

    .ti-prune .flatpickr-wrapper {
        display: block;
        width: 100%;
    }

    .ti-prune-count {
        min-height: 38px;
        padding: 8px 12px;
        border-radius: 8px;
        background: var(--ti-surface-2);
        border: 1px solid var(--ti-line);
        font-size: 13px;
    }

    .ti-prune-count strong {
        color: var(--ti-strong);
    }

    @media (max-width: 768px) {
        .ti-header-row,
        .ti-tabs {
            padding-left: 16px;
            padding-right: 16px;
        }

        .ti-wrap {
            padding: 12px 16px 40px;
        }

        .ti-sub {
            display: none;
        }

        .ti-kv th {
            width: 120px;
        }
    }
</style>
@endverbatim
