@verbatim
/* ----------------------------------------------------------------- theme */

// partials/theme-boot already applied the stored choice before paint; this wires the switch.
const THEME_KEY = 'telescope-inspector.theme';
const THEME_MODES = ['auto', 'light', 'dark'];
const THEME_LABELS = { auto: 'Auto', light: 'Light', dark: 'Dark' };
const THEME_ICONS = { auto: 'ri-contrast-2-line', light: 'ri-sun-line', dark: 'ri-moon-line' };
const darkSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
let themePreference = readThemePreference();

function readThemePreference() {
    try {
        const stored = localStorage.getItem(THEME_KEY);
        return THEME_MODES.includes(stored) ? stored : 'auto';
    } catch (error) {
        return 'auto';
    }
}

function storeThemePreference(preference) {
    themePreference = preference;
    try {
        localStorage.setItem(THEME_KEY, preference);
    } catch (error) {
        // Storage blocked: the choice still applies until the page is reloaded.
    }
}

// The theme actually shown: "auto" resolves to the operating system's choice.
function effectiveTheme() {
    if (themePreference !== 'auto') {
        return themePreference;
    }
    return darkSchemeQuery.matches ? 'dark' : 'light';
}

function applyTheme() {
    const theme = effectiveTheme();
    document.documentElement.setAttribute('data-theme', theme);
    document.documentElement.setAttribute('data-bs-theme', theme);
    document.getElementById('ti-flatpickr-dark').disabled = theme !== 'dark';

    const button = document.getElementById('ti-theme');
    button.querySelector('i').className = THEME_ICONS[themePreference];
    button.querySelector('span').textContent = THEME_LABELS[themePreference];
    button.title = 'Theme: ' + THEME_LABELS[themePreference] + '. Click to switch.';
}

document.getElementById('ti-theme').addEventListener('click', () => {
    const next = THEME_MODES[(THEME_MODES.indexOf(themePreference) + 1) % THEME_MODES.length];
    storeThemePreference(next);
    applyTheme();
});
darkSchemeQuery.addEventListener('change', applyTheme);
applyTheme();
@endverbatim
