<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <header class="bg-gradient-to-r from-indigo-700 to-violet-700 text-white">
        <div class="max-w-5xl mx-auto px-4 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Scoreboard</h1>
                <p class="text-indigo-100 text-sm">
                    <?= e($result['subject'] ?? '') ?> | <?= ucfirst(e($result['difficulty'] ?? '')) ?> | <?= strtoupper(e($result['question_type'] ?? '')) ?>
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="history.back()" class="rounded-lg border border-white/40 px-3 py-2 text-sm hover:bg-white/10" aria-label="Go back">←</button>
                <a href="<?= e(app_url('/dashboard')) ?>" class="rounded-lg bg-white text-indigo-700 px-3 py-2 text-sm font-semibold hover:bg-indigo-100">Dashboard</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <div class="rounded-2xl bg-white border border-slate-200 shadow p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Great job, <?= e($playerName) ?>.</h2>
                    <p class="text-slate-600 text-sm mt-1">Here’s your result summary.</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-500">Score</p>
                    <p class="text-3xl font-extrabold text-indigo-700">
                        <?= (int)($result['score'] ?? 0) ?> / <?= (int)($result['total'] ?? 0) ?>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        Time taken: <span class="font-semibold"><?= (int)($result['time_taken'] ?? 0) ?>s</span>
                    </p>
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-slate-200 p-4">
                <h3 class="font-bold text-slate-900">Leaderboard (Top 10)</h3>
                <p class="text-sm text-slate-600 mt-1">Ranked by score (desc), then time (asc).</p>

                <?php if (!empty($leaderboard)): ?>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-3">#</th>
                                    <th class="py-2 pr-3">Name</th>
                                    <th class="py-2 pr-3">Score</th>
                                    <th class="py-2 pr-3">Time</th>
                                    <th class="py-2 pr-3">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($leaderboard as $idx => $row): ?>
                                    <tr class="border-t border-slate-100">
                                        <td class="py-2 pr-3 font-semibold"><?= $idx + 1 ?></td>
                                        <td class="py-2 pr-3"><?= e((string)($row['user_name'] ?? '')) ?></td>
                                        <td class="py-2 pr-3">
                                            <?= (int)($row['score'] ?? 0) ?> / <?= (int)($row['total_marks'] ?? 0) ?>
                                        </td>
                                        <td class="py-2 pr-3"><?= (int)($row['time_taken'] ?? 0) ?>s</td>
                                        <td class="py-2 pr-3"><?= e((string)($row['submitted_at'] ?? '')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-slate-600 mt-3">No leaderboard entries yet. Submit one quiz to appear here.</p>
                <?php endif; ?>
            </div>

            <div class="mt-6 space-y-3">
                <?php foreach (($result['review'] ?? []) as $i => $row): ?>
                    <div class="rounded-xl border border-slate-200 p-4 <?= !empty($row['is_correct']) ? 'bg-emerald-50' : 'bg-rose-50' ?>">
                        <p class="font-semibold text-slate-900">
                            Q<?= $i + 1 ?>. <?= e($row['text'] ?? '') ?>
                        </p>

                        <div class="mt-2 text-sm text-slate-700">
                            <p><span class="font-semibold">Your answer:</span> <?= e((string)($row['your'] ?? '')) ?></p>
                            <p><span class="font-semibold">Correct:</span>
                                <?php if (($result['question_type'] ?? '') === 'short'): ?>
                                    <?= e((string)($row['option_a'] ?? '')) ?>
                                <?php else: ?>
                                    <?= strtoupper(e((string)($row['correct'] ?? ''))) ?>
                                    <?php if (($result['question_type'] ?? '') === 'tf'): ?>
                                        (<?= (strtolower((string)($row['correct'] ?? 'a')) === 'a') ? 'True' : 'False' ?>)
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($row['explanation'])): ?>
                                <p class="mt-1 text-slate-600"><span class="font-semibold">Explanation:</span> <?= e((string)$row['explanation']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>
</html>

