@verbatim
/* ------------------------------------------------------------ detail view */

/*
 * Telescope's own show endpoint also returns every entry of the batch
 * (thousands of queries for a heavy request). The list endpoint filtered by
 * batch_id returns only entries of this type, so it is tried first.
 */
async function fetchEntry(type, uuid, batchId) {
    if (batchId) {
        const data = await api('POST', type, { batch_id: batchId, take: 50 });
        setRecording(data.status);
        const hit = (data.entries || []).find((entry) => entry.id === uuid);
        if (hit) {
            return hit;
        }
    }
    try {
        const data = await api('GET', type + '/' + encodeURIComponent(uuid));
        return data.entry;
    } catch (error) {
        if (error.kind === 'not-found') {
            return null; // pruned, or a link from another environment
        }
        throw error;
    }
}

function isScalar(value) {
    return value === null || ['string', 'number', 'boolean'].includes(typeof value);
}

async function renderDetail(type, uuid, batchId) {
    const main = document.getElementById('ti-main');
    main.replaceChildren(el('div', { class: 'ti-empty' }, loader('Loading entry...')));

    let entry;
    try {
        entry = await fetchEntry(type, uuid, batchId);
    } catch (error) {
        main.replaceChildren(backLink(type), errorBox(error));
        return;
    }
    if (currentRoute().uuid !== uuid) {
        return; // user navigated away meanwhile
    }
    if (!entry) {
        main.replaceChildren(backLink(type), el('div', { class: 'ti-alert', text: 'This entry no longer exists (Telescope prunes old entries).' }));
        return;
    }

    const content = entry.content || {};
    const overview = [['When', entry.created_at], ['Batch', entry.batch_id], ['Sequence', entry.sequence]];
    const sections = [];

    if (content.user && typeof content.user === 'object') {
        overview.push(['User', [userLabel(content.user), content.user.email && content.user.name ? ' (' + content.user.email + ')' : ''].join('')]);
    }
    Object.entries(content).forEach(([key, value]) => {
        const inOverview = key !== 'user' && isScalar(value) && String(value).length <= 300;
        (inOverview ? overview : sections).push([key, value]);
    });

    const header = el('div', { class: 'ti-card' }, [
        el('div', { class: 'ti-card-h' }, [
            el('h6', { class: 'mono wrap', text: clip(TYPES[type].title(content) || type, 300) }),
            content.response_status ? statusBadge(content.response_status) : null,
            content.status && type === 'jobs' ? badge(content.status, jobTone(content.status), true) : null,
            content.level ? badge(content.level, levelTone(content.level)) : null,
            el('a', { class: 'ms-auto', href: TELESCOPE_PATH + '/' + type + '/' + entry.id, target: '_blank', rel: 'noopener', text: 'Open in Telescope' }),
        ]),
        el('div', { class: 'ti-card-b' }, [
            el('table', { class: 'ti-kv' }, el('tbody', {}, overview.map(([k, v]) => el('tr', {}, [el('th', { text: k }), el('td', { class: 'mono', text: v === null ? 'null' : String(v) })])))),
        ]),
    ]);

    const related = [relatedCard(entry, entry.batch_id, type === 'jobs' ? 'Recorded where the job was dispatched' : 'Recorded in the same batch')];
    if (type === 'jobs' && content.updated_batch_id && content.updated_batch_id !== entry.batch_id) {
        related.unshift(relatedCard(entry, content.updated_batch_id, 'Recorded while the job ran'));
    }

    main.replaceChildren(backLink(type), header, ...sections.map(([key, value]) => sectionCard(key, value, content)), ...related);
}

function backLink(type) {
    return el('div', { class: 'mb-2' }, [el('a', { href: pageUrl(type), 'data-nav': '', class: 'btn btn-sm btn-ti-outline' }, [el('i', { class: 'ri-arrow-left-line' }), ' Back to ' + TYPES[type].label])]);
}

function sectionCard(key, value, content) {
    const text = typeof value === 'string' ? value : JSON.stringify(value, null, 2);
    const size = text ? text.length : 0;
    const body = el('div', { class: 'ti-card-b' });
    let rendered = false;

    const show = () => {
        if (rendered) return;
        rendered = true;
        body.appendChild(renderSection(key, value, text, content));
    };
    const toggle = () => {
        const hidden = body.classList.toggle('d-none');
        if (!hidden) show();
        icon.className = hidden ? 'ri-arrow-right-s-line' : 'ri-arrow-down-s-line';
    };
    const icon = el('i', { class: 'ri-arrow-right-s-line' });
    const card = el('div', { class: 'ti-card' }, [
        el('div', { class: 'ti-card-h ti-sec-h', onclick: toggle }, [
            icon,
            el('h6', { text: key.replace(/_/g, ' ') }),
            el('span', { class: 'size', text: bytesLabel(size) }),
            el('button', {
                type: 'button',
                class: 'btn btn-sm btn-ti-outline ms-auto py-0',
                text: 'Copy',
                onclick: (event) => {
                    event.stopPropagation();
                    const button = event.currentTarget;
                    copyText(text).then(() => flash(() => { button.textContent = 'Copied'; }, () => { button.textContent = 'Copy'; }));
                },
            }),
        ]),
        body,
    ]);
    body.classList.add('d-none');
    if (size <= AUTO_OPEN_LIMIT) {
        toggle();
    }
    return card;
}

function renderSection(key, value, text, content) {
    if (key === 'line_preview' && value && typeof value === 'object') {
        return el('pre', { class: 'ti-pre' }, Object.entries(value).map(([line, code]) => el('span', {
            class: Number(line) === Number(content.line) ? 'ti-line-hl' : '',
            text: line.padStart(5, ' ') + '  ' + code + '\n',
        })));
    }
    if (key === 'trace' && Array.isArray(value)) {
        return el('pre', { class: 'ti-pre' }, value.map((frame) => (frame.file || '[internal]') + ':' + (frame.line || '') + '\n').join(''));
    }
    return longText(text);
}

function longText(text) {
    const pre = el('pre', { class: 'ti-pre', text: text.length > PREVIEW_LIMIT ? text.slice(0, PREVIEW_LIMIT) : text });
    if (text.length <= PREVIEW_LIMIT) {
        return pre;
    }
    const more = el('button', {
        type: 'button',
        class: 'btn btn-sm btn-ti-outline mt-2',
        text: 'Show all (' + bytesLabel(text.length) + ')',
        onclick: () => { pre.textContent = text; more.remove(); },
    });
    return el('div', {}, [pre, el('div', { class: 'ti-note mt-1', text: 'Showing the first ' + bytesLabel(PREVIEW_LIMIT) + '.' }), more]);
}
@endverbatim
