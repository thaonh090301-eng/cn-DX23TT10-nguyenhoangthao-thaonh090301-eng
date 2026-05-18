<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Schedules') ?></title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'schedules'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Management</p>
                <h1>Schedules</h1>
            </div>
            <div class="header-actions">
                <a class="button" href="/schedules/calendar">Calendar</a>
                <a class="button primary" href="/schedules/create">New Schedule</a>
            </div>
        </section>

        <?php if (!empty($flash['success'])): ?>
            <div class="alert success"><?= $e($flash['success']) ?></div>
        <?php endif; ?>

        <section class="panel">
            <?php if ($schedules === []): ?>
                <p class="empty-state">No schedules yet. Create activities first, then add schedules.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Activity</th>
                                <th>Category</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?= $e($schedule['title']) ?></td>
                                    <td><?= $e($schedule['activity_title']) ?></td>
                                    <td>
                                        <span class="color-chip" style="--chip: <?= $e($schedule['category_color']) ?>"></span>
                                        <?= $e($schedule['category_name']) ?>
                                    </td>
                                    <td><?= $e($schedule['start_at']) ?></td>
                                    <td><?= $e($schedule['end_at']) ?></td>
                                    <td><?= $e(ucfirst($schedule['status'])) ?></td>
                                    <td class="actions">
                                        <a href="/schedules/<?= $e($schedule['id']) ?>/edit">Edit</a>
                                        <a class="danger-link" href="/schedules/<?= $e($schedule['id']) ?>/delete">Delete</a>
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
