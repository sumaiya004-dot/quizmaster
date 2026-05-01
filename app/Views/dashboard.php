<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Smooth transition for focus rings and hover effects */
        .focus-ring {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<!-- STEP 1: BODY COLOR UPGRADE -->
<body class="min-h-screen bg-slate-50 text-slate-900">

    <!-- STEP 2: HEADER POLISH -->
    <header class="bg-slate-900 text-white border-b border-white/10">
        <div class="max-w-5xl mx-auto px-4 py-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">QuizMaster</h1>
                <p class="text-slate-400 text-sm">Empower your learning</p>
            </div>
            <div class="flex gap-3">
                <button onclick="history.back()" class="rounded-xl border border-white/20 px-3 py-2 text-sm hover:bg-white/10 transition" aria-label="Go back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                </button>
                <a href="<?= e(app_url('/logout')) ?>" class="rounded-xl bg-white text-slate-900 px-4 py-2 text-sm font-semibold hover:bg-slate-100 transition">Logout</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10">
        
        <!-- STEP 3: HERO CARD ADD (Premium Look) -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl p-8 shadow-md mb-8">
            <h2 class="text-3xl font-bold tracking-tight">
                Hello, <?= e($_SESSION['user_name'] ?? 'Student') ?>!
            </h2>
            <p class="text-sm opacity-80 mt-1">
                Ready to challenge yourself today? Pick your topic and start winning.
            </p>
        </div>

        <!-- STEP 4: MAIN CARD UPGRADE -->
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition p-8 border border-slate-200">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Start a Quiz in 3 Steps</h2>
                <p class="text-sm text-slate-500">Select Subject -> Difficulty -> Question Type</p>
            </div>

            <!-- Error Alerts -->
            <?php if ($error === 'invalid_selection'): ?>
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Invalid quiz selection. Please try again.
                </div>
            <?php elseif ($error === 'no_questions'): ?>
                <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-700 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    No questions found for this combination.
                </div>
            <?php endif; ?>

            <!-- STEP 5 & 6: FORM GRID & SELECT FIX -->
            <form method="get" action="<?= e(app_url('/dashboard')) ?>" class="grid md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">1) Subject</label>
                    <select name="subject" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition outline-none">
                        <option value="">Choose subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= e($subject) ?>" <?= $selectedSubject === $subject ? 'selected' : '' ?>>
                                <?= e($subject) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">2) Difficulty</label>
                    <select name="difficulty" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition outline-none">
                        <option value="">Choose difficulty</option>
                        <?php foreach ($difficulties as $difficulty): ?>
                            <option value="<?= e($difficulty) ?>" <?= $selectedDifficulty === $difficulty ? 'selected' : '' ?>>
                                <?= ucfirst(e($difficulty)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">3) Question Type</label>
                    <select name="question_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition outline-none">
                        <option value="">Choose type</option>
                        <?php foreach ($questionTypes as $type): ?>
                            <option value="<?= e($type) ?>" <?= $selectedQuestionType === $type ? 'selected' : '' ?>>
                                <?= strtoupper(e($type)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- STEP 7: BUTTON FIX -->
                <div class="md:col-span-3 pt-2">
                    <button type="submit" class="w-full md:w-auto rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-3 font-semibold hover:scale-[1.02] hover:shadow-lg active:scale-[0.98] transition-all">
                        Apply Selection
                    </button>
                </div>
            </form>

            <!-- STEP 8: RESULT BOX POLISH -->
            <?php if ($selectedSubject && $selectedDifficulty && $selectedQuestionType): ?>
                <div class="mt-8 rounded-xl bg-indigo-50 border border-indigo-200 p-6 text-indigo-900 shadow-sm animate-in fade-in slide-in-from-bottom-2 duration-300">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium opacity-70">Configuration Ready</p>
                            <h4 class="text-lg font-bold">
                                <?= e($selectedSubject) ?> • <?= ucfirst(e($selectedDifficulty)) ?> • <?= strtoupper(e($selectedQuestionType)) ?>
                            </h4>
                            <div class="mt-1 text-sm font-medium text-indigo-600">
                                Available Questions: <span class="text-lg"><?= (int) $matchedQuestionCount ?></span>
                            </div>
                        </div>
                        
                        <a
                            href="<?= e(app_url('/quiz/start')) ?>?subject=<?= urlencode($selectedSubject) ?>&difficulty=<?= urlencode($selectedDifficulty) ?>&question_type=<?= urlencode($selectedQuestionType) ?>"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white px-6 py-3 font-bold hover:bg-indigo-700 hover:shadow-md transition transform active:scale-95"
                        >
                            Start Quiz Now
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>