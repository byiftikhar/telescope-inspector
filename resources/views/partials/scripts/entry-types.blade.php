@verbatim
/* ------------------------------------------------------------- entry types */

const when = { key: 'createdAt', label: 'When', render: (row) => el('span', { class: 'muted text-nowrap', text: row.createdAt }) };

const TYPES = {
    requests: {
        label: 'Requests',
        entryType: 'request',
        summarize: (c) => ({ method: c.method, uri: c.uri, action: c.controller_action, status: c.response_status, duration: c.duration, memory: c.memory, user: userLabel(c.user), userEmail: (c.user && c.user.email) || '', userId: c.user ? c.user.id : null, ip: c.ip_address }),
        title: (c) => (c.method || '') + ' ' + (c.uri || ''),
        columns: [
            when,
            { label: 'Method', render: (r) => badge(r.method, methodTone(r.method)) },
            { label: 'URI', render: (r) => uriCell(r.uri) },
            { label: 'Action', render: (r) => actionCell(r.action) },
            { label: 'Status', cls: 'text-center', render: (r) => statusBadge(r.status) },
            { label: 'Duration', cls: 'text-end', render: (r) => durationCell(r.duration) },
            { label: 'User', render: (r) => userCell(r) },
        ],
        numeric: (r) => r.duration,
    },
    'client-requests': {
        label: 'HTTP Client',
        entryType: 'client_request',
        summarize: (c) => ({ method: c.method, uri: c.uri, status: c.response_status, duration: c.duration }),
        title: (c) => (c.method || '') + ' ' + (c.uri || ''),
        columns: [
            when,
            { label: 'Method', render: (r) => badge(r.method, methodTone(r.method)) },
            { label: 'URI', render: (r) => uriCell(r.uri) },
            { label: 'Status', cls: 'text-center', render: (r) => (r.status ? statusBadge(r.status) : '') },
            { label: 'Duration', cls: 'text-end', render: (r) => durationCell(r.duration) },
        ],
        numeric: (r) => r.duration,
    },
    jobs: {
        label: 'Jobs',
        entryType: 'job',
        summarize: (c) => ({ name: c.name, queue: c.queue, connection: c.connection, jobStatus: c.status, tries: c.tries }),
        title: (c) => c.name,
        columns: [
            when,
            { label: 'Job', render: (r) => el('span', { class: 'mono wrap', text: r.name }) },
            { label: 'Queue', render: (r) => r.queue },
            { label: 'Connection', render: (r) => r.connection },
            { label: 'Status', cls: 'text-center', render: (r) => badge(r.jobStatus, jobTone(r.jobStatus), true) },
            { label: 'Tries', render: (r) => (r.tries === null || r.tries === undefined ? '' : String(r.tries)) },
        ],
    },
    commands: {
        label: 'Commands',
        entryType: 'command',
        summarize: (c) => ({ command: c.command, exitCode: c.exit_code }),
        title: (c) => c.command,
        columns: [
            when,
            { label: 'Command', render: (r) => el('span', { class: 'mono wrap', text: r.command }) },
            { label: 'Exit code', cls: 'text-center', render: (r) => badge(r.exitCode, Number(r.exitCode) === 0 ? 'ok' : 'bad', true) },
        ],
    },
    schedule: {
        label: 'Schedule',
        entryType: 'schedule',
        summarize: (c) => ({ command: c.command, description: c.description, expression: c.expression }),
        title: (c) => c.description || c.command,
        columns: [
            when,
            { label: 'Command', render: (r) => el('span', { class: 'mono wrap', text: r.command }) },
            { label: 'Description', render: (r) => r.description },
            { label: 'Expression', render: (r) => el('span', { class: 'mono', text: r.expression }) },
        ],
    },
    exceptions: {
        label: 'Exceptions',
        entryType: 'exception',
        summarize: (c) => ({ cls: c.class, message: c.message, location: projectPath(c.file) + ':' + (c.line || ''), occurrences: c.occurrences }),
        title: (c) => shortClass(c.class),
        columns: [
            when,
            { label: 'Exception', render: (r) => el('span', { class: 'mono', title: r.cls, text: shortClass(r.cls) }) },
            { label: 'Message', render: (r) => el('div', { class: 'clip', title: r.message, text: r.message }) },
            { label: 'Location', render: (r) => el('span', { class: 'mono wrap', text: r.location }) },
            { label: 'Seen', render: (r) => String(r.occurrences || 1) },
        ],
    },
    logs: {
        label: 'Logs',
        entryType: 'log',
        summarize: (c) => ({ level: c.level, message: clip(c.message, 2000) }),
        title: (c) => clip(c.message, 120),
        columns: [
            when,
            { label: 'Level', render: (r) => badge(r.level, levelTone(r.level)) },
            { label: 'Message', render: (r) => el('div', { class: 'clip', title: clip(r.message, 400), text: r.message }) },
        ],
    },
    queries: {
        label: 'Queries',
        entryType: 'query',
        summarize: (c) => ({ sql: clip(c.sql, 2000), time: c.time, slow: c.slow, location: c.file ? projectPath(c.file) + ':' + c.line : '', connection: c.connection }),
        title: (c) => 'Query (' + ms(c.time) + ')',
        columns: [
            when,
            { label: 'SQL', render: (r) => el('div', { class: 'mono clip', title: clip(r.sql, 600), text: r.sql }) },
            { label: 'Time', cls: 'text-end', render: (r) => (r.slow ? badge(ms(r.time), 'bad') : el('span', { class: 'ti-num', text: ms(r.time) })) },
            { label: 'Location', render: (r) => el('span', { class: 'mono wrap', text: r.location }) },
        ],
        numeric: (r) => r.time,
    },
    mail: {
        label: 'Mail',
        entryType: 'mail',
        summarize: (c) => ({ mailable: c.mailable, subject: c.subject, to: Object.keys(c.to || {}).join(', '), queued: c.queued }),
        title: (c) => c.subject || shortClass(c.mailable),
        columns: [
            when,
            { label: 'Mailable', render: (r) => el('span', { class: 'mono', title: r.mailable, text: shortClass(r.mailable) }) },
            { label: 'Subject', render: (r) => r.subject },
            { label: 'To', render: (r) => el('div', { class: 'clip', text: r.to }) },
        ],
    },
    notifications: {
        label: 'Notifications',
        entryType: 'notification',
        summarize: (c) => ({ notification: c.notification, channel: c.channel, notifiable: c.notifiable }),
        title: (c) => shortClass(c.notification),
        columns: [
            when,
            { label: 'Notification', render: (r) => el('span', { class: 'mono', title: r.notification, text: shortClass(r.notification) }) },
            { label: 'Channel', render: (r) => r.channel },
            { label: 'Notifiable', render: (r) => el('div', { class: 'clip', text: r.notifiable }) },
        ],
    },
    events: {
        label: 'Events',
        entryType: 'event',
        summarize: (c) => ({ name: c.name, listeners: (c.listeners || []).length }),
        title: (c) => c.name,
        columns: [
            when,
            { label: 'Event', render: (r) => el('span', { class: 'mono wrap', text: r.name }) },
            { label: 'Listeners', render: (r) => String(r.listeners) },
        ],
    },
    cache: {
        label: 'Cache',
        entryType: 'cache',
        summarize: (c) => ({ action: c.type, key: c.key }),
        title: (c) => (c.type || '') + ' ' + (c.key || ''),
        columns: [
            when,
            { label: 'Action', render: (r) => badge(r.action, 'mute') },
            { label: 'Key', render: (r) => el('span', { class: 'mono wrap', text: r.key }) },
        ],
    },
    redis: {
        label: 'Redis',
        entryType: 'redis',
        summarize: (c) => ({ command: clip(c.command, 1000), time: c.time, connection: c.connection }),
        title: (c) => clip(c.command, 80),
        columns: [
            when,
            { label: 'Command', render: (r) => el('div', { class: 'mono clip', text: r.command }) },
            { label: 'Time', render: (r) => ms(r.time) },
        ],
        numeric: (r) => r.time,
    },
};

// Related tabs offered on a detail page (entries sharing the same batch_id).
const RELATED = ['requests', 'commands', 'jobs', 'exceptions', 'logs', 'queries', 'client-requests', 'mail', 'notifications', 'events', 'cache', 'redis'];
@endverbatim
