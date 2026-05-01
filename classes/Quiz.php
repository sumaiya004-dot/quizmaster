<?php
// classes/Quiz.php
require_once __DIR__ . '/../config/database.php';

class Quiz {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ---------- Subjects ----------
    public function getAllSubjects(): array {
        return $this->db->query("SELECT * FROM subjects ORDER BY name")->fetchAll();
    }

    public function getSubjectById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function createSubject(string $name, string $icon, string $desc): void {
        $stmt = $this->db->prepare("INSERT INTO subjects (name, icon, description) VALUES (:n,:i,:d)");
        $stmt->execute([':n' => $name, ':i' => $icon, ':d' => $desc]);
    }

    public function updateSubject(int $id, string $name, string $icon, string $desc): void {
        $stmt = $this->db->prepare("UPDATE subjects SET name=:n, icon=:i, description=:d WHERE id=:id");
        $stmt->execute([':n' => $name, ':i' => $icon, ':d' => $desc, ':id' => $id]);
    }

    public function deleteSubject(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM subjects WHERE id=:id");
        $stmt->execute([':id' => $id]);
    }

    // ---------- Quizzes ----------
    public function getQuizzesBySubjectAndDifficulty(int $subjectId, string $difficulty): array {
        $stmt = $this->db->prepare("
            SELECT q.*, s.name AS subject_name, s.icon,
                   (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
            FROM quizzes q JOIN subjects s ON q.subject_id = s.id
            WHERE q.subject_id = :sid AND q.difficulty = :diff AND q.is_active = 1
        ");
        $stmt->execute([':sid' => $subjectId, ':diff' => $difficulty]);
        return $stmt->fetchAll();
    }

    public function getQuizById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT q.*, s.name AS subject_name, s.icon
            FROM quizzes q JOIN subjects s ON q.subject_id = s.id
            WHERE q.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAllQuizzes(): array {
        return $this->db->query("
            SELECT q.*, s.name AS subject_name,
                   (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
            FROM quizzes q JOIN subjects s ON q.subject_id = s.id
            ORDER BY q.created_at DESC
        ")->fetchAll();
    }

    public function createQuiz(int $subjectId, string $title, string $diff, int $timeLimit): int {
        $stmt = $this->db->prepare(
            "INSERT INTO quizzes (subject_id, title, difficulty, time_limit) VALUES (:s,:t,:d,:tl)"
        );
        $stmt->execute([':s' => $subjectId, ':t' => $title, ':d' => $diff, ':tl' => $timeLimit]);
        return (int)$this->db->lastInsertId();
    }

    public function deleteQuiz(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM quizzes WHERE id=:id");
        $stmt->execute([':id' => $id]);
    }

    public function updateQuizMarks(int $quizId): void {
        $stmt = $this->db->prepare(
            "UPDATE quizzes SET total_marks=(SELECT COALESCE(SUM(marks),0) FROM questions WHERE quiz_id=:id) WHERE id=:id2"
        );
        $stmt->execute([':id' => $quizId, ':id2' => $quizId]);
    }

    // ---------- Questions ----------
    public function getQuestionsByQuiz(int $quizId): array {
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE quiz_id = :qid ORDER BY id");
        $stmt->execute([':qid' => $quizId]);
        return $stmt->fetchAll();
    }

    public function addQuestion(int $quizId, array $data): void {
        $stmt = $this->db->prepare("
            INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option, marks)
            VALUES (:qid,:qt,:a,:b,:c,:d,:co,:m)
        ");
        $stmt->execute([
            ':qid' => $quizId,
            ':qt'  => $data['question_text'],
            ':a'   => $data['option_a'],
            ':b'   => $data['option_b'],
            ':c'   => $data['option_c'],
            ':d'   => $data['option_d'],
            ':co'  => $data['correct_option'],
            ':m'   => $data['marks'] ?? 1,
        ]);
        $this->updateQuizMarks($quizId);
    }

    public function deleteQuestion(int $id): void {
        $stmt = $this->db->prepare("SELECT quiz_id FROM questions WHERE id=:id");
        $stmt->execute([':id' => $id]);
        $q = $stmt->fetch();
        $del = $this->db->prepare("DELETE FROM questions WHERE id=:id");
        $del->execute([':id' => $id]);
        if ($q) $this->updateQuizMarks($q['quiz_id']);
    }

    // ---------- Results ----------
    public function submitResult(int $userId, int $quizId, array $answers): array {
        $quiz      = $this->getQuizById($quizId);
        $questions = $this->getQuestionsByQuiz($quizId);
        $score     = 0;
        $breakdown = [];

        foreach ($questions as $q) {
            $selected = $answers[$q['id']] ?? null;
            $correct  = ($selected === $q['correct_option']);
            if ($correct) $score += $q['marks'];
            $breakdown[] = [
                'question'   => $q['question_text'],
                'selected'   => $selected,
                'correct'    => $q['correct_option'],
                'is_correct' => $correct,
                'marks'      => $q['marks'],
            ];
        }

        $timeTaken = isset($_SESSION['quiz_start']) ? (time() - $_SESSION['quiz_start']) : 0;
        $stmt = $this->db->prepare("
            INSERT INTO results (user_id, quiz_id, score, total_marks, time_taken, answers)
            VALUES (:uid, :qid, :sc, :tm, :tt, :ans)
        ");
        $stmt->execute([
            ':uid' => $userId,
            ':qid' => $quizId,
            ':sc'  => $score,
            ':tm'  => $quiz['total_marks'],
            ':tt'  => $timeTaken,
            ':ans' => json_encode($breakdown),
        ]);

        $resultId = (int)$this->db->lastInsertId();
        return ['result_id' => $resultId, 'score' => $score, 'total' => $quiz['total_marks'], 'breakdown' => $breakdown];
    }

    public function getResultById(int $resultId): ?array {
        $stmt = $this->db->prepare("
            SELECT r.*, r.user_id, q.title, q.difficulty, s.name AS subject_name, s.icon, u.name AS student_name
            FROM results r
            JOIN quizzes q ON r.quiz_id = q.id
            JOIN subjects s ON q.subject_id = s.id
            JOIN users u ON r.user_id = u.id
            WHERE r.id = :id
        ");
        $stmt->execute([':id' => $resultId]);
        return $stmt->fetch() ?: null;
    }

    public function getUserResults(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, q.title, q.difficulty, s.name AS subject_name, s.icon
            FROM results r
            JOIN quizzes q ON r.quiz_id = q.id
            JOIN subjects s ON q.subject_id = s.id
            WHERE r.user_id = :uid
            ORDER BY r.submitted_at DESC
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAdminStats(): array {
        return [
            'students'  => $this->db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
            'quizzes'   => $this->db->query("SELECT COUNT(*) FROM quizzes")->fetchColumn(),
            'questions' => $this->db->query("SELECT COUNT(*) FROM questions")->fetchColumn(),
            'attempts'  => $this->db->query("SELECT COUNT(*) FROM results")->fetchColumn(),
        ];
    }
}
