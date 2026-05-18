document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('app-ready');

    const root = document.documentElement;
    const preferenceConfig = {
        theme: {
            defaultValue: 'light',
            storageKey: 'pto-theme',
            values: ['light', 'dark'],
        },
        accent: {
            defaultValue: 'blue',
            storageKey: 'pto-accent',
            values: ['blue', 'purple', 'green', 'orange'],
        },
        density: {
            defaultValue: 'comfortable',
            storageKey: 'pto-density',
            values: ['comfortable', 'compact'],
        },
    };

    const readStoredPreference = (preference) => {
        const config = preferenceConfig[preference];

        if (!config) {
            return '';
        }

        try {
            return window.localStorage.getItem(config.storageKey) || '';
        } catch (error) {
            return '';
        }
    };

    const writeStoredPreference = (preference, value) => {
        const config = preferenceConfig[preference];

        if (!config) {
            return;
        }

        try {
            window.localStorage.setItem(config.storageKey, value);
        } catch (error) {
            // Keep the controls usable if browser storage is blocked.
        }
    };

    const setActivePreference = (preference, value) => {
        document.querySelectorAll(`[data-preference="${preference}"]`).forEach((button) => {
            const isActive = button.dataset.value === value;
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const applyPreference = (preference, value, persist = false) => {
        const config = preferenceConfig[preference];

        if (!config || !config.values.includes(value)) {
            return;
        }

        root.dataset[preference] = value;
        setActivePreference(preference, value);

        if (persist) {
            writeStoredPreference(preference, value);
        }
    };

    Object.keys(preferenceConfig).forEach((preference) => {
        const config = preferenceConfig[preference];
        const storedValue = readStoredPreference(preference);
        const value = config.values.includes(storedValue) ? storedValue : config.defaultValue;

        applyPreference(preference, value);
    });

    document.querySelectorAll('[data-preference]').forEach((button) => {
        button.addEventListener('click', () => {
            const preference = button.dataset.preference || '';
            const value = button.dataset.value || '';

            applyPreference(preference, value, true);
        });
    });

    const personalization = document.querySelector('.personalization');
    const personalizationToggle = document.querySelector('[data-personalization-toggle]');
    const personalizationPanel = document.querySelector('[data-personalization-panel]');

    const closePersonalization = () => {
        if (!personalizationToggle || !personalizationPanel) {
            return;
        }

        personalizationPanel.hidden = true;
        personalizationToggle.setAttribute('aria-expanded', 'false');
    };

    if (personalizationToggle && personalizationPanel) {
        personalizationToggle.addEventListener('click', () => {
            const isOpen = !personalizationPanel.hidden;

            personalizationPanel.hidden = isOpen;
            personalizationToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });

        document.addEventListener('click', (event) => {
            if (personalization && !personalization.contains(event.target)) {
                closePersonalization();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closePersonalization();
            }
        });
    }

    const legacyThemeToggle = document.querySelector('[data-theme-toggle]');

    if (legacyThemeToggle) {
        legacyThemeToggle.addEventListener('click', () => {
            const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            applyPreference('theme', nextTheme, true);
        });
    }
});
