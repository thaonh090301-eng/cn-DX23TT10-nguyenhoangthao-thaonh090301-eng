<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Delete Schedule') ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'schedules'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Schedules</p>
                <h1>Delete Schedule</h1>
            </div>
        </section>

        <section class="panel form-stack">
            <p>
                Delete <strong><?= $e($schedule['title']) ?></strong>
                for <?= $e($schedule['activity_title']) ?>?
            </p>

            <form method="post" action="/schedules/<?= $e($schedule['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/schedules">Cancel</a>
                    <button class="button danger" type="submit">Delete</button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
