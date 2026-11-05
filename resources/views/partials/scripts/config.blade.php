@verbatim
const settings = JSON.parse(document.getElementById('ti-settings').textContent);
const TELESCOPE_PATH = settings.telescopeUrl;
const API = TELESCOPE_PATH + '/telescope-api/';
const MATCH_TARGET = settings.matchTarget;  // matches to collect per "search" / "load more" click
const AUTO_OPEN_LIMIT = 20000;    // detail sections smaller than this (chars) open by default
const PREVIEW_LIMIT = 50000;      // characters rendered before "Show all"
const RELATED_TAKE = 25;

if (!TELESCOPE_PATH) {
    document.getElementById('ti-main').appendChild(el('div', { class: 'ti-alert', text: 'Telescope is not enabled in this application (TELESCOPE_ENABLED), so there is no data to show.' }));
    return;
}
document.getElementById('ti-open-telescope').href = TELESCOPE_PATH;
@endverbatim
