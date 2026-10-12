import { editLinkRequestEvent } from './edit-mode.js';
import { confirmDialog } from '../lib/confirmation.js';

const editableControlSelector = '[data-edit-link-control]';

function formControl(form, name) {
    return form.elements.namedItem(name);
}

function setControlValue(form, name, value) {
    const control = formControl(form, name);

    if (control instanceof RadioNodeList) {
        control.value = value;
    } else if (control instanceof HTMLInputElement
        || control instanceof HTMLSelectElement
        || control instanceof HTMLTextAreaElement) {
        control.value = value;
    }
}

function setCheckboxValue(form, name, checked) {
    const control = formControl(form, name);

    if (control instanceof HTMLInputElement) {
        control.checked = checked;
    }
}

function clearValidation(form) {
    form.querySelectorAll('[name]').forEach((control) => {
        if (typeof control.setCustomValidity === 'function') {
            control.setCustomValidity('');
        }
    });
}

function applyValidation(form, errors) {
    Object.entries(errors).forEach(([name, message]) => {
        const control = formControl(form, name);

        if (control instanceof RadioNodeList) {
            const firstControl = control[0];
            firstControl?.setCustomValidity?.(message);
        } else {
            control?.setCustomValidity?.(message);
        }
    });
}

export function initEditLink() {
    const dialog = document.querySelector('[data-edit-link-modal]');

    if (!(dialog instanceof HTMLDialogElement)) {
        return;
    }

    const form = dialog.querySelector('[data-edit-link-form]');
    const status = dialog.querySelector('[data-edit-link-status]');
    const saveButton = dialog.querySelector('[data-edit-link-save]');
    const deleteButton = dialog.querySelector('[data-edit-link-delete]');
    const openUrlButton = dialog.querySelector('[data-edit-link-open-url]');
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

    const setStatus = (message, isError = false) => {
        if (!(status instanceof HTMLElement)) {
            return;
        }

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

    const endpointFor = (id) => endpointTemplate.replace('__LINK_ID__', encodeURIComponent(id));

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

    const populateForm = (link) => {
        setControlValue(form, 'id', String(link.id));
        setControlValue(form, 'title', link.title ?? '');
        setControlValue(form, 'url', link.url ?? '');
        setControlValue(form, 'collection_id', String(link.collection_id ?? ''));
        setControlValue(form, 'importance', link.importance ?? '');
        setControlValue(form, 'description', link.description ?? '');
        setCheckboxValue(form, 'is_favorite', Boolean(link.is_favorite));
        setCheckboxValue(form, 'is_pending_review', Boolean(link.is_pending_review));
    };

    const openEditor = async (link) => {
        if (!(link instanceof HTMLAnchorElement) || !link.dataset.linkId) {
            return;
        }

        loadController?.abort();
        loadController = new AbortController();
        const sequence = ++loadSequence;
        const id = link.dataset.linkId;

        form.reset();
        clearValidation(form);
        setStatus('Loading link…');
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

            if (sequence !== loadSequence) {
                return;
            }

            if (!response.ok || !result.link) {
                throw new Error(result.error || 'Unable to load this link.');
            }

            populateForm(result.link);
            setStatus('');
            setBusy(false);
            formControl(form, 'title')?.focus();
        } catch (error) {
            if (error.name === 'AbortError' || sequence !== loadSequence) {
                return;
            }

            setStatus(error.message || 'Unable to load this link.', true);
            setBusy(false);
        }
    };

    document.addEventListener(editLinkRequestEvent, (event) => {
        if (!(event instanceof CustomEvent)) {
            return;
        }

        void openEditor(event.detail?.link);
    });

    form.addEventListener('input', () => clearValidation(form));

    if (openUrlButton instanceof HTMLButtonElement) {
        openUrlButton.addEventListener('click', () => {
            const urlControl = formControl(form, 'url');

            if (!(urlControl instanceof HTMLInputElement)) {
                return;
            }

            let url;

            try {
                url = new URL(urlControl.value.trim());

                if (!['http:', 'https:'].includes(url.protocol)) {
                    throw new TypeError('Unsupported URL protocol.');
                }
            } catch {
                urlControl.setCustomValidity('Enter a valid HTTP or HTTPS URL.');
                urlControl.reportValidity();
                urlControl.focus();
                return;
            }

            urlControl.setCustomValidity('');
            window.open(url.href, '_blank', 'noopener,noreferrer');
        });
    }

    if (deleteButton instanceof HTMLButtonElement) {
        deleteButton.addEventListener('click', async () => {
            const idControl = formControl(form, 'id');
            const titleControl = formControl(form, 'title');

            if (!(idControl instanceof HTMLInputElement) || idControl.value === '') {
                setStatus('No link is selected.', true);
                return;
            }

            const title = titleControl instanceof HTMLInputElement
                ? titleControl.value.trim()
                : '';
            const confirmed = await confirmDialog({
                title: 'Delete link?',
                message: title
                    ? `The link “${title}” will be removed. Do you want to continue?`
                    : 'This link will be removed. Do you want to continue?',
                confirmLabel: 'Delete link',
            });

            if (!confirmed) {
                return;
            }

            setStatus('Deleting link…');
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
                    throw new Error(result.error || 'Unable to delete this link.');
                }

                dialog.hidden = true;
                dialog.close();
                window.location.reload();
            } catch (error) {
                setStatus(error.message || 'Unable to delete this link.', true);
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
            setStatus('No link is selected.', true);
            return;
        }

        const payload = {
            title: data.get('title'),
            url: data.get('url'),
            collection_id: data.get('collection_id'),
            importance: data.get('importance'),
            description: data.get('description'),
            is_favorite: data.get('is_favorite') === '1',
            is_pending_review: data.get('is_pending_review') === '1',
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

                throw new Error(result.error || 'Unable to save this link.');
            }

            dialog.hidden = true;
            dialog.close();
            window.location.reload();
        } catch (error) {
            setStatus(error.message || 'Unable to save this link.', true);
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
