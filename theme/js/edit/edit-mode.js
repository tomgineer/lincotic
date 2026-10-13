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

    if (!(toggle instanceof HTMLInputElement)) {
        return;
    }

    const syncEditableLinks = () => {
        document.querySelectorAll(editableLinkSelector).forEach((link) => {
            link.classList.toggle('edit-mode', toggle.checked && isVisible(link));
        });
    };

    toggle.checked = restoreEditMode();

    toggle.addEventListener('change', () => {
        persistEditMode(toggle.checked);
        syncEditableLinks();
    });

    document.addEventListener('click', (event) => {
        if (!toggle.checked || !(event.target instanceof Element)) {
            return;
        }

        const link = event.target.closest(editableLinkSelector);

        if (!(link instanceof HTMLAnchorElement) || !link.classList.contains('edit-mode')) {
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
