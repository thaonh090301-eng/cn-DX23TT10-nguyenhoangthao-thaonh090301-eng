<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\TimeLogRepository;
use DateTimeImmutable;
use Exception;

class FocusController extends Controller
{
    private const DEMO_USER_ID = 1;
    private const DURATIONS = [25, 45, 60];

    private ActivityRepository $activities;
    private TimeLogRepository $timeLogs;

    public function __construct()
    {
        $this->activities = new ActivityRepository();
        $this->timeLogs = new TimeLogRepository();
    }

    public function index(): string
    {
        return $this->view('focus/index', [
            'title' => __('nav.focus'),
            'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
            'durations' => self::DURATIONS,
            'focus' => $this->defaultFocus(),
            'errors' => [],
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function store(): string
    {
        $data = $this->focusDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('focus/index', [
                'title' => __('nav.focus'),
                'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
                'durations' => self::DURATIONS,
                'focus' => $data,
                'errors' => $errors,
                'flash' => [],
            ]);
        }

        $this->timeLogs->create(self::DEMO_USER_ID, [
            'activity_id' => $data['activity_id'],
            'schedule_id' => null,
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
            'duration_minutes' => $data['duration_minutes'],
            'note' => 'Tạo từ chế độ tập trung',
        ]);
        $this->flash('success', __('flash.focus_saved'));

        return $this->redirect('/time-logs?date=' . urlencode(substr((string) $data['started_at'], 0, 10)));
    }

    private function focusDataFromRequest(): array
    {
        $duration = (int) ($_POST['duration_minutes'] ?? 25);
        $endedAt = $this->normalizeDateTime((string) ($_POST['ended_at'] ?? ''));
        $startedAt = $this->normalizeDateTime((string) ($_POST['started_at'] ?? ''));

        if ($endedAt !== null && $startedAt === null && in_array($duration, self::DURATIONS, true)) {
            $startedAt = (new DateTimeImmutable($endedAt))
                ->modify('-' . $duration . ' minutes')
                ->format('Y-m-d H:i:s');
        }

        return [
            'activity_id' => (int) ($_POST['activity_id'] ?? 0),
            'duration_minutes' => $duration,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['activity_id'] <= 0 || $this->activities->findByUser($data['activity_id'], self::DEMO_USER_ID) === null) {
            $errors['activity_id'] = __('validation.valid_activity');
        }

        if (!in_array((int) $data['duration_minutes'], self::DURATIONS, true)) {
            $errors['duration_minutes'] = __('validation.valid_focus_duration');
        }

        if ($data['started_at'] === null || $data['ended_at'] === null) {
            $errors['timer'] = __('validation.focus_timer_required');
        } elseif (strtotime($data['ended_at']) <= strtotime($data['started_at'])) {
            $errors['timer'] = __('validation.focus_timer_required');
        }

        return $errors;
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

    private function defaultFocus(): array
    {
        return [
            'activity_id' => 0,
            'duration_minutes' => 25,
            'started_at' => '',
            'ended_at' => '',
        ];
    }
}
