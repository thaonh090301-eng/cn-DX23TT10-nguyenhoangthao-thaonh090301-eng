<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class TimeLogRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function allByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT tl.*,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.user_id = :user_id
             ORDER BY tl.started_at DESC, tl.ended_at DESC'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function dailyReportByUser(int $userId, string $date): array
    {
        $startAt = $date . ' 00:00:00';
        $endAt = date('Y-m-d H:i:s', strtotime($startAt . ' +1 day'));
        $scheduledRows = $this->scheduledReportRows($userId, $startAt, $endAt);
        $unscheduledRows = $this->unscheduledReportRows($userId, $startAt, $endAt);
        $rows = array_merge($scheduledRows, $unscheduledRows);

        usort($rows, static function (array $a, array $b): int {
            $aStart = $a['planned_start_at'] ?? $a['actual_started_at'] ?? '';
            $bStart = $b['planned_start_at'] ?? $b['actual_started_at'] ?? '';

            return strcmp((string) $aStart, (string) $bStart);
        });

        return $rows;
    }

    public function findByUser(int $id, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT tl.*,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.id = :id AND tl.user_id = :user_id
             LIMIT 1'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $timeLog = $statement->fetch();

        return $timeLog ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO time_logs
                (user_id, activity_id, schedule_id, started_at, ended_at, duration_minutes, note)
             VALUES
                (:user_id, :activity_id, :schedule_id, :started_at, :ended_at, :duration_minutes, :note)'
        );
        $statement->execute([
            'user_id' => $userId,
            'activity_id' => $data['activity_id'],
            'schedule_id' => $data['schedule_id'] ?? null,
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
            'duration_minutes' => $data['duration_minutes'],
            'note' => $data['note'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findBySchedule(int $scheduleId, int $userId): ?array
    {
        $statement = $this->db->prepare(
            'SELECT *
             FROM time_logs
             WHERE schedule_id = :schedule_id AND user_id = :user_id
             ORDER BY id DESC
             LIMIT 1'
        );
        $statement->execute([
            'schedule_id' => $scheduleId,
            'user_id' => $userId,
        ]);

        $timeLog = $statement->fetch();

        return $timeLog ?: null;
    }

    public function createFromSchedule(int $userId, array $schedule): int
    {
        $durationMinutes = max(0, (int) floor((strtotime($schedule['end_at']) - strtotime($schedule['start_at'])) / 60));

        return $this->create($userId, [
            'activity_id' => (int) $schedule['activity_id'],
            'schedule_id' => (int) $schedule['id'],
            'started_at' => $schedule['start_at'],
            'ended_at' => $schedule['end_at'],
            'duration_minutes' => $durationMinutes,
            'note' => \__('time_report.auto_note'),
        ]);
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $statement = $this->db->prepare(
            'UPDATE time_logs
             SET activity_id = :activity_id,
                 started_at = :started_at,
                 ended_at = :ended_at,
                 duration_minutes = :duration_minutes,
                 note = :note
             WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
            'activity_id' => $data['activity_id'],
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
            'duration_minutes' => $data['duration_minutes'],
            'note' => $data['note'],
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $statement = $this->db->prepare(
            'DELETE FROM time_logs WHERE id = :id AND user_id = :user_id'
        );
        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() > 0;
    }

    private function scheduledReportRows(int $userId, string $startAt, string $endAt): array
    {
        $statement = $this->db->prepare(
            'SELECT
                    s.id AS schedule_id,
                    s.activity_id,
                    s.title AS schedule_title,
                    s.start_at AS planned_start_at,
                    s.end_at AS planned_end_at,
                    TIMESTAMPDIFF(MINUTE, s.start_at, s.end_at) AS planned_minutes,
                    s.status AS schedule_status,
                    s.notes AS schedule_notes,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color,
                    tl.id AS time_log_id,
                    tl.started_at AS actual_started_at,
                    tl.ended_at AS actual_ended_at,
                    tl.duration_minutes AS actual_minutes,
                    tl.note AS actual_note,
                    :row_type AS row_type
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             LEFT JOIN time_logs tl
                ON tl.id = (
                    SELECT tl2.id
                    FROM time_logs tl2
                    WHERE tl2.schedule_id = s.id
                        AND tl2.user_id = :subquery_user_id
                    ORDER BY tl2.started_at DESC, tl2.id DESC
                    LIMIT 1
                )
             WHERE s.user_id = :schedule_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND s.start_at >= :start_at
                AND s.start_at < :end_at
                AND s.status <> :cancelled_status
             ORDER BY s.start_at ASC, s.end_at ASC'
        );
        $statement->execute([
            'row_type' => 'scheduled',
            'subquery_user_id' => $userId,
            'schedule_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'cancelled_status' => 'cancelled',
        ]);

        return $statement->fetchAll();
    }

    private function unscheduledReportRows(int $userId, string $startAt, string $endAt): array
    {
        $statement = $this->db->prepare(
            'SELECT
                    NULL AS schedule_id,
                    tl.activity_id,
                    NULL AS schedule_title,
                    NULL AS planned_start_at,
                    NULL AS planned_end_at,
                    NULL AS planned_minutes,
                    NULL AS schedule_status,
                    NULL AS schedule_notes,
                    a.title AS activity_title,
                    c.name AS category_name,
                    c.color AS category_color,
                    tl.id AS time_log_id,
                    tl.started_at AS actual_started_at,
                    tl.ended_at AS actual_ended_at,
                    tl.duration_minutes AS actual_minutes,
                    tl.note AS actual_note,
                    :row_type AS row_type
             FROM time_logs tl
             INNER JOIN activities a ON a.id = tl.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             WHERE tl.user_id = :log_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND tl.schedule_id IS NULL
                AND tl.started_at >= :start_at
                AND tl.started_at < :end_at
             ORDER BY tl.started_at ASC, tl.ended_at ASC'
        );
        $statement->execute([
            'row_type' => 'unscheduled',
            'log_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return $statement->fetchAll();
    }
}
