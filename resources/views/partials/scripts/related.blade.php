@verbatim
/* ---------------------------------------------- related (same batch) tabs */

function relatedCard(entry, batchId, title) {
    const nav = el('ul', { class: 'nav nav-pills ti-related-tabs gap-1' });
    const pane = el('div', { class: 'mt-2' }, el('div', { class: 'ti-note', text: 'Checking what else was recorded...' }));
    const card = el('div', { class: 'ti-card' }, [
        el('div', { class: 'ti-card-h' }, [el('h6', { text: title }), el('span', { class: 'ti-note mono', text: batchId })]),
        el('div', { class: 'ti-card-b' }, [nav, pane]),
    ]);

    // One tiny probe per type (take 2, the entry itself may be one) so only tabs with entries show.
    Promise.all(RELATED.map((other) => api('POST', other, { batch_id: batchId, take: 2 })
        .then((data) => ((data.entries || []).some((e) => e.id !== entry.id) ? other : null))
        .catch(() => null)))
        .then((found) => {
            const available = found.filter(Boolean);
            if (!available.length) {
                pane.replaceChildren(el('div', { class: 'ti-note', text: 'Nothing else was recorded in this batch.' }));
                return;
            }
            const open = (other, link) => {
                nav.querySelectorAll('.nav-link').forEach((node) => node.classList.remove('active'));
                link.classList.add('active');
                loadRelated(other, entry, batchId, pane);
            };
            available.forEach((other) => {
                const link = el('a', { href: 'javascript:void(0)', class: 'nav-link', text: TYPES[other].label });
                link.addEventListener('click', () => open(other, link));
                nav.appendChild(el('li', { class: 'nav-item' }, link));
            });
            open(available[0], nav.querySelector('.nav-link'));
        });
    return card;
}

function loadRelated(other, entry, batchId, pane) {
    const rows = [];
    let cursor = null;
    let done = false;
    const filter = el('input', { type: 'text', class: 'form-control form-control-sm mb-2', placeholder: 'Filter loaded ' + TYPES[other].label.toLowerCase() });
    const stats = el('div', { class: 'ti-note mb-2' });
    const tbody = el('tbody');
    const footer = el('div', { class: 'p-2 text-center' });
    const table = el('div', { class: 'table-responsive' }, el('table', { class: 'ti-table' }, [
        el('thead', {}, el('tr', {}, TYPES[other].columns.map((col) => el('th', { text: col.label })).concat(other === 'queries' ? [el('th', { text: 'Repeats' })] : []))),
        tbody,
    ]));
    pane.replaceChildren(filter, stats, table, footer);

    const draw = () => {
        const repeats = {};
        rows.forEach((r) => { if (r.familyHash) repeats[r.familyHash] = (repeats[r.familyHash] || 0) + 1; });
        const visible = rows.filter((r) => r.uuid !== entry.id && hasAllWords(r.haystack, filter.value));
        tbody.replaceChildren(...visible.map((row) => {
            const tr = entryRow(other, row);
            if (other === 'queries') {
                const count = (row.familyHash && repeats[row.familyHash]) || 1;
                tr.appendChild(el('td', {}, count > 1 ? badge(count + 'x', 'warn') : '1'));
            }
            return tr;
        }));
        if (other === 'queries') {
            const total = rows.reduce((sum, r) => sum + Number(r.time || 0), 0);
            const slow = rows.filter((r) => r.slow).length;
            const duplicated = Object.values(repeats).filter((n) => n > 1).length;
            stats.textContent = rows.length + ' loaded, ' + ms(total) + ' in total, ' + slow + ' slow, ' + duplicated + ' repeated statements (possible N+1).';
        } else {
            stats.textContent = rows.length + ' loaded.';
        }
    };

    const more = async () => {
        footer.replaceChildren(el('span', { class: 'ti-note', text: 'Loading...' }));
        try {
            const body = { batch_id: batchId, take: RELATED_TAKE };
            if (cursor) body.before = cursor;
            const data = await api('POST', other, body);
            const entries = data.entries || [];
            entries.forEach((e) => rows.push(toSummary(e, other)));
            if (entries.length) cursor = entries[entries.length - 1].sequence;
            done = entries.length < RELATED_TAKE;
            draw();
            footer.replaceChildren(done ? '' : el('button', { type: 'button', class: 'btn btn-sm btn-ti-outline', text: 'Load ' + RELATED_TAKE + ' more', onclick: more }));
        } catch (error) {
            footer.replaceChildren(errorBox(error));
        }
    };

    filter.addEventListener('input', draw);
    more();
}
@endverbatim
