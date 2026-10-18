const storageKey = 'lincotic.hubSettings.v1';

export const defaultSettings = Object.freeze({
    fullWidth: false,
    hideDescriptions: false,
});

function normalizeSettings(value) {
    if (value === null || typeof value !== 'object' || Array.isArray(value)) {
        return { ...defaultSettings };
    }

    return {
        fullWidth: value.fullWidth === true,
        hideDescriptions: value.hideDescriptions === true,
    };
}

export function readSettings() {
    try {
        return normalizeSettings(JSON.parse(localStorage.getItem(storageKey)));
    } catch {
        return { ...defaultSettings };
    }
}

export function writeSettings(settings) {
    const normalizedSettings = normalizeSettings(settings);

    try {
        localStorage.setItem(storageKey, JSON.stringify(normalizedSettings));
    } catch {
        // The settings still work for this page when browser storage is unavailable.
    }

    return normalizedSettings;
}

export function applySettings(settings) {
    const normalizedSettings = normalizeSettings(settings);
    const root = document.documentElement;

    root.toggleAttribute('data-hub-full-width', normalizedSettings.fullWidth);
    root.toggleAttribute('data-hide-collection-descriptions', normalizedSettings.hideDescriptions);

    return normalizedSettings;
}

export function isSettingsStorageEvent(event) {
    return event.key === storageKey || event.key === null;
}
