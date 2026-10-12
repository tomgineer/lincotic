export function initLinkFilters() {
    const filters = document.querySelector('[data-link-filters]');

    if (filters instanceof HTMLFormElement) {
        filters.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => filters.requestSubmit());
        });
    }
}
