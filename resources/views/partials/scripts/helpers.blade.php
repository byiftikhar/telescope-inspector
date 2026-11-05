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

function badge(text, tone) {
    return el('span', { class: 'ti-badge ti-b-' + (tone || 'mute'), text: String(text) });
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

// Path in full, query string shortened (DataTables queries run to several KB).
function uriCell(uri) {
    const value = String(uri || '');
    const at = value.indexOf('?');
    if (at === -1) {
        return el('span', { class: 'mono wrap', text: value });
    }
    return el('span', { class: 'mono wrap', title: clip(value, 2000) }, [value.slice(0, at), el('span', { class: 'ti-query', text: clip(value.slice(at), 90) })]);
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

async function api(method, path, body, signal) {
    const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': xsrfToken() };
    if (body) {
        headers['Content-Type'] = 'application/json';
    }
    const response = await fetch(API + path, {
        method: method,
        credentials: 'same-origin',
        headers: headers,
        body: body ? JSON.stringify(body) : undefined,
        signal: signal,
    });
    if (response.status === 401 || response.status === 403) {
        throw new ApiError('auth', 'You are not signed in to the portal, or your account is not allowed to view Telescope.');
    }
    if (response.status === 419) {
        throw new ApiError('session', 'Your session expired. Reload the page.');
    }
    if (!response.ok) {
        throw new ApiError('http', 'Telescope answered with HTTP ' + response.status + '.');
    }
    return response.json();
}

function errorBox(error) {
    const box = el('div', { class: 'ti-alert' }, [el('strong', { text: 'Could not load data. ' }), error.message || String(error)]);
    if (error.kind === 'auth' && settings.loginUrl) {
        box.appendChild(el('div', { class: 'mt-1' }, [el('a', { href: settings.loginUrl, text: 'Sign in' })]));
    }
    return box;
}

function setRecording(status) {
    const node = document.getElementById('ti-recording');
    const tones = { enabled: 'ok', paused: 'warn', disabled: 'bad' };
    node.className = 'ti-badge ti-b-' + (tones[status] || 'mute');
    node.textContent = 'Recording: ' + (status || 'unknown');
}
@endverbatim
