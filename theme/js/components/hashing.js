import { copyInputValue, showCopyFeedback } from '../lib/clipboard.js';

export function initHashing() {
    const root = document.querySelector('[data-hashing-utility]');

    if (!(root instanceof HTMLElement)) {
        return;
    }

    const input = root.querySelector('[data-hash-input]');
    const trim = root.querySelector('[data-hash-trim]');
    const status = root.querySelector('[data-hash-status]');
    const byteCount = root.querySelector('[data-hash-byte-count]');
    const outputs = root.querySelectorAll('[data-hash-output]');
    const endpoint = root.dataset.hashUrl;
    const csrfHeader = root.dataset.csrfHeader;
    let csrfToken = root.dataset.csrfToken;
    let debounceTimer;
    let activeRequest;

    if (!(input instanceof HTMLTextAreaElement) || !endpoint || !csrfHeader || !csrfToken) {
        return;
    }

    const clearOutputs = () => {
        outputs.forEach((output) => {
            if (output instanceof HTMLInputElement) {
                output.value = '';
                output.closest('[data-copy-entry]')?.querySelector('[data-copy-hash]')?.setAttribute('disabled', '');
            }
        });
    };

    const updateByteCount = () => {
        if (byteCount instanceof HTMLElement) {
            const bytes = new TextEncoder().encode(input.value).length;
            byteCount.textContent = `${bytes.toLocaleString('en-US')} ${bytes === 1 ? 'byte' : 'bytes'}`;
        }
    };

    const generate = async () => {
        const value = trim instanceof HTMLInputElement && trim.checked
            ? input.value.trim()
            : input.value;

        updateByteCount();

        if (value === '') {
            activeRequest?.abort();
            clearOutputs();

            if (status instanceof HTMLElement) {
                status.textContent = 'Waiting for input.';
            }

            return;
        }

        activeRequest?.abort();
        activeRequest = new AbortController();

        if (status instanceof HTMLElement) {
            status.textContent = 'Generating hashes…';
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    [csrfHeader]: csrfToken,
                },
                body: JSON.stringify({ value }),
                signal: activeRequest.signal,
            });

            const result = await response.json();

            if (!response.ok || !result.hashes) {
                throw new Error(result.error || 'Unable to generate hashes.');
            }

            if (typeof result.csrf === 'string' && result.csrf !== '') {
                csrfToken = result.csrf;
                root.dataset.csrfToken = result.csrf;
            }

            outputs.forEach((output) => {
                if (!(output instanceof HTMLInputElement)) {
                    return;
                }

                output.value = result.hashes[output.dataset.hashOutput] ?? '';
                const button = output.closest('[data-copy-entry]')?.querySelector('[data-copy-hash]');

                if (button instanceof HTMLButtonElement) {
                    button.disabled = output.value === '';
                }
            });

            if (status instanceof HTMLElement) {
                status.textContent = 'All five hashes are ready.';
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            clearOutputs();

            if (status instanceof HTMLElement) {
                status.textContent = error.message || 'Unable to generate hashes.';
            }
        }
    };

    const scheduleGeneration = (delay = 220) => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(generate, delay);
    };

    input.addEventListener('input', () => {
        updateByteCount();
        scheduleGeneration();
    });

    if (trim instanceof HTMLInputElement) {
        trim.addEventListener('change', () => scheduleGeneration(0));
    }

    root.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-copy-hash]');

        if (!(button instanceof HTMLButtonElement)) {
            return;
        }

        const output = button.closest('[data-copy-entry]')?.querySelector('[data-hash-output]');

        if (!(output instanceof HTMLInputElement) || output.value === '') {
            return;
        }

        try {
            await copyInputValue(output);
            showCopyFeedback(root, button, status, `${button.dataset.copyHash.toUpperCase()} copied.`);
        } catch {
            if (status instanceof HTMLElement) {
                status.textContent = 'Unable to copy this hash. Select it and copy manually.';
            }
        }
    });

    updateByteCount();
}
