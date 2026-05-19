<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.focus')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'focus'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('focus.eyebrow')) ?></p>
                <h1><?= $e(__('nav.focus')) ?></h1>
            </div>
        </section>

        <?php foreach (['success', 'warning', 'danger'] as $flashType): ?>
            <?php if (!empty($flash[$flashType])): ?>
                <div class="alert <?= $e($flashType) ?>"><?= $e($flash[$flashType]) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <section class="focus-layout">
            <form class="panel focus-panel" method="post" action="/focus" data-focus-form>
                <div class="section-heading">
                    <div>
                        <p class="eyebrow"><?= $e(__('focus.session_setup')) ?></p>
                        <h2><?= $e(__('focus.panel_title')) ?></h2>
                    </div>
                </div>

                <?php if ($activities === []): ?>
                    <div class="empty-state compact">
                        <p><?= $e(__('focus.no_activities')) ?></p>
                        <a class="button primary" href="/activities/create"><?= $e(__('action.new_activity')) ?></a>
                    </div>
                <?php else: ?>
                    <label>
                        <span><?= $e(__('label.activity')) ?></span>
                        <select name="activity_id" data-focus-activity required>
                            <option value=""><?= $e(__('option.choose_activity')) ?></option>
                            <?php foreach ($activities as $activity): ?>
                                <option value="<?= $e($activity['id']) ?>" <?= (int) ($focus['activity_id'] ?? 0) === (int) $activity['id'] ? 'selected' : '' ?>>
                                    <?= $e(display_activity_title($activity['title'])) ?> · <?= $e(display_category_name($activity['category_name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['activity_id'])): ?>
                            <small class="field-error"><?= $e($errors['activity_id']) ?></small>
                        <?php endif; ?>
                    </label>

                    <fieldset class="focus-duration-group">
                        <legend><?= $e(__('focus.duration')) ?></legend>
                        <div class="focus-duration-options">
                            <?php foreach ($durations as $duration): ?>
                                <label class="focus-duration-option">
                                    <input
                                        type="radio"
                                        name="duration_minutes"
                                        value="<?= $e($duration) ?>"
                                        data-focus-duration
                                        <?= (int) ($focus['duration_minutes'] ?? 25) === $duration ? 'checked' : '' ?>
                                    >
                                    <span><?= $e($duration) ?> <?= $e(__('unit.min')) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($errors['duration_minutes'])): ?>
                            <small class="field-error"><?= $e($errors['duration_minutes']) ?></small>
                        <?php endif; ?>
                    </fieldset>

                    <input type="hidden" name="started_at" value="<?= $e($focus['started_at'] ?? '') ?>" data-focus-started-at>
                    <input type="hidden" name="ended_at" value="<?= $e($focus['ended_at'] ?? '') ?>" data-focus-ended-at>

                    <?php if (!empty($errors['timer'])): ?>
                        <div class="alert danger"><?= $e($errors['timer']) ?></div>
                    <?php endif; ?>

                    <div class="focus-actions">
                        <button class="button primary" type="button" data-focus-start><?= $e(__('focus.start')) ?></button>
                        <button class="button" type="button" data-focus-pause disabled><?= $e(__('focus.pause')) ?></button>
                        <button class="button" type="button" data-focus-reset><?= $e(__('focus.reset')) ?></button>
                    </div>

                    <button class="button primary focus-save-button" type="submit" data-focus-save hidden>
                        <?= $e(__('focus.save_to_time_log')) ?>
                    </button>
                <?php endif; ?>
            </form>

            <section
                class="panel focus-timer-panel"
                data-focus-timer
                data-focus-initial-duration="<?= $e($focus['duration_minutes'] ?? 25) ?>"
                data-focus-ready="<?= $e(__('focus.ready_copy')) ?>"
                data-focus-running="<?= $e(__('focus.running_copy')) ?>"
                data-focus-paused="<?= $e(__('focus.paused_copy')) ?>"
                data-focus-completed="<?= $e(__('focus.completed_copy')) ?>"
                data-focus-pause-label="<?= $e(__('focus.pause')) ?>"
                data-focus-resume-label="<?= $e(__('focus.resume')) ?>"
            >
                <p class="eyebrow"><?= $e(__('focus.timer_label')) ?></p>
                <div class="focus-timer-display" data-focus-display>25:00</div>
                <p data-focus-status><?= $e(__('focus.ready_copy')) ?></p>
                <div class="focus-orbit" aria-hidden="true">
                    <span></span>
                </div>
            </section>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
