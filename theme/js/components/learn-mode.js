const learnableLinkSelector = '.link-card';
const learnModeStorageKey = 'lincotic.learnMode';
const openLearnSidebarEvent = 'lincotic:learn:open';
const closeLearnSidebarEvent = 'lincotic:learn:close';

function restoreLearnMode() {
    try {
        return sessionStorage.getItem(learnModeStorageKey) === '1';
    } catch {
        return false;
    }
}

function persistLearnMode(enabled) {
    try {
        if (enabled) {
            sessionStorage.setItem(learnModeStorageKey, '1');
        } else {
            sessionStorage.removeItem(learnModeStorageKey);
        }
    } catch {
        // Learn mode still works when browser storage is unavailable.
    }
}

export function initLearnMode() {
    const toggle = document.querySelector('[data-learn-mode-toggle]');
    let transitionResetFrame = 0;

    if (!(toggle instanceof HTMLInputElement)) {
        return;
    }

    const syncLearnableLinks = () => {
        const links = [...document.querySelectorAll(learnableLinkSelector)];
        const learnableLinks = toggle.checked
            ? new Set(links)
            : new Set();

        document.documentElement.classList.add('learn-mode-syncing');

        links.forEach((link) => {
            link.classList.toggle('mode-learn', learnableLinks.has(link));
        });

        cancelAnimationFrame(transitionResetFrame);
        transitionResetFrame = requestAnimationFrame(() => {
            document.documentElement.classList.remove('learn-mode-syncing');
        });
    };

    toggle.checked = restoreLearnMode();

    toggle.addEventListener('change', () => {
        if (toggle.checked) {
            const editModeToggle = document.querySelector('[data-edit-mode-toggle]');

            if (editModeToggle instanceof HTMLInputElement && editModeToggle.checked) {
                editModeToggle.checked = false;
                editModeToggle.dispatchEvent(new Event('change'));
            }
        }

        persistLearnMode(toggle.checked);
        syncLearnableLinks();

        if (!toggle.checked) {
            document.dispatchEvent(new Event(closeLearnSidebarEvent));
        }
    });

    document.addEventListener('click', (event) => {
        if (!toggle.checked || !(event.target instanceof Element)) {
            return;
        }

        const link = event.target.closest(learnableLinkSelector);

        if (!(link instanceof HTMLAnchorElement) || !link.classList.contains('mode-learn')) {
            return;
        }

        event.preventDefault();

        const linkId = link.dataset.linkId;

        if (!/^[1-9]\d*$/.test(linkId ?? '')) {
            return;
        }

        document.dispatchEvent(new CustomEvent(openLearnSidebarEvent, {
            detail: {
                linkId,
                trigger: link,
            },
        }));
    });

    if (toggle.checked) {
        const editModeToggle = document.querySelector('[data-edit-mode-toggle]');

        if (editModeToggle instanceof HTMLInputElement && editModeToggle.checked) {
            editModeToggle.checked = false;
            editModeToggle.dispatchEvent(new Event('change'));
        }
    }

    syncLearnableLinks();
}
