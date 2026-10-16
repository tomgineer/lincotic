const editableLinkSelector = '.link-card';
const editModeStorageKey = 'lincotic.editMode';
export const editLinkRequestEvent = 'lincotic:edit-link';

function restoreEditMode() {
    try {
        return sessionStorage.getItem(editModeStorageKey) === '1';
    } catch {
        return false;
    }
}

function persistEditMode(enabled) {
    try {
        if (enabled) {
            sessionStorage.setItem(editModeStorageKey, '1');
        } else {
            sessionStorage.removeItem(editModeStorageKey);
        }
    } catch {
        // Edit mode still works when browser storage is unavailable.
    }
}

function isVisible(element) {
    return !element.hidden
        && element.getClientRects().length > 0
        && window.getComputedStyle(element).visibility !== 'hidden';
}

export function initEditMode() {
    const toggle = document.querySelector('[data-edit-mode-toggle]');
    let transitionResetFrame = 0;

    if (!(toggle instanceof HTMLInputElement)) {
        return;
    }

    const syncEditableLinks = () => {
        const links = [...document.querySelectorAll(editableLinkSelector)];
        const editableLinks = toggle.checked
            ? new Set(links.filter(isVisible))
            : new Set();

        document.documentElement.classList.add('edit-mode-syncing');

        links.forEach((link) => {
            link.classList.toggle('mode-edit', editableLinks.has(link));
        });

        cancelAnimationFrame(transitionResetFrame);
        transitionResetFrame = requestAnimationFrame(() => {
            document.documentElement.classList.remove('edit-mode-syncing');
        });
    };

    toggle.checked = restoreEditMode();

    toggle.addEventListener('change', () => {
        if (toggle.checked) {
            const learnModeToggle = document.querySelector('[data-learn-mode-toggle]');

            if (learnModeToggle instanceof HTMLInputElement && learnModeToggle.checked) {
                learnModeToggle.checked = false;
                learnModeToggle.dispatchEvent(new Event('change'));
            }
        }

        persistEditMode(toggle.checked);
        syncEditableLinks();
    });

    document.addEventListener('click', (event) => {
        if (!toggle.checked || !(event.target instanceof Element)) {
            return;
        }

        const link = event.target.closest(editableLinkSelector);

        if (!(link instanceof HTMLAnchorElement) || !link.classList.contains('mode-edit')) {
            return;
        }

        event.preventDefault();
        link.dispatchEvent(new CustomEvent(editLinkRequestEvent, {
            bubbles: true,
            detail: { link },
        }));
    });

    syncEditableLinks();
}
