<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

class QuizModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ================= SUBJECT =================
    public function getAllSubjects(): array
    {
        return $this->db->fetchAll("SELECT * FROM subjects ORDER BY name");
    }

    // ================= USER RESULTS =================
    public function getUserResults(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT r.*, q.title, q.difficulty, s.name AS subject_name, s.icon
             FROM results r
             JOIN quizzes q ON r.quiz_id = q.id
             JOIN subjects s ON q.subject_id = s.id
             WHERE r.user_id = :uid
             ORDER BY r.submitted_at DESC",
            [':uid' => $userId]
        );
    }

    // ================= GET QUIZZES =================
    public function getQuizzes(?int $subjectId = null, ?string $difficulty = null, string $type = 'all'): array
    {
        $sql = "SELECT q.*, s.name AS subject_name, s.icon,
                       (SELECT COUNT(*) FROM questions 
                        WHERE quiz_id = q.id 
                        AND (:type1 = 'all' OR question_type = :type2)) AS question_count
                FROM quizzes q
                JOIN subjects s ON q.subject_id = s.id
                WHERE q.is_active = 1";

        $params = [
            ':type1' => $type,
            ':type2' => $type
        ];

        if ($subjectId !== null) {
            $sql .= " AND q.subject_id = :sid";
            $params[':sid'] = $subjectId;
        }

        if (!empty($difficulty)) {
            $sql .= " AND q.difficulty = :diff";
            $params[':diff'] = $difficulty;
        }

        // only show quizzes that have questions
        $sql .= " AND (SELECT COUNT(*) FROM questions 
                       WHERE quiz_id = q.id 
                       AND (:type3 = 'all' OR question_type = :type4)) > 0";

        $params[':type3'] = $type;
        $params[':type4'] = $type;

        return $this->db->fetchAll($sql, $params);
    }

    // ================= GET SINGLE QUIZ =================
    public function getQuizById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM quizzes WHERE id = :id AND is_active = 1 LIMIT 1",
            [':id' => $id]
        ) ?: null;
    }

    // ================= GET QUESTIONS =================
    public function getQuestions(int $quizId, string $type = 'all'): array
    {
        $sql = "SELECT DISTINCT q.* FROM questions q WHERE q.quiz_id = :qid";
        $params = [':qid' => $quizId];

        if ($type !== 'all') {
            $sql .= " AND q.question_type = :type";
            $params[':type'] = $type;
        }

        $sql .= " ORDER BY q.id";

        $rows = $this->db->fetchAll($sql, $params);

        // Deduplicate by question id in case of any join-based duplicates
        $seen = [];
        $unique = [];
        foreach ($rows as $row) {
            if (!isset($seen[$row['id']])) {
                $seen[$row['id']] = true;
                $unique[] = $row;
            }
        }

        return $unique;
    }

    // ================= SUBMIT RESULT =================
    public function submitResult(int $userId, int $quizId, array $answers, string $type = 'all'): array
    {
        // getQuestions() already deduplicates, so no double-scoring
        $questions = $this->getQuestions($quizId, $type);
        $score = 0;
        $total = 0;
        $breakdown = [];

        foreach ($questions as $q) {
            $selected = $answers[$q['id']] ?? null;
            $isShort = $q['question_type'] === 'short';

            $isCorrect = $isShort
                ? strtolower(trim((string)$selected)) === strtolower(trim((string)$q['option_a']))
                : $selected === $q['correct_option'];

            if ($isCorrect) {
                $score += (int)$q['marks'];
            }

            $total += (int)$q['marks'];

            $breakdown[] = [
                'id' => $q['id'],
                'question' => $q['question_text'],
                'selected' => $selected,
                'correct' => $q['correct_option'],
                'is_correct' => $isCorrect,
                'marks' => $q['marks']
            ];
        }

        $timeTaken = $_SESSION['quiz_start_' . $quizId] ?? time();
        $timeTaken = time() - $timeTaken;

        $this->db->execute(
            "INSERT INTO results (user_id, quiz_id, score, total_marks, time_taken, answers, submitted_at)
             VALUES (:uid,:qid,:sc,:tm,:tt,:ans,NOW())",
            [
                ':uid' => $userId,
                ':qid' => $quizId,
                ':sc' => $score,
                ':tm' => $total,
                ':tt' => $timeTaken,
                ':ans' => json_encode($breakdown),
            ]
        );

        unset($_SESSION['quiz_start_' . $quizId]);

        return [
            'result_id' => $this->db->lastInsertId(),
            'score' => $score,
            'total' => $total
        ];
    }

    // ================= GET RESULT =================
    public function getResultById(int $resultId): ?array
    {
        return $this->db->fetch(
            "SELECT r.*, q.title, s.name AS subject_name, u.name AS student_name
             FROM results r
             JOIN quizzes q ON r.quiz_id = q.id
             JOIN subjects s ON q.subject_id = s.id
             JOIN users u ON r.user_id = u.id
             WHERE r.id = :id",
            [':id' => $resultId]
        ) ?: null;
    }
}