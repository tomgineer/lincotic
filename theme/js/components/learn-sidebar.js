export function initLearnSidebar() {
    const sidebar = document.querySelector('[data-learn-sidebar]');

    if (!(sidebar instanceof HTMLElement) || typeof sidebar.showPopover !== 'function') {
        return;
    }

    // Keep the empty sidebar open while its UI is being designed.
    if (sidebar.hasAttribute('data-preview-open')) {
        sidebar.showPopover();
    }
}
