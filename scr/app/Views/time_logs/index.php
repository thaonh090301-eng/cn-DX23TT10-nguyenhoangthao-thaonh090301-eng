<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Time Logs') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'time_logs'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Tracking</p>
                <h1>Time Logs</h1>
            </div>
            <a class="button primary" href="/time-logs/create">New Time Log</a>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($timeLogs === []): ?>
                <p class="empty-state">No time logs yet. Create activities first, then record actual time.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Category</th>
                                <th>Actual Start</th>
                                <th>Actual End</th>
                                <th>Duration</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timeLogs as $timeLog): ?>
                                <tr>
                                    <td><?= $e($timeLog['activity_title']) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($timeLog['category_color']) ?>"></span>
                                        <?= $e($timeLog['category_name']) ?>
                                    </td>
                                    <td><?= $e($timeLog['started_at']) ?></td>
                                    <td><?= $e($timeLog['ended_at']) ?></td>
                                    <td><?= $e($timeLog['duration_minutes']) ?> min</td>
                                    <td><?= $e($timeLog['note'] ?? '') ?></td>
                                    <td class="actions">
                                        <a href="/time-logs/<?= $e($timeLog['id']) ?>/edit">Edit</a>
                                        <a class="danger-link" href="/time-logs/<?= $e($timeLog['id']) ?>/delete">Delete</a>
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
