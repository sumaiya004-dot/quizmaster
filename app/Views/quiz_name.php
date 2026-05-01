<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Your Name</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-950 via-violet-900 to-indigo-900 text-white">
    <div class="max-w-md mx-auto px-4 py-10">
        <button onclick="history.back()" class="mb-6 rounded-lg border border-white/30 px-3 py-1.5 text-sm hover:bg-white/10" aria-label="Go back">←</button>

        <div class="rounded-2xl bg-white/10 p-7 shadow-2xl backdrop-blur-lg border border-white/20">
            <h1 class="text-3xl font-bold mb-2">Before we start…</h1>
            <p class="text-white/80 mb-6">Enter the name you want to see on your quiz and scoreboard.</p>

            <form method="post" action="<?= e(app_url('/quiz/name')) ?>" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="subject" value="<?= e($subject) ?>">
                <input type="hidden" name="difficulty" value="<?= e($difficulty) ?>">
                <input type="hidden" name="question_type" value="<?= e($questionType) ?>">

                <div>
                    <label class="block text-sm mb-1">Display Name</label>
                    <input
                        type="text"
                        name="player_name"
                        value="<?= e($prefillName) ?>"
                        maxlength="40"
                        required
                        class="w-full rounded-lg bg-white/90 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500"
                    >
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2.5 font-semibold hover:bg-indigo-400">
                    Start Quiz
                </button>
            </form>
        </div>
    </div>
</body>
</html>

