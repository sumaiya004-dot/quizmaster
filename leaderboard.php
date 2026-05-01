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

if (isset($_GET['action']) && $_GET['action'] === 'history') {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $uid = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
        if ($uid <= 0) throw new RuntimeException('Invalid user');

        $rows = $db->fetchAll(
            "SELECT s.name AS category,
                    ROUND(r.score / NULLIF(r.total_marks,0) * 100, 1) AS score_pct,
                    r.submitted_at
             FROM results r
             JOIN quizzes q ON q.id = r.quiz_id
             JOIN subjects s ON s.id = q.subject_id
             WHERE r.user_id = :uid
             ORDER BY r.submitted_at DESC
             LIMIT 20",
            [':uid' => $uid]
        );

        echo json_encode(['ok' => true, 'rows' => $rows], JSON_UNESCAPED_UNICODE);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

try {
    $all = $db->fetchAll(
        "SELECT u.id, u.name, u.avatar,
                COUNT(r.id) AS total_quizzes,
                COALESCE(SUM(r.score), 0) AS total_score,
                ROUND(AVG(r.score / NULLIF(r.total_marks,0)*100), 1) AS avg_pct
         FROM users u
         LEFT JOIN results r ON r.user_id = u.id
         WHERE u.role = 'student'
         GROUP BY u.id, u.name, u.avatar
         ORDER BY total_score DESC, avg_pct DESC, total_quizzes DESC"
    );
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h2 style="font-family:sans-serif;padding:2rem;color:#b91c1c">Leaderboard load failed: ' . xss($e->getMessage()) . '</h2>';
    exit;
}

$top5 = array_slice($all, 0, 5);
$myRank = null;
foreach ($all as $i => $row) {
    if ((int) $row['id'] === (int) $_SESSION['user_id']) {
        $myRank = $i + 1;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard — QuizMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <main class="max-w-7xl mx-auto px-4 py-8">
        <button onclick="history.back()" class="min-h-[44px] mb-4 rounded border bg-white px-3 py-2 text-sm">Back</button>
        <div class="bg-gradient-to-r from-indigo-700 to-violet-700 text-white rounded-2xl p-6">
            <h1 class="text-2xl md:text-3xl font-bold">Advanced Leaderboard</h1>
            <p class="text-indigo-100 mt-1">Your rank: <strong><?= $myRank ?? 'N/A' ?></strong></p>
        </div>

        <section class="mt-4 bg-white border rounded-xl p-4">
            <h2 class="font-bold text-lg mb-3">Top 5</h2>
            <div class="grid md:grid-cols-5 gap-3">
                <?php foreach ($top5 as $i => $u): ?>
                    <?php
                        $badge = $i === 0 ? '🥇 Gold' : ($i === 1 ? '🥈 Silver' : ($i === 2 ? '🥉 Bronze' : '#' . ($i + 1)));
                    ?>
                    <div class="rounded-xl border p-3 bg-slate-50 min-h-[44px]">
                        <p class="text-sm font-bold"><?= $badge ?></p>
                        <p class="font-semibold truncate"><?= xss((string) $u['name']) ?></p>
                        <p class="text-xs text-gray-600"><?= (int) $u['total_score'] ?> pts</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="mt-4 bg-white border rounded-xl p-4">
            <h2 class="font-bold text-lg mb-3">All Students</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">#</th>
                            <th class="py-2 pr-3">Name</th>
                            <th class="py-2 pr-3">Quizzes</th>
                            <th class="py-2 pr-3">Avg %</th>
                            <th class="py-2 pr-3">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($all as $i => $u): ?>
                        <tr class="border-b <?= ((int) $u['id'] === (int) $_SESSION['user_id']) ? 'bg-indigo-50' : '' ?>">
                            <td class="py-2 pr-3"><?= $i + 1 ?></td>
                            <td class="py-2 pr-3">
                                <button data-user-id="<?= (int) $u['id'] ?>" data-user-name="<?= xss((string) $u['name']) ?>" class="min-h-[44px] text-indigo-700 underline historyBtn">
                                    <?= xss((string) $u['name']) ?>
                                </button>
                            </td>
                            <td class="py-2 pr-3"><?= (int) $u['total_quizzes'] ?></td>
                            <td class="py-2 pr-3"><?= xss((string) ($u['avg_pct'] ?? 0)) ?>%</td>
                            <td class="py-2 pr-3"><?= (int) $u['total_score'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="historyModal" class="hidden fixed inset-0 bg-black/50 p-4 z-50">
        <div class="max-w-3xl mx-auto mt-10 bg-white rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="font-bold text-lg">History</h3>
                <button id="closeModal" class="min-h-[44px] px-3 py-2 rounded border">Close</button>
            </div>
            <div class="overflow-x-auto mt-3">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">Category</th>
                            <th class="py-2 pr-3">Score %</th>
                            <th class="py-2 pr-3">Date</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('historyModal');
        const historyBody = document.getElementById('historyBody');
        const modalTitle = document.getElementById('modalTitle');
        document.getElementById('closeModal').addEventListener('click', () => modal.classList.add('hidden'));

        document.querySelectorAll('.historyBtn').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const uid = btn.dataset.userId;
                const name = btn.dataset.userName;
                modalTitle.textContent = `${name} — Exam History`;
                historyBody.innerHTML = '<tr><td class="py-2" colspan="3">Loading...</td></tr>';
                modal.classList.remove('hidden');
                try {
                    const res = await fetch(`leaderboard.php?action=history&user_id=${encodeURIComponent(uid)}`);
                    const data = await res.json();
                    if (!res.ok || !data.ok) throw new Error(data.message || 'Failed to load history');
                    historyBody.innerHTML = '';
                    data.rows.forEach((row) => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b';
                        tr.innerHTML = `<td class="py-2 pr-3">${row.category}</td><td class="py-2 pr-3">${row.score_pct}%</td><td class="py-2 pr-3">${row.submitted_at}</td>`;
                        historyBody.appendChild(tr);
                    });
                    if (data.rows.length === 0) historyBody.innerHTML = '<tr><td class="py-2" colspan="3">No history yet.</td></tr>';
                } catch (err) {
                    historyBody.innerHTML = `<tr><td class="py-2 text-rose-600" colspan="3">${err.message}</td></tr>`;
                }
            });
        });
    </script>
</body>
</html>

