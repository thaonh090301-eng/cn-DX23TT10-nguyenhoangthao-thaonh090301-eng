<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\DashboardRepository;
use App\Repositories\ImportantDateRepository;
use App\Repositories\ReminderRepository;

class DashboardController extends Controller
{
    private const DEMO_USER_ID = 1;

    private DashboardRepository $dashboard;
    private ImportantDateRepository $importantDates;
    private ReminderRepository $reminders;

    public function __construct()
    {
        $this->dashboard = new DashboardRepository();
        $this->importantDates = new ImportantDateRepository();
        $this->reminders = new ReminderRepository();
    }

    public function index(): string
    {
        $threshold = $this->personalThresholdMinutes();
        $summary = $this->dashboard->summary(self::DEMO_USER_ID);
        $personalMinutes = $this->dashboard->personalOrRecreationActualMinutesToday(self::DEMO_USER_ID);
        $alerts = $this->alerts($summary, $personalMinutes, $threshold);
        $productivityScore = $this->productivityScore($summary, $personalMinutes, $threshold);

        return $this->view('dashboard/index', [
            'title' => \__('nav.dashboard'),
            'summary' => $summary,
            'plannedByCategory' => $this->dashboard->plannedMinutesByCategoryThisWeek(self::DEMO_USER_ID),
            'actualByCategory' => $this->dashboard->actualMinutesByCategoryThisWeek(self::DEMO_USER_ID),
            'alerts' => $alerts,
            'personalThresholdMinutes' => $threshold,
            'personalActualMinutes' => $personalMinutes,
            'productivityScore' => $productivityScore,
            'productivityBadges' => $this->productivityBadges($summary, $alerts),
            'upcomingReminders' => $this->reminders->upcomingToday(self::DEMO_USER_ID, 4),
            'upcomingImportantDates' => $this->importantDates->upcomingWithinDays(self::DEMO_USER_ID, 7, 4),
        ]);
    }

    private function personalThresholdMinutes(): int
    {
        $config = require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

        return (int) ($config['dashboard']['personal_daily_threshold_minutes'] ?? 180);
    }

    private function alerts(array $summary, int $personalMinutes, int $threshold): array
    {
        $alerts = [];

        if ($summary['actual_today_minutes'] > $summary['planned_today_minutes']) {
            $alerts[] = [
                'type' => 'danger',
                'message' => \__('alert.actual_over_planned'),
            ];
        }

        if ($personalMinutes > $threshold) {
            $alerts[] = [
                'type' => 'warning',
                'message' => \__('alert.personal_threshold_exceeded'),
            ];
        }

        if ($summary['planned_today_minutes'] > 0 && $summary['time_logs_today_count'] === 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => \__('alert.planned_without_actual'),
            ];
        } elseif ($summary['time_logs_today_count'] === 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => \__('alert.no_logs_today'),
            ];
        }

        return $alerts;
    }

    private function productivityScore(array $summary, int $personalMinutes, int $threshold): array
    {
        $score = 50;
        $rules = [];
        $plannedMinutes = (int) $summary['planned_today_minutes'];
        $actualMinutes = (int) $summary['actual_today_minutes'];
        $actualOverPlanned = $actualMinutes - $plannedMinutes;
        $closeThreshold = $plannedMinutes > 0 ? max(15, (int) round($plannedMinutes * 0.2)) : 0;

        if ((int) $summary['time_logs_today_count'] > 0) {
            $score += 15;
            $rules[] = $this->scoreRule('dashboard.score.rule.logged', 15, 'positive');
        }

        if ($plannedMinutes > 0 && $actualMinutes > 0) {
            $difference = abs($actualMinutes - $plannedMinutes);

            if ($difference <= $closeThreshold) {
                $score += 20;
                $rules[] = $this->scoreRule('dashboard.score.rule.close_to_plan', 20, 'positive');
            }
        }

        if ((int) $summary['focus_logs_today_count'] > 0) {
            $score += 10;
            $rules[] = $this->scoreRule('dashboard.score.rule.focus', 10, 'positive');
        }

        if (
            $plannedMinutes > 0
            && $actualOverPlanned > $closeThreshold
            && ($actualOverPlanned >= 90 || $actualMinutes >= (int) ceil($plannedMinutes * 1.5))
        ) {
            $score -= 20;
            $rules[] = $this->scoreRule('dashboard.score.rule.over_planned', -20, 'negative');
        }

        if ($personalMinutes > $threshold) {
            $score -= 15;
            $rules[] = $this->scoreRule('dashboard.score.rule.personal_over', -15, 'negative');
        }

        if ($rules === []) {
            $rules[] = $this->scoreRule('dashboard.score.rule.neutral', 0, 'neutral');
        }

        return [
            'value' => max(0, min(100, $score)),
            'rules' => $rules,
        ];
    }

    private function productivityBadges(array $summary, array $alerts): array
    {
        $badges = [];

        if ((int) ($summary['scheduled_today_count'] ?? 0) > 0) {
            $badges[] = ['label' => __('dashboard.badge.planned'), 'type' => 'info'];
        }

        if ((int) $summary['time_logs_today_count'] > 0) {
            $badges[] = ['label' => __('dashboard.badge.logged'), 'type' => 'success'];
        }

        if ($alerts === []) {
            $badges[] = ['label' => __('dashboard.badge.balanced'), 'type' => 'success'];
        }

        if ((int) ($summary['focus_logs_today_count'] ?? 0) > 0) {
            $badges[] = ['label' => __('dashboard.badge.focus'), 'type' => 'warning'];
        }

        return $badges;
    }

    private function scoreRule(string $labelKey, int $points, string $type): array
    {
        return [
            'label' => __($labelKey),
            'points' => $points,
            'type' => $type,
        ];
    }
}
