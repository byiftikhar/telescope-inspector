@verbatim
<script>
    // Runs before the stylesheet is applied so the page never flashes the wrong theme.
    // The choice (auto, light or dark) is a per-browser convenience; auto follows the OS.
    (function () {
        var preference = 'auto';
        try {
            preference = localStorage.getItem('telescope-inspector.theme') || 'auto';
        } catch (error) {
            preference = 'auto';
        }
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var theme = preference === 'dark' || (preference === 'auto' && prefersDark) ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', theme);
        document.documentElement.setAttribute('data-bs-theme', theme);
        var flatpickrDark = document.getElementById('ti-flatpickr-dark');
        if (flatpickrDark) {
            flatpickrDark.disabled = theme !== 'dark';
        }
    })();
</script>
@endverbatim
