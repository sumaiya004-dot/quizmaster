<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\QuizModel;
use App\Models\UserModel;
use Core\Controller;

class DashboardController extends Controller
{
    private QuizModel $quizModel;
    private UserModel $userModel;

    public function __construct()
    {
        requireLogin();

        // 🔥 IMPORTANT FIX (autoload problem fix)
        require_once APP_PATH . '/Models/QuizModel.php';
        require_once APP_PATH . '/Models/UserModel.php';

        $this->quizModel = new QuizModel();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $subjects = $this->quizModel->getAllSubjects();
        $leaderboard = $this->userModel->getLeaderboard(5);
        $myResults = $this->quizModel->getUserResults((int) $_SESSION['user_id']);

        // 🔥 FIXED FILTER
        $selectedSubject = !empty($_GET['subject']) ? (int) $_GET['subject'] : null;
        $selectedDifficulty = !empty($_GET['difficulty']) ? sanitizeInput((string) $_GET['difficulty']) : null;
        $selectedType = !empty($_GET['type']) ? sanitizeInput((string) $_GET['type']) : 'all'; // 🔥 FIX

        // get quizzes
        $quizzes = $this->quizModel->getQuizzes($selectedSubject, $selectedDifficulty, $selectedType);

        // stats
        $quizCount = count($myResults);
        $avgScore = 0.0;

        if ($quizCount > 0) {
            $pcts = array_map(
                static fn(array $r): float => ((int) $r['total_marks'] > 0)
                    ? ((int) $r['score'] / (int) $r['total_marks'] * 100)
                    : 0,
                $myResults
            );
            $avgScore = round(array_sum($pcts) / count($pcts), 1);
        }

        $this->render('dashboard/index', compact(
            'subjects',
            'leaderboard',
            'myResults',
            'quizzes',
            'selectedSubject',
            'selectedDifficulty',
            'selectedType',
            'quizCount',
            'avgScore'
        ));
    }
}