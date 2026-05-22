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

    const setupDisclosure = (containerSelector, toggleSelector, panelSelector) => {
        const container = document.querySelector(containerSelector);
        const toggle = document.querySelector(toggleSelector);
        const panel = document.querySelector(panelSelector);

        if (!container || !toggle || !panel) {
            return () => {};
        }

        const close = () => {
            panel.hidden = true;
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', () => {
            const isOpen = !panel.hidden;

            panel.hidden = isOpen;
            toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        });

        document.addEventListener('click', (event) => {
            if (!container.contains(event.target) && !panel.contains(event.target)) {
                close();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });

        return close;
    };

    const closeSettingsPanel = setupDisclosure('.settings-menu', '[data-settings-toggle]', '[data-settings-panel]');
    setupDisclosure('.quick-add', '[data-quick-add-toggle]', '[data-quick-add-panel]');
    const closeNotificationsPanel = setupDisclosure('.notification-bell-menu', '[data-notifications-toggle]', '[data-notifications-panel]');
    const settingsToggle = document.querySelector('[data-settings-toggle]');
    const settingsPanel = document.querySelector('[data-settings-panel]');

    if (settingsPanel && settingsPanel.parentElement !== document.body) {
        document.body.appendChild(settingsPanel);
    }

    const positionSettingsPanel = () => {
        if (!settingsToggle || !settingsPanel || settingsPanel.hidden) {
            return;
        }

        const margin = 12;
        const gap = 10;
        const toggleRect = settingsToggle.getBoundingClientRect();
        const panelRect = settingsPanel.getBoundingClientRect();
        let left = toggleRect.right + gap;
        let top = toggleRect.top;

        if (left + panelRect.width > window.innerWidth - margin) {
            left = window.innerWidth - panelRect.width - margin;
        }

        if (top + panelRect.height > window.innerHeight - margin) {
            top = window.innerHeight - panelRect.height - margin;
        }

        settingsPanel.style.left = `${Math.max(margin, left)}px`;
        settingsPanel.style.top = `${Math.max(margin, top)}px`;
    };

    settingsToggle?.addEventListener('click', () => {
        window.setTimeout(positionSettingsPanel, 0);
    });
    window.addEventListener('resize', positionSettingsPanel);
    window.addEventListener('scroll', positionSettingsPanel, true);

    const legacyThemeToggle = document.querySelector('[data-theme-toggle]');

    if (legacyThemeToggle) {
        legacyThemeToggle.addEventListener('click', () => {
            const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            applyPreference('theme', nextTheme, true);
        });
    }

    const toastRegion = document.querySelector('[data-toast-region]');
    const toastConfig = document.querySelector('.toast-config');
    const shownToasts = new Set();

    const showToast = (message, type = 'info') => {
        const text = String(message || '').trim();

        if (!toastRegion || text === '' || shownToasts.has(`${type}:${text}`)) {
            return;
        }

        shownToasts.add(`${type}:${text}`);

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.setAttribute('role', type === 'danger' ? 'alert' : 'status');

        const messageElement = document.createElement('p');
        messageElement.textContent = text;

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'toast-close';
        closeButton.textContent = toastConfig?.dataset.toastDismiss || 'Close';
        closeButton.addEventListener('click', () => {
            toast.remove();
        });

        toast.append(messageElement, closeButton);
        toastRegion.appendChild(toast);

        window.setTimeout(() => {
            toast.remove();
        }, 5200);
    };

    document.querySelectorAll('.alert.success').forEach((alert) => {
        showToast(alert.textContent, 'success');
    });

    document.querySelectorAll('.alert.danger, .alert.warning').forEach((alert) => {
        showToast(alert.textContent, alert.classList.contains('warning') ? 'warning' : 'danger');
    });

    if (document.querySelector('.field-error')) {
        showToast(toastConfig?.dataset.toastValidation || '', 'danger');
    }

    const requiredMessage = toastConfig?.dataset.requiredMessage || '';

    document.querySelectorAll('[required]').forEach((field) => {
        field.addEventListener('invalid', () => {
            if (field.validity.valueMissing) {
                field.setCustomValidity(requiredMessage);
            }
        });

        field.addEventListener('input', () => {
            field.setCustomValidity('');
        });

        field.addEventListener('change', () => {
            field.setCustomValidity('');
        });
    });

    const notificationToggles = Array.from(document.querySelectorAll('[data-notification-toggle]'));
    const notificationBellToggle = document.querySelector('[data-notifications-toggle]');
    const notificationBadge = document.querySelector('[data-notifications-count]');
    const notificationList = document.querySelector('[data-notifications-list]');
    const notificationStatusNote = document.querySelector('[data-notification-status-note]');
    const notificationFilterButtons = Array.from(document.querySelectorAll('[data-notification-filter]'));
    const reminderToastTemplate = toastConfig?.dataset.reminderTemplate || ':title';
    const notificationUnsupportedMessage = toastConfig?.dataset.notificationUnsupported || '';
    const notificationDefaultBody = toastConfig?.dataset.notificationDefaultBody || '';
    const notificationFallbackTitle = toastConfig?.dataset.notificationReminderTitle || '';
    const notificationGraceMs = 120000;
    const notificationEnabledStorageKey = 'pto-notifications-enabled';
    const notifiedReminderCache = new Map();
    let todayReminders = [];
    let notificationFilter = 'all';
    let notificationPollIntervalId = null;

    const notificationPermissionState = () => {
        if (!('Notification' in window)) {
            return 'unsupported';
        }

        return Notification.permission;
    };

    const notificationsEnabled = () => {
        try {
            return window.localStorage.getItem(notificationEnabledStorageKey) === '1';
        } catch (error) {
            return false;
        }
    };

    const setNotificationsEnabled = (enabled) => {
        try {
            window.localStorage.setItem(notificationEnabledStorageKey, enabled ? '1' : '0');
        } catch (error) {
            // Keep the toggle usable if storage is blocked.
        }
    };

    const updateNotificationToggleUi = () => {
        const enabled = notificationsEnabled();

        notificationToggles.forEach((button) => {
            button.setAttribute('aria-pressed', button.dataset.value === (enabled ? 'on' : 'off') ? 'true' : 'false');
        });
    };

    const updateNotificationStatusNote = () => {
        if (!notificationStatusNote || !toastConfig) {
            return;
        }

        if (notificationsEnabled()) {
            const state = notificationPermissionState();

            if (state === 'unsupported') {
                notificationStatusNote.textContent = toastConfig.dataset.notificationStatusUnsupported || '';
                return;
            }

            if (state === 'denied') {
                notificationStatusNote.textContent = toastConfig.dataset.notificationStatusDenied || '';
                return;
            }

            notificationStatusNote.textContent = toastConfig.dataset.notificationStatusGranted || '';
            return;
        }

        notificationStatusNote.textContent = toastConfig.dataset.notificationStatusDefault || '';
    };

    const reminderDateKey = (remindAt) => {
        const rawValue = String(remindAt || '');

        if (/^\d{4}-\d{2}-\d{2}/.test(rawValue)) {
            return rawValue.slice(0, 10);
        }

        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    };

    const reminderStorageKey = (dateKey) => `pto-reminders-notified:${dateKey}`;

    const reminderNotificationKey = (reminder) => {
        const id = String(reminder.id || '').trim();
        const remindAt = String(reminder.remind_at || '').trim();

        return `${id}:${remindAt}`;
    };

    const notifiedReminderKeysFor = (dateKey) => {
        if (notifiedReminderCache.has(dateKey)) {
            return notifiedReminderCache.get(dateKey);
        }

        let keys = new Set();

        try {
            const storedValue = window.localStorage.getItem(reminderStorageKey(dateKey));
            const parsedValue = storedValue ? JSON.parse(storedValue) : [];

            if (Array.isArray(parsedValue)) {
                keys = new Set(parsedValue.map((value) => String(value)));
            }
        } catch (error) {
            keys = new Set();
        }

        notifiedReminderCache.set(dateKey, keys);

        return keys;
    };

    const saveNotifiedReminderKeys = (dateKey) => {
        const keys = notifiedReminderKeysFor(dateKey);

        try {
            window.localStorage.setItem(reminderStorageKey(dateKey), JSON.stringify([...keys]));
        } catch (error) {
            // Reminder notifications still work for the current page if storage is unavailable.
        }
    };

    const hasReminderBeenNotified = (reminder) => {
        const dateKey = reminderDateKey(reminder.remind_at);

        return notifiedReminderKeysFor(dateKey).has(reminderNotificationKey(reminder));
    };

    const markReminderNotified = (reminder) => {
        const dateKey = reminderDateKey(reminder.remind_at);
        const keys = notifiedReminderKeysFor(dateKey);

        keys.add(reminderNotificationKey(reminder));
        saveNotifiedReminderKeys(dateKey);
    };

    const showReminderFallback = (title) => {
        const message = reminderToastTemplate.replace(':title', title);

        showToast(message, 'warning');
    };

    const showReminderNotification = (reminder) => {
        const title = String(reminder.title || '').trim() || notificationFallbackTitle;
        const body = String(reminder.note || '').trim() || notificationDefaultBody;

        if (!('Notification' in window) || Notification.permission !== 'granted') {
            showReminderFallback(title);
            return;
        }

        try {
            new Notification(title, { body });
        } catch (error) {
            showReminderFallback(title);
        }
    };

    const reminderTimestamp = (reminder) => new Date(reminder.remind_at || '').getTime();

    const isReminderStale = (reminder, now = Date.now()) => {
        const start = reminderTimestamp(reminder);

        return Number.isFinite(start) && now - start > notificationGraceMs;
    };

    const formatNotificationTime = (value) => {
        const date = new Date(value || '');

        if (!Number.isFinite(date.getTime())) {
            return '';
        }

        const locale = (root.lang || document.documentElement.lang || 'vi').toLowerCase().startsWith('en') ? 'en-US' : 'vi-VN';

        return new Intl.DateTimeFormat(locale, {
            hour: '2-digit',
            minute: '2-digit',
            hour12: locale === 'en-US',
        }).format(date);
    };

    const renderNotificationList = () => {
        const now = Date.now();
        const activeReminders = todayReminders.filter((reminder) => !isReminderStale(reminder, now));

        if (notificationBadge) {
            const count = activeReminders.filter((reminder) => !hasReminderBeenNotified(reminder)).length;
            notificationBadge.hidden = count === 0;
            notificationBadge.textContent = count > 9 ? '9+' : String(count);
        }

        updateNotificationStatusNote();

        if (!notificationList) {
            return;
        }

        const reminders = notificationFilter === 'unread'
            ? activeReminders.filter((reminder) => !hasReminderBeenNotified(reminder))
            : todayReminders.slice();

        if (reminders.length === 0) {
            notificationList.innerHTML = '';
            const empty = document.createElement('p');
            empty.className = 'empty-state';
            empty.textContent = notificationFilter === 'unread'
                ? (toastConfig?.dataset.notificationUnreadEmpty || toastConfig?.dataset.notificationEmpty || '')
                : (toastConfig?.dataset.notificationAllEmpty || toastConfig?.dataset.notificationEmpty || '');
            notificationList.appendChild(empty);
            return;
        }

        notificationList.innerHTML = '';

        reminders
            .slice()
            .sort((a, b) => {
                const aStale = isReminderStale(a, now) ? 1 : 0;
                const bStale = isReminderStale(b, now) ? 1 : 0;

                if (aStale !== bStale) {
                    return aStale - bStale;
                }

                return reminderTimestamp(a) - reminderTimestamp(b);
            })
            .forEach((reminder) => {
                const item = document.createElement('article');
                const stale = isReminderStale(reminder, now);
                item.className = `notification-item${!stale && !hasReminderBeenNotified(reminder) ? ' unread' : ''}${stale ? ' past' : ''}`;

                const head = document.createElement('div');
                head.className = 'notification-item-head';

                const title = document.createElement('strong');
                title.textContent = String(reminder.title || '').trim() || notificationFallbackTitle;

                const time = document.createElement('span');
                time.className = 'notification-time';
                time.textContent = `${formatNotificationTime(reminder.remind_at)} · ${stale ? (toastConfig?.dataset.notificationPastLabel || '') : (toastConfig?.dataset.notificationUpcomingLabel || '')}`;

                head.append(title, time);
                item.appendChild(head);

                const note = String(reminder.note || '').trim();

                if (note !== '') {
                    const noteElement = document.createElement('p');
                    noteElement.textContent = note;
                    item.appendChild(noteElement);
                }

                notificationList.appendChild(item);
            });
    };

    const processDueReminders = (reminders) => {
        if (!notificationsEnabled()) {
            return;
        }

        if (notificationPermissionState() !== 'granted') {
            setNotificationsEnabled(false);
            updateNotificationToggleUi();
            stopReminderPolling();
            renderNotificationList();
            return;
        }

        const now = Date.now();

        reminders.forEach((reminder) => {
            const start = reminderTimestamp(reminder);

            if (!Number.isFinite(start)) {
                return;
            }

            const diffMs = now - start;
            const isDueNow = diffMs >= 0 && diffMs <= notificationGraceMs;

            if (!isDueNow || hasReminderBeenNotified(reminder)) {
                return;
            }

            markReminderNotified(reminder);
            showReminderNotification(reminder);
        });

        renderNotificationList();
    };

    const loadTodayReminders = async (notify = false) => {
        try {
            const response = await fetch('/api/reminders/today', {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json();
            const reminders = Array.isArray(payload.reminders) ? payload.reminders : [];
            todayReminders = reminders;
            renderNotificationList();

            if (notify) {
                processDueReminders(reminders);
            }
        } catch (error) {
            // Reminder polling should never interrupt the main UI.
        }
    };

    const checkPersonalReminders = async () => {
        await loadTodayReminders(true);
    };

    const stopReminderPolling = () => {
        if (notificationPollIntervalId !== null) {
            window.clearInterval(notificationPollIntervalId);
            notificationPollIntervalId = null;
        }
    };

    const startReminderPolling = () => {
        stopReminderPolling();
        checkPersonalReminders();
        notificationPollIntervalId = window.setInterval(checkPersonalReminders, 60000);
    };

    const enableNotifications = async () => {
        const state = notificationPermissionState();

        if (state === 'unsupported') {
            setNotificationsEnabled(false);
            updateNotificationToggleUi();
            updateNotificationStatusNote();
            stopReminderPolling();
            showToast(notificationUnsupportedMessage, 'warning');
            return;
        }

        if (state !== 'granted') {
            const permission = await Notification.requestPermission();

            if (permission !== 'granted') {
                setNotificationsEnabled(false);
                updateNotificationToggleUi();
                updateNotificationStatusNote();
                stopReminderPolling();
                showToast(toastConfig?.dataset.notificationDenied || '', 'warning');
                return;
            }
        }

        setNotificationsEnabled(true);
        updateNotificationToggleUi();
        updateNotificationStatusNote();
        showToast(toastConfig?.dataset.notificationGranted || '', 'success');
        startReminderPolling();
    };

    const disableNotifications = () => {
        setNotificationsEnabled(false);
        updateNotificationToggleUi();
        updateNotificationStatusNote();
        stopReminderPolling();
    };

    notificationToggles.forEach((button) => {
        button.addEventListener('click', () => {
            if (button.dataset.value === 'on') {
                enableNotifications();
                return;
            }

            disableNotifications();
        });
    });

    notificationBellToggle?.addEventListener('click', () => {
        loadTodayReminders(false);
    });

    notificationFilterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            notificationFilter = button.dataset.notificationFilter === 'unread' ? 'unread' : 'all';

            notificationFilterButtons.forEach((filterButton) => {
                filterButton.setAttribute('aria-pressed', filterButton === button ? 'true' : 'false');
            });

            renderNotificationList();
        });
    });

    if (toastRegion) {
        updateNotificationToggleUi();
        updateNotificationStatusNote();
        loadTodayReminders(false);

        if (notificationsEnabled() && notificationPermissionState() === 'granted') {
            startReminderPolling();
        } else if (notificationsEnabled()) {
            setNotificationsEnabled(false);
            updateNotificationToggleUi();
            updateNotificationStatusNote();
            stopReminderPolling();
        }
    }

    document.querySelectorAll('.timetable-item[data-reminder-item]').forEach((item) => {
        const actions = item.querySelector('.actions');
        const scheduleId = item.dataset.reminderId;

        if (!actions || !scheduleId || actions.querySelector('[data-timetable-delete]')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'post';
        form.action = `/timetable/schedules/${encodeURIComponent(scheduleId)}`;
        form.dataset.timetableDelete = 'true';

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const dateInput = document.createElement('input');
        dateInput.type = 'hidden';
        dateInput.name = 'date';
        dateInput.value = document.querySelector('input[name="date"]')?.value || '';

        const button = document.createElement('button');
        button.className = 'button compact danger';
        button.type = 'submit';
        button.textContent = toastConfig?.dataset.deleteLabel || '';

        form.addEventListener('submit', (event) => {
            const message = toastConfig?.dataset.deleteScheduleMessage || '';

            if (message !== '' && !window.confirm(message)) {
                event.preventDefault();
            }
        });

        form.append(methodInput, dateInput, button);
        actions.appendChild(form);
    });

    document.querySelectorAll('[data-reminder-repeat]').forEach((select) => {
        const form = select.closest('form');
        const weeklyField = form?.querySelector('[data-reminder-weekly]');
        const intervalFields = form?.querySelector('[data-reminder-interval]');
        const updateReminderRepeatFields = () => {
            const repeatType = select.value;

            if (weeklyField) {
                weeklyField.hidden = repeatType !== 'weekly';
            }

            if (intervalFields) {
                intervalFields.hidden = repeatType !== 'interval';
            }
        };

        select.addEventListener('change', updateReminderRepeatFields);
        updateReminderRepeatFields();
    });

    const deleteModal = document.querySelector('[data-delete-modal]');
    const deleteModalMessage = document.querySelector('[data-delete-modal-message]');
    const deleteModalConfirm = document.querySelector('[data-delete-modal-confirm]');
    const deleteModalCancel = document.querySelector('[data-delete-modal-cancel]');
    let pendingDeleteForm = null;

    const closeDeleteModal = () => {
        if (!deleteModal) {
            return;
        }

        deleteModal.hidden = true;
        pendingDeleteForm = null;
    };

    document.querySelectorAll('form[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true' || !deleteModal) {
                return;
            }

            event.preventDefault();
            pendingDeleteForm = form;

            if (deleteModalMessage && form.dataset.confirmMessage) {
                deleteModalMessage.textContent = form.dataset.confirmMessage;
            }

            deleteModal.hidden = false;
            deleteModalConfirm?.focus();
        });
    });

    deleteModalConfirm?.addEventListener('click', () => {
        if (!pendingDeleteForm) {
            return;
        }

        pendingDeleteForm.dataset.confirmed = 'true';
        pendingDeleteForm.submit();
    });

    deleteModalCancel?.addEventListener('click', closeDeleteModal);
    deleteModal?.addEventListener('click', (event) => {
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && deleteModal && !deleteModal.hidden) {
            closeDeleteModal();
        }
    });

    document.querySelectorAll('[data-filter-controls]').forEach((controls) => {
        const targetId = controls.dataset.filterTarget || '';
        const table = document.getElementById(targetId);

        if (!table) {
            return;
        }

        const rows = Array.from(table.querySelectorAll('[data-filter-row]'));
        const filterEmpty = document.querySelector(`[data-filter-empty="${targetId}"]`);
        const searchInput = controls.querySelector('[data-filter-search]');
        const selectFilters = Array.from(controls.querySelectorAll('[data-filter-select]'));

        selectFilters.forEach((select) => {
            const field = select.dataset.filterSelect || '';
            const values = [...new Set(rows.map((row) => row.dataset[field] || '').filter(Boolean))].sort((a, b) => a.localeCompare(b));

            values.forEach((value) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });
        });

        const applyFilters = () => {
            const search = (searchInput?.value || '').trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach((row) => {
                const searchText = (row.dataset.search || row.textContent || '').toLowerCase();
                const matchesSearch = search === '' || searchText.includes(search);
                const matchesSelects = selectFilters.every((select) => {
                    const field = select.dataset.filterSelect || '';
                    return select.value === '' || row.dataset[field] === select.value;
                });
                const isVisible = matchesSearch && matchesSelects;

                row.hidden = !isVisible;

                if (isVisible) {
                    visibleCount += 1;
                }
            });

            if (filterEmpty) {
                filterEmpty.hidden = visibleCount > 0;
            }
        };

        searchInput?.addEventListener('input', applyFilters);
        selectFilters.forEach((select) => {
            select.addEventListener('change', applyFilters);
        });
        applyFilters();
    });

    const assistantRefreshRegion = document.querySelector('[data-assistant-refresh]');

    if (assistantRefreshRegion) {
        window.setInterval(async () => {
            try {
                const response = await fetch('/assistant', {
                    headers: {
                        Accept: 'text/html',
                    },
                });

                if (!response.ok) {
                    return;
                }

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nextRegion = doc.querySelector('[data-assistant-refresh]');

                if (nextRegion) {
                    assistantRefreshRegion.innerHTML = nextRegion.innerHTML;
                }
            } catch (error) {
                // Assistant refresh is best-effort and should never interrupt the page.
            }
        }, 60000);
    }

    document.querySelectorAll('[data-focus-form]').forEach((form) => {
        const timerPanel = document.querySelector('[data-focus-timer]');
        const display = document.querySelector('[data-focus-display]');
        const status = document.querySelector('[data-focus-status]');
        const activitySelect = form.querySelector('[data-focus-activity]');
        const durationInputs = Array.from(form.querySelectorAll('[data-focus-duration]'));
        const startButton = form.querySelector('[data-focus-start]');
        const pauseButton = form.querySelector('[data-focus-pause]');
        const resetButton = form.querySelector('[data-focus-reset]');
        const saveButton = form.querySelector('[data-focus-save]');
        const startedAtInput = form.querySelector('[data-focus-started-at]');
        const endedAtInput = form.querySelector('[data-focus-ended-at]');

        if (!timerPanel || !display || !status || !startButton || !pauseButton || !resetButton || !saveButton) {
            return;
        }

        const labels = {
            ready: timerPanel.dataset.focusReady || 'Ready when you are.',
            running: timerPanel.dataset.focusRunning || 'Focus session is running.',
            paused: timerPanel.dataset.focusPaused || 'Paused.',
            completed: timerPanel.dataset.focusCompleted || 'Session complete.',
            pause: timerPanel.dataset.focusPauseLabel || 'Pause',
            resume: timerPanel.dataset.focusResumeLabel || 'Resume',
        };
        let totalSeconds = Math.max(1, Number(timerPanel.dataset.focusInitialDuration || 25)) * 60;
        let remainingSeconds = totalSeconds;
        let intervalId = null;
        let completed = false;

        const pad = (value) => String(value).padStart(2, '0');

        const selectedDurationMinutes = () => {
            const selected = durationInputs.find((input) => input.checked);
            return Math.max(1, Number(selected?.value || 25));
        };

        const formatDuration = (seconds) => {
            const minutes = Math.floor(seconds / 60);
            const rest = seconds % 60;

            return `${pad(minutes)}:${pad(rest)}`;
        };

        const formatDateTime = (date) => {
            const year = date.getFullYear();
            const month = pad(date.getMonth() + 1);
            const day = pad(date.getDate());
            const hours = pad(date.getHours());
            const minutes = pad(date.getMinutes());
            const seconds = pad(date.getSeconds());

            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        };

        const setControlsLocked = (locked) => {
            activitySelect?.toggleAttribute('disabled', locked);
            durationInputs.forEach((input) => {
                input.disabled = locked;
            });
        };

        const render = () => {
            display.textContent = formatDuration(remainingSeconds);
        };

        const clearTimer = () => {
            if (intervalId !== null) {
                window.clearInterval(intervalId);
                intervalId = null;
            }
        };

        const resetSession = () => {
            clearTimer();
            totalSeconds = selectedDurationMinutes() * 60;
            remainingSeconds = totalSeconds;
            completed = false;
            setControlsLocked(false);
            startButton.disabled = false;
            pauseButton.disabled = true;
            pauseButton.textContent = labels.pause;
            saveButton.hidden = true;
            startedAtInput.value = '';
            endedAtInput.value = '';
            status.textContent = labels.ready;
            render();
        };

        const completeSession = () => {
            clearTimer();
            remainingSeconds = 0;
            completed = true;

            const endedAt = new Date();
            const startedAt = new Date(endedAt.getTime() - (totalSeconds * 1000));

            startedAtInput.value = formatDateTime(startedAt);
            endedAtInput.value = formatDateTime(endedAt);
            startButton.disabled = true;
            pauseButton.disabled = true;
            saveButton.hidden = false;
            status.textContent = labels.completed;
            render();
        };

        const tick = () => {
            remainingSeconds = Math.max(0, remainingSeconds - 1);
            render();

            if (remainingSeconds <= 0) {
                completeSession();
            }
        };

        const startSession = () => {
            if (completed) {
                resetSession();
            }

            if (activitySelect && !activitySelect.value) {
                form.reportValidity();
                return;
            }

            if (remainingSeconds <= 0) {
                resetSession();
            }

            clearTimer();
            setControlsLocked(true);
            startButton.disabled = true;
            pauseButton.disabled = false;
            pauseButton.textContent = labels.pause;
            saveButton.hidden = true;
            status.textContent = labels.running;
            intervalId = window.setInterval(tick, 1000);
        };

        const pauseOrResume = () => {
            if (completed) {
                return;
            }

            if (intervalId !== null) {
                clearTimer();
                startButton.disabled = false;
                pauseButton.textContent = labels.resume;
                status.textContent = labels.paused;
                return;
            }

            startSession();
        };

        durationInputs.forEach((input) => {
            input.addEventListener('change', () => {
                if (intervalId !== null || completed) {
                    return;
                }

                totalSeconds = selectedDurationMinutes() * 60;
                remainingSeconds = totalSeconds;
                render();
            });
        });

        startButton.addEventListener('click', startSession);
        pauseButton.addEventListener('click', pauseOrResume);
        resetButton.addEventListener('click', resetSession);
        form.addEventListener('submit', (event) => {
            if (!completed) {
                event.preventDefault();
                return;
            }

            setControlsLocked(false);
        });
        resetSession();
    });
});
