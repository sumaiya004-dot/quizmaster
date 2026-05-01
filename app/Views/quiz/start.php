<?php $pageTitle = 'Quiz Start'; ?>
<?php include APP_PATH . '/Views/partials/head.php'; ?>
<?php include APP_PATH . '/Views/partials/navbar.php'; ?>

<!-- Confirmation Modal -->
<div id="confirmModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:32px;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
    <div style="font-size:48px;text-align:center;margin-bottom:12px;">⚠️</div>
    <h2 style="font-size:1.2rem;font-weight:700;text-align:center;margin-bottom:8px;">Submit Quiz?</h2>
    <p id="modalMessage" style="text-align:center;color:#555;margin-bottom:24px;font-size:0.95rem;"></p>
    <div style="display:flex;gap:12px;">
      <button onclick="closeModal()" style="flex:1;padding:10px;border:2px solid #e5e7eb;border-radius:8px;background:#fff;cursor:pointer;font-weight:600;color:#374151;">Go Back</button>
      <button onclick="forceSubmit()" style="flex:1;padding:10px;border:none;border-radius:8px;background:#6366f1;color:#fff;cursor:pointer;font-weight:600;">Submit Anyway</button>
    </div>
  </div>
</div>

<main class="max-w-5xl mx-auto px-4 py-8 flex-1">
  <button onclick="history.back()" class="btn-ui mb-4" aria-label="Go back">←</button>

  <div class="card-ui p-6 mb-4 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold"><?= xss($quiz['title']) ?></h1>
      <p class="text-sm text-gray-600"><?= xss($quiz['subject_name']) ?> | <?= xss($quiz['difficulty']) ?></p>
    </div>
    <div style="display:flex;align-items:center;gap:12px;">
      <div id="progressBadge" style="font-size:0.8rem;background:#f0fdf4;color:#16a34a;border-radius:8px;padding:4px 12px;font-weight:600;">
        0 / <?= count($questions) ?> answered
      </div>
      <div class="text-sm rounded bg-indigo-50 text-indigo-700 px-3 py-2">Time left: <span id="timer"></span></div>
    </div>
  </div>

  <form method="POST" action="<?= BASE_URL ?>/quiz/submit" id="quizForm" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= xss(generateCsrf()) ?>">
    <input type="hidden" name="quiz_id" value="<?= (int) $quiz['id'] ?>">
    <input type="hidden" name="question_type" value="<?= xss((string) $type) ?>">

    <?php foreach ($questions as $idx => $q): ?>
      <div class="card-ui p-5" id="qcard_<?= (int)$q['id'] ?>">
        <h2 class="font-semibold mb-3">
          <span id="qnum_<?= (int)$q['id'] ?>" style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#e0e7ff;color:#4338ca;font-size:0.75rem;text-align:center;line-height:24px;margin-right:6px;font-weight:700;"><?= $idx + 1 ?></span>
          <?= xss($q['question_text']) ?>
        </h2>
        <?php if (($q['question_type'] ?? '') === 'short'): ?>
          <input type="text" name="q_<?= (int) $q['id'] ?>" class="input-ui track-answer" data-qid="<?= (int)$q['id'] ?>" placeholder="Type your answer">
        <?php else: ?>
          <?php
            $isTf = ($q['question_type'] ?? '') === 'tf';
            $opts = $isTf
              ? ['a' => 'True', 'b' => 'False']
              : ['a' => $q['option_a'], 'b' => $q['option_b'], 'c' => $q['option_c'], 'd' => $q['option_d']];
          ?>
          <div class="grid gap-2">
            <?php foreach ($opts as $key => $label): ?>
              <label class="flex items-center gap-2 rounded-lg px-2 py-2 transition hover:bg-indigo-50">
                <input type="radio" name="q_<?= (int) $q['id'] ?>" value="<?= xss((string) $key) ?>" class="track-answer" data-qid="<?= (int)$q['id'] ?>">
                <span><?= strtoupper($key) ?>) <?= xss((string) $label) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <button type="button" onclick="handleSubmit()" class="btn-primary" style="width:100%;padding:14px;font-size:1rem;">
      Submit Quiz
    </button>
  </form>
</main>

<script>
  const totalQuestions = <?= count($questions) ?>;
  const answeredSet = new Set();

  // Track answered questions
  document.querySelectorAll('.track-answer').forEach(el => {
    el.addEventListener('change', () => {
      const qid = el.dataset.qid;
      const val = el.type === 'text' ? el.value.trim() : el.value;
      if (val) {
        answeredSet.add(qid);
      } else {
        answeredSet.delete(qid);
      }
      updateProgress();
    });
    // For text inputs, also track on input event
    if (el.type === 'text') {
      el.addEventListener('input', () => {
        const qid = el.dataset.qid;
        if (el.value.trim()) {
          answeredSet.add(qid);
        } else {
          answeredSet.delete(qid);
        }
        updateProgress();
      });
    }
  });

  function updateProgress() {
    const count = answeredSet.size;
    const badge = document.getElementById('progressBadge');
    badge.textContent = `${count} / ${totalQuestions} answered`;
    if (count === totalQuestions) {
      badge.style.background = '#f0fdf4';
      badge.style.color = '#16a34a';
    } else {
      badge.style.background = '#fefce8';
      badge.style.color = '#ca8a04';
    }
  }

  function handleSubmit() {
    const unanswered = totalQuestions - answeredSet.size;
    if (unanswered === 0) {
      // All answered — submit directly
      document.getElementById('quizForm').submit();
    } else {
      // Show warning modal
      const msg = unanswered === 1
        ? `You have <strong>1 question</strong> unanswered. It will be marked as wrong.`
        : `You have <strong>${unanswered} questions</strong> unanswered. They will be marked as wrong.`;
      document.getElementById('modalMessage').innerHTML = msg;
      const modal = document.getElementById('confirmModal');
      modal.style.display = 'flex';
    }
  }

  function closeModal() {
    document.getElementById('confirmModal').style.display = 'none';
  }

  function forceSubmit() {
    document.getElementById('quizForm').submit();
  }

  // Close modal on backdrop click
  document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  // Timer
  let remaining = <?= (int) $remaining ?>;
  const timer = document.getElementById('timer');
  const fmt = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;
  const tick = () => {
    timer.textContent = fmt(Math.max(0, remaining));
    if (remaining <= 0) { document.getElementById('quizForm').submit(); return; }
    remaining -= 1;
  };
  tick();
  setInterval(tick, 1000);
</script>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>

