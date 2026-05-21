<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ActivityRepository;
use App\Repositories\CategoryRepository;

class ActivityController extends Controller
{
    private const DEMO_USER_ID = 1;
    private const PRIORITIES = ['low', 'medium', 'high'];

    private ActivityRepository $activities;
    private CategoryRepository $categories;

    public function __construct()
    {
        $this->activities = new ActivityRepository();
        $this->categories = new CategoryRepository();
    }

    public function index(): string
    {
        $statusFilter = $this->statusFilter();
        $activities = $this->activities->allByUser($this->authUserId());
        $hasActivities = $activities !== [];

        if ($statusFilter !== 'all') {
            $activities = array_values(array_filter($activities, static function (array $activity) use ($statusFilter): bool {
                $isActive = (int) $activity['is_active'] === 1;

                return $statusFilter === 'active' ? $isActive : !$isActive;
            }));
        }

        return $this->view('activities/index', [
            'title' => 'Activities',
            'activities' => $activities,
            'hasActivities' => $hasActivities,
            'selectedStatus' => $statusFilter,
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function create(): string
    {
        return $this->view('activities/create', [
            'title' => 'Create Activity',
            'activity' => $this->defaultActivity(),
            'categories' => $this->categories->allByUser($this->authUserId()),
            'priorities' => self::PRIORITIES,
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->activityDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('activities/create', [
                'title' => 'Create Activity',
                'activity' => $data,
                'categories' => $this->categories->allByUser($this->authUserId()),
                'priorities' => self::PRIORITIES,
                'errors' => $errors,
            ]);
        }

        $this->activities->create($this->authUserId(), $data);
        $this->flash('success', \__('flash.activity_created'));

        return $this->redirect('/activities');
    }

    public function edit(string $id): string
    {
        $activity = $this->findActivityOrFail((int) $id);

        return $this->view('activities/edit', [
            'title' => 'Edit Activity',
            'activity' => $activity,
            'categories' => $this->categories->allByUser($this->authUserId()),
            'priorities' => self::PRIORITIES,
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $activity = $this->findActivityOrFail((int) $id);
        $data = $this->activityDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('activities/edit', [
                'title' => 'Edit Activity',
                'activity' => array_merge($activity, $data),
                'categories' => $this->categories->allByUser($this->authUserId()),
                'priorities' => self::PRIORITIES,
                'errors' => $errors,
            ]);
        }

        $this->activities->update((int) $id, $this->authUserId(), $data);
        $this->flash('success', \__('flash.activity_updated'));

        return $this->redirect('/activities');
    }

    public function delete(string $id): string
    {
        return $this->view('activities/delete', [
            'title' => 'Delete Activity',
            'activity' => $this->findActivityOrFail((int) $id),
            'errors' => [],
        ]);
    }

    public function destroy(string $id): string
    {
        $activity = $this->findActivityOrFail((int) $id);

        if ((int) $activity['schedules_count'] > 0) {
            http_response_code(422);

            return $this->view('activities/delete', [
                'title' => 'Delete Activity',
                'activity' => $activity,
                'errors' => ['activity' => \__('validation.activity_delete_schedules')],
            ]);
        }

        if ((int) $activity['time_logs_count'] > 0) {
            http_response_code(422);

            return $this->view('activities/delete', [
                'title' => 'Delete Activity',
                'activity' => $activity,
                'errors' => ['activity' => \__('validation.activity_delete_time_logs')],
            ]);
        }

        $this->activities->delete((int) $id, $this->authUserId());
        $this->flash('success', \__('flash.activity_deleted'));

        return $this->redirect('/activities');
    }

    private function activityDataFromRequest(): array
    {
        return [
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'priority' => (string) ($_POST['priority'] ?? 'medium'),
            'estimated_minutes' => (int) ($_POST['estimated_minutes'] ?? 30),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors['title'] = \__('validation.activity_title_required');
        }

        if ($data['category_id'] <= 0 || $this->categories->findByUser($data['category_id'], $this->authUserId()) === null) {
            $errors['category_id'] = \__('validation.valid_category');
        }

        if (!in_array($data['priority'], self::PRIORITIES, true)) {
            $errors['priority'] = \__('validation.valid_priority');
        }

        if ($data['estimated_minutes'] <= 0) {
            $errors['estimated_minutes'] = \__('validation.estimated_minutes_positive');
        }

        return $errors;
    }

    private function defaultActivity(): array
    {
        return [
            'category_id' => 0,
            'title' => '',
            'description' => '',
            'priority' => 'medium',
            'estimated_minutes' => 30,
            'is_active' => 1,
        ];
    }

    private function statusFilter(): string
    {
        $status = (string) ($_GET['status'] ?? 'all');

        return in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all';
    }

    private function findActivityOrFail(int $id): array
    {
        $activity = $this->activities->findByUser($id, $this->authUserId());

        if ($activity !== null) {
            return $activity;
        }

        http_response_code(404);
        exit(\__('not_found.activity'));
    }
}
