const FILTER_DEFAULTS = {
    collection: 'all',
    flag: 'all',
    importance: 'all',
    sort: 'title',
};

export function initLinkFilters() {
    const form = document.querySelector('[data-link-filters]');

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const search = form.elements.namedItem('q');

    const resetFilters = () => {
        Object.entries(FILTER_DEFAULTS).forEach(([name, value]) => {
            const select = form.elements.namedItem(name);

            if (select instanceof HTMLSelectElement) {
                select.value = value;
            }
        });
    };

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => {
            if (search instanceof HTMLInputElement) {
                search.value = '';
            }

            form.requestSubmit();
        });
    });

    if (!(search instanceof HTMLInputElement)) {
        return;
    }

    if (search.autofocus) {
        requestAnimationFrame(() => {
            search.focus({ preventScroll: true });
            search.setSelectionRange(search.value.length, search.value.length);
        });
    }

    form.addEventListener('submit', () => {
        if (search.value.trim() !== '') {
            resetFilters();
        }
    });

    search.addEventListener('input', () => {
        if (search.value === '') {
            form.requestSubmit();
        }
    });
}
