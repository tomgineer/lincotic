const editableControlSelector = '[data-edit-user-control]';

function formControl(form, name) {
    return form.elements.namedItem(name);
}

function clearValidation(form) {
    form.querySelectorAll('[name]').forEach((control) => {
        control.setCustomValidity?.('');
    });
}

function applyValidation(form, errors) {
    Object.entries(errors).forEach(([name, message]) => {
        const control = formControl(form, name);

        if (control instanceof RadioNodeList) {
            control[0]?.setCustomValidity?.(message);
        } else {
            control?.setCustomValidity?.(message);
        }
    });
}

export function initEditUser() {
    const dialog = document.querySelector('[data-edit-user-modal]');

    if (!(dialog instanceof HTMLDialogElement)) {
        return;
    }

    const form = dialog.querySelector('[data-edit-user-form]');
    const status = dialog.querySelector('[data-edit-user-status]');
    const saveButton = dialog.querySelector('[data-edit-user-save]');
    const createEndpoint = dialog.dataset.createEndpoint;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;

    if (!(form instanceof HTMLFormElement)
        || !(saveButton instanceof HTMLButtonElement)
        || !createEndpoint
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

    const setBusy = (busy, label = 'Add user') => {
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

    document.querySelectorAll('[data-add-user]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            form.reset();
            clearValidation(form);
            setStatus('');
            setBusy(false);

            if (!dialog.open) {
                dialog.showModal();
            }

            formControl(form, 'username')?.focus();
        });
    });

    form.addEventListener('input', () => clearValidation(form));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearValidation(form);

        if (!form.reportValidity()) {
            return;
        }

        const data = new FormData(form);
        const payload = {
            username: data.get('username'),
            email: data.get('email'),
            password: data.get('password'),
            role: data.get('role'),
            active: data.get('active') === '1',
        };

        setStatus('Adding user…');
        setBusy(true, 'Adding…');

        try {
            const response = await fetch(createEndpoint, {
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

                throw new Error(result.error || 'Unable to add this user.');
            }

            dialog.hidden = true;
            dialog.close();
            window.location.reload();
        } catch (error) {
            setStatus(error.message || 'Unable to add this user.', true);
            setBusy(false);
        }
    });

    dialog.addEventListener('close', () => {
        form.reset();
        clearValidation(form);
        setStatus('');
        setBusy(false);
    });
}
