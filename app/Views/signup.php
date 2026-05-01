<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-950 via-violet-900 to-indigo-900 text-white">
    <div class="max-w-md mx-auto px-4 py-10">
        <button onclick="history.back()" class="mb-6 rounded-lg border border-white/30 px-3 py-1.5 text-sm hover:bg-white/10" aria-label="Go back">←</button>

        <div class="rounded-2xl bg-white/10 p-7 shadow-2xl backdrop-blur-lg border border-white/20">
            <h1 class="text-3xl font-bold mb-2">Create Account</h1>
            <p class="text-white/80 mb-6">Join QuizMaster and start your quiz journey.</p>

            <?php if (!empty($errors)): ?>
                <div class="mb-4 rounded-lg bg-red-500/20 border border-red-300/40 p-3 text-sm">
                    <?php foreach ($errors as $msg): ?>
                        <p>- <?= e($msg) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(app_url('/signup')) ?>" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div>
                    <label class="block text-sm mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full rounded-lg bg-white/90 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" name="email" required class="w-full rounded-lg bg-white/90 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm mb-1">Password</label>
                    <input type="password" name="password" required class="w-full rounded-lg bg-white/90 px-3 py-2 text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>
                <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2.5 font-semibold hover:bg-indigo-400">Sign Up</button>
            </form>

            <p class="mt-5 text-sm text-white/80">
                Already have an account?
                <a href="<?= e(app_url('/login')) ?>" class="font-semibold text-indigo-200 hover:underline">Login</a>
            </p>
        </div>
    </div>
</body>
</html>
