<?php

use App\Core\Lang;

$activeNav = $activeNav ?? '';
$navItems = [
    'home' => ['label' => __('nav.home'), 'href' => '/'],
    'dashboard' => ['label' => __('nav.dashboard'), 'href' => '/dashboard'],
    'optimizer' => ['label' => __('nav.optimizer'), 'href' => '/optimizer'],
    'categories' => ['label' => __('nav.categories'), 'href' => '/categories'],
    'activities' => ['label' => __('nav.activities'), 'href' => '/activities'],
    'schedules' => ['label' => __('nav.schedules'), 'href' => '/schedules'],
    'calendar' => ['label' => __('nav.calendar'), 'href' => '/schedules/calendar'],
    'time_logs' => ['label' => __('nav.time_logs'), 'href' => '/time-logs'],
];

$currentLocale = Lang::locale();
?>
<nav class="top-nav">
    <div class="nav-links">
        <?php foreach ($navItems as $key => $item): ?>
            <a href="<?= $e($item['href']) ?>" <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
                <?= $e($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="language-switch" aria-label="<?= $e(__('language.switcher')) ?>">
        <a href="/lang?locale=vi" <?= $currentLocale === 'vi' ? 'aria-current="true"' : '' ?>>
            <?= $e(__('language.vi')) ?>
        </a>
        <a href="/lang?locale=en" <?= $currentLocale === 'en' ? 'aria-current="true"' : '' ?>>
            <?= $e(__('language.en')) ?>
        </a>
    </div>
</nav>
