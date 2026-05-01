<?php $pageTitle = 'QuizMaster Pro'; ?>

<?php include APP_PATH . '/Views/partials/head.php'; ?>
<?php include APP_PATH . '/Views/partials/navbar.php'; ?>

<!-- min-h-screen নিশ্চিত করবে যে কন্টেন্ট কম হলেও মেইন সেকশনটি পুরো জায়গা নিবে, ফলে ফুটার নিচে থাকবে -->
<main class="relative overflow-hidden bg-slate-950 text-white flex-1 min-h-[calc(100vh-200px)]">
  <div class="absolute inset-0 opacity-40" style="background:radial-gradient(circle at 20% 20%,#4f46e5 0,transparent 45%),radial-gradient(circle at 80% 70%,#7c3aed 0,transparent 42%)"></div>

  <!-- Hero Section -->
  <section class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16 md:py-24 text-center">
    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-4 py-2 text-xs text-white/80 transition hover:border-indigo-300 hover:bg-white/10">
      Smart Assessment Platform • 5 Subjects • 3 Levels
    </div>
    <h1 class="mt-6 text-4xl md:text-7xl font-extrabold leading-tight">
      Elevate Your
      <span class="bg-gradient-to-r from-indigo-300 via-violet-300 to-indigo-200 bg-clip-text text-transparent">Knowledge Game</span>
    </h1>
    <p class="mt-4 text-base md:text-xl text-white/70 max-w-3xl mx-auto">
      Adaptive quizzes · Real-time timers · Global leaderboards · Built-in analytics.
    </p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
      <a href="<?= BASE_URL ?>/auth/signup" class="min-h-[44px] inline-flex items-center rounded-xl bg-indigo-500 px-6 py-3 font-bold shadow-lg shadow-indigo-500/30 transition hover:-translate-y-0.5 hover:bg-indigo-400">Start for Free</a>
      <a href="<?= BASE_URL ?>/auth/login" class="min-h-[44px] inline-flex items-center rounded-xl border border-white/20 bg-white/5 px-6 py-3 font-semibold transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">Sign In</a>
    </div>
  </section>

  <!-- Quick Features -->
  <section id="features" class="relative max-w-7xl mx-auto px-4 sm:px-6 pb-14">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
      <div class="rounded-2xl border border-white/15 bg-white/5 p-4 min-h-[88px] transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <p class="text-2xl font-extrabold">5+</p>
        <p class="text-sm text-white/70 mt-1">Subjects</p>
      </div>
      <div class="rounded-2xl border border-white/15 bg-white/5 p-4 min-h-[88px] transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <p class="text-2xl font-extrabold">20s</p>
        <p class="text-sm text-white/70 mt-1">Countdown / Q</p>
      </div>
      <div class="rounded-2xl border border-white/15 bg-white/5 p-4 min-h-[88px] transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <p class="text-2xl font-extrabold">3</p>
        <p class="text-sm text-white/70 mt-1">Category Levels</p>
      </div>
      <div class="rounded-2xl border border-white/15 bg-white/5 p-4 min-h-[88px] transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <p class="text-2xl font-extrabold">100%</p>
        <p class="text-sm text-white/70 mt-1">Secure</p>
      </div>
      <div class="rounded-2xl border border-white/15 bg-white/5 p-4 min-h-[88px] col-span-2 md:col-span-1 transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <p class="text-2xl font-extrabold">⚡</p>
        <p class="text-sm text-white/70 mt-1">Auto-Submit + Analytics</p>
      </div>
    </div>
  </section>

  <!-- Statistics Section -->
  <section class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <h2 class="text-2xl font-bold mb-8 text-center">Statistics</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center transition hover:-translate-y-0.5 hover:border-indigo-300">
        <div class="text-2xl mb-2">👥</div>
        <p class="text-3xl font-extrabold"><?= (int)($stats['total_users'] ?? 0) ?></p>
        <p class="text-sm text-white/60 mt-1">Total Users</p>
      </div>
      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center transition hover:-translate-y-0.5 hover:border-indigo-300">
        <div class="text-2xl mb-2">📝</div>
        <p class="text-3xl font-extrabold"><?= (int)($stats['total_quizzes'] ?? 0) ?></p>
        <p class="text-sm text-white/60 mt-1">Total Quizzes</p>
      </div>
      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center transition hover:-translate-y-0.5 hover:border-indigo-300">
        <div class="text-2xl mb-2">📊</div>
        <p class="text-3xl font-extrabold"><?= (int)($stats['total_attempts'] ?? 0) ?></p>
        <p class="text-sm text-white/60 mt-1">Total Attempts</p>
      </div>
      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center transition hover:-translate-y-0.5 hover:border-indigo-300">
        <div class="text-2xl mb-2">🎯</div>
        <p class="text-3xl font-extrabold"><?= xss((string)($stats['accuracy_rate'] ?? 0)) ?>%</p>
        <p class="text-sm text-white/60 mt-1">Accuracy Rate</p>
      </div>
    </div>
  </section>

  <!-- Subject Showcase Section (Heading Re-added) -->
  <section class="relative max-w-7xl mx-auto px-4 sm:px-6 pb-16">
    <div class="mb-6">
      <h2 class="text-xl md:text-2xl font-bold">Subject Showcase</h2>
      <p class="text-sm text-white/60 mt-1">Explore our wide range of categories and subjects</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
      <?php foreach ($subjects as $subject): ?>
        <div class="rounded-2xl border border-white/15 bg-white/5 p-4 min-h-[120px] transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
          <div class="text-2xl"><?= xss((string)$subject['icon']) ?></div>
          <p class="mt-2 font-bold"><?= xss((string)$subject['name']) ?></p>
          <p class="mt-1 text-sm text-white/70"><?= xss((string)$subject['description']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Difficulty Levels Section -->
  <section class="relative max-w-7xl mx-auto px-4 sm:px-6 pb-16">
    <div class="mb-6">
      <h2 class="text-xl md:text-2xl font-bold">Difficulty Levels</h2>
      <p class="text-sm text-white/60 mt-1">Challenge yourself with different complexity levels</p>
    </div>
    <div class="grid md:grid-cols-3 gap-3">
      <div class="rounded-2xl border border-emerald-300/30 bg-emerald-500/10 p-4 transition hover:-translate-y-0.5">
        <p class="font-bold text-emerald-200">Easy</p>
        <p class="text-sm text-emerald-100/80 mt-1">Basic concepts and quick warm-up questions.</p>
      </div>
      <div class="rounded-2xl border border-amber-300/30 bg-amber-500/10 p-4 transition hover:-translate-y-0.5">
        <p class="font-bold text-amber-200">Medium</p>
        <p class="text-sm text-amber-100/80 mt-1">Balanced challenge for regular practice and exams.</p>
      </div>
      <div class="rounded-2xl border border-rose-300/30 bg-rose-500/10 p-4 transition hover:-translate-y-0.5">
        <p class="font-bold text-rose-200">Hard</p>
        <p class="text-sm text-rose-100/80 mt-1">Advanced problems for high performers.</p>
      </div>
    </div>
  </section>

  <!-- Why Choose Us Section -->
  <section class="relative max-w-7xl mx-auto px-4 sm:px-6 py-16" id="about">
    <h2 class="text-2xl font-bold mb-8 text-center">Why Choose Us</h2>
    <div class="grid md:grid-cols-3 gap-6">
      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <div class="text-2xl mb-3">⚡</div>
        <p class="text-lg font-bold">Fast Performance</p>
        <p class="text-sm text-white/60 mt-2">Smooth and optimized quiz experience with real-time interaction.</p>
      </div>

      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <div class="text-2xl mb-3">🔒</div>
        <p class="text-lg font-bold">Secure System</p>
        <p class="text-sm text-white/60 mt-2">Built with strong security including hashing, CSRF protection, and validation.</p>
      </div>

      <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-white/10">
        <div class="text-2xl mb-3">📈</div>
        <p class="text-lg font-bold">Smart Analytics</p>
        <p class="text-sm text-white/60 mt-2">Track performance with real-time results and leaderboard insights.</p>
      </div>
    </div>
  </section>
</main>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>