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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$db = Database::getInstance();
$quizId = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 1;
$type = sanitizeInput((string) ($_GET['type'] ?? 'all'));
$normalizedType = $type === 'short_answer' ? 'short' : $type;

try {
    $quiz = $db->fetchOne(
        "SELECT q.*, s.name AS subject_name, s.icon
         FROM quizzes q
         JOIN subjects s ON s.id = q.subject_id
         WHERE q.id = :id AND q.is_active = 1
         LIMIT 1",
        [':id' => $quizId]
    );

    if (!$quiz) {
        throw new RuntimeException('Quiz not found.');
    }

    if ($normalizedType === 'all') {
        $questions = $db->fetchAll(
            "SELECT id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option, explanation, marks
             FROM questions
             WHERE quiz_id = :qid
             ORDER BY id ASC",
            [':qid' => $quizId]
        );
    } else {
        $questions = $db->fetchAll(
            "SELECT id, question_text, question_type, option_a, option_b, option_c, option_d, correct_option, explanation, marks
             FROM questions
             WHERE quiz_id = :qid AND question_type = :type
             ORDER BY id ASC",
            [':qid' => $quizId, ':type' => $normalizedType]
        );
    }

    if ($questions === []) {
        throw new RuntimeException('No questions found for this quiz/type.');
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h2 style="font-family:sans-serif;padding:2rem;color:#b91c1c">Quiz load failed: ' . xss($e->getMessage()) . '</h2>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Engine — QuizMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <header class="bg-gradient-to-r from-indigo-700 to-violet-700 text-white">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl md:text-2xl font-bold">⚡ QuizMaster Engine</h1>
                <p class="text-sm text-indigo-100"><?= xss((string) $quiz['title']) ?> | <?= xss((string) $quiz['subject_name']) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-indigo-100">20s per question</p>
                <p id="timer" class="text-2xl font-extrabold">20</p>
            </div>
        </div>
    </header>

    <main id="quizScreen" class="max-w-7xl mx-auto p-4 md:p-6 flex flex-col md:flex-row gap-4 md:gap-6">
        <aside class="w-full md:w-80 order-1 md:order-none">
            <div class="bg-white rounded-2xl border p-4 sticky top-4">
                <h2 class="font-bold text-base md:text-lg mb-3">Question Grid</h2>
                <div id="grid" class="grid grid-cols-5 md:grid-cols-10 gap-2"></div>
                <div class="mt-4 text-xs text-slate-600 space-y-1">
                    <div><span class="inline-block w-3 h-3 rounded bg-violet-500 mr-1"></span> Current</div>
                    <div><span class="inline-block w-3 h-3 rounded bg-emerald-500 mr-1"></span> Answered</div>
                    <div><span class="inline-block w-3 h-3 rounded bg-slate-300 mr-1"></span> Unanswered</div>
                </div>
            </div>
        </aside>

        <section class="flex-1 order-2 md:order-none">
            <div class="bg-white rounded-2xl border p-5 md:p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="font-semibold text-base md:text-lg">Question <span id="qNum">1</span> of <span id="qTotal"></span></h2>
                    <button onclick="history.back()" class="min-h-[44px] px-3 py-2 rounded border text-sm" aria-label="Go back">←</button>
                </div>
                <p id="qText" class="text-base md:text-lg font-medium text-slate-800 mb-4"></p>
                <div id="options" class="space-y-2"></div>
                <div id="shortWrap" class="hidden space-y-2">
                    <input id="shortInput" type="text" class="w-full min-h-[44px] rounded border px-3 py-2" placeholder="Type your answer">
                    <button id="shortSubmit" class="min-h-[44px] px-4 py-2 rounded bg-indigo-600 text-white font-semibold">Submit Answer</button>
                </div>
                <div id="feedback" class="hidden mt-4 rounded-lg p-3 text-sm"></div>
                <div class="mt-5 flex gap-2">
                    <button id="prevBtn" class="min-h-[44px] px-4 py-2 rounded border">Previous</button>
                    <button id="nextBtn" class="min-h-[44px] px-4 py-2 rounded bg-indigo-600 text-white">Next</button>
                </div>
            </div>
        </section>
    </main>

    <div class="fixed bottom-4 right-4 flex flex-col gap-2">
        <button id="restartBtn" class="min-h-[44px] px-4 py-2 rounded-xl bg-amber-500 text-white font-semibold shadow-lg">Restart</button>
        <button id="endBtn" class="min-h-[44px] px-4 py-2 rounded-xl bg-rose-600 text-white font-semibold shadow-lg">End Quiz</button>
    </div>

    <script>
        const csrfToken = <?= json_encode($_SESSION['csrf_token']) ?>;
        const quizId = <?= (int) $quizId ?>;
        const questions = <?= json_encode($questions, JSON_UNESCAPED_UNICODE) ?>;
        const state = questions.map(() => ({ answered: false, skipped: false, correct: false, answer: null, timeUp: false }));

        let current = 0;
        let timeLeft = 20;
        let timerRef = null;

        const timerEl = document.getElementById('timer');
        const gridEl = document.getElementById('grid');
        const qNumEl = document.getElementById('qNum');
        const qTotalEl = document.getElementById('qTotal');
        const qTextEl = document.getElementById('qText');
        const optionsEl = document.getElementById('options');
        const shortWrap = document.getElementById('shortWrap');
        const shortInput = document.getElementById('shortInput');
        const shortSubmit = document.getElementById('shortSubmit');
        const feedbackEl = document.getElementById('feedback');
        const quizScreen = document.getElementById('quizScreen');

        qTotalEl.textContent = String(questions.length);

        function renderGrid() {
            gridEl.innerHTML = '';
            questions.forEach((_, idx) => {
                const btn = document.createElement('button');
                btn.className = 'min-h-[44px] rounded text-sm font-semibold';
                const s = state[idx];
                if (idx === current) btn.className += ' bg-violet-500 text-white';
                else if (s.answered) btn.className += ' bg-emerald-500 text-white';
                else btn.className += ' bg-slate-300 text-slate-700';
                btn.textContent = String(idx + 1);
                btn.addEventListener('click', () => gotoQuestion(idx));
                gridEl.appendChild(btn);
            });
        }

        function startTimer() {
            clearInterval(timerRef);
            timeLeft = 20;
            tick();
            timerRef = setInterval(tick, 1000);
        }

        function tick() {
            timerEl.textContent = String(timeLeft);
            if (timeLeft <= 5) {
                timerEl.classList.add('text-rose-400');
                quizScreen.classList.add('bg-rose-50');
            } else {
                timerEl.classList.remove('text-rose-400');
                quizScreen.classList.remove('bg-rose-50');
            }
            if (timeLeft <= 0) {
                state[current].skipped = true;
                state[current].answered = false;
                state[current].correct = false;
                state[current].answer = '__timeout__';
                state[current].timeUp = true;
                showFeedback(false, 'Time ended. Auto-skipped (counted as wrong).');
                clearInterval(timerRef);
                return;
            }
            timeLeft -= 1;
        }

        function showFeedback(ok, message, explanation = '') {
            feedbackEl.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-800', 'bg-rose-50', 'border-rose-200', 'text-rose-800');
            feedbackEl.classList.add(ok ? 'bg-emerald-50' : 'bg-rose-50', ok ? 'border-emerald-200' : 'border-rose-200', 'border', ok ? 'text-emerald-800' : 'text-rose-800');
            feedbackEl.innerHTML = `<strong>${ok ? 'Correct' : 'Wrong'}</strong> — ${message}${explanation ? `<div class="mt-1">${explanation}</div>` : ''}`;
        }

        function evaluate(answerValue) {
            const q = questions[current];
            const isShort = q.question_type === 'short';
            let ok = false;
            if (isShort) {
                ok = String(answerValue || '').trim().toLowerCase() === String(q.option_a || '').trim().toLowerCase();
            } else {
                ok = String(answerValue) === String(q.correct_option);
            }
            state[current].answered = true;
            state[current].skipped = false;
            state[current].correct = ok;
            state[current].answer = String(answerValue);
            const explanation = q.explanation ? `Explanation: ${q.explanation}` : '';
            if (isShort) {
                const msg = ok ? 'Your short answer is correct.' : `Correct answer: ${q.option_a}`;
                showFeedback(ok, msg, explanation);
            } else {
                showFeedback(ok, ok ? 'Great choice.' : `Correct option is ${String(q.correct_option).toUpperCase()}.`, explanation);
            }
            renderGrid();
        }

        function renderQuestion() {
            const q = questions[current];
            const s = state[current];
            qNumEl.textContent = String(current + 1);
            qTextEl.textContent = q.question_text;
            optionsEl.innerHTML = '';
            shortWrap.classList.add('hidden');
            feedbackEl.classList.add('hidden');
            shortInput.value = '';

            if (q.question_type === 'short') {
                shortWrap.classList.remove('hidden');
            } else {
                const opts = q.question_type === 'tf'
                    ? [{k: 'a', v: 'True'}, {k: 'b', v: 'False'}]
                    : [{k: 'a', v: q.option_a}, {k: 'b', v: q.option_b}, {k: 'c', v: q.option_c}, {k: 'd', v: q.option_d}];

                opts.forEach((opt) => {
                    const btn = document.createElement('button');
                    btn.className = 'w-full min-h-[44px] text-left rounded-lg border px-3 py-2 hover:border-indigo-400';
                    btn.textContent = `${opt.k.toUpperCase()}) ${opt.v}`;
                    btn.addEventListener('click', () => evaluate(opt.k));
                    optionsEl.appendChild(btn);
                });
            }

            if (s.timeUp) {
                showFeedback(false, 'Time ended. Auto-skipped (counted as wrong).');
            } else if (s.answered) {
                const explanation = q.explanation ? `Explanation: ${q.explanation}` : '';
                showFeedback(s.correct, s.correct ? 'Already answered correctly.' : 'Already answered.', explanation);
            }

            renderGrid();
            startTimer();
        }

        function gotoQuestion(idx) {
            current = idx;
            renderQuestion();
        }

        function gatherSummary() {
            let correct = 0, incorrect = 0, skipped = 0, totalMarks = 0, earned = 0;
            const answers = {};
            questions.forEach((q, i) => {
                const s = state[i];
                totalMarks += Number(q.marks || 1);
                answers[q.id] = s.answer;
                if (s.answered && s.correct) {
                    correct += 1;
                    earned += Number(q.marks || 1);
                } else if (s.skipped || s.answer === '__timeout__' || s.answer === null) {
                    skipped += 1;
                    incorrect += 1;
                } else {
                    incorrect += 1;
                }
            });
            return { correct, incorrect, skipped, earned, totalMarks, answers };
        }

        async function finishQuiz() {
            const summary = gatherSummary();
            try {
                const res = await fetch('results.php?action=submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        csrf_token: csrfToken,
                        quiz_id: quizId,
                        summary,
                        state,
                    }),
                });
                const data = await res.json();
                if (!res.ok || !data.ok) throw new Error(data.message || 'Submit failed');
                window.location.href = `results.php?result_id=${encodeURIComponent(data.result_id)}`;
            } catch (err) {
                alert('Failed to submit quiz: ' + err.message);
            }
        }

        document.getElementById('prevBtn').addEventListener('click', () => {
            if (current > 0) gotoQuestion(current - 1);
        });
        document.getElementById('nextBtn').addEventListener('click', () => {
            if (current < questions.length - 1) gotoQuestion(current + 1);
            else finishQuiz();
        });
        document.getElementById('endBtn').addEventListener('click', finishQuiz);
        document.getElementById('restartBtn').addEventListener('click', () => {
            state.forEach((s) => { s.answered = false; s.skipped = false; s.correct = false; s.answer = null; s.timeUp = false; });
            current = 0;
            renderQuestion();
        });
        shortSubmit.addEventListener('click', () => evaluate(shortInput.value));

        renderQuestion();
    </script>
</body>
</html>

