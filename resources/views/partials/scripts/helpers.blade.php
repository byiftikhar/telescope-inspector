@verbatim
/* ---------------------------------------------------------------- helpers */

function el(tag, attrs, children) {
    const node = document.createElement(tag);
    Object.entries(attrs || {}).forEach(([key, value]) => {
        if (value === undefined || value === null || value === false) {
            return;
        }
        if (key === 'class') {
            node.className = value;
        } else if (key === 'text') {
            node.textContent = value;
        } else if (key.startsWith('on')) {
            node.addEventListener(key.slice(2), value);
        } else {
            node.setAttribute(key, value);
        }
    });
    [].concat(children || []).forEach((child) => {
        if (child === null || child === undefined || child === false) {
            return;
        }
        node.appendChild(typeof child === 'string' || typeof child === 'number' ? document.createTextNode(String(child)) : child);
    });
    return node;
}

function loader(text) {
    return el('div', { class: 'ti-loader', role: 'status' }, [
        el('span', { class: 'spinner-border spinner-border-sm', 'aria-hidden': 'true' }),
        el('span', { text: text }),
    ]);
}

function badge(text, tone, pill) {
    return el('span', { class: 'ti-badge ti-b-' + (tone || 'mute') + (pill ? ' is-pill' : ''), text: String(text) });
}

// Status codes are round pills in the design; methods and other labels are square badges.
function statusBadge(status) {
    return badge(status, httpTone(status), true);
}

function methodTone(method) {
    const map = { GET: 'info', HEAD: 'info', POST: 'indigo', PUT: 'warn', PATCH: 'warn', DELETE: 'bad' };
    return map[String(method).toUpperCase()] || 'mute';
}

const FAST_MS = 400;
const SLOW_MS = 800;

// Duration coloured by speed: under 400 ms green, under 800 ms amber, slower red.
function durationCell(value) {
    if (value === undefined || value === null || value === '') {
        return '';
    }
    const number = Number(value);
    let tone = 'bad';
    if (number < FAST_MS) {
        tone = 'ok';
    } else if (number < SLOW_MS) {
        tone = 'warn';
    }
    return el('span', { class: 'ti-num ti-t-' + tone, text: ms(value) });
}

function userCell(row) {
    if (!row.user) {
        return el('span', { class: 'ti-guest', text: 'Guest' });
    }
    return el('span', { class: 'ti-user', title: row.userEmail || '', text: row.user });
}

function actionCell(action) {
    if (action === 'Closure') {
        return el('span', { class: 'ti-closure', text: 'Closure' });
    }
    return el('div', { class: 'clip', title: action, text: action });
}

function httpTone(status) {
    const code = Number(status);
    if (code >= 500) return 'bad';
    if (code >= 400) return 'warn';
    if (code >= 300) return 'info';
    if (code >= 200) return 'ok';
    return 'mute';
}

function levelTone(level) {
    const map = { debug: 'mute', info: 'info', notice: 'info', warning: 'warn', error: 'bad', critical: 'bad', alert: 'bad', emergency: 'bad' };
    return map[String(level).toLowerCase()] || 'mute';
}

function jobTone(status) {
    const map = { processed: 'ok', failed: 'bad', pending: 'warn' };
    return map[status] || 'mute';
}

function ms(value) {
    if (value === undefined || value === null || value === '') return '';
    return Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' ms';
}

function bytesLabel(chars) {
    if (chars < 1024) return chars + ' B';
    if (chars < 1024 * 1024) return (chars / 1024).toFixed(1) + ' KB';
    return (chars / 1024 / 1024).toFixed(2) + ' MB';
}

function shortClass(name) {
    return String(name || '').split('\\').pop();
}

// Only the path is shown (query strings, e.g. DataTables', run to several KB);
// the full URI stays in the tooltip, the copy button and the detail page.
function uriCell(uri) {
    const value = String(uri || '');
    const path = value.split('?')[0];
    const text = el('span', { class: 'ti-uri-text', title: clip(value, 2000), text: path });
    const copy = el('button', {
        type: 'button',
        class: 'ti-copy',
        title: 'Copy URI',
        'aria-label': 'Copy URI',
        onclick: (event) => {
            event.stopPropagation();
            const icon = copy.querySelector('i');
            copyText(value).then(() => flash(() => { icon.className = 'ri-check-line'; }, () => { icon.className = 'ri-file-copy-line'; }));
        },
    }, el('i', { class: 'ri-file-copy-line' }));
    return el('div', { class: 'ti-uri' }, [text, copy]);
}

// Copies text to the clipboard; resolves once copied.
function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    const area = el('textarea', { style: 'position:fixed;opacity:0' });
    area.value = text;
    document.body.appendChild(area);
    area.select();
    document.execCommand('copy');
    area.remove();
    return Promise.resolve();
}

// Shows a short confirmation (e.g. "Copied") and puts the original back after a moment.
function flash(show, restore) {
    show();
    setTimeout(restore, 1200);
}

// "/home/x/project/app/Foo.php" -> "app/Foo.php" (cuts at the first known project folder).
function projectPath(file) {
    const value = String(file || '');
    const match = value.match(/\/(app|vendor|routes|resources|database|config|bootstrap|public|storage|tests)\//);
    return match ? value.slice(match.index + 1) : value;
}

function userLabel(user) {
    if (!user || typeof user !== 'object') return '';
    return user.name || user.email || (user.id ? '#' + user.id : '');
}

function clip(text, max) {
    const value = String(text === undefined || text === null ? '' : text);
    return value.length > max ? value.slice(0, max) + '...' : value;
}

function xsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

class ApiError extends Error {
    constructor(kind, message) {
        super(message);
        this.kind = kind;
    }
}

async function request(method, url, body, signal) {
    const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': xsrfToken() };
    if (body) {
        headers['Content-Type'] = 'application/json';
    }
    const response = await fetch(url, {
        method: method,
        credentials: 'same-origin',
        headers: headers,
        body: body ? JSON.stringify(body) : undefined,
        signal: signal,
    });
    if (response.status === 401 || response.status === 403) {
        throw new ApiError('auth', 'You are not signed in, or your account is not allowed to do this.');
    }
    if (response.status === 419) {
        throw new ApiError('session', 'Your session expired. Reload the page.');
    }
    if (response.status === 422) {
        const data = await response.json().catch(() => ({}));
        throw new ApiError('validation', data.message || 'Some of the values are not valid.');
    }
    if (!response.ok) {
        throw new ApiError('http', 'The server answered with HTTP ' + response.status + '.');
    }
    return response.json();
}

// Telescope's own JSON API, relative to its path.
function api(method, path, body, signal) {
    return request(method, API + path, body, signal);
}

function errorBox(error, title) {
    const box = el('div', { class: 'ti-alert' }, [el('strong', { text: (title || 'Could not load data.') + ' ' }), error.message || String(error)]);
    if (error.kind === 'auth' && settings.loginUrl) {
        box.appendChild(el('div', { class: 'mt-1' }, [el('a', { href: settings.loginUrl, text: 'Sign in' })]));
    }
    return box;
}

function setRecording(status) {
    const node = document.getElementById('ti-recording');
    const tones = { enabled: 'ok', paused: 'warn', disabled: 'bad' };
    const labels = { enabled: 'active', paused: 'paused', disabled: 'off' };
    node.className = 'ti-recording' + (tones[status] ? ' is-' + tones[status] : '');
    node.querySelector('.text').textContent = 'Recording: ' + (labels[status] || 'unknown');
}
@endverbatim
