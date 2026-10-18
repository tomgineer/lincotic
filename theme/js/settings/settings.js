import {
    applySettings,
    isSettingsStorageEvent,
    readSettings,
    writeSettings,
} from './state.js';

export function initSettings() {
    const settingsPopover = document.querySelector('[data-hub-settings]');

    if (!(settingsPopover instanceof HTMLElement)) {
        return;
    }

    const fullWidthToggle = settingsPopover.querySelector('[data-setting-full-width]');
    const hideDescriptionsToggle = settingsPopover.querySelector('[data-setting-hide-descriptions]');

    if (
        !(fullWidthToggle instanceof HTMLInputElement)
        || !(hideDescriptionsToggle instanceof HTMLInputElement)
    ) {
        return;
    }

    const syncControls = (settings) => {
        fullWidthToggle.checked = settings.fullWidth;
        hideDescriptionsToggle.checked = settings.hideDescriptions;
    };

    const updateSettings = () => {
        const settings = writeSettings({
            fullWidth: fullWidthToggle.checked,
            hideDescriptions: hideDescriptionsToggle.checked,
        });

        applySettings(settings);
    };

    syncControls(applySettings(readSettings()));

    fullWidthToggle.addEventListener('change', updateSettings);
    hideDescriptionsToggle.addEventListener('change', updateSettings);

    window.addEventListener('storage', (event) => {
        if (!isSettingsStorageEvent(event)) {
            return;
        }

        syncControls(applySettings(readSettings()));
    });
}
