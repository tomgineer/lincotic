import { editLinkRequestEvent } from './edit-mode.js';
import { confirmDialog } from '../lib/confirmation.js';

const editableControlSelector = '[data-edit-link-control], [data-editor-control]';
const replaceEditorContentEvent = 'lincotic:editor:set-content';

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
    const modalTitle = dialog.querySelector('[data-edit-link-title]');
    const modalDescription = dialog.querySelector('[data-edit-link-description]');
    const status = dialog.querySelector('[data-edit-link-status]');
    const saveButton = dialog.querySelector('[data-edit-link-save]');
    const deleteButton = dialog.querySelector('[data-edit-link-delete]');
    const pasteUrlButton = dialog.querySelector('[data-edit-link-paste-url]');
    const openUrlButton = dialog.querySelector('[data-edit-link-open-url]');
    const detailsTab = dialog.querySelector('[data-edit-link-details-tab]');
    const createEndpoint = dialog.dataset.createEndpoint;
    const endpointTemplate = dialog.dataset.endpointTemplate;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;
    let loadController = null;
    let loadSequence = 0;
    let mode = 'edit';

    if (!(form instanceof HTMLFormElement)
        || !(modalTitle instanceof HTMLElement)
        || !(modalDescription instanceof HTMLElement)
        || !(saveButton instanceof HTMLButtonElement)
        || !createEndpoint
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

    const defaultSaveLabel = () => mode === 'create' ? 'Add link' : 'Save changes';

    const setBusy = (busy, label = defaultSaveLabel()) => {
        form.setAttribute('aria-busy', String(busy));
        form.querySelectorAll(editableControlSelector).forEach((control) => {
            control.disabled = busy;
        });
        saveButton.disabled = busy;
        saveButton.textContent = label;
    };

    const setMode = (nextMode) => {
        mode = nextMode;
        const creating = mode === 'create';

        modalTitle.textContent = creating ? 'Add link' : 'Edit link';
        modalDescription.textContent = creating
            ? 'Add a new destination.'
            : 'Update the destination, organization, and status of this link.';

        const collectionControl = formControl(form, 'collection_id');

        if (collectionControl instanceof HTMLSelectElement) {
            const unassignedOption = collectionControl.querySelector(
                '[data-edit-link-unassigned-option]',
            );

            if (creating) {
                const option = unassignedOption ?? new Option('[Unassigned]', '', true, true);
                option.dataset.editLinkUnassignedOption = '';

                if (!unassignedOption) {
                    collectionControl.prepend(option);
                }

                option.selected = true;
                collectionControl.required = false;
            } else {
                unassignedOption?.remove();
                collectionControl.required = true;
            }
        }

        if (deleteButton instanceof HTMLButtonElement) {
            deleteButton.hidden = creating;
        }

        saveButton.textContent = defaultSaveLabel();
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
        setControlValue(form, 'documentation', link.documentation ?? '');
        setCheckboxValue(form, 'is_favorite', Boolean(link.is_favorite));
        setCheckboxValue(form, 'is_pending_review', Boolean(link.is_pending_review));
        setCheckboxValue(form, 'is_hidden', Boolean(link.is_hidden));

        const documentationControl = formControl(form, 'documentation');

        if (documentationControl instanceof HTMLTextAreaElement) {
            documentationControl.dispatchEvent(new Event(replaceEditorContentEvent));
        }
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
        setMode('edit');
        setStatus('Loading link…');
        setBusy(true, 'Loading…');

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

            if (!dialog.open) {
                dialog.showModal();
            }
        } catch (error) {
            if (error.name === 'AbortError' || sequence !== loadSequence) {
                return;
            }

            setStatus(error.message || 'Unable to load this link.', true);
            setBusy(false);

            if (!dialog.open) {
                dialog.showModal();
            }
        }
    };

    const openCreator = (trigger) => {
        if (!(trigger instanceof HTMLButtonElement)) {
            return;
        }

        loadSequence++;
        loadController?.abort();
        loadController = null;

        form.reset();
        clearValidation(form);
        setMode('create');
        setControlValue(form, 'id', '');

        const collectionId = trigger.dataset.collectionId ?? '';

        if (/^[1-9]\d*$/.test(collectionId)) {
            setControlValue(form, 'collection_id', collectionId);
        }

        setStatus('');
        setBusy(false);

        if (!dialog.open) {
            dialog.showModal();
        }

    };

    document.addEventListener(editLinkRequestEvent, (event) => {
        if (!(event instanceof CustomEvent)) {
            return;
        }

        void openEditor(event.detail?.link);
    });

    document.querySelectorAll('[data-add-link]').forEach((trigger) => {
        trigger.addEventListener('click', () => openCreator(trigger));
    });

    form.addEventListener('input', () => clearValidation(form));
    form.addEventListener('invalid', () => {
        if (detailsTab instanceof HTMLInputElement) {
            detailsTab.checked = true;
        }
    }, true);

    if (pasteUrlButton instanceof HTMLButtonElement) {
        pasteUrlButton.addEventListener('click', async () => {
            const urlControl = formControl(form, 'url');

            if (!(urlControl instanceof HTMLInputElement)) {
                return;
            }

            if (!navigator.clipboard?.readText) {
                setStatus('Clipboard access is not available in this browser.', true);
                return;
            }

            try {
                const clipboardValue = (await navigator.clipboard.readText()).trim();

                if (clipboardValue === '') {
                    setStatus('The clipboard is empty.', true);
                    return;
                }

                urlControl.value = clipboardValue;
                urlControl.dispatchEvent(new Event('input', { bubbles: true }));
                setStatus('');
                urlControl.focus();
            } catch {
                setStatus('Clipboard access was denied.', true);
                urlControl.focus();
            }
        });
    }

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
            if (mode !== 'edit') {
                return;
            }

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
        const creating = mode === 'create';

        if (!creating && (typeof id !== 'string' || id === '')) {
            setStatus('No link is selected.', true);
            return;
        }

        const payload = {
            title: data.get('title'),
            url: data.get('url'),
            collection_id: data.get('collection_id'),
            importance: data.get('importance'),
            description: data.get('description'),
            documentation: data.get('documentation'),
            is_favorite: data.get('is_favorite') === '1',
            is_pending_review: data.get('is_pending_review') === '1',
            is_hidden: data.get('is_hidden') === '1',
        };

        setStatus(creating ? 'Adding link…' : 'Saving changes…');
        setBusy(true, creating ? 'Adding…' : 'Saving…');

        try {
            const response = await fetch(
                creating ? createEndpoint : endpointFor(id),
                {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        [csrfHeader]: csrfToken,
                    },
                    body: JSON.stringify(payload),
                },
            );
            const result = await readResponse(response);

            if (!response.ok) {
                setBusy(false);

                if (result.errors && typeof result.errors === 'object') {
                    applyValidation(form, result.errors);
                    form.reportValidity();
                }

                throw new Error(result.error || (creating
                    ? 'Unable to add this link.'
                    : 'Unable to save this link.'));
            }

            dialog.hidden = true;
            dialog.close();
            window.location.reload();
        } catch (error) {
            setStatus(error.message || (creating
                ? 'Unable to add this link.'
                : 'Unable to save this link.'), true);
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
        setMode('edit');
        setBusy(false);
    });
}
