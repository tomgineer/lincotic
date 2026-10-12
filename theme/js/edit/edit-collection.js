import { confirmDialog } from '../lib/confirmation.js';

const editableControlSelector = '[data-edit-collection-control]';

function formControl(form, name) {
    return form.elements.namedItem(name);
}

function setControlValue(form, name, value) {
    const control = formControl(form, name);

    if (control instanceof HTMLInputElement || control instanceof HTMLTextAreaElement) {
        control.value = value;
    }
}

function clearValidation(form) {
    form.querySelectorAll('[name]').forEach((control) => {
        control.setCustomValidity?.('');
    });
}

function applyValidation(form, errors) {
    Object.entries(errors).forEach(([name, message]) => {
        formControl(form, name)?.setCustomValidity?.(message);
    });
}

export function initEditCollection() {
    const dialog = document.querySelector('[data-edit-collection-modal]');

    if (!(dialog instanceof HTMLDialogElement)) {
        return;
    }

    const form = dialog.querySelector('[data-edit-collection-form]');
    const status = dialog.querySelector('[data-edit-collection-status]');
    const saveButton = dialog.querySelector('[data-edit-collection-save]');
    const deleteButton = dialog.querySelector('[data-edit-collection-delete]');
    const endpointTemplate = dialog.dataset.endpointTemplate;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;
    let loadController = null;
    let loadSequence = 0;

    if (!(form instanceof HTMLFormElement)
        || !(saveButton instanceof HTMLButtonElement)
        || !endpointTemplate
        || !csrfHeader
        || !csrfToken) {
        return;
    }

    const endpointFor = (id) => endpointTemplate.replace(
        '__COLLECTION_ID__',
        encodeURIComponent(id),
    );

    const setStatus = (message, isError = false) => {
        if (!(status instanceof HTMLElement)) return;

        status.textContent = message;
        status.classList.toggle('text-error', isError);
        status.classList.toggle('text-base-content/60', !isError);
    };

    const setBusy = (busy, label = 'Save changes') => {
        form.setAttribute('aria-busy', String(busy));
        form.querySelectorAll(editableControlSelector).forEach((control) => {
            control.disabled = busy;
        });
        saveButton.disabled = busy;
        saveButton.textContent = label;
    };

    const updateCsrf = (result) => {
        if (typeof result.csrf === 'string' && result.csrf !== '') {
            csrfToken = result.csrf;
            dialog.dataset.csrfToken = result.csrf;
        }
    };

    const readResponse = async (response) => {
        let result = {};

        try {
            result = await response.json();
        } catch {
            // A generic message below handles non-JSON server responses.
        }

        updateCsrf(result);
        return result;
    };

    const populateForm = (collection) => {
        setControlValue(form, 'id', String(collection.id));
        setControlValue(form, 'name', collection.name ?? '');
        setControlValue(form, 'slug', collection.slug ?? '');
        setControlValue(form, 'description', collection.description ?? '');
        setControlValue(form, 'icon', collection.icon ?? '');

        const featured = formControl(form, 'is_featured');

        if (featured instanceof HTMLInputElement) {
            featured.checked = Boolean(collection.is_featured);
        }
    };

    const openEditor = async (trigger) => {
        if (!(trigger instanceof HTMLButtonElement)
            || !/^[1-9]\d*$/.test(trigger.dataset.collectionId ?? '')) {
            return;
        }

        loadController?.abort();
        loadController = new AbortController();
        const sequence = ++loadSequence;
        const id = trigger.dataset.collectionId;

        form.reset();
        clearValidation(form);
        setStatus('Loading collection…');
        setBusy(true, 'Loading…');

        if (!dialog.open) {
            dialog.showModal();
        }

        try {
            const response = await fetch(endpointFor(id), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: loadController.signal,
            });
            const result = await readResponse(response);

            if (sequence !== loadSequence) return;

            if (!response.ok || !result.collection) {
                throw new Error(result.error || 'Unable to load this collection.');
            }

            populateForm(result.collection);
            setStatus('');
            setBusy(false);
            formControl(form, 'name')?.focus();
        } catch (error) {
            if (error.name === 'AbortError' || sequence !== loadSequence) return;

            setStatus(error.message || 'Unable to load this collection.', true);
            setBusy(false);
        }
    };

    document.querySelectorAll('[data-edit-collection]').forEach((trigger) => {
        trigger.addEventListener('click', () => void openEditor(trigger));
    });

    form.addEventListener('input', () => clearValidation(form));

    if (deleteButton instanceof HTMLButtonElement) {
        deleteButton.addEventListener('click', async () => {
            const idControl = formControl(form, 'id');
            const nameControl = formControl(form, 'name');

            if (!(idControl instanceof HTMLInputElement) || idControl.value === '') {
                setStatus('No collection is selected.', true);
                return;
            }

            const name = nameControl instanceof HTMLInputElement
                ? nameControl.value.trim()
                : '';
            const confirmed = await confirmDialog({
                title: 'Delete collection?',
                message: name
                    ? `The collection “${name}” will be removed. Do you want to continue?`
                    : 'This collection will be removed. Do you want to continue?',
                confirmLabel: 'Delete collection',
            });

            if (!confirmed) return;

            setStatus('Deleting collection…');
            setBusy(true);

            try {
                const response = await fetch(endpointFor(idControl.value), {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        [csrfHeader]: csrfToken,
                    },
                });
                const result = await readResponse(response);

                if (!response.ok) {
                    throw new Error(result.error || 'Unable to delete this collection.');
                }

                dialog.hidden = true;
                dialog.close();
                window.location.reload();
            } catch (error) {
                setStatus(error.message || 'Unable to delete this collection.', true);
                setBusy(false);
            }
        });
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearValidation(form);

        const data = new FormData(form);
        const id = data.get('id');

        if (typeof id !== 'string' || id === '') {
            setStatus('No collection is selected.', true);
            return;
        }

        const payload = {
            name: data.get('name'),
            slug: data.get('slug'),
            description: data.get('description'),
            icon: data.get('icon'),
            is_featured: data.get('is_featured') === '1',
        };

        setStatus('Saving changes…');
        setBusy(true, 'Saving…');

        try {
            const response = await fetch(endpointFor(id), {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfToken,
                },
                body: JSON.stringify(payload),
            });
            const result = await readResponse(response);

            if (!response.ok) {
                setBusy(false);

                if (result.errors && typeof result.errors === 'object') {
                    applyValidation(form, result.errors);
                    form.reportValidity();
                }

                throw new Error(result.error || 'Unable to save this collection.');
            }

            dialog.hidden = true;
            dialog.close();
            window.location.reload();
        } catch (error) {
            setStatus(error.message || 'Unable to save this collection.', true);
            setBusy(false);
        }
    });

    dialog.addEventListener('close', () => {
        loadSequence++;
        loadController?.abort();
        loadController = null;
        form.reset();
        clearValidation(form);
        setStatus('');
        setBusy(false);
    });
}
