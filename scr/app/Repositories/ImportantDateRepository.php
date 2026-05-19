<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use DateTimeImmutable;
use PDO;

class ImportantDateRepository
{
    public const TYPES = ['holiday', 'travel', 'date', 'anniversary', 'deadline', 'birthday', 'exam', 'other'];

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
        $this->ensureTable();
    }

    public function allByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM important_dates
             WHERE user_id = :user_id
             ORDER BY event_date ASC, title ASC'
        );
        $statement->execute(['user_id' => $userId]);

        return $this->sortByCountdown($this->enrichMany($statement->fetchAll()));
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM important_dates
             WHERE id = :id AND user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $importantDate = $statement->fetch();

        return $importantDate ? $this->enrich($importantDate) : null;
    }

    public function upcomingWithinDays(int $userId, int $days = 7, int $limit = 6): array
    {
        $upcoming = array_filter(
            $this->allByUser($userId),
            static fn (array $importantDate): bool => (int) $importantDate['countdown_days'] >= 0
                && (int) $importantDate['countdown_days'] <= $days
        );

        return array_slice(array_values($upcoming), 0, max(1, $limit));
    }

    public function calendarEventsByUser(int $userId): array
    {
        return array_map(static function (array $importantDate): array {
            return [
                'id' => 'important-date-' . $importantDate['id'],
                'title' => '★ ' . $importantDate['title'],
                'start' => $importantDate['next_event_date'],
                'allDay' => true,
                'backgroundColor' => '#b45309',
                'borderColor' => '#92400e',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'kind' => 'important_date',
                    'type' => \__('important_date.type.' . $importantDate['type']),
                    'countdown' => \__('important_date.countdown_days', [
                        'days' => (int) $importantDate['countdown_days'],
                    ]),
                    'note' => $importantDate['note'] ?? '',
                ],
            ];
        }, $this->allByUser($userId));
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO important_dates
                (user_id, title, event_date, `type`, note, remind_before_days, repeat_yearly)
             VALUES
                (:user_id, :title, :event_date, :type, :note, :remind_before_days, :repeat_yearly)'
        );
        $statement->execute([
            'user_id' => $userId,
            'title' => $data['title'],
            'event_date' => $data['event_date'],
            'type' => $data['type'],
            'note' => $data['note'],
            'remind_before_days' => $data['remind_before_days'],
            'repeat_yearly' => $data['repeat_yearly'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE important_dates
             SET title = :title,
                 event_date = :event_date,
                 `type` = :type,
                 note = :note,
                 remind_before_days = :remind_before_days,
                 repeat_yearly = :repeat_yearly
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'title' => $data['title'],
            'event_date' => $data['event_date'],
            'type' => $data['type'],
            'note' => $data['note'],
            'remind_before_days' => $data['remind_before_days'],
            'repeat_yearly' => $data['repeat_yearly'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM important_dates WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    private function enrichMany(array $importantDates): array
    {
        return array_map(fn (array $importantDate): array => $this->enrich($importantDate), $importantDates);
    }

    private function enrich(array $importantDate): array
    {
        $today = new DateTimeImmutable('today');
        $eventDate = new DateTimeImmutable((string) $importantDate['event_date']);
        $nextEventDate = $this->nextEventDate($eventDate, (int) $importantDate['repeat_yearly'] === 1, $today);
        $countdownDays = (int) $today->diff($nextEventDate)->format('%r%a');
        $remindBeforeDays = max(0, (int) ($importantDate['remind_before_days'] ?? 0));
        $isReminderDue = $countdownDays >= 0 && $countdownDays <= $remindBeforeDays;

        $importantDate['next_event_date'] = $nextEventDate->format('Y-m-d');
        $importantDate['countdown_days'] = $countdownDays;
        $importantDate['reminder_status_key'] = $isReminderDue
            ? 'important_date.reminder_status.due'
            : 'important_date.reminder_status.waiting';

        if ($countdownDays < 0) {
            $importantDate['reminder_status_key'] = 'important_date.reminder_status.past';
        }

        return $importantDate;
    }

    private function nextEventDate(DateTimeImmutable $eventDate, bool $repeatYearly, DateTimeImmutable $today): DateTimeImmutable
    {
        if (!$repeatYearly) {
            return $eventDate;
        }

        $candidate = $eventDate->setDate(
            (int) $today->format('Y'),
            (int) $eventDate->format('m'),
            (int) $eventDate->format('d')
        );

        if ($candidate < $today) {
            $candidate = $candidate->modify('+1 year');
        }

        return $candidate;
    }

    private function sortByCountdown(array $importantDates): array
    {
        usort($importantDates, static function (array $a, array $b): int {
            $aCountdown = (int) $a['countdown_days'];
            $bCountdown = (int) $b['countdown_days'];
            $aSort = $aCountdown < 0 ? 1000000 + abs($aCountdown) : $aCountdown;
            $bSort = $bCountdown < 0 ? 1000000 + abs($bCountdown) : $bCountdown;

            if ($aSort !== $bSort) {
                return $aSort <=> $bSort;
            }

            if ((int) $a['countdown_days'] === (int) $b['countdown_days']) {
                return strcmp((string) $a['title'], (string) $b['title']);
            }

            return $aCountdown <=> $bCountdown;
        });

        return $importantDates;
    }

    private function ensureTable(): void
    {
        $statement = $this->db->prepare(
            "CREATE TABLE IF NOT EXISTS important_dates (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                title VARCHAR(180) NOT NULL,
                event_date DATE NOT NULL,
                `type` ENUM('holiday', 'travel', 'date', 'anniversary', 'deadline', 'birthday', 'exam', 'other') NOT NULL DEFAULT 'other',
                note TEXT NULL,
                remind_before_days INT UNSIGNED NOT NULL DEFAULT 7,
                repeat_yearly TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_important_dates_user_date (user_id, event_date),
                KEY idx_important_dates_user_type (user_id, `type`),
                CONSTRAINT fk_important_dates_user
                    FOREIGN KEY (user_id) REFERENCES users(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $statement->execute();
    }
}
