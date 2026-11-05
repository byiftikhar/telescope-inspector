@verbatim
/* ----------------------------------------------------------------- router */

function currentRoute() {
    const parts = location.hash.replace(/^#\/?/, '').split('/');
    const type = TYPES[parts[0]] ? parts[0] : 'requests';
    return { type: type, uuid: parts[1] || null, batchId: parts[2] || null };
}

function route() {
    const r = currentRoute();
    renderTabs(r.type);
    if (r.uuid) {
        renderDetail(r.type, r.uuid, r.batchId);
    } else {
        renderList(r.type);
    }
    window.scrollTo(0, 0);
}

window.addEventListener('hashchange', route);
route();
@endverbatim
