<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\TimeLogRepository;
use DateTimeImmutable;
use Exception;

class TimeLogController extends Controller
{
    private const DEMO_USER_ID = 1;

    private TimeLogRepository $timeLogs;
    private ActivityRepository $activities;
    private ScheduleRepository $schedules;

    public function __construct()
    {
        $this->timeLogs = new TimeLogRepository();
        $this->activities = new ActivityRepository();
        $this->schedules = new ScheduleRepository();
    }

    public function index(): string
    {
        $date = $this->dateFromRequest();
        $reportRows = array_map(fn (array $row): array => $this->withReportStatus($row), $this->timeLogs->dailyReportByUser(self::DEMO_USER_ID, $date));

        return $this->view('time_logs/index', [
            'title' => \__('nav.time_logs'),
            'reportRows' => $reportRows,
            'selectedDate' => $date,
            'summary' => $this->reportSummary($reportRows),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function create(): string
    {
        return $this->view('time_logs/create', [
            'title' => \__('time_report.action.unscheduled'),
            'timeLog' => $this->defaultTimeLog(),
            'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->timeLogDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('time_logs/create', [
                'title' => \__('time_report.action.unscheduled'),
                'timeLog' => $data,
                'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
                'errors' => $errors,
            ]);
        }

        $this->timeLogs->create(self::DEMO_USER_ID, $data);
        $this->flash('success', \__('flash.time_log_created'));

        return $this->redirect('/time-logs?date=' . urlencode(substr((string) $data['started_at'], 0, 10)));
    }

    public function confirmSchedule(string $id): string
    {
        $schedule = $this->schedules->findByUser((int) $id, self::DEMO_USER_ID);

        if ($schedule === null) {
            http_response_code(404);
            exit(\__('not_found.schedule'));
        }

        if ($this->timeLogs->findBySchedule((int) $id, self::DEMO_USER_ID) !== null) {
            $this->flash('warning', \__('flash.time_log_schedule_duplicate'));

            return $this->redirect('/time-logs?date=' . urlencode(substr((string) $schedule['start_at'], 0, 10)));
        }

        $this->timeLogs->createFromSchedule(self::DEMO_USER_ID, $schedule);
        $this->flash('success', \__('flash.time_log_schedule_confirmed'));

        return $this->redirect('/time-logs?date=' . urlencode(substr((string) $schedule['start_at'], 0, 10)));
    }

    public function edit(string $id): string
    {
        $timeLog = $this->findTimeLogOrFail((int) $id);

        return $this->view('time_logs/edit', [
            'title' => \__('page.edit_time_log'),
            'timeLog' => $timeLog,
            'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $timeLog = $this->findTimeLogOrFail((int) $id);
        $data = $this->timeLogDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('time_logs/edit', [
                'title' => \__('page.edit_time_log'),
                'timeLog' => array_merge($timeLog, $data),
                'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
                'errors' => $errors,
            ]);
        }

        $this->timeLogs->update((int) $id, self::DEMO_USER_ID, $data);
        $this->flash('success', \__('flash.time_log_updated'));

        return $this->redirect('/time-logs?date=' . urlencode(substr((string) $data['started_at'], 0, 10)));
    }

    public function delete(string $id): string
    {
        return $this->view('time_logs/delete', [
            'title' => 'Delete Time Log',
            'timeLog' => $this->findTimeLogOrFail((int) $id),
        ]);
    }

    public function destroy(string $id): string
    {
        $this->findTimeLogOrFail((int) $id);
        $this->timeLogs->delete((int) $id, self::DEMO_USER_ID);
        $this->flash('success', \__('flash.time_log_deleted'));

        return $this->redirect('/time-logs');
    }

    private function timeLogDataFromRequest(): array
    {
        $startedAt = $this->normalizeDateTime((string) ($_POST['started_at'] ?? ''));
        $endedAt = $this->normalizeDateTime((string) ($_POST['ended_at'] ?? ''));

        return [
            'activity_id' => (int) ($_POST['activity_id'] ?? 0),
            'schedule_id' => null,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration_minutes' => $this->durationMinutes($startedAt, $endedAt),
            'note' => trim((string) ($_POST['note'] ?? '')),
        ];
    }

    private function normalizeDateTime(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            return (new DateTimeImmutable($value))->format('Y-m-d H:i:s');
        } catch (Exception) {
            return null;
        }
    }

    private function durationMinutes(?string $startedAt, ?string $endedAt): int
    {
        if ($startedAt === null || $endedAt === null) {
            return 0;
        }

        $seconds = strtotime($endedAt) - strtotime($startedAt);

        return max(0, (int) floor($seconds / 60));
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['activity_id'] <= 0 || $this->activities->findByUser($data['activity_id'], self::DEMO_USER_ID) === null) {
            $errors['activity_id'] = \__('validation.valid_activity');
        }

        if ($data['started_at'] === null) {
            $errors['started_at'] = \__('validation.actual_start_required');
        }

        if ($data['ended_at'] === null) {
            $errors['ended_at'] = \__('validation.actual_end_required');
        }

        if ($data['started_at'] !== null && $data['ended_at'] !== null && strtotime($data['ended_at']) <= strtotime($data['started_at'])) {
            $errors['ended_at'] = \__('validation.actual_end_after_start');
        }

        return $errors;
    }

    private function defaultTimeLog(): array
    {
        $date = $this->dateFromRequest();

        return [
            'activity_id' => 0,
            'schedule_id' => null,
            'started_at' => $date . ' 08:00:00',
            'ended_at' => $date . ' 09:00:00',
            'duration_minutes' => 60,
            'note' => '',
        ];
    }

    private function dateFromRequest(): string
    {
        $date = trim((string) ($_GET['date'] ?? date('Y-m-d')));

        if (!$this->isDate($date)) {
            return date('Y-m-d');
        }

        return $date;
    }

    private function isDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function withReportStatus(array $row): array
    {
        $plannedMinutes = $row['planned_minutes'] === null ? null : (int) $row['planned_minutes'];
        $actualMinutes = $row['actual_minutes'] === null ? null : (int) $row['actual_minutes'];
        $hasActual = $row['time_log_id'] !== null;

        if (!$hasActual) {
            $row['report_status'] = __('time_report.status.unconfirmed');
            $row['report_status_type'] = 'warning';

            return $row;
        }

        if ($row['actual_started_at'] === null || $row['actual_ended_at'] === null || $actualMinutes === null || $actualMinutes <= 0) {
            $row['report_status'] = __('time_report.status.invalid');
            $row['report_status_type'] = 'alarm';

            return $row;
        }

        if ($plannedMinutes === null) {
            $row['report_status'] = __('time_report.status.unscheduled');
            $row['report_status_type'] = 'info';

            return $row;
        }

        $difference = $actualMinutes - $plannedMinutes;
        $absoluteDifference = abs($difference);

        if ($absoluteDifference <= 15) {
            $row['report_status'] = __('time_report.status.on_plan');
            $row['report_status_type'] = 'success';
        } elseif ($difference > 60 || $actualMinutes >= (int) ceil($plannedMinutes * 1.5)) {
            $row['report_status'] = __('time_report.status.overrun');
            $row['report_status_type'] = 'alarm';
        } else {
            $row['report_status'] = __('time_report.status.slight_diff');
            $row['report_status_type'] = 'warning';
        }

        return $row;
    }

    private function reportSummary(array $rows): array
    {
        $summary = [
            'planned_minutes' => 0,
            'actual_minutes' => 0,
            'scheduled_count' => 0,
            'confirmed_count' => 0,
            'missing_count' => 0,
        ];

        foreach ($rows as $row) {
            if ($row['planned_minutes'] !== null) {
                $summary['planned_minutes'] += (int) $row['planned_minutes'];
                $summary['scheduled_count']++;
            }

            if ($row['actual_minutes'] !== null) {
                $summary['actual_minutes'] += (int) $row['actual_minutes'];
                $summary['confirmed_count']++;
            }

            if ($row['schedule_id'] !== null && $row['time_log_id'] === null) {
                $summary['missing_count']++;
            }
        }

        return $summary;
    }

    private function findTimeLogOrFail(int $id): array
    {
        $timeLog = $this->timeLogs->findByUser($id, self::DEMO_USER_ID);

        if ($timeLog !== null) {
            return $timeLog;
        }

        http_response_code(404);
        exit(\__('not_found.time_log'));
    }
}
