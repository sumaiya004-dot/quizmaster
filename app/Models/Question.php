<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

final class Question
{
    public function getSubjects(): array
    {
        $sql = 'SELECT id, name FROM subjects ORDER BY name ASC';
        $stmt = Database::getInstance()->pdo()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFilteredQuestions(
        string $subjectName,
        string $difficulty,
        string $questionType
    ): array {
        $normalizedDifficulty = $this->normalizeDifficulty($difficulty);

        $sql = 'SELECT
                    q.id,
                    q.question_text,
                    q.option_a,
                    q.option_b,
                    q.option_c,
                    q.option_d,
                    q.correct_option,
                    q.marks,
                    q.question_type,
                    q.explanation,
                    s.name AS subject_name,
                    z.difficulty
                FROM questions q
                INNER JOIN quizzes z ON z.id = q.quiz_id
                INNER JOIN subjects s ON s.id = z.subject_id
                WHERE s.name = :subject
                  AND z.difficulty = :difficulty
                  AND q.question_type = :question_type
                ORDER BY q.id ASC';

        $stmt = Database::getInstance()->pdo()->prepare($sql);
        $stmt->execute([
            'subject' => $subjectName,
            'difficulty' => $normalizedDifficulty,
            'question_type' => $questionType,
        ]);

        return $stmt->fetchAll();
    }

    public function countFilteredQuestions(
        string $subjectName,
        string $difficulty,
        string $questionType
    ): int {
        $normalizedDifficulty = $this->normalizeDifficulty($difficulty);

        $sql = 'SELECT COUNT(*) AS total
                FROM questions q
                INNER JOIN quizzes z ON z.id = q.quiz_id
                INNER JOIN subjects s ON s.id = z.subject_id
                WHERE s.name = :subject
                  AND z.difficulty = :difficulty
                  AND q.question_type = :question_type';

        $stmt = Database::getInstance()->pdo()->prepare($sql);
        $stmt->execute([
            'subject' => $subjectName,
            'difficulty' => $normalizedDifficulty,
            'question_type' => $questionType,
        ]);

        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function getQuizMeta(string $subjectName, string $difficulty): ?array
    {
        $normalizedDifficulty = $this->normalizeDifficulty($difficulty);

        $sql = 'SELECT z.id, z.time_limit, z.title
                FROM quizzes z
                INNER JOIN subjects s ON s.id = z.subject_id
                WHERE s.name = :subject
                  AND z.difficulty = :difficulty
                  AND z.is_active = 1
                ORDER BY z.id ASC
                LIMIT 1';

        $stmt = Database::getInstance()->pdo()->prepare($sql);
        $stmt->execute([
            'subject' => $subjectName,
            'difficulty' => $normalizedDifficulty,
        ]);

        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    private function normalizeDifficulty(string $difficulty): string
    {
        return match (strtolower($difficulty)) {
            'easy' => 'low',
            'medium' => 'medium',
            'hard' => 'high',
            default => $difficulty,
        };
    }
}
