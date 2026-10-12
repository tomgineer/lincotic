export function initLinkSearch() {
    const search = document.querySelector('[data-link-search-form]');

    if (!(search instanceof HTMLFormElement)) {
        return;
    }

    const input = search.elements.namedItem('q');
    const clearSearch = search.querySelector('[data-clear-search]');

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    if (clearSearch instanceof HTMLButtonElement) {
        input.addEventListener('input', () => {
            clearSearch.disabled = input.value === '';
        });

        clearSearch.addEventListener('click', () => {
            input.value = '';
            search.requestSubmit();
        });
    }

    if (input.autofocus) {
        requestAnimationFrame(() => {
            input.focus({ preventScroll: true });
            input.setSelectionRange(input.value.length, input.value.length);
        });
    }
}
