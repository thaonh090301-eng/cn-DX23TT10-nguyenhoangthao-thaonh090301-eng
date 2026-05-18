<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\DashboardRepository;

class DashboardController extends Controller
{
    private const DEMO_USER_ID = 1;

    private DashboardRepository $dashboard;

    public function __construct()
    {
        $this->dashboard = new DashboardRepository();
    }

    public function index(): string
    {
        $threshold = $this->personalThresholdMinutes();
        $summary = $this->dashboard->summary(self::DEMO_USER_ID);
        $personalMinutes = $this->dashboard->personalOrRecreationActualMinutesToday(self::DEMO_USER_ID);

        return $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'summary' => $summary,
            'plannedByCategory' => $this->dashboard->plannedMinutesByCategoryThisWeek(self::DEMO_USER_ID),
            'actualByCategory' => $this->dashboard->actualMinutesByCategoryThisWeek(self::DEMO_USER_ID),
            'alerts' => $this->alerts($summary, $personalMinutes, $threshold),
            'personalThresholdMinutes' => $threshold,
            'personalActualMinutes' => $personalMinutes,
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
                'message' => 'Actual time today is greater than planned time.',
            ];
        }

        if ($personalMinutes > $threshold) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Personal or recreation time has exceeded the daily threshold.',
            ];
        }

        if ($summary['time_logs_today_count'] === 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'No time logs have been recorded today.',
            ];
        }

        return $alerts;
    }
}
