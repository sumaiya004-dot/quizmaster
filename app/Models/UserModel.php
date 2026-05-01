<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

class UserModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(string $name, string $email, string $hashedPassword): int
    {
        $this->db->execute(
            "INSERT INTO users (name, email, password, role, avatar, created_at) VALUES (:name, :email, :password, 'student', '🎓', NOW())",
            [':name' => $name, ':email' => $email, ':password' => $hashedPassword]
        );
        return $this->db->lastInsertId();
    }

    public function findByEmail(string $email): array|false
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = :email LIMIT 1", [':email' => $email]);
    }

    public function emailExists(string $email): bool
    {
        return (bool) $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE email = :email", [':email' => $email]);
    }

    public function emailExistsForOtherUser(string $email, int $userId): bool
    {
        return (bool) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE email = :email AND id != :id",
            [':email' => $email, ':id' => $userId]
        );
    }

    public function findById(int $id): array|false
    {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = :id LIMIT 1", [':id' => $id]);
    }

    public function updateProfile(int $id, string $name, string $email, string $avatar): void
    {
        $this->db->execute(
            "UPDATE users SET name = :name, email = :email, avatar = :avatar WHERE id = :id",
            [':name' => $name, ':email' => $email, ':avatar' => $avatar, ':id' => $id]
        );
    }

    public function updatePassword(int $id, string $hash): void
    {
        $this->db->execute(
            "UPDATE users SET password = :password WHERE id = :id",
            [':password' => $hash, ':id' => $id]
        );
    }

    public function deleteUser(int $id): void
    {
        $this->db->execute("DELETE FROM users WHERE id = :id", [':id' => $id]);
    }

    public function getNotes(int $userId): string
    {
        $row = $this->db->fetchOne("SELECT content FROM user_notes WHERE user_id = :uid", [':uid' => $userId]);
        return $row ? (string) $row['content'] : '';
    }

    public function saveNotes(int $userId, string $content): void
    {
        $this->db->execute(
            "INSERT INTO user_notes (user_id, content, updated_at) VALUES (:uid, :content, NOW())
             ON DUPLICATE KEY UPDATE content = :content2, updated_at = NOW()",
            [':uid' => $userId, ':content' => $content, ':content2' => $content]
        );
    }

    public function getLeaderboard(int $limit = 20): array
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.name, u.avatar,
                    COUNT(r.id) AS total_quizzes,
                    COALESCE(SUM(r.score), 0) AS total_score,
                    ROUND(AVG(r.score / NULLIF(r.total_marks,0)*100),1) AS avg_pct
             FROM users u
             LEFT JOIN results r ON r.user_id = u.id
             WHERE u.role = 'student'
             GROUP BY u.id
             ORDER BY total_score DESC, avg_pct DESC
             LIMIT " . (int) $limit
        );
    }
}

