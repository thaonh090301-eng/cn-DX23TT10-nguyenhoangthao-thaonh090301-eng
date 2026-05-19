<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.assistant')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'assistant'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('assistant.eyebrow')) ?></p>
                <h1><?= $e(__('nav.assistant')) ?></h1>
            </div>
        </section>

        <section class="panel dashboard-section">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><?= $e(__('section.suggestions')) ?></p>
                    <h2><?= $e(__('assistant.panel_title')) ?></h2>
                </div>
                <span class="threshold-note"><?= $e(__('assistant.generated_at', ['time' => format_app_time($generatedAt)])) ?></span>
            </div>
            <p><?= $e(__('assistant.description')) ?></p>
        </section>

        <section class="assistant-grid" aria-label="<?= $e(__('assistant.suggestions_label')) ?>">
            <?php foreach ($suggestions as $suggestion): ?>
                <article class="assistant-card <?= $e($suggestion['severity']) ?>">
                    <div class="assistant-card-header">
                        <span class="assistant-severity"><?= $e(__('assistant.severity.' . $suggestion['severity'])) ?></span>
                        <h2><?= $e($suggestion['title']) ?></h2>
                    </div>
                    <p><?= $e($suggestion['explanation']) ?></p>
                    <div class="assistant-recommendation">
                        <strong><?= $e(__('assistant.recommendation')) ?></strong>
                        <span><?= $e($suggestion['recommendation']) ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
