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

// "/" focuses the search box, as in most developer tools, unless the user is typing or a dialog is open.
function focusSearchShortcut(event) {
    if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) {
        return;
    }
    const active = document.activeElement;
    const typing = active && (['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName) || active.isContentEditable);
    const search = document.getElementById('f-q');
    if (typing || !search || document.querySelector('.modal.show, .swal2-container')) {
        return;
    }
    event.preventDefault();
    search.focus();
}

document.addEventListener('keydown', focusSearchShortcut);
window.addEventListener('hashchange', route);
route();
@endverbatim
