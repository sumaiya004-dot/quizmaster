<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/quizmaster');

require_once __DIR__ . '/Core/Database.php';
require_once __DIR__ . '/app/helpers.php';

use Core\Database;

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}

$db = Database::getInstance();

if (isset($_GET['action']) && $_GET['action'] === 'submit') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw ?: '{}', true);
        if (!is_array($payload)) {
            throw new RuntimeException('Invalid payload.');
        }

        $token = (string) ($payload['csrf_token'] ?? '');
        if (!verifyCsrf($token)) {
            throw new RuntimeException('CSRF verification failed.');
        }

        $quizId = (int) ($payload['quiz_id'] ?? 0);
        $summary = $payload['summary'] ?? [];
        if (!is_array($summary) || $quizId <= 0) {
            throw new RuntimeException('Missing quiz summary.');
        }

        $earned = (int) ($summary['earned'] ?? 0);
        $totalMarks = max(1, (int) ($summary['totalMarks'] ?? 1));
        $incorrect = (int) ($summary['incorrect'] ?? 0);
        $skipped = (int) ($summary['skipped'] ?? 0);
        $correct = (int) ($summary['correct'] ?? 0);

        $answersJson = json_encode([
            'answers' => $summary['answers'] ?? [],
            'state' => $payload['state'] ?? [],
            'analytics' => compact('correct', 'incorrect', 'skipped', 'earned', 'totalMarks'),
        ], JSON_UNESCAPED_UNICODE);

        $db->execute(
            "INSERT INTO results (user_id, quiz_id, score, total_marks, time_taken, answers, submitted_at)
             VALUES (:uid, :qid, :score, :total, :time_taken, :answers, NOW())",
            [
                ':uid' => (int) $_SESSION['user_id'],
                ':qid' => $quizId,
                ':score' => $earned,
                ':total' => $totalMarks,
                ':time_taken' => 0,
                ':answers' => $answersJson,
            ]
        );

        echo json_encode(['ok' => true, 'result_id' => $db->lastInsertId()]);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

$resultId = isset($_GET['result_id']) ? (int) $_GET['result_id'] : 0;

try {
    if ($resultId > 0) {
        $result = $db->fetchOne(
            "SELECT r.*, q.title, q.difficulty, s.name AS subject_name
             FROM results r
             JOIN quizzes q ON q.id = r.quiz_id
             JOIN subjects s ON s.id = q.subject_id
             WHERE r.id = :id AND r.user_id = :uid
             LIMIT 1",
            [':id' => $resultId, ':uid' => (int) $_SESSION['user_id']]
        );
    } else {
        $result = $db->fetchOne(
            "SELECT r.*, q.title, q.difficulty, s.name AS subject_name
             FROM results r
             JOIN quizzes q ON q.id = r.quiz_id
             JOIN subjects s ON s.id = q.subject_id
             WHERE r.user_id = :uid
             ORDER BY r.id DESC
             LIMIT 1",
            [':uid' => (int) $_SESSION['user_id']]
        );
    }

    if (!$result) {
        throw new RuntimeException('No result found.');
    }

    $answers = json_decode((string) $result['answers'], true) ?: [];
    $a = $answers['analytics'] ?? [];
    $correct = (int) ($a['correct'] ?? 0);
    $incorrect = (int) ($a['incorrect'] ?? 0);
    $skipped = (int) ($a['skipped'] ?? 0);
    $scorePct = (int) round(((int) $result['score'] / max(1, (int) $result['total_marks'])) * 100);

    $category = $scorePct >= 80 ? 'Outstanding' : ($scorePct >= 50 ? 'Medium' : 'Improvement');

    $previous = $db->fetchOne(
        "SELECT r.id, r.score, r.total_marks
         FROM results r
         WHERE r.user_id = :uid AND r.quiz_id = :qid AND r.id < :id
         ORDER BY r.id DESC
         LIMIT 1",
        [
            ':uid' => (int) $_SESSION['user_id'],
            ':qid' => (int) $result['quiz_id'],
            ':id' => (int) $result['id'],
        ]
    );

    $trendText = 'First attempt';
    if ($previous) {
        $prevPct = (int) round(((int) $previous['score'] / max(1, (int) $previous['total_marks'])) * 100);
        if ($scorePct > $prevPct) $trendText = 'Improved ↑';
        elseif ($scorePct < $prevPct) $trendText = 'Down ↓';
        else $trendText = 'Same →';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h2 style="font-family:sans-serif;padding:2rem;color:#b91c1c">Result load failed: ' . xss($e->getMessage()) . '</h2>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results — QuizMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <main class="max-w-5xl mx-auto px-4 py-8">
        <button onclick="history.back()" class="min-h-[44px] mb-4 rounded border bg-white px-3 py-2 text-sm" aria-label="Go back">←</button>
        <div class="bg-gradient-to-r from-indigo-700 to-violet-700 text-white rounded-2xl p-6">
            <h1 class="text-2xl md:text-3xl font-bold">Performance Analytics</h1>
            <p class="text-indigo-100 mt-1"><?= xss((string) $result['title']) ?> | <?= xss((string) $result['subject_name']) ?></p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
            <div class="bg-white border rounded-xl p-4"><p class="text-xs text-gray-500">Correct</p><p class="text-2xl font-bold text-emerald-600"><?= $correct ?></p></div>
            <div class="bg-white border rounded-xl p-4"><p class="text-xs text-gray-500">Incorrect</p><p class="text-2xl font-bold text-rose-600"><?= $incorrect ?></p></div>
            <div class="bg-white border rounded-xl p-4"><p class="text-xs text-gray-500">Skipped</p><p class="text-2xl font-bold text-slate-600"><?= $skipped ?></p></div>
            <div class="bg-white border rounded-xl p-4"><p class="text-xs text-gray-500">Score %</p><p class="text-2xl font-bold text-indigo-700"><?= $scorePct ?>%</p></div>
        </div>

        <div class="bg-white border rounded-xl p-5 mt-4">
            <p class="text-base md:text-lg"><strong>Category:</strong> <?= xss($category) ?></p>
            <p class="text-base md:text-lg mt-1"><strong>Comparison:</strong> <?= xss($trendText) ?></p>
            <p class="text-sm text-gray-600 mt-2">Score: <?= (int) $result['score'] ?> / <?= (int) $result['total_marks'] ?></p>
        </div>

        <a href="leaderboard.php" class="inline-block mt-4 min-h-[44px] px-4 py-2 rounded bg-indigo-600 text-white font-semibold">View Leaderboard</a>
    </main>
</body>
</html>

