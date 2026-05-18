<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\ScheduleRepository;
use DateTimeImmutable;
use Exception;

class ScheduleController extends Controller
{
    private const DEMO_USER_ID = 1;
    private const STATUSES = ['scheduled', 'completed', 'cancelled'];

    private ScheduleRepository $schedules;
    private ActivityRepository $activities;

    public function __construct()
    {
        $this->schedules = new ScheduleRepository();
        $this->activities = new ActivityRepository();
    }

    public function index(): string
    {
        return $this->view('schedules/index', [
            'title' => 'Schedules',
            'schedules' => $this->schedules->allByUser(self::DEMO_USER_ID),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function calendar(): string
    {
        return $this->view('schedules/calendar', [
            'title' => 'Schedule Calendar',
        ]);
    }

    public function api(): string
    {
        header('Content-Type: application/json; charset=utf-8');

        return json_encode(
            $this->schedules->calendarEventsByUser(self::DEMO_USER_ID),
            JSON_UNESCAPED_UNICODE
        ) ?: '[]';
    }

    public function create(): string
    {
        return $this->view('schedules/create', [
            'title' => 'Create Schedule',
            'schedule' => $this->defaultSchedule(),
            'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
            'statuses' => self::STATUSES,
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->scheduleDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('schedules/create', [
                'title' => 'Create Schedule',
                'schedule' => $data,
                'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
                'statuses' => self::STATUSES,
                'errors' => $errors,
            ]);
        }

        $this->schedules->create(self::DEMO_USER_ID, $data);
        $this->flash('success', 'Schedule created successfully.');

        return $this->redirect('/schedules');
    }

    public function edit(string $id): string
    {
        $schedule = $this->findScheduleOrFail((int) $id);

        return $this->view('schedules/edit', [
            'title' => 'Edit Schedule',
            'schedule' => $schedule,
            'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
            'statuses' => self::STATUSES,
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $schedule = $this->findScheduleOrFail((int) $id);
        $data = $this->scheduleDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('schedules/edit', [
                'title' => 'Edit Schedule',
                'schedule' => array_merge($schedule, $data),
                'activities' => $this->activities->allByUser(self::DEMO_USER_ID),
                'statuses' => self::STATUSES,
                'errors' => $errors,
            ]);
        }

        $this->schedules->update((int) $id, self::DEMO_USER_ID, $data);
        $this->flash('success', 'Schedule updated successfully.');

        return $this->redirect('/schedules');
    }

    public function delete(string $id): string
    {
        return $this->view('schedules/delete', [
            'title' => 'Delete Schedule',
            'schedule' => $this->findScheduleOrFail((int) $id),
        ]);
    }

    public function destroy(string $id): string
    {
        $this->findScheduleOrFail((int) $id);
        $this->schedules->delete((int) $id, self::DEMO_USER_ID);
        $this->flash('success', 'Schedule deleted successfully.');

        return $this->redirect('/schedules');
    }

    private function scheduleDataFromRequest(): array
    {
        return [
            'activity_id' => (int) ($_POST['activity_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'start_at' => $this->normalizeDateTime((string) ($_POST['start_at'] ?? '')),
            'end_at' => $this->normalizeDateTime((string) ($_POST['end_at'] ?? '')),
            'status' => (string) ($_POST['status'] ?? 'scheduled'),
            'notes' => trim((string) ($_POST['notes'] ?? '')),
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

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['activity_id'] <= 0 || $this->activities->findByUser($data['activity_id'], self::DEMO_USER_ID) === null) {
            $errors['activity_id'] = 'Choose a valid activity.';
        }

        if ($data['title'] === '') {
            $errors['title'] = 'Schedule title is required.';
        }

        if ($data['start_at'] === null) {
            $errors['start_at'] = 'Start time is required.';
        }

        if ($data['end_at'] === null) {
            $errors['end_at'] = 'End time is required.';
        }

        if ($data['start_at'] !== null && $data['end_at'] !== null && strtotime($data['end_at']) <= strtotime($data['start_at'])) {
            $errors['end_at'] = 'End time must be later than start time.';
        }

        if (!in_array($data['status'], self::STATUSES, true)) {
            $errors['status'] = 'Choose a valid status.';
        }

        return $errors;
    }

    private function defaultSchedule(): array
    {
        return [
            'activity_id' => 0,
            'title' => '',
            'start_at' => date('Y-m-d 08:00:00'),
            'end_at' => date('Y-m-d 09:00:00'),
            'status' => 'scheduled',
            'notes' => '',
        ];
    }

    private function findScheduleOrFail(int $id): array
    {
        $schedule = $this->schedules->findByUser($id, self::DEMO_USER_ID);

        if ($schedule !== null) {
            return $schedule;
        }

        http_response_code(404);
        exit('Schedule not found.');
    }
}
