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
                <h1><?= $e(__('nav.time_logs')) ?></h1>
            </div>
            <a class="button primary" href="/time-logs/create"><?= $e(__('action.new_time_log')) ?></a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($timeLogs === []): ?>
                <div class="empty-state">
                    <p><?= $e(__('empty.time_logs')) ?></p>
                    <a class="button primary" href="/time-logs/create"><?= $e(__('action.new_time_log')) ?></a>
                </div>
            <?php else: ?>
                <div class="filter-bar" data-filter-controls data-filter-target="time-logs-table">
                    <label class="filter-field">
                        <span><?= $e(__('filter.search')) ?></span>
                        <input type="search" data-filter-search placeholder="<?= $e(__('filter.search_placeholder')) ?>">
                    </label>
                    <label class="filter-field">
                        <span><?= $e(__('label.activity')) ?></span>
                        <select data-filter-select="activity">
                            <option value=""><?= $e(__('filter.all_activities')) ?></option>
                        </select>
                    </label>
                    <label class="filter-field">
                        <span><?= $e(__('label.category')) ?></span>
                        <select data-filter-select="category">
                            <option value=""><?= $e(__('filter.all_categories')) ?></option>
                        </select>
                    </label>
                </div>
                <div class="table-wrap">
                    <table id="time-logs-table">
                        <thead>
                            <tr>
                                <th><?= $e(__('label.activity')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('label.actual_start')) ?></th>
                                <th><?= $e(__('label.actual_end')) ?></th>
                                <th><?= $e(__('label.duration')) ?></th>
                                <th><?= $e(__('label.note')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timeLogs as $timeLog): ?>
                                <?php
                                    $activityTitle = display_activity_title($timeLog['activity_title']);
                                    $categoryName = display_category_name($timeLog['category_name']);
                                    $note = (string) ($timeLog['note'] ?? '');
                                    $searchText = implode(' ', [$activityTitle, $categoryName, $note, $timeLog['started_at'], $timeLog['ended_at']]);
                                ?>
                                <tr data-filter-row data-search="<?= $e($searchText) ?>" data-activity="<?= $e($activityTitle) ?>" data-category="<?= $e($categoryName) ?>">
                                    <td><?= $e($activityTitle) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($timeLog['category_color']) ?>"></span>
                                        <?= $e($categoryName) ?>
                                    </td>
                                    <td><?= $e($timeLog['started_at']) ?></td>
                                    <td><?= $e($timeLog['ended_at']) ?></td>
                                    <td><?= $e($timeLog['duration_minutes']) ?> <?= $e(__('unit.min')) ?></td>
                                    <td><?= $e($timeLog['note'] ?? '') ?></td>
                                    <td class="actions">
                                        <a href="/time-logs/<?= $e($timeLog['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                        <a class="danger-link" href="/time-logs/<?= $e($timeLog['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="empty-state filter-empty" data-filter-empty="time-logs-table" hidden><?= $e(__('filter.no_results')) ?></p>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
