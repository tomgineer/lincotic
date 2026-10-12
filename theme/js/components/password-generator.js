import { copyInputValue, showCopyFeedback } from '../lib/clipboard.js';

export function initPasswordGenerator() {
    const generator = document.querySelector('[data-password-generator]');

    if (!(generator instanceof HTMLElement)) {
        return;
    }

    const status = generator.querySelector('[data-copy-status]');
    generator.querySelectorAll('[data-copy-password]').forEach((button) => {
        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        button.addEventListener('click', async () => {
            const entry = button.closest('[data-password-entry]');
            const input = entry?.querySelector('[data-password-value]');

            if (!(input instanceof HTMLInputElement)) {
                return;
            }

            try {
                await copyInputValue(input);
                showCopyFeedback(
                    generator,
                    button,
                    status,
                    `${input.value.length} character password copied.`,
                );
            } catch {
                if (status instanceof HTMLElement) {
                    status.textContent = 'Unable to copy the password. Select it and copy manually.';
                }
            }
        });
    });
}
