@verbatim
/* ----------------------------------------------------------------- prune */

const PRUNE_DATE_FORMAT = 'Y-m-d H:i';
const PREVIEW_DELAY_MS = 350;

// Telescope stores server time. The page gets the server clock at render time,
// so presets like "older than 1 day" are computed on the server's clock, not the browser's.
const serverClockOffset = parseServerTime(settings.serverNow).getTime() - Date.now();

// "2026-11-05 13:45:00" -> a Date whose local fields equal the server wall clock.
function parseServerTime(text) {
    const [datePart, timePart] = text.split(' ');
    const [year, month, day] = datePart.split('-').map(Number);
    const [hours, minutes, seconds] = timePart.split(':').map(Number);
    return new Date(year, month - 1, day, hours, minutes, seconds || 0);
}

function serverTimeHoursAgo(hours) {
    return new Date(Date.now() + serverClockOffset - hours * 3600 * 1000);
}

function pruneCriteria(form) {
    return {
        from: form.from.value.trim() || null,
        to: form.to.value.trim(),
        types: $(form.types).val() || [],
        keep_exceptions: form.keep_exceptions.checked,
    };
}

function typeLabels(entryTypes) {
    return Object.values(TYPES).filter((def) => entryTypes.includes(def.entryType)).map((def) => def.label);
}

// Plain-sentence summary of what will be deleted, used in the count box and the confirm dialog.
function describePrune(criteria) {
    const range = criteria.from ? 'between ' + criteria.from + ' and ' + criteria.to : 'up to ' + criteria.to;
    const types = criteria.types.length ? typeLabels(criteria.types).join(', ') : 'all types';
    const exceptions = criteria.keep_exceptions ? ', keeping exceptions' : '';
    return 'Entries recorded ' + range + ' (' + types + exceptions + ').';
}

// "1 entry", "1,250 entries".
function entriesLabel(count) {
    return count.toLocaleString() + (count === 1 ? ' entry' : ' entries');
}

function resetLists() {
    Object.keys(states).forEach((type) => delete states[type]);
}

function initPrune() {
    if (!settings.prune) {
        return;
    }
    const modalElement = document.getElementById('ti-prune-modal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const form = document.getElementById('ti-prune-form');
    const countBox = document.getElementById('ti-prune-count');
    const submit = document.getElementById('ti-prune-submit');
    let previewTimer = null;
    let previewRun = 0;
    let matchCount = 0;

    const schedulePreview = () => {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(preview, PREVIEW_DELAY_MS);
    };

    // "static" keeps the calendar inside the modal, so the modal's focus trap allows typing the time.
    const pickerOptions = { enableTime: true, time_24hr: true, dateFormat: PRUNE_DATE_FORMAT, allowInput: true, static: true, onChange: schedulePreview };
    const fromPicker = flatpickr(form.from, pickerOptions);
    const toPicker = flatpickr(form.to, pickerOptions);

    Object.values(TYPES).forEach((def) => form.types.appendChild(el('option', { value: def.entryType, text: def.label })));
    $(form.types).selectpicker({ container: false, style: 'btn-sm', width: '100%' });
    form.types.addEventListener('change', schedulePreview);
    form.keep_exceptions.addEventListener('change', schedulePreview);
    form.from.addEventListener('input', schedulePreview);
    form.to.addEventListener('input', schedulePreview);

    form.querySelectorAll('[data-hours]').forEach((button) => button.addEventListener('click', () => {
        fromPicker.clear(false);
        toPicker.setDate(serverTimeHoursAgo(Number(button.dataset.hours)), true);
    }));

    async function preview() {
        const criteria = pruneCriteria(form);
        const run = ++previewRun;
        submit.disabled = true;
        if (!criteria.to) {
            countBox.replaceChildren(el('span', { class: 'ti-note', text: 'Pick a quick range, or at least the "To" date, to see how many entries match.' }));
            return;
        }
        countBox.replaceChildren(loader('Counting matching entries...'));
        try {
            const data = await request('POST', settings.prune.previewUrl, criteria);
            if (run !== previewRun) {
                return; // a newer preview is already on its way
            }
            matchCount = data.count;
            countBox.replaceChildren(
                el('div', {}, [el('strong', { text: entriesLabel(matchCount) }), matchCount === 1 ? ' matches.' : ' match.']),
                el('div', { class: 'ti-note mt-1', text: describePrune(criteria) }),
            );
            submit.disabled = matchCount === 0;
        } catch (error) {
            if (run === previewRun) {
                countBox.replaceChildren(errorBox(error, 'Cannot count entries.'));
            }
        }
    }

    async function prune(event) {
        event.preventDefault();
        const criteria = pruneCriteria(form);
        const answer = await Swal.fire({
            target: modalElement,
            theme: effectiveTheme(),
            icon: 'warning',
            title: 'Delete ' + entriesLabel(matchCount) + '?',
            text: describePrune(criteria) + ' This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete them',
            cancelButtonText: 'Cancel',
            focusCancel: true,
            buttonsStyling: false,
            customClass: { confirmButton: 'btn btn-sm btn-ti-danger', cancelButton: 'btn btn-sm btn-ti-outline ms-2' },
        });
        if (!answer.isConfirmed) {
            return;
        }

        submit.disabled = true;
        countBox.replaceChildren(loader('Deleting entries...'));
        try {
            const data = await request('POST', settings.prune.url, criteria);
            modal.hide();
            resetLists();
            route();
            Swal.fire({
                theme: effectiveTheme(),
                icon: 'success',
                title: entriesLabel(data.deleted) + ' deleted',
                timer: 2500,
                showConfirmButton: false,
            });
        } catch (error) {
            countBox.replaceChildren(errorBox(error, 'Nothing was deleted.'));
            submit.disabled = false;
        }
    }

    form.addEventListener('submit', prune);
    document.getElementById('ti-prune-open').addEventListener('click', () => {
        modal.show();
        preview();
    });
}

initPrune();
@endverbatim
