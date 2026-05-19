<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, name, email, password_hash, role
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, name, email, role
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function accountStats(int $userId): array
    {
        return [
            'activities_count' => $this->countTableRows('activities', $userId),
            'schedules_count' => $this->countTableRows('schedules', $userId),
            'time_logs_count' => $this->countTableRows('time_logs', $userId),
        ];
    }

    private function countTableRows(string $table, int $userId): int
    {
        $allowedTables = ['activities', 'schedules', 'time_logs'];

        if (!in_array($table, $allowedTables, true)) {
            return 0;
        }

        $statement = $this->db->prepare(
            'SELECT COUNT(*) FROM ' . $table . ' WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return (int) $statement->fetchColumn();
    }
}
