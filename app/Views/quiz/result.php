<?php $pageTitle = 'Quiz Result'; ?>
<?php include APP_PATH . '/Views/partials/head.php'; ?>
<?php include APP_PATH . '/Views/partials/navbar.php'; ?>

<main class="max-w-5xl mx-auto px-4 py-8 flex-1">
  <button onclick="history.back()" class="btn-ui mb-4" aria-label="Go back">←</button>
  <div class="card-ui p-6 mb-4">
    <h1 class="text-2xl font-bold">Result</h1>
    <p class="text-sm text-gray-600 mt-1"><?= xss($result['title']) ?> | <?= xss($result['subject_name']) ?></p>
    <p class="text-3xl font-extrabold text-indigo-700 mt-3"><?= (int) $result['score'] ?> / <?= (int) $result['total_marks'] ?> (<?= (int) $pct ?>%)</p>
  </div>

  <div class="space-y-3">
    <?php foreach ($breakdown as $i => $b): ?>
      <div class="rounded-xl border p-4 transition hover:-translate-y-0.5 <?= !empty($b['is_correct']) ? 'border-green-200 bg-green-50/30' : 'border-red-200 bg-red-50/30' ?>">
        <p class="font-semibold">Q<?= $i + 1 ?>. <?= xss($b['question']) ?></p>
        <p class="text-sm mt-1">Your answer: <strong><?= xss((string) ($b['selected'] ?? '')) ?></strong></p>
        <p class="text-sm">Correct: <strong><?= xss((string) ($b['correct'] ?? '')) ?></strong></p>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>

