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
            <?php $currentLocale = \App\Core\Lang::locale(); ?>
            <div class="login-logo" aria-label="<?= $e(__('app.sidebar_title')) ?>">
                <img
                    class="login-logo-img login-logo-light"
                    src="/assets/images/logos/login-logo-light.png"
                    alt="<?= $e(__('app.sidebar_title')) ?>"
                    onerror="this.hidden=true"
                >
                <span class="login-logo-fallback login-logo-fallback-light">
                    <strong><?= $e(__('app.sidebar_title')) ?></strong>
                    <small><?= $e(__('app.sidebar_subtitle')) ?></small>
                </span>
                <img
                    class="login-logo-img login-logo-dark"
                    src="/assets/images/logos/login-logo-dark.png"
                    alt="<?= $e(__('app.sidebar_title')) ?>"
                    onerror="this.hidden=true"
                >
                <span class="login-logo-fallback login-logo-fallback-dark">
                    <strong><?= $e(__('app.sidebar_title')) ?></strong>
                    <small><?= $e(__('app.sidebar_subtitle')) ?></small>
                </span>
            </div>
            <div class="login-panel-header">
                <div>
                    <p class="eyebrow"><?= $e(__('auth.demo_eyebrow')) ?></p>
                    <h1><?= $e(__('auth.login_title')) ?></h1>
                    <p><?= $e(__('auth.login_copy')) ?></p>
                </div>
                <div class="language-switch" aria-label="<?= $e(__('language.switcher')) ?>">
                    <a href="/login?locale=vi" <?= $currentLocale === 'vi' ? 'aria-current="true"' : '' ?>>VI</a>
                    <a href="/login?locale=en" <?= $currentLocale === 'en' ? 'aria-current="true"' : '' ?>>EN</a>
                </div>
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
