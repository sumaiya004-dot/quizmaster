<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Quiz Started</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <header class="bg-gradient-to-r from-indigo-700 to-violet-700 text-white">
        <div class="max-w-5xl mx-auto px-4 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Quiz Started</h1>
                <p class="text-indigo-100 text-sm">
                    <?= e($subject) ?> | <?= ucfirst(e($difficulty)) ?> | <?= strtoupper(e($questionType)) ?>
                </p>
                <p class="text-indigo-100 text-sm mt-1">
                    Good luck, <span class="font-semibold"><?= e((string)($_SESSION['quiz_player_name'] ?? ($_SESSION['user_name'] ?? 'Player'))) ?></span>.
                </p>
            </div>
            <div class="flex gap-2 items-center">
                <div class="hidden sm:block rounded-lg bg-white/15 border border-white/25 px-3 py-2 text-sm">
                    Time left: <span id="timeLeft" class="font-semibold">--:--</span>
                </div>
                <button onclick="history.back()" class="rounded-lg border border-white/40 px-3 py-2 text-sm hover:bg-white/10" aria-label="Go back">←</button>
                <a href="<?= e(app_url('/dashboard')) ?>" class="rounded-lg bg-white text-indigo-700 px-3 py-2 text-sm font-semibold hover:bg-indigo-100">Dashboard</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <div class="mb-4 rounded-lg bg-indigo-50 border border-indigo-100 p-4 text-indigo-900">
            <p class="font-semibold">Total Questions: <?= count($questions) ?></p>
            <p class="text-sm">Answer all questions, then submit to see your personalized scoreboard.</p>
        </div>

        <form method="post" action="<?= e(app_url('/quiz/submit')) ?>" class="space-y-4" id="quizForm">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <?php foreach ($questions as $index => $question): ?>
                <?php $qid = (int) $question['id']; ?>
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <h2 class="font-semibold text-slate-900 mb-3">
                        Q<?= $index + 1 ?>. <?= e($question['question_text']) ?>
                    </h2>

                    <?php if ($questionType === 'mcq'): ?>
                        <div class="grid gap-2 text-slate-700">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="answers[<?= $qid ?>]" value="a" required>
                                <span>A) <?= e($question['option_a']) ?></span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="answers[<?= $qid ?>]" value="b">
                                <span>B) <?= e($question['option_b']) ?></span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="answers[<?= $qid ?>]" value="c">
                                <span>C) <?= e($question['option_c']) ?></span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="answers[<?= $qid ?>]" value="d">
                                <span>D) <?= e($question['option_d']) ?></span>
                            </label>
                        </div>
                    <?php elseif ($questionType === 'tf'): ?>
                        <div class="grid gap-2 text-slate-700">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="answers[<?= $qid ?>]" value="a" required>
                                <span>True</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" name="answers[<?= $qid ?>]" value="b">
                                <span>False</span>
                            </label>
                        </div>
                    <?php else: ?>
                        <div>
                            <label class="block text-sm text-slate-600 mb-1">Your answer</label>
                            <input
                                type="text"
                                name="answers[<?= $qid ?>]"
                                required
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500"
                                placeholder="Type your answer…"
                            >
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($question['explanation'])): ?>
                        <p class="mt-3 text-xs text-slate-500">
                            Hint: <?= e($question['explanation']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="pt-2">
                <button type="submit" class="rounded-lg bg-indigo-600 text-white px-6 py-3 font-semibold hover:bg-indigo-500">
                    Submit Quiz
                </button>
            </div>
        </form>
    </main>
    <script>
        const startedAt = <?= (int) (($_SESSION['active_quiz']['started_at'] ?? time())) ?>;
        const timeLimit = <?= (int) (($_SESSION['active_quiz']['time_limit'] ?? 600)) ?>;
        const timeLeftEl = document.getElementById('timeLeft');
        const form = document.getElementById('quizForm');

        function pad(n) { return String(n).padStart(2, '0'); }

        function tick() {
            const now = Math.floor(Date.now() / 1000);
            const elapsed = Math.max(0, now - startedAt);
            const remaining = Math.max(0, timeLimit - elapsed);

            const m = Math.floor(remaining / 60);
            const s = remaining % 60;
            if (timeLeftEl) timeLeftEl.textContent = `${pad(m)}:${pad(s)}`;

            if (remaining <= 0 && form) {
                form.submit();
            }
        }

        tick();
        setInterval(tick, 1000);
    </script>
</body>
</html>
