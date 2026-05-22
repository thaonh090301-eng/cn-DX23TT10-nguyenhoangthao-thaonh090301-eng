<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ReminderRepository
{
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
             FROM reminders
             WHERE user_id = :user_id
             ORDER BY is_active DESC, remind_time ASC, title ASC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM reminders
             WHERE id = :id AND user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $reminder = $statement->fetch();

        return $reminder ?: null;
    }

    public function activeForDate(int $userId, string $date): array
    {
        $dayOfWeek = (int) date('w', strtotime($date));
        $statement = $this->db->prepare(
            'SELECT *
             FROM reminders
             WHERE user_id = :user_id
                AND is_active = 1
                AND (
                    repeat_type = :daily_repeat
                    OR (repeat_type = :none_repeat AND DATE(created_at) = :selected_date)
                    OR (repeat_type = :weekly_repeat AND day_of_week = :day_of_week)
                    OR repeat_type = :interval_repeat
                )
             ORDER BY remind_time ASC, title ASC'
        );
        $statement->execute([
            'user_id' => $userId,
            'daily_repeat' => 'daily',
            'none_repeat' => 'none',
            'weekly_repeat' => 'weekly',
            'interval_repeat' => 'interval',
            'selected_date' => $date,
            'day_of_week' => $dayOfWeek,
        ]);

        return $this->expandOccurrences($statement->fetchAll(), $date);
    }

    public function upcomingToday(int $userId, int $limit = 5): array
    {
        $currentTime = date('H:i:s');
        $reminders = array_values(array_filter($this->activeForDate($userId, date('Y-m-d')), static function (array $reminder) use ($currentTime): bool {
            return (string) $reminder['remind_time'] >= $currentTime;
        }));

        return array_slice($reminders, 0, max(1, $limit));
    }

    public function missedToday(int $userId, int $limit = 3): array
    {
        $currentTime = date('H:i:s');
        $reminders = array_values(array_filter($this->activeForDate($userId, date('Y-m-d')), static function (array $reminder) use ($currentTime): bool {
            return (string) $reminder['remind_time'] < $currentTime;
        }));
        usort($reminders, static function (array $a, array $b): int {
            return strcmp((string) $b['remind_time'], (string) $a['remind_time']);
        });

        return array_slice($reminders, 0, max(1, $limit));
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO reminders
                (user_id, title, note, remind_time, repeat_type, day_of_week, interval_minutes, is_active)
             VALUES
                (:user_id, :title, :note, :remind_time, :repeat_type, :day_of_week, :interval_minutes, :is_active)'
        );
        $statement->execute([
            'user_id' => $userId,
            'title' => $data['title'],
            'note' => $data['note'],
            'remind_time' => $data['remind_time'],
            'repeat_type' => $data['repeat_type'],
            'day_of_week' => $data['day_of_week'],
            'interval_minutes' => $data['interval_minutes'],
            'is_active' => $data['is_active'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE reminders
             SET title = :title,
                 note = :note,
                 remind_time = :remind_time,
                 repeat_type = :repeat_type,
                 day_of_week = :day_of_week,
                 interval_minutes = :interval_minutes,
                 is_active = :is_active
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'title' => $data['title'],
            'note' => $data['note'],
            'remind_time' => $data['remind_time'],
            'repeat_type' => $data['repeat_type'],
            'day_of_week' => $data['day_of_week'],
            'interval_minutes' => $data['interval_minutes'],
            'is_active' => $data['is_active'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function setActive(int $id, int $userId, bool $isActive): bool
    {
        $statement = $this->db->prepare(
            'UPDATE reminders
             SET is_active = :is_active
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'is_active' => $isActive ? 1 : 0,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM reminders WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    private function ensureTable(): void
    {
        $statement = $this->db->prepare(
            "CREATE TABLE IF NOT EXISTS reminders (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                title VARCHAR(150) NOT NULL,
                note TEXT NULL,
                remind_time TIME NOT NULL,
                repeat_type ENUM('none', 'daily', 'weekly', 'interval') NOT NULL DEFAULT 'none',
                day_of_week TINYINT UNSIGNED NULL,
                interval_minutes INT UNSIGNED NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_reminders_user_time (user_id, remind_time),
                KEY idx_reminders_active (user_id, is_active),
                CONSTRAINT fk_reminders_user
                    FOREIGN KEY (user_id) REFERENCES users(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $statement->execute();
        $this->ensureIntervalSupport();
    }

    private function expandOccurrences(array $reminders, string $date): array
    {
        $occurrences = [];

        foreach ($reminders as $reminder) {
            if (($reminder['repeat_type'] ?? '') !== 'interval') {
                $reminder['occurrence_id'] = (string) $reminder['id'];
                $occurrences[] = $reminder;
                continue;
            }

            $intervalMinutes = (int) ($reminder['interval_minutes'] ?? 0);

            if ($intervalMinutes <= 0) {
                continue;
            }

            $startTimestamp = strtotime($date . ' ' . (string) $reminder['remind_time']);
            $endTimestamp = strtotime($date . ' 23:59:59');

            if ($startTimestamp === false || $endTimestamp === false) {
                continue;
            }

            for ($timestamp = $startTimestamp; $timestamp <= $endTimestamp; $timestamp += $intervalMinutes * 60) {
                $occurrence = $reminder;
                $occurrence['remind_time'] = date('H:i:s', $timestamp);
                $occurrence['occurrence_id'] = $reminder['id'] . '-' . date('Hi', $timestamp);
                $occurrences[] = $occurrence;
            }
        }

        usort($occurrences, static function (array $a, array $b): int {
            $timeCompare = strcmp((string) $a['remind_time'], (string) $b['remind_time']);

            return $timeCompare !== 0 ? $timeCompare : strcmp((string) $a['title'], (string) $b['title']);
        });

        return $occurrences;
    }

    private function ensureIntervalSupport(): void
    {
        $columns = $this->db->query('SHOW COLUMNS FROM reminders')->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_map(static fn (array $column): string => (string) $column['Field'], $columns);

        if (!in_array('interval_minutes', $columnNames, true)) {
            $this->db->exec('ALTER TABLE reminders ADD interval_minutes INT UNSIGNED NULL AFTER day_of_week');
        }

        $repeatTypeColumn = null;

        foreach ($columns as $column) {
            if (($column['Field'] ?? '') === 'repeat_type') {
                $repeatTypeColumn = (string) $column['Type'];
                break;
            }
        }

        if ($repeatTypeColumn !== null && !str_contains($repeatTypeColumn, "'interval'")) {
            $this->db->exec("ALTER TABLE reminders MODIFY repeat_type ENUM('none', 'daily', 'weekly', 'interval') NOT NULL DEFAULT 'none'");
        }
    }
}
