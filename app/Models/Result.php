<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

final class Result
{
    public function topForQuiz(int $quizId, int $limit = 10): array
    {
        $sql = 'SELECT
                    r.score,
                    r.total_marks,
                    r.time_taken,
                    r.submitted_at,
                    u.name AS user_name
                FROM results r
                INNER JOIN users u ON u.id = r.user_id
                WHERE r.quiz_id = :quiz_id
                ORDER BY r.score DESC, r.time_taken ASC, r.submitted_at DESC
                LIMIT ' . (int) $limit;

        $stmt = Database::getInstance()->pdo()->prepare($sql);
        $stmt->execute(['quiz_id' => $quizId]);
        return $stmt->fetchAll();
    }
}

