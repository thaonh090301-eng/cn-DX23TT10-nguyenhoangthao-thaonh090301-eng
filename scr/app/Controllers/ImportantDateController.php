<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ImportantDateRepository;
use DateTimeImmutable;

class ImportantDateController extends Controller
{
    private const DEMO_USER_ID = 1;

    private ImportantDateRepository $importantDates;

    public function __construct()
    {
        $this->importantDates = new ImportantDateRepository();
    }

    public function index(): string
    {
        return $this->view('important_dates/index', [
            'title' => __('nav.important_dates'),
            'importantDates' => $this->importantDates->allByUser(self::DEMO_USER_ID),
            'flash' => $this->consumeFlash(),
        ]);
    }

    public function create(): string
    {
        return $this->view('important_dates/create', [
            'title' => __('page.create_important_date'),
            'importantDate' => $this->defaultImportantDate(),
            'types' => ImportantDateRepository::TYPES,
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $this->importantDateDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('important_dates/create', [
                'title' => __('page.create_important_date'),
                'importantDate' => $data,
                'types' => ImportantDateRepository::TYPES,
                'errors' => $errors,
            ]);
        }

        $this->importantDates->create(self::DEMO_USER_ID, $data);
        $this->flash('success', __('flash.important_date_created'));

        return $this->redirect('/important-dates');
    }

    public function edit(string $id): string
    {
        return $this->view('important_dates/edit', [
            'title' => __('page.edit_important_date'),
            'importantDate' => $this->findImportantDateOrFail((int) $id),
            'types' => ImportantDateRepository::TYPES,
            'errors' => [],
        ]);
    }

    public function update(string $id): string
    {
        $importantDate = $this->findImportantDateOrFail((int) $id);
        $data = $this->importantDateDataFromRequest();
        $errors = $this->validate($data);

        if ($errors !== []) {
            http_response_code(422);

            return $this->view('important_dates/edit', [
                'title' => __('page.edit_important_date'),
                'importantDate' => array_merge($importantDate, $data),
                'types' => ImportantDateRepository::TYPES,
                'errors' => $errors,
            ]);
        }

        $this->importantDates->update((int) $id, self::DEMO_USER_ID, $data);
        $this->flash('success', __('flash.important_date_updated'));

        return $this->redirect('/important-dates');
    }

    public function delete(string $id): string
    {
        return $this->view('important_dates/delete', [
            'title' => __('page.delete_important_date'),
            'importantDate' => $this->findImportantDateOrFail((int) $id),
        ]);
    }

    public function destroy(string $id): string
    {
        $this->findImportantDateOrFail((int) $id);
        $this->importantDates->delete((int) $id, self::DEMO_USER_ID);
        $this->flash('success', __('flash.important_date_deleted'));

        return $this->redirect('/important-dates');
    }

    private function importantDateDataFromRequest(): array
    {
        $type = trim((string) ($_POST['type'] ?? 'other'));
        $remindBeforeDays = (int) ($_POST['remind_before_days'] ?? 7);

        return [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'event_date' => $this->normalizeDate((string) ($_POST['event_date'] ?? '')),
            'type' => in_array($type, ImportantDateRepository::TYPES, true) ? $type : 'other',
            'note' => trim((string) ($_POST['note'] ?? '')),
            'remind_before_days' => max(0, $remindBeforeDays),
            'repeat_yearly' => isset($_POST['repeat_yearly']) ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors['title'] = __('validation.important_date_title_required');
        }

        if ($data['event_date'] === null) {
            $errors['event_date'] = __('validation.valid_important_date');
        }

        if (!in_array($data['type'], ImportantDateRepository::TYPES, true)) {
            $errors['type'] = __('validation.valid_important_date_type');
        }

        if ($data['remind_before_days'] < 0 || $data['remind_before_days'] > 3650) {
            $errors['remind_before_days'] = __('validation.valid_remind_before_days');
        }

        return $errors;
    }

    private function normalizeDate(string $value): ?string
    {
        $value = trim($value);
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $date->format('Y-m-d');
    }

    private function defaultImportantDate(): array
    {
        return [
            'title' => '',
            'event_date' => date('Y-m-d'),
            'type' => 'other',
            'note' => '',
            'remind_before_days' => 7,
            'repeat_yearly' => 0,
        ];
    }

    private function findImportantDateOrFail(int $id): array
    {
        $importantDate = $this->importantDates->findByUser($id, self::DEMO_USER_ID);

        if ($importantDate !== null) {
            return $importantDate;
        }

        http_response_code(404);
        exit(__('not_found.important_date'));
    }
}
