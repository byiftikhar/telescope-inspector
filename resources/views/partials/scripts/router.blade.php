@verbatim
/* ----------------------------------------------------------------- router */

// Clean URLs: {base}/requests, {base}/requests/<uuid>/<batch>. The server answers every
// path under the base with this page, so these links can be refreshed and shared.
function currentRoute() {
    const rest = location.pathname.startsWith(settings.baseUrl) ? location.pathname.slice(settings.baseUrl.length) : '';
    const parts = rest.split('/').filter(Boolean).map(decodeURIComponent);
    const type = TYPES[parts[0]] ? parts[0] : 'requests';
    return { type: type, uuid: parts[1] || null, batchId: parts[2] || null };
}

function pageUrl(type, uuid, batchId) {
    return settings.baseUrl + '/' + [type, uuid, batchId].filter(Boolean).map(encodeURIComponent).join('/');
}

function navigate(url) {
    history.pushState(null, '', url);
    route();
}

// Plain left clicks on the page's own links stay in the page; Ctrl/Cmd/Shift-click still open a new tab.
function followInPageLink(event) {
    const link = event.target.closest('a[data-nav]');
    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }
    event.preventDefault();
    navigate(link.getAttribute('href'));
}

// Links saved before clean URLs ({base}#/requests/...) still open the right view.
function upgradeHashLink() {
    if (!location.hash.startsWith('#/')) {
        return;
    }
    const [type, uuid, batchId] = location.hash.slice(2).split('/');
    history.replaceState(null, '', pageUrl(TYPES[type] ? type : 'requests', uuid, batchId));
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
document.addEventListener('click', followInPageLink);
window.addEventListener('popstate', route);
upgradeHashLink();
route();
@endverbatim
