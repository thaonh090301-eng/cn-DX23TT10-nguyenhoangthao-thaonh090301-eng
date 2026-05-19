<?php
$accountStats = [
    ['label' => __('account.total_activities'), 'value' => $stats['activities_count'] ?? 0],
    ['label' => __('account.total_schedules'), 'value' => $stats['schedules_count'] ?? 0],
    ['label' => __('account.total_time_logs'), 'value' => $stats['time_logs_count'] ?? 0],
];
$accountName = (string) ($accountUser['name'] ?? 'Demo User');
$avatarInitial = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($accountName, 0, 1, 'UTF-8'), 'UTF-8')
    : strtoupper(substr($accountName, 0, 1));
?>
<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('nav.account')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell">
        <?php $activeNav = 'account'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('account.eyebrow')) ?></p>
                <h1><?= $e(__('nav.account')) ?></h1>
            </div>
        </section>

        <section class="panel account-panel">
            <div class="account-profile">
                <div class="account-avatar" aria-hidden="true">
                    <?= $e($avatarInitial) ?>
                </div>
                <div>
                    <p class="eyebrow"><?= $e(__('account.signed_in_as')) ?></p>
                    <h2><?= $e($accountName) ?></h2>
                    <p><?= $e($accountUser['email'] ?? '') ?></p>
                </div>
            </div>

            <div class="account-stats">
                <?php foreach ($accountStats as $stat): ?>
                    <article class="stat-card mini">
                        <span><?= $e($stat['label']) ?></span>
                        <strong><?= $e($stat['value']) ?></strong>
                    </article>
                <?php endforeach; ?>
            </div>

            <form method="post" action="/logout" class="form-actions">
                <button class="button danger" type="submit"><?= $e(__('auth.logout')) ?></button>
            </form>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
