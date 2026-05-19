<?php
$formatTime = static function (mixed $value): string {
    if ($value === null || $value === '') {
        return __('time_report.not_recorded');
    }

    return format_app_time($value);
};
$formatMinutes = static fn (mixed $value): string => $value === null ? __('time_report.not_recorded') : format_duration_minutes($value);
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.time_logs')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'time_logs'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.tracking')) ?></p>
                <h1><?= $e(__('time_report.title')) ?></h1>
            </div>
            <div class="header-actions">
                <a class="button" href="/time-logs/create?date=<?= $e($selectedDate) ?>"><?= $e(__('time_report.action.unscheduled')) ?></a>
                <button class="button primary" type="button" onclick="window.print()"><?= $e(__('time_report.action.print')) ?></button>
            </div>
        </section>

        <?php foreach (['success', 'warning', 'danger'] as $flashType): ?>
            <?php if (!empty($flash[$flashType])): ?>
                <div class="alert <?= $e($flashType) ?>"><?= $e($flash[$flashType]) ?></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <section class="panel dashboard-section time-report-toolbar">
            <form class="filter-bar time-report-filter" method="get" action="/time-logs">
                <label class="filter-field">
                    <span><?= $e(__('time_report.filter_date')) ?></span>
                    <input type="date" name="date" value="<?= $e($selectedDate) ?>">
                </label>
                <div class="form-actions">
                    <button class="button primary" type="submit"><?= $e(__('time_report.action.view_report')) ?></button>
                </div>
            </form>

            <div class="time-report-summary">
                <article class="stat-card mini">
                    <span><?= $e(__('time_report.summary.planned')) ?></span>
                    <strong><?= $e(format_duration_minutes($summary['planned_minutes'])) ?></strong>
                </article>
                <article class="stat-card mini">
                    <span><?= $e(__('time_report.summary.actual')) ?></span>
                    <strong><?= $e(format_duration_minutes($summary['actual_minutes'])) ?></strong>
                </article>
                <article class="stat-card mini">
                    <span><?= $e(__('time_report.summary.missing')) ?></span>
                    <strong><?= $e($summary['missing_count']) ?></strong>
                </article>
            </div>
        </section>

        <section class="panel">
            <?php if ($reportRows === []): ?>
                <div class="empty-state">
                    <p><?= $e(__('time_report.empty')) ?></p>
                    <a class="button primary" href="/schedules/create"><?= $e(__('action.new_schedule')) ?></a>
                </div>
            <?php else: ?>
                <div class="table-wrap report-table-wrap">
                    <table class="time-report-table">
                        <thead>
                            <tr>
                                <th><?= $e(__('label.activity')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('time_report.planned_start')) ?></th>
                                <th><?= $e(__('time_report.planned_end')) ?></th>
                                <th><?= $e(__('time_report.planned_duration')) ?></th>
                                <th><?= $e(__('label.actual_start')) ?></th>
                                <th><?= $e(__('label.actual_end')) ?></th>
                                <th><?= $e(__('time_report.actual_duration')) ?></th>
                                <th><?= $e(__('label.status')) ?></th>
                                <th><?= $e(__('label.note')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportRows as $row): ?>
                                <?php
                                    $activityTitle = display_activity_title($row['activity_title']);
                                    $categoryName = display_category_name($row['category_name']);
                                    $note = display_note($row['actual_note'] ?? $row['schedule_notes'] ?? '');
                                ?>
                                <tr>
                                    <td><?= $e($activityTitle) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($row['category_color']) ?>"></span>
                                        <?= $e($categoryName) ?>
                                    </td>
                                    <td><?= $e($formatTime($row['planned_start_at'])) ?></td>
                                    <td><?= $e($formatTime($row['planned_end_at'])) ?></td>
                                    <td><?= $e($formatMinutes($row['planned_minutes'])) ?></td>
                                    <td><?= $e($formatTime($row['actual_started_at'])) ?></td>
                                    <td><?= $e($formatTime($row['actual_ended_at'])) ?></td>
                                    <td><?= $e($formatMinutes($row['actual_minutes'])) ?></td>
                                    <td>
                                        <span class="status-pill <?= $e($row['report_status_type']) ?>">
                                            <?= $e($row['report_status']) ?>
                                        </span>
                                    </td>
                                    <td><?= $e($note) ?></td>
                                    <td class="actions report-actions">
                                        <?php if ($row['time_log_id'] !== null): ?>
                                            <a href="/time-logs/<?= $e($row['time_log_id']) ?>/edit"><?= $e(__('time_report.action.edit_actual')) ?></a>
                                        <?php elseif ($row['schedule_id'] !== null): ?>
                                            <form method="post" action="/time-logs/schedules/<?= $e($row['schedule_id']) ?>/confirm">
                                                <button class="button compact primary" type="submit"><?= $e(__('time_report.action.confirm_schedule')) ?></button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($row['time_log_id'] !== null && $row['row_type'] === 'unscheduled'): ?>
                                            <a class="danger-link" href="/time-logs/<?= $e($row['time_log_id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
