<?php $pageTitle = 'Leaderboard'; ?>
<?php include APP_PATH . '/Views/partials/head.php'; ?>
<?php include APP_PATH . '/Views/partials/navbar.php'; ?>

<!-- STEP 1: Body logic applied via main wrapper (Clean bg-slate-50) -->
<main class="min-h-screen flex flex-col bg-slate-50 text-slate-900 flex-1">

  <!-- MAIN CONTAINER ALIGN -->
  <div class="max-w-5xl mx-auto px-4 py-8 md:py-10 w-full space-y-6 md:space-y-8">
    
    <!-- BACK BUTTON FIX -->
    <button onclick="history.back()" class="bg-white p-2.5 rounded-xl shadow-sm border border-slate-200 hover:bg-slate-50 hover:border-indigo-200 transition inline-flex items-center justify-center group" aria-label="Go back">
      <i data-lucide="arrow-left" class="w-5 h-5 text-slate-400 group-hover:text-indigo-600 transition"></i>
    </button>

    <!-- HERO SECTION POLISH -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-3xl p-6 md:p-8 shadow-md hover:shadow-lg transition">
      <h2 class="text-2xl md:text-3xl font-bold tracking-tight">🏆 Leaderboard</h2>
      <p class="text-xs md:text-sm opacity-80 mt-1">
        Track top students, compare performance, and see your rank progress.
      </p>
    </div>

    <?php
      $currentUserId = (int)($_SESSION['user_id'] ?? 0);
      $myRank = null;
      foreach ($leaderboard as $idx => $entry) {
        if ((int)$entry['id'] === $currentUserId) {
          $myRank = $idx + 1;
          break;
        }
      }
      $topThree = array_slice($leaderboard, 0, 3);
    ?>

    <!-- TOP USER CARDS UPGRADE -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
      <?php foreach ($topThree as $idx => $entry): ?>
        <?php
          $badge = $idx === 0 ? '🥇 Gold' : ($idx === 1 ? '🥈 Silver' : '🥉 Bronze');
          $badgeClass = $idx === 0 ? 'bg-amber-100 text-amber-700' : ($idx === 1 ? 'bg-slate-100 text-slate-700' : 'bg-orange-100 text-orange-700');
        ?>
        <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-200 hover:shadow-md transition hover:-translate-y-1">
          <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full <?= $badgeClass ?>">
            <?= $badge ?>
          </span>
          <p class="mt-4 text-lg md:text-xl font-bold text-slate-900 truncate"><?= xss((string)$entry['name']) ?></p>
          <div class="mt-2 flex items-center gap-3 text-sm text-slate-500 font-medium">
            <span class="text-indigo-600"><?= (int)$entry['total_score'] ?> pts</span>
            <span class="opacity-30">•</span>
            <span><?= xss((string)($entry['avg_pct'] ?? 0)) ?>% avg</span>
          </div>
        </div>
      <?php endforeach; ?>
    </section>

    <!-- TABLE SECTION -->
    <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 md:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h2 class="text-xl font-bold text-slate-900">All Students Ranking</h2>
        <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 flex items-center gap-2">
          <span class="text-sm text-indigo-600 font-medium">Your Rank:</span>
          <span class="font-bold text-indigo-700 text-lg">#<?= $myRank ?? 'N/A' ?></span>
        </div>
      </div>

      <!-- TABLE SCROLL FIX APPLIED HERE -->
      <div class="overflow-x-auto -mx-4 md:mx-0">
        <div class="inline-block min-w-full align-middle">
          <div class="overflow-hidden border border-slate-100 md:rounded-xl">
            <table class="min-w-full text-sm">
              <thead class="bg-slate-50 text-slate-500 text-[10px] md:text-xs uppercase tracking-wider">
                <tr class="text-left">
                  <th class="py-4 px-4 font-bold"># Rank</th>
                  <th class="py-4 px-4 font-bold">Student</th>
                  <th class="py-4 px-4 font-bold text-center">Quizzes</th>
                  <th class="py-4 px-4 font-bold text-center">Avg %</th>
                  <th class="py-4 px-4 font-bold text-right">Points</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <?php foreach ($leaderboard as $i => $entry): ?>
                  <?php $isMe = (int)$entry['id'] === $currentUserId; ?>
                  <tr class="transition <?= $isMe ? 'bg-indigo-50/50' : 'hover:bg-slate-50' ?>">
                    <td class="py-4 px-4">
                      <span class="<?= $i < 3 ? 'font-bold text-indigo-600' : 'text-slate-400 font-medium' ?>">
                        <?= sprintf("%02d", $i + 1) ?>
                      </span>
                    </td>
                    <td class="py-4 px-4 whitespace-nowrap">
                      <div class="flex items-center gap-2">
                        <span class="text-slate-900 font-semibold"><?= xss((string)$entry['name']) ?></span>
                        <?php if ($isMe): ?>
                          <span class="bg-indigo-600 text-white text-[9px] px-1.5 py-0.5 rounded uppercase font-bold">Me</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td class="py-4 px-4 text-center text-slate-600"><?= (int)$entry['total_quizzes'] ?></td>
                    <td class="py-4 px-4 text-center">
                      <span class="bg-white border border-slate-200 px-2 py-1 rounded-lg text-slate-700 font-bold text-[11px]">
                        <?= xss((string)($entry['avg_pct'] ?? 0)) ?>%
                      </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                      <span class="text-indigo-700 font-bold"><?= number_format((int)$entry['total_score']) ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

<script>
  lucide.createIcons();
</script>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>