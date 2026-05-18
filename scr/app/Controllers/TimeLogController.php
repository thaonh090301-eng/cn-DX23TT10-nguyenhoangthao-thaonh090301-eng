<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\TimeLogRepository;
use DateTimeImmutable;
use Exception;

class TimeLogController extends Controller
{
    private const DEMO_USER_ID = 1;

    private TimeLogRepository $timeLogs;
    private ActivityRepository $activities;

    public function __construct()
    {
        $this->timeLogs = new TimeLogRepository();
        $this->activities = new ActivityRepository();
    }

    public function index(): string
    {
        return $this->view('time_logs/index', [
            'title' => 'Time Logs',
            'timeLogs' => $this->timeLogs->allByUser(self::DEMO_USER_ID),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function create(): string
    {
        return $this->view('time_logs/create', [
            'title' => 'Create Time Log',
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
                'title' => 'Create Time Log',
                'timeLog' => $data,
                'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
                'errors' => $errors,
            ]);
        }

        $this->timeLogs->create(self::DEMO_USER_ID, $data);
        $this->flash('success', \__('flash.time_log_created'));

        return $this->redirect('/time-logs');
    }

    public function edit(string $id): string
    {
        $timeLog = $this->findTimeLogOrFail((int) $id);

        return $this->view('time_logs/edit', [
            'title' => 'Edit Time Log',
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
                'title' => 'Edit Time Log',
                'timeLog' => array_merge($timeLog, $data),
                'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
                'errors' => $errors,
            ]);
        }

        $this->timeLogs->update((int) $id, self::DEMO_USER_ID, $data);
        $this->flash('success', \__('flash.time_log_updated'));

        return $this->redirect('/time-logs');
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
        return [
            'activity_id' => 0,
            'started_at' => date('Y-m-d 08:00:00'),
            'ended_at' => date('Y-m-d 09:00:00'),
            'duration_minutes' => 60,
            'note' => '',
        ];
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
