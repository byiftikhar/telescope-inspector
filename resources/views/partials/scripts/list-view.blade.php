@verbatim
/* -------------------------------------------------------------- list view */

function renderTabs(active) {
    const nav = document.getElementById('ti-tabs');
    nav.replaceChildren(...Object.entries(TYPES).map(([key, def]) => {
        const isActive = key === active;
        return el('a', { href: pageUrl(key), 'data-nav': '', class: isActive ? 'active' : '', 'aria-current': isActive ? 'page' : null }, [
            def.label,
            isActive ? el('span', { class: 'ti-tab-count', id: 'ti-tab-count' }) : null,
        ]);
    }));
}

function readFilters(form) {
    const multi = (id) => $(form).find('#' + id).val() || [];
    return {
        q: form.q.value.trim(),
        user: form.user.value.trim(),
        method: multi('f-method'),
        status: multi('f-status'),
        jobStatus: multi('f-job-status'),
        level: multi('f-level'),
        minMs: form.minMs.value.trim(),
        tag: form.tag.value.trim(),
        from: form.from.value.trim(),
        to: form.to.value.trim(),
        take: String($(form).find('#f-take').val() || settings.defaultTake),
    };
}

function writeFilters(form, f) {
    form.q.value = f.q;
    form.user.value = f.user;
    form.minMs.value = f.minMs;
    form.tag.value = f.tag;
    form.from._flatpickr.setDate(f.from || null, false);
    form.to._flatpickr.setDate(f.to || null, false);
    $(form).find('#f-method').selectpicker('val', f.method);
    $(form).find('#f-status').selectpicker('val', f.status);
    $(form).find('#f-job-status').selectpicker('val', f.jobStatus);
    $(form).find('#f-level').selectpicker('val', f.level);
    $(form).find('#f-take').selectpicker('val', String(f.take));
}

function renderList(type) {
    const main = document.getElementById('ti-main');
    main.replaceChildren(document.getElementById('tpl-list').content.cloneNode(true));
    const form = document.getElementById('ti-filter-form');

    form.querySelectorAll('[data-types]').forEach((group) => {
        group.classList.toggle('d-none', !group.dataset.types.split(',').includes(type));
    });
    form.querySelectorAll('.ti-date').forEach((input) => {
        flatpickr(input, { enableTime: true, time_24hr: true, dateFormat: 'Y-m-d H:i', allowInput: true });
    });
    $(form).find('.ti-picker').selectpicker({ container: false, style: 'btn-sm', width: '100%' });
    form.q.placeholder = 'Search ' + TYPES[type].label.toLowerCase() + ' (all words must match)';

    const st = stateFor(type);
    writeFilters(form, st.filters);

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        st.filters = readFilters(form);
        scan(type, true);
    });
    document.getElementById('ti-reset').addEventListener('click', () => {
        st.filters = emptyFilters();
        writeFilters(form, st.filters);
        scan(type, true);
    });
    document.getElementById('ti-stop').addEventListener('click', () => stopScan(type));
    document.getElementById('ti-refresh').addEventListener('click', () => scan(type, true));
    document.getElementById('ti-list-title').textContent = TYPES[type].label;

    document.getElementById('ti-thead').replaceChildren(el('tr', {}, TYPES[type].columns.map((col) => el('th', { class: col.cls, scope: 'col', text: col.label }))));

    if (!st.loadedOnce) {
        scan(type, true);
    } else {
        renderListBody(type);
    }
}

// Ctrl/Cmd-click opens the entry in a new tab, like a link would.
function openRow(event, url) {
    if (event.ctrlKey || event.metaKey) {
        window.open(url, '_blank', 'noopener');
        return;
    }
    navigate(url);
}

// A clickable table row for one summary; opens the entry's detail page.
function entryRow(type, row) {
    const url = pageUrl(type, row.uuid, row.batchId);
    const tr = el('tr', { onclick: (event) => openRow(event, url) });
    TYPES[type].columns.forEach((col) => tr.appendChild(el('td', { class: col.cls }, col.render(row))));
    return tr;
}

function isListOf(type) {
    const route = currentRoute();
    return route.type === type && !route.uuid;
}

function renderListBody(type) {
    const tbody = document.getElementById('ti-tbody');
    if (!tbody || !isListOf(type)) {
        return;
    }
    const st = stateFor(type);

    tbody.replaceChildren(...st.rows.map((row) => entryRow(type, row)));

    const filtered = hasClientFilters(st.filters);
    const shown = st.rows.length.toLocaleString();
    document.getElementById('ti-list-count').textContent = filtered ? shown + ' of ' + st.scanned.toLocaleString() + ' scanned' : shown + ' fetched';
    const tabCount = document.getElementById('ti-tab-count');
    if (tabCount) {
        tabCount.textContent = shown;
    }
    document.getElementById('ti-stop').classList.toggle('d-none', !st.running);

    renderListState(st);
    renderFooterMeta(st, filtered);
    renderFooterActions(type, st, filtered);
}

// Errors and "nothing found" sit between the table and the footer bar.
function renderListState(st) {
    const box = document.getElementById('ti-list-state');
    if (st.error) {
        box.replaceChildren(el('div', { class: 'py-3' }, errorBox(st.error)));
    } else if (!st.running && !st.rows.length) {
        box.replaceChildren(el('div', { class: 'ti-empty', text: st.exhausted ? 'Nothing found.' : 'No entries yet.' }));
    } else {
        box.replaceChildren();
    }
}

function renderFooterMeta(st, filtered) {
    const shown = el('strong', { text: st.rows.length.toLocaleString() });
    const summary = filtered
        ? ['Showing ', shown, ' matches of ', el('strong', { text: st.scanned.toLocaleString() }), ' scanned']
        : ['Showing ', shown, ' fetched entries'];
    const parts = [el('span', {}, summary)];
    const host = st.rows.length ? st.rows[0].hostname : '';
    if (host) {
        parts.push(el('span', { class: 'ti-sep', text: '|' }));
        parts.push(el('span', {}, [el('span', { class: 'ti-live-dot' }), 'Server: ', el('strong', { text: host })]));
    }
    document.getElementById('ti-footer-meta').replaceChildren(...parts);
}

function renderFooterActions(type, st, filtered) {
    const parts = [];
    if (st.running) {
        parts.push(loader(loadingText(st, filtered)));
    } else if (st.exhausted && st.rows.length) {
        parts.push(el('span', { text: 'Reached the end of the recorded entries.' }));
    } else if (!st.exhausted && !st.error) {
        parts.push(el('button', {
            type: 'button',
            class: 'btn btn-ti-outline btn-ti-more',
            text: (filtered ? 'Keep searching' : 'Load older') + ' (' + batchSize(st.filters) + ')',
            onclick: () => scan(type, false),
        }));
    }
    document.getElementById('ti-list-footer').replaceChildren(...parts);
}

function loadingText(st, filtered) {
    if (filtered) {
        return 'Searching older entries (' + st.scanned.toLocaleString() + ' checked so far)...';
    }
    return st.rows.length ? 'Loading older entries...' : 'Loading entries...';
}
@endverbatim
