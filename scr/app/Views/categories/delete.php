<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Delete Category') ?></title>
    <link rel="stylesheet" href="../../assets/css/app.css">
</head>
<body>
    <main class="app-shell narrow">
        <?php $activeNav = 'categories'; require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'navigation.php'; ?>

        <section class="page-header">
            <div>
                <p class="eyebrow">Categories</p>
                <h1>Delete Category</h1>
            </div>
        </section>

        <section class="panel form-stack">
            <?php if (!empty($errors['category'])): ?>
                <div class="alert danger"><?= $e($errors['category']) ?></div>
            <?php endif; ?>

            <p>
                Delete <strong><?= $e($category['name']) ?></strong>?
                This category currently has <?= $e($category['activities_count']) ?> activities.
            </p>

            <form method="post" action="/categories/<?= $e($category['id']) ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="form-actions">
                    <a class="button" href="/categories">Cancel</a>
                    <button class="button danger" type="submit">Delete</button>
                </div>
            </form>
        </section>
    </main>
    <script src="../../assets/js/app.js"></script>
</body>
</html>
