<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.important_dates')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'important_dates'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('important_date.eyebrow')) ?></p>
                <h1><?= $e(__('nav.important_dates')) ?></h1>
            </div>
            <a class="button primary" href="/important-dates/create"><?= $e(__('action.new_important_date')) ?></a>
        </section>

        <?php foreach (['success', 'warning', 'danger'] as $flashType): ?>
            <?php if (!empty($flash[$flashType])): ?>
                <div class="alert <?= $e($flashType) ?>"><?= $e($flash[$flashType]) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <section class="panel">
            <?php if ($importantDates === []): ?>
                <div class="empty-state">
                    <p><?= $e(__('empty.important_dates')) ?></p>
                </div>
            <?php else: ?>
                <div class="important-date-grid">
                    <?php foreach ($importantDates as $importantDate): ?>
                        <?php
                            $countdownDays = (int) $importantDate['countdown_days'];
                            $reminderDue = $importantDate['reminder_status_key'] === 'important_date.reminder_status.due';
                            $statusClass = $countdownDays < 0 ? 'warning' : ($reminderDue ? 'success' : 'info');
                            $countdownLabel = $countdownDays < 0
                                ? __('important_date.countdown_past', ['days' => abs($countdownDays)])
                                : ($countdownDays === 0 ? __('important_date.countdown_today') : __('important_date.countdown_days', ['days' => $countdownDays]));
                        ?>
                        <article class="important-date-card">
                            <div class="important-date-card-head">
                                <span class="status-pill <?= $e($statusClass) ?>">
                                    <?= $e(__('important_date.type.' . $importantDate['type'])) ?>
                                </span>
                                <strong><?= $e(format_app_date($importantDate['next_event_date'])) ?></strong>
                            </div>
                            <h2><?= $e($importantDate['title']) ?></h2>
                            <p class="important-date-countdown">
                                <?= $e($countdownLabel) ?>
                            </p>
                            <p><?= $e(__($importantDate['reminder_status_key'], ['days' => $importantDate['remind_before_days']])) ?></p>
                            <?php if (!empty($importantDate['note'])): ?>
                                <p class="help-text"><?= $e($importantDate['note']) ?></p>
                            <?php endif; ?>
                            <div class="important-date-meta">
                                <span><?= $e(__('important_date.original_date')) ?>: <?= $e(format_app_date($importantDate['event_date'])) ?></span>
                                <?php if ((int) $importantDate['repeat_yearly'] === 1): ?>
                                    <span><?= $e(__('important_date.yearly_badge')) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="actions">
                                <a href="/important-dates/<?= $e($importantDate['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                <a class="danger-link" href="/important-dates/<?= $e($importantDate['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
