<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\QuizModel;
use Core\Controller;

class QuizController extends Controller
{
    private QuizModel $quizModel;

    public function __construct()
    {
        requireLogin();

        require_once APP_PATH . '/Models/QuizModel.php';
        $this->quizModel = new QuizModel();
    }

    // GET /quiz/start/{id}
    public function start(int $quizId = 0): void
    {
        if ($quizId <= 0) {
            $this->redirect('/dashboard');
        }

        $quiz = $this->quizModel->getQuizById($quizId);
        if (!$quiz) {
            $this->redirect('/dashboard');
        }

        $type = !empty($_GET['question_type']) ? sanitizeInput((string) $_GET['question_type']) : 'all';

        $questions = $this->quizModel->getQuestions($quizId, $type);
        if (empty($questions)) {
            $this->redirect('/dashboard');
        }

        // Store quiz start time in session for time tracking
        $_SESSION['quiz_start_' . $quizId] = time();

        $timeLimit = (int) ($quiz['time_limit'] ?? 1200);
        $remaining = $timeLimit;

        $this->render('quiz/start', compact('quiz', 'questions', 'type', 'remaining'));
    }

    // POST /quiz/submit
    public function submit(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/dashboard');
        }

        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!verifyCsrf($token)) {
            $this->redirect('/dashboard');
        }

        $quizId = (int) ($_POST['quiz_id'] ?? 0);
        $type   = !empty($_POST['question_type']) ? sanitizeInput((string) $_POST['question_type']) : 'all';

        if ($quizId <= 0) {
            $this->redirect('/dashboard');
        }

        // Collect answers from POST: field names are q_{question_id}
        $answers = [];
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, 'q_')) {
                $qId = (int) substr($key, 2);
                $answers[$qId] = sanitizeInput((string) $value);
            }
        }

        $userId = (int) $_SESSION['user_id'];
        $result = $this->quizModel->submitResult($userId, $quizId, $answers, $type);

        $this->redirect('/quiz/result/' . $result['result_id']);
    }

    // GET /quiz/result/{id}
    public function result(int $resultId = 0): void
    {
        if ($resultId <= 0) {
            $this->redirect('/dashboard');
        }

        $result = $this->quizModel->getResultById($resultId);
        if (!$result) {
            $this->redirect('/dashboard');
        }

        // Only allow the owner to view
        if ((int) $result['user_id'] !== (int) $_SESSION['user_id']) {
            $this->redirect('/dashboard');
        }

        $breakdown = json_decode((string) ($result['answers'] ?? '[]'), true) ?: [];
        $pct = ($result['total_marks'] > 0)
            ? round($result['score'] / $result['total_marks'] * 100)
            : 0;

        $this->render('quiz/result', compact('result', 'breakdown', 'pct'));
    }
}
