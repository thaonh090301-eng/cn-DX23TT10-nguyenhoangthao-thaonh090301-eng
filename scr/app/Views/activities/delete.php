<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Delete Activity') ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'activities'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Activities</p>
                <h1>Delete Activity</h1>
            </div>
        </section>

        <section class="panel form-stack">
            <?php if (!empty($errors['activity'])): ?>
                <div class="alert danger"><?= $e($errors['activity']) ?></div>
            <?php endif; ?>

            <p>
                Delete <strong><?= $e($activity['title']) ?></strong>
                from <?= $e($activity['category_name']) ?>?
                This activity currently has <?= $e($activity['schedules_count'] ?? 0) ?> schedules
                and <?= $e($activity['time_logs_count'] ?? 0) ?> time logs.
            </p>

            <form method="post" action="/activities/<?= $e($activity['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/activities">Cancel</a>
                    <button class="button danger" type="submit">Delete</button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
