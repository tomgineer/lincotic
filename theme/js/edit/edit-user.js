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
    const updateEndpointTemplate = dialog.dataset.updateEndpointTemplate;
    const csrfHeader = dialog.dataset.csrfHeader;
    let csrfToken = dialog.dataset.csrfToken;
    let currentUserId = '';

    if (!(form instanceof HTMLFormElement)
        || !(saveButton instanceof HTMLButtonElement)
        || !createEndpoint
        || !updateEndpointTemplate
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

    const setBusy = (busy, label = currentUserId === '' ? 'Add user' : 'Save changes') => {
        form.setAttribute('aria-busy', String(busy));
        form.querySelectorAll(editableControlSelector).forEach((control) => {
            control.disabled = busy;
        });
        saveButton.disabled = busy;
        saveButton.textContent = label;
    };

    const prepareCreate = () => {
        currentUserId = '';
        form.reset();
        clearValidation(form);
        setStatus('');

        const id = formControl(form, 'id');
        const password = formControl(form, 'password');
        const title = dialog.querySelector('[data-edit-user-title]');
        const description = dialog.querySelector('[data-edit-user-description]');
        const passwordHint = dialog.querySelector('[data-edit-user-password-hint]');

        if (id instanceof HTMLInputElement) id.value = '';
        if (password instanceof HTMLInputElement) password.required = true;
        if (title instanceof HTMLElement) title.textContent = 'Add user';
        if (description instanceof HTMLElement) description.textContent = 'Create a new account and choose its access level.';
        if (passwordHint instanceof HTMLElement) {
            passwordHint.textContent = `Use at least ${password?.minLength || 8} characters.`;
        }

        setBusy(false);
    };

    const prepareEdit = (trigger) => {
        currentUserId = trigger.dataset.userId || '';
        form.reset();
        clearValidation(form);
        setStatus('');

        const id = formControl(form, 'id');
        const username = formControl(form, 'username');
        const email = formControl(form, 'email');
        const password = formControl(form, 'password');
        const role = formControl(form, 'role');
        const active = formControl(form, 'active');
        const title = dialog.querySelector('[data-edit-user-title]');
        const description = dialog.querySelector('[data-edit-user-description]');
        const passwordHint = dialog.querySelector('[data-edit-user-password-hint]');

        if (id instanceof HTMLInputElement) id.value = currentUserId;
        if (username instanceof HTMLInputElement) username.value = trigger.dataset.userUsername || '';
        if (email instanceof HTMLInputElement) email.value = trigger.dataset.userEmail || '';
        if (password instanceof HTMLInputElement) {
            password.value = '';
            password.required = false;
        }
        if (role instanceof RadioNodeList) role.value = trigger.dataset.userRole || 'user';
        if (active instanceof HTMLInputElement) active.checked = trigger.dataset.userActive === '1';
        if (title instanceof HTMLElement) title.textContent = 'Edit user';
        if (description instanceof HTMLElement) description.textContent = 'Update this account and its access level.';
        if (passwordHint instanceof HTMLElement) passwordHint.textContent = 'Leave blank to keep the current password.';

        setBusy(false);
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
            prepareCreate();
            document.getElementById('users-popover')?.hidePopover?.();

            if (!dialog.open) {
                dialog.showModal();
            }

        });
    });

    document.querySelectorAll('[data-edit-user]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            prepareEdit(trigger);
            document.getElementById('users-popover')?.hidePopover?.();

            if (!dialog.open) {
                dialog.showModal();
            }

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

        const endpoint = currentUserId === ''
            ? createEndpoint
            : updateEndpointTemplate.replace('__USER_ID__', encodeURIComponent(currentUserId));
        const action = currentUserId === '' ? 'Adding' : 'Saving';

        setStatus(`${action} user…`);
        setBusy(true, `${action}…`);

        try {
            const response = await fetch(endpoint, {
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
        prepareCreate();
    });

    prepareCreate();
}
