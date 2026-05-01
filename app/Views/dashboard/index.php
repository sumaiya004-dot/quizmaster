<?php 
$pageTitle = 'Dashboard';
include APP_PATH . '/Views/partials/head.php'; 
?>

<main class="flex-1">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 md:py-10 w-full">
    
    <!-- Back Button -->
    <button onclick="history.back()" 
    class="inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600 transition text-sm font-medium mb-4 md:mb-6">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
    </button>

    <!-- HERO SECTION -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-3xl p-6 md:p-8 shadow-md mb-8 transition hover:shadow-lg">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
          <h2 class="text-2xl sm:text-4xl font-bold tracking-tight">
            Hello, <?= explode(' ', $_SESSION['user_name'] ?? 'Student')[0] ?>!
          </h2>
          <p class="opacity-90 mt-2">Ready to challenge yourself today? Pick a subject and show your skills.</p>
        </div>
        
        <div class="flex gap-4">
          <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
            <p class="text-[10px] uppercase opacity-70 tracking-wider">Quizzes</p>
            <p class="text-2xl font-bold"><?= (int)($quizCount ?? 0) ?></p>
          </div>
          <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20">
            <p class="text-[10px] uppercase opacity-70 tracking-wider">Avg Score</p>
            <p class="text-2xl font-bold"><?= (int)($avgScore ?? 0) ?>%</p>
          </div>
        </div>
      </div>
    </div>

    <!-- FILTER FORM -->
    <section class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 mb-8">
      <h3 class="text-lg font-bold mb-6 text-slate-800">Start a Quiz (Customize)</h3>
      
      <!-- Grid updated to 4 columns for desktop to accommodate the new select -->
      <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Subject -->
        <div class="space-y-1.5">
          <label class="text-xs font-bold text-slate-500 uppercase ml-1 tracking-tight">Subject</label>
          <select name="subject" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
            <option value="">All Subjects</option>
            <?php foreach ($subjects as $s): ?>
              <option value="<?= (int)$s['id'] ?>" <?= (isset($_GET['subject']) && $_GET['subject'] == $s['id']) ? 'selected' : '' ?>>
                <?= xss($s['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Difficulty -->
        <div class="space-y-1.5">
          <label class="text-xs font-bold text-slate-500 uppercase ml-1 tracking-tight">Difficulty</label>
          <select name="difficulty" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
            <option value="">Any Difficulty</option>
            <option value="low" <?= (isset($_GET['difficulty']) && $_GET['difficulty'] == 'low') ? 'selected' : '' ?>>Easy</option>
            <option value="medium" <?= (isset($_GET['difficulty']) && $_GET['difficulty'] == 'medium') ? 'selected' : '' ?>>Medium</option>
            <option value="high" <?= (isset($_GET['difficulty']) && $_GET['difficulty'] == 'high') ? 'selected' : '' ?>>Hard</option>
          </select>
        </div>

        <!-- Quiz Type (The New Addition) -->
        <div class="space-y-1.5">
          <label class="text-xs font-bold text-slate-500 uppercase ml-1 tracking-tight">Quiz Type</label>
          <select name="type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
            <option value="">All Types</option>
            <option value="mcq" <?= (isset($_GET['type']) && $_GET['type'] == 'mcq') ? 'selected' : '' ?>>MCQ</option>
            <option value="tf" <?= (isset($_GET['type']) && $_GET['type'] == 'tf') ? 'selected' : '' ?>>True/False</option>
            <option value="short" <?= (isset($_GET['type']) && $_GET['type'] == 'short') ? 'selected' : '' ?>>Short Answer</option>
          </select>
        </div>

        <!-- Button -->
        <div class="flex items-end">
          <button class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 active:scale-95">
            Apply Selection
          </button>
        </div>
      </form>
    </section>

    <!-- QUIZ LIST -->
    <div class="space-y-4">
      <h3 class="text-lg font-bold text-slate-800 ml-1">Recommended Quizzes</h3>
      
      <div class="grid gap-4">
        <?php if(!empty($quizzes)): foreach($quizzes as $quiz): ?>
          <div class="group bg-white p-5 rounded-2xl border border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4 hover:shadow-md hover:border-indigo-200 transition-all">
            <div class="flex items-center gap-4 w-full md:w-auto">
              <div class="w-14 h-14 bg-slate-50 text-indigo-600 rounded-2xl flex items-center justify-center font-bold text-2xl group-hover:bg-indigo-50 transition-colors">
                🎯
              </div>
              <div>
                <h4 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors"><?= xss($quiz['title']) ?></h4>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                   <p class="text-sm text-slate-500 flex items-center gap-1.5">
                     <i data-lucide="clock" class="w-3.5 h-3.5"></i> <?= (int)($quiz['time_limit'] / 60) ?> min
                   </p>
                   <p class="text-sm text-slate-500 flex items-center gap-1.5">
                     <i data-lucide="help-circle" class="w-3.5 h-3.5"></i> <?= (int)$quiz['question_count'] ?> Questions
                   </p>
                </div>
              </div>
            </div>
            <a href="<?= BASE_URL ?>/quiz/start/<?= (int)$quiz['id'] ?>" class="w-full md:w-auto bg-slate-900 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-indigo-600 transition text-center shadow-sm">
              Start Now
            </a>
          </div>
        <?php endforeach; else: ?>
          <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
             <p class="text-slate-400 font-medium">No quizzes found matching your current filter.</p>
             <a href="<?= BASE_URL ?>/dashboard" class="text-indigo-600 text-sm font-bold mt-2 inline-block">Clear all filters</a>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</main>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>