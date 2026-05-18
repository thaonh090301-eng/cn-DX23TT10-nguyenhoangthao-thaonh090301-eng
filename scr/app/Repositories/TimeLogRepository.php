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
                (user_id, activity_id, started_at, ended_at, duration_minutes, note)
             VALUES
                (:user_id, :activity_id, :started_at, :ended_at, :duration_minutes, :note)'
        );
        $statement->execute([
            'user_id' => $userId,
            'activity_id' => $data['activity_id'],
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
            'duration_minutes' => $data['duration_minutes'],
            'note' => $data['note'],
        ]);

        return (int) $this->db->lastInsertId();
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
}
