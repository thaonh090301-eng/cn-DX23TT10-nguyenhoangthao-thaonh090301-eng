<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class TimetableRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function schedulesForDate(int $userId, string $date): array
    {
        $rangeStart = $date . ' 00:00:00';
        $rangeEnd = date('Y-m-d H:i:s', strtotime($rangeStart . ' +1 day'));

        $statement = $this->db->prepare(
            'SELECT
                    s.id,
                    s.activity_id,
                    s.title,
                    s.start_at,
                    s.end_at,
                    s.status,
                    s.notes,
                    a.title AS activity_title,
                    a.priority AS activity_priority,
                    c.name AS category_name,
                    c.color AS category_color,
                    tl.id AS time_log_id,
                    tl.started_at AS actual_started_at,
                    tl.ended_at AS actual_ended_at,
                    tl.duration_minutes AS actual_minutes
             FROM schedules s
             INNER JOIN activities a ON a.id = s.activity_id
             INNER JOIN categories c ON c.id = a.category_id
             LEFT JOIN time_logs tl
                ON tl.id = (
                    SELECT tl2.id
                    FROM time_logs tl2
                    WHERE tl2.schedule_id = s.id
                        AND tl2.user_id = :log_user_id
                    ORDER BY tl2.started_at DESC, tl2.id DESC
                    LIMIT 1
                )
             WHERE s.user_id = :schedule_user_id
                AND a.user_id = :activity_user_id
                AND c.user_id = :category_user_id
                AND s.start_at < :range_end
                AND s.end_at > :range_start
                AND s.status <> :cancelled_status
             ORDER BY s.start_at ASC, s.end_at ASC'
        );
        $statement->execute([
            'log_user_id' => $userId,
            'schedule_user_id' => $userId,
            'activity_user_id' => $userId,
            'category_user_id' => $userId,
            'range_start' => $rangeStart,
            'range_end' => $rangeEnd,
            'cancelled_status' => 'cancelled',
        ]);

        return $statement->fetchAll();
    }
}
