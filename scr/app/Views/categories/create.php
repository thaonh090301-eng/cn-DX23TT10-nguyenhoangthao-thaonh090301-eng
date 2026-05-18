<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('page.create_category')) ?></title>
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'categories'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow"><?= $e(__('nav.categories')) ?></p>
                <h1><?= $e(__('page.create_category')) ?></h1>
            </div>
        </section>

        <form class="panel form-stack" method="post" action="/categories">
            <label>
                <span><?= $e(__('label.name')) ?></span>
                <input type="text" name="name" value="<?= $e($category['name'] ?? '') ?>" required>
                <?php if (!empty($errors['name'])): ?>
                    <small class="field-error"><?= $e($errors['name']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span><?= $e(__('label.color')) ?></span>
                <input type="color" name="color" value="<?= $e($category['color'] ?? '#2563eb') ?>" required>
                <?php if (!empty($errors['color'])): ?>
                    <small class="field-error"><?= $e($errors['color']) ?></small>
                <?php endif; ?>
            </label>

            <label>
                <span><?= $e(__('label.sort_order')) ?></span>
                <input type="number" name="sort_order" value="<?= $e($category['sort_order'] ?? 0) ?>" min="0">
            </label>

            <div class="form-actions">
                <a class="button" href="/categories"><?= $e(__('action.cancel')) ?></a>
                <button class="button primary" type="submit"><?= $e(__('action.create')) ?></button>
            </div>
        </form>
    </main>
    <script src="../assets/js/app.js"></script>
</body>
</html>
