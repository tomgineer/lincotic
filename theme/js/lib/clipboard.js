const resetTimers = new WeakMap();

export async function copyInputValue(input) {
    if (navigator.clipboard?.writeText) {
        try {
            await navigator.clipboard.writeText(input.value);
            return;
        } catch {
            // Fall through to the selection-based copy method.
        }
    }

    input.focus({ preventScroll: true });
    input.select();
    input.setSelectionRange(0, input.value.length);

    if (!document.execCommand('copy')) {
        throw new Error('Copy failed');
    }

    input.setSelectionRange(0, 0);
}

export function showCopyFeedback(root, button, status, message) {
    root.querySelectorAll('[data-copied="true"]').forEach((copiedButton) => {
        copiedButton.removeAttribute('data-copied');
    });

    button.dataset.copied = 'true';

    if (status instanceof HTMLElement) {
        status.textContent = message;
    }

    const activeTimer = resetTimers.get(button);
    window.clearTimeout(activeTimer);

    resetTimers.set(button, window.setTimeout(() => {
        button.removeAttribute('data-copied');
        resetTimers.delete(button);
    }, 1600));
}
