<script>
    (() => {
        try {
            const root = document.documentElement;
            const storage = window.localStorage;
            const theme = storage.getItem('pto-theme');
            const accent = storage.getItem('pto-accent');
            const density = storage.getItem('pto-density');

            if (['dark', 'light'].includes(theme)) {
                root.dataset.theme = theme;
            }

            if (['blue', 'purple', 'green', 'orange'].includes(accent)) {
                root.dataset.accent = accent;
            }

            if (['comfortable', 'compact'].includes(density)) {
                root.dataset.density = density;
            }
        } catch (error) {
            // localStorage may be unavailable in private browsing or strict browser modes.
        }
    })();
</script>
