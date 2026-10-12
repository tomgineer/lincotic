const editableLinkSelector = '.link-card';
export const editLinkRequestEvent = 'lincotic:edit-link';

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

    toggle.addEventListener('change', syncEditableLinks);

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
