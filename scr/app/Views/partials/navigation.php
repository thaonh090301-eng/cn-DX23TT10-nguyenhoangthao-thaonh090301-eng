<?php

$activeNav = $activeNav ?? '';
$navItems = [
    'home' => ['label' => 'Home', 'href' => '/'],
    'categories' => ['label' => 'Categories', 'href' => '/categories'],
    'activities' => ['label' => 'Activities', 'href' => '/activities'],
    'schedules' => ['label' => 'Schedules', 'href' => '/schedules'],
    'calendar' => ['label' => 'Calendar', 'href' => '/schedules/calendar'],
];
?>
<nav class="top-nav">
    <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= $e($item['href']) ?>" <?= $activeNav === $key ? 'aria-current="page"' : '' ?>>
            <?= $e($item['label']) ?>
        </a>
    <?php endforeach; ?>
</nav>
