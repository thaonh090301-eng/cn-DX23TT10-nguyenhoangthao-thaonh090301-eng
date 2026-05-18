<?php
$dateTimeLocal = static fn (mixed $value): string => str_replace(' ', 'T', substr((string) $value, 0, 16));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Create Time Log') ?></title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'time_logs'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Time Logs</p>
                <h1>Create Time Log</h1>
            </div>
        </section>

        <?php if ($activities === []): ?>
            <div class="alert danger">Create at least one activity before adding time logs.</div>
        <?php endif; ?>

        <form class="panel form-stack" method="post" action="/time-logs">
            <label>
                <span>Activity</span>
                <select name="activity_id" required>
                    <option value="">Choose activity</option>
                    <?php foreach ($activities as $activity): ?>
                        <option value="<?= $e($activity['id']) ?>" <?= ((int) ($timeLog['activity_id'] ?? 0) === (int) $activity['id']) ? 'selected' : '' ?>>
                            <?= $e($activity['title']) ?> - <?= $e($activity['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['activity_id'])): ?>
                    <small class="field-error"><?= $e($errors['activity_id']) ?></small>
                <?php endif; ?>
            </label>

            <div class="form-grid">
                <label>
                    <span>Actual Start</span>
                    <input type="datetime-local" name="started_at" value="<?= $e($dateTimeLocal($timeLog['started_at'] ?? '')) ?>" required>
                    <?php if (!empty($errors['started_at'])): ?>
                        <small class="field-error"><?= $e($errors['started_at']) ?></small>
                    <?php endif; ?>
                </label>

                <label>
                    <span>Actual End</span>
                    <input type="datetime-local" name="ended_at" value="<?= $e($dateTimeLocal($timeLog['ended_at'] ?? '')) ?>" required>
                    <?php if (!empty($errors['ended_at'])): ?>
                        <small class="field-error"><?= $e($errors['ended_at']) ?></small>
                    <?php endif; ?>
                </label>
            </div>

            <label>
                <span>Note</span>
                <textarea name="note" rows="4"><?= $e($timeLog['note'] ?? '') ?></textarea>
            </label>

            <div class="form-actions">
                <a class="button" href="/time-logs">Cancel</a>
                <button class="button primary" type="submit">Create</button>
            </div>
        </form>
    </main>
    <script src="../assets/js/app.js"></script>
</body>
</html>
