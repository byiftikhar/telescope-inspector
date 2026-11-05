<header class="ti-header">
    <div class="ti-header-row">
        <div class="ti-brand">
            <div class="ti-logo" aria-hidden="true">
                <span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" x2="22" y1="12" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                </span>
            </div>
            <h1>
                Telescope Inspector
                @if ($settings['version'])
                    <span class="ti-version">{{ $settings['version'] }}</span>
                @endif
            </h1>
            <span class="ti-sub">Lightweight search over Telescope's entries. Times are server time ({{ $settings['timezone'] }}).</span>
        </div>
        <div class="ti-header-actions">
            <span id="ti-recording" class="ti-recording" title="Telescope recording status">
                <span class="dot"></span><span class="text">Recording: unknown</span>
            </span>
            @if ($settings['prune'])
                <button type="button" class="ti-head-btn" id="ti-prune-open">
                    <i class="ri-delete-bin-6-line"></i><span>Prune</span>
                </button>
            @endif
            <button type="button" class="ti-head-btn" id="ti-theme" title="Theme">
                <i class="ri-contrast-2-line"></i><span class="label-mono">Auto</span>
            </button>
            <a id="ti-open-telescope" class="ti-head-btn" href="/telescope" target="_blank" rel="noopener">
                <span>Open Telescope</span><i class="ri-external-link-line"></i>
            </a>
        </div>
    </div>
    <nav class="ti-tabs" id="ti-tabs" aria-label="Entry types"></nav>
</header>
<main class="ti-wrap" id="ti-main"></main>
