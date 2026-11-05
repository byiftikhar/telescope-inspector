@verbatim
/* -------------------------------------------------------------- list view */

function renderTabs(active) {
    const nav = document.getElementById('ti-tabs');
    nav.replaceChildren(...Object.entries(TYPES).map(([key, def]) => el('a', { href: '#/' + key, class: key === active ? 'active' : '', text: def.label })));
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

    document.getElementById('ti-thead').replaceChildren(el('tr', {}, TYPES[type].columns.map((col) => el('th', { text: col.label }))));

    if (!st.loadedOnce) {
        scan(type, true);
    } else {
        renderListBody(type);
    }
}

// A clickable table row for one summary; opens the entry's detail page.
function entryRow(type, row) {
    const tr = el('tr', { onclick: () => { location.hash = '#/' + type + '/' + row.uuid + '/' + row.batchId; } });
    TYPES[type].columns.forEach((col) => tr.appendChild(el('td', {}, col.render(row))));
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
    const progress = document.getElementById('ti-progress');
    progress.textContent = filtered
        ? 'Scanned ' + st.scanned.toLocaleString() + ' entries, ' + st.rows.length.toLocaleString() + ' match' + (st.running ? '. Scanning...' : '')
        : st.rows.length.toLocaleString() + ' shown' + (st.running ? '. Loading...' : '');
    document.getElementById('ti-stop').classList.toggle('d-none', !st.running);

    const footer = document.getElementById('ti-list-footer');
    const parts = [];
    if (st.error) {
        parts.push(errorBox(st.error));
    } else if (!st.running && !st.rows.length) {
        parts.push(el('div', { class: 'ti-empty', text: st.exhausted ? 'Nothing found.' : 'No entries yet.' }));
    }
    if (!st.running && !st.exhausted && !st.error) {
        parts.push(el('button', {
            type: 'button',
            class: 'btn btn-sm btn-ti-outline mt-2',
            text: filtered ? 'Keep searching older entries' : 'Load older',
            onclick: () => scan(type, false),
        }));
    }
    if (st.exhausted && st.rows.length) {
        parts.push(el('div', { class: 'ti-note', text: 'Reached the end of the recorded entries.' }));
    }
    footer.replaceChildren(...parts);
}
@endverbatim
