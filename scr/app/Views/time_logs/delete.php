<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Delete Time Log') ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'time_logs'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Time Logs</p>
                <h1>Delete Time Log</h1>
            </div>
        </section>

        <section class="panel form-stack">
            <p>
                Delete the time log for <strong><?= $e($timeLog['activity_title']) ?></strong>
                from <?= $e($timeLog['started_at']) ?> to <?= $e($timeLog['ended_at']) ?>?
            </p>

            <form method="post" action="/time-logs/<?= $e($timeLog['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/time-logs">Cancel</a>
                    <button class="button danger" type="submit">Delete</button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
