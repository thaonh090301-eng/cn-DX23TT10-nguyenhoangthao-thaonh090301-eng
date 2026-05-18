<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('app.title')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'home'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="intro-panel">
            <p class="eyebrow"><?= $e(__('home.eyebrow')) ?></p>
            <h1><?= $e(__('app.title')) ?></h1>
            <p><?= $e(__('home.description')) ?></p>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
