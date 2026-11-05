@verbatim
/* ------------------------------------------------------------ list state */

const states = {};

function freshState() {
    return { rows: [], cursor: null, exhausted: false, scanned: 0, running: false, controller: null, filters: emptyFilters(), loadedOnce: false };
}

function emptyFilters() {
    return { q: '', user: '', method: [], status: [], jobStatus: [], level: [], minMs: '', tag: '', from: '', to: '', take: String(settings.defaultTake) };
}

function stateFor(type) {
    if (!states[type]) {
        states[type] = freshState();
    }
    return states[type];
}

function toSummary(entry, type) {
    const content = entry.content || {};
    const summary = TYPES[type].summarize(content);
    summary.uuid = entry.id;
    summary.sequence = entry.sequence;
    summary.batchId = entry.batch_id;
    summary.createdAt = entry.created_at;
    summary.familyHash = entry.family_hash || content.hash || null;
    summary.hostname = content.hostname || '';
    summary.haystack = Object.values(summary).filter((v) => typeof v === 'string' || typeof v === 'number').join(' ').toLowerCase();
    return summary;
}

// Entries fetched per request: the chosen "Fetch" size, or the configured default.
function batchSize(filters) {
    return Number(filters.take) || settings.defaultTake;
}

function hasClientFilters(f) {
    return Boolean(f.q || f.user || f.method.length || f.status.length || f.jobStatus.length || f.level.length || f.minMs !== '' || f.from || f.to);
}

// True when every space-separated word of the needle occurs in the (lower-cased) text.
function hasAllWords(text, needle) {
    return needle.toLowerCase().split(/\s+/).filter(Boolean).every((word) => text.includes(word));
}

function matches(row, f, type) {
    if (f.q && !hasAllWords(row.haystack, f.q)) return false;
    if (f.user && !matchesUser(row, f.user)) return false;
    if (f.method.length && !f.method.includes(String(row.method || '').toUpperCase())) return false;
    if (f.status.length && !f.status.includes(String(row.status || '').charAt(0))) return false;
    if (f.jobStatus.length && !f.jobStatus.includes(row.jobStatus)) return false;
    if (f.level.length && !f.level.includes(String(row.level || '').toLowerCase())) return false;
    if (f.minMs !== '' && TYPES[type].numeric && Number(TYPES[type].numeric(row) || 0) < Number(f.minMs)) return false;
    if (f.from && row.createdAt < f.from) return false;
    if (f.to && row.createdAt > f.to) return false;
    return true;
}

// A number is a user id (exact); anything else is matched against name and email, all words.
function matchesUser(row, needle) {
    if (/^\d+$/.test(needle)) {
        return String(row.userId) === needle;
    }
    return hasAllWords(((row.user || '') + ' ' + (row.userEmail || '')).toLowerCase(), needle);
}

// A numeric user id is turned into Telescope's own "Auth:<id>" tag so the
// server filters it, unless a tag is already set (several tags are OR-ed).
function serverTag(f, type) {
    if (f.tag) return f.tag;
    if (type === 'requests' && /^\d+$/.test(f.user)) return 'Auth:' + f.user;
    return '';
}

/*
 * Fetch Telescope pages (newest first) until MATCH_TARGET new matches are
 * collected, the data runs out, the "from" time is passed, or Stop is hit.
 * Only slim summaries are kept; the full entry content is dropped at once.
 */
async function scan(type, reset) {
    const st = stateFor(type);
    if (st.controller) {
        st.controller.abort();
    }
    if (reset) {
        Object.assign(st, { rows: [], cursor: null, exhausted: false, scanned: 0 });
    }
    st.controller = new AbortController();
    st.running = true;
    st.error = null;
    st.loadedOnce = true;
    const signal = st.controller.signal;
    const f = st.filters;
    let found = 0;
    renderListBody(type);

    try {
        while (!st.exhausted && found < MATCH_TARGET) {
            const body = { take: batchSize(f) };
            if (st.cursor) body.before = st.cursor;
            const tag = serverTag(f, type);
            if (tag) body.tag = tag;
            const data = await api('POST', type, body, signal);
            setRecording(data.status);
            const entries = data.entries || [];
            if (!entries.length) {
                st.exhausted = true;
                break;
            }
            st.cursor = entries[entries.length - 1].sequence;
            st.scanned += entries.length;
            entries.forEach((entry) => {
                const row = toSummary(entry, type);
                if (matches(row, f, type)) {
                    st.rows.push(row);
                    found++;
                }
            });
            const oldest = entries[entries.length - 1].created_at;
            if (entries.length < body.take || (f.from && oldest < f.from)) {
                st.exhausted = true;
            }
            if (!hasClientFilters(f)) {
                break; // plain browsing: one page per click
            }
            renderListBody(type);
        }
    } catch (error) {
        if (error.name !== 'AbortError') {
            st.error = error;
        }
    } finally {
        st.running = false;
        st.controller = null;
        renderListBody(type);
    }
}

function stopScan(type) {
    const st = stateFor(type);
    if (st.controller) {
        st.controller.abort();
    }
}
@endverbatim
