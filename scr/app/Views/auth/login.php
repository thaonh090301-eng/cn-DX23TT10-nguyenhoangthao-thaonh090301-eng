<!doctype html>
<html lang="<?= $e(\App\Core\Lang::locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e(__('auth.login_title')) ?></title>
    <?php require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'theme_boot.php'; ?>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <main class="login-shell">
        <section class="login-panel">
            <div>
                <p class="eyebrow"><?= $e(__('auth.demo_eyebrow')) ?></p>
                <h1><?= $e(__('auth.login_title')) ?></h1>
                <p><?= $e(__('auth.login_copy')) ?></p>
            </div>

            <?php foreach (['success', 'warning', 'danger'] as $flashType): ?>
                <?php if (!empty($flash[$flashType])): ?>
                    <div class="alert <?= $e($flashType) ?>"><?= $e($flash[$flashType]) ?></div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (!empty($errors['login'])): ?>
                <div class="alert danger"><?= $e($errors['login']) ?></div>
            <?php endif; ?>

            <form class="form-stack" method="post" action="/login">
                <label>
                    <span><?= $e(__('auth.email')) ?></span>
                    <input type="email" name="email" value="<?= $e($credentials['email'] ?? '') ?>" autocomplete="email" required>
                </label>

                <label>
                    <span><?= $e(__('auth.password')) ?></span>
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>

                <button class="button primary" type="submit"><?= $e(__('auth.login_button')) ?></button>
            </form>

            <div class="demo-credentials">
                <strong><?= $e(__('auth.demo_credentials')) ?></strong>
                <span><?= $e(__('auth.demo_email')) ?>: demo@example.com</span>
                <span><?= $e(__('auth.demo_password')) ?>: password</span>
            </div>
        </section>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
