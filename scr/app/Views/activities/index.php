<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.activities')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'activities'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('section.management')) ?></p>
                <h1><?= $e(__('nav.activities')) ?></h1>
            </div>
            <a class="button primary" href="/activities/create"><?= $e(__('action.new_activity')) ?></a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if (($hasActivities ?? true) === false): ?>
                <div class="empty-state">
                    <p><?= $e(__('empty.activities')) ?></p>
                </div>
            <?php else: ?>
                <form class="filter-bar" method="get" action="/activities" data-filter-controls data-filter-target="activities-table">
                    <label class="filter-field">
                        <span><?= $e(__('filter.search')) ?></span>
                        <input type="search" data-filter-search placeholder="<?= $e(__('filter.search_placeholder')) ?>">
                    </label>
                    <label class="filter-field">
                        <span><?= $e(__('label.category')) ?></span>
                        <select data-filter-select="category">
                            <option value=""><?= $e(__('filter.all_categories')) ?></option>
                        </select>
                    </label>
                    <label class="filter-field">
                        <span><?= $e(__('label.status')) ?></span>
                        <select name="status" onchange="this.form.submit()">
                            <option value="all" <?= ($selectedStatus ?? 'all') === 'all' ? 'selected' : '' ?>><?= $e(__('filter.all_statuses')) ?></option>
                            <option value="active" <?= ($selectedStatus ?? 'all') === 'active' ? 'selected' : '' ?>><?= $e(__('status.active')) ?></option>
                            <option value="inactive" <?= ($selectedStatus ?? 'all') === 'inactive' ? 'selected' : '' ?>><?= $e(__('status.inactive')) ?></option>
                        </select>
                    </label>
                </form>
                <?php if ($activities === []): ?>
                    <p class="empty-state"><?= $e(__('filter.no_results')) ?></p>
                <?php else: ?>
                <div class="table-wrap">
                    <table id="activities-table">
                        <thead>
                            <tr>
                                <th><?= $e(__('label.title')) ?></th>
                                <th><?= $e(__('label.category')) ?></th>
                                <th><?= $e(__('label.priority')) ?></th>
                                <th><?= $e(__('label.estimate')) ?></th>
                                <th><?= $e(__('label.status')) ?></th>
                                <th><?= $e(__('label.actions')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activities as $activity): ?>
                                <?php
                                    $activityTitle = display_activity_title($activity['title']);
                                    $categoryName = display_category_name($activity['category_name']);
                                    $priorityLabel = __('priority.' . $activity['priority']);
                                    $statusLabel = ((int) $activity['is_active'] === 1) ? __('status.active') : __('status.inactive');
                                    $searchText = implode(' ', [$activityTitle, $categoryName, $priorityLabel, $statusLabel]);
                                ?>
                                <tr data-filter-row data-search="<?= $e($searchText) ?>" data-category="<?= $e($categoryName) ?>" data-status="<?= $e($statusLabel) ?>">
                                    <td><?= $e($activityTitle) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($activity['category_color']) ?>"></span>
                                        <?= $e($categoryName) ?>
                                    </td>
                                    <td><?= $e($priorityLabel) ?></td>
                                    <td><?= $e($activity['estimated_minutes']) ?> <?= $e(__('unit.min')) ?></td>
                                    <td><?= $e($statusLabel) ?></td>
                                    <td class="actions">
                                        <a href="/activities/<?= $e($activity['id']) ?>/edit"><?= $e(__('action.edit')) ?></a>
                                        <a class="danger-link" href="/activities/<?= $e($activity['id']) ?>/delete"><?= $e(__('action.delete')) ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="empty-state filter-empty" data-filter-empty="activities-table" hidden><?= $e(__('filter.no_results')) ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
