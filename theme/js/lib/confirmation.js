export function confirmDialog({
    title = 'Are you sure?',
    message = 'Please confirm that you want to continue.',
    confirmLabel = 'Confirm',
} = {}) {
    const dialog = document.querySelector('[data-confirmation-modal]');
    const titleElement = dialog?.querySelector('[data-confirmation-title]');
    const messageElement = dialog?.querySelector('[data-confirmation-message]');
    const confirmButton = dialog?.querySelector('[data-confirmation-confirm]');
    const cancelButtons = dialog?.querySelectorAll('[data-confirmation-cancel]');

    if (!(dialog instanceof HTMLDialogElement)
        || !(titleElement instanceof HTMLElement)
        || !(messageElement instanceof HTMLElement)
        || !(confirmButton instanceof HTMLButtonElement)
        || !cancelButtons?.length) {
        return Promise.resolve(false);
    }

    if (dialog.open) {
        return Promise.resolve(false);
    }

    titleElement.textContent = title;
    messageElement.textContent = message;
    confirmButton.textContent = confirmLabel;

    return new Promise((resolve) => {
        let settled = false;

        const finish = (confirmed) => {
            if (settled) return;

            settled = true;
            confirmButton.removeEventListener('click', confirm);
            cancelButtons.forEach((button) => button.removeEventListener('click', cancel));
            dialog.removeEventListener('close', cancel);
            dialog.removeEventListener('cancel', cancel);

            if (dialog.open) {
                dialog.close();
            }

            resolve(confirmed);
        };

        const confirm = () => finish(true);
        const cancel = (event) => {
            event?.preventDefault();
            finish(false);
        };

        confirmButton.addEventListener('click', confirm);
        cancelButtons.forEach((button) => button.addEventListener('click', cancel));
        dialog.addEventListener('close', cancel);
        dialog.addEventListener('cancel', cancel);

        dialog.showModal();

        confirmButton.focus();
    });
}
