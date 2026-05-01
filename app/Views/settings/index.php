<?php $pageTitle = 'Settings'; ?>
<?php include APP_PATH . '/Views/partials/head.php'; ?>
<?php include APP_PATH . '/Views/partials/navbar.php'; ?>

<!-- CSS Upgrade (Inline for immediate effect, should be in your main.css) -->
<style>
  .btn-primary {
    min-height: 44px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    padding: 0.65rem 1.2rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    transition: all .25s ease;
    border: none;
    cursor: pointer;
  }
  .btn-primary:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.35);
    filter: brightness(1.05);
  }
  .input-ui {
    width: 100%;
    min-height: 44px;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0.6rem 0.85rem;
    transition: all .2s ease;
  }
  .input-ui:hover {
    border-color: #c7d2fe;
    background: #fff;
  }
  .input-ui:focus {
    outline: none;
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
  .card-upgrade {
    background: #fff;
    padding: 1.5rem;
    border-radius: 1.25rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }
  .card-upgrade:hover {
    box-shadow: 0 10px 20px rgba(0,0,0,0.04);
  }
</style>

<main class="min-h-screen flex flex-col bg-slate-50 text-slate-800 flex-1">

  <!-- STEP 1: MAIN WRAPPER -->
  <div class="max-w-5xl mx-auto px-4 py-10 w-full space-y-8">
    
    <!-- STEP 8: BACK BUTTON POLISH -->
    <button onclick="history.back()" class="inline-flex items-center gap-2 text-slate-400 hover:text-indigo-600 transition text-sm font-medium mb-2">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Dashboard
    </button>

    <!-- STEP 2 & 4: TITLE SECTION POLISH -->
    <div class="text-center">
      <h1 class="text-3xl font-bold tracking-tight text-slate-900">Settings</h1>
      <p class="text-slate-500 text-sm mt-1">
        Manage password, notes, and account safety
      </p>
    </div>

    <!-- STEP 3: 2 COLUMN GRID -->
    <div class="grid md:grid-cols-2 gap-8">

      <!-- STEP 4 & 5: PASSWORD CARD UPGRADE -->
      <section class="card-upgrade flex flex-col">
        <h3 class="font-semibold text-slate-900 text-lg mb-4">Password Settings</h3>
        
        <?php if (!empty($pwdError)): ?><div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"><?= xss($pwdError) ?></div><?php endif; ?>
        <?php if (!empty($pwdSuccess)): ?><div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700"><?= xss($pwdSuccess) ?></div><?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/settings/index" class="space-y-4 flex-1 flex flex-col">
          <input type="hidden" name="csrf_token" value="<?= xss(generateCsrf()) ?>">
          <input type="hidden" name="action" value="password">
          
          <input type="password" name="current_password" placeholder="Current password" class="input-ui" required>
          <input type="password" name="new_password" placeholder="New password" class="input-ui" required>
          <input type="password" name="confirm_password" placeholder="Confirm new password" class="input-ui" required>

          <!-- STEP 6: BUTTON ALIGN FIX -->
          <div class="mt-auto pt-6 flex justify-end">
            <button class="btn-primary w-full md:w-auto px-10">Update Password</button>
          </div>
        </form>
      </section>

      <!-- STEP 5 & 7: NOTES CARD POLISH -->
      <section class="card-upgrade">
        <h3 class="font-semibold text-slate-900 text-lg mb-4">Auto Saved Notes</h3>
        <p class="text-sm text-slate-500 mb-4">
          Your personal thoughts and snippets, saved instantly as you type.
        </p>

        <textarea id="notesArea" class="input-ui h-44 resize-none leading-relaxed" placeholder="Start typing your notes here..."><?= xss($notes) ?></textarea>
        
        <div class="flex justify-between items-center mt-3">
          <div class="flex items-center gap-2">
            <div id="noteIndicator" class="w-2 h-2 rounded-full bg-slate-300"></div>
            <p id="noteStatus" class="text-xs text-slate-400 font-medium tracking-wide">Idle</p>
          </div>
          <span class="text-[10px] font-bold uppercase tracking-widest text-slate-300">Cloud Sync Active</span>
        </div>
      </section>

    </div>

    <!-- STEP 6: DANGER ZONE (REFINED) -->
    <section class="bg-white p-8 rounded-2xl border border-red-100 shadow-sm hover:shadow-md transition">
      <div class="flex items-center gap-3 mb-4">
        <div class="p-2 bg-red-50 rounded-lg">
          <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
        </div>
        <h3 class="text-red-600 font-bold text-lg">Danger Zone — Delete Account</h3>
      </div>

      <?php if (!empty($dangerError)): ?><div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"><?= xss($dangerError) ?></div><?php endif; ?>

      <p class="text-sm text-slate-500 mb-5 leading-relaxed">
        Permanently remove your account and all associated data. Type <span class="font-mono font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">DELETE</span> to confirm this action.
      </p>

      <form method="POST" action="<?= BASE_URL ?>/settings/index" class="flex flex-col sm:flex-row gap-4">
        <input type="hidden" name="csrf_token" value="<?= xss(generateCsrf()) ?>">
        <input type="hidden" name="action" value="delete_account">
        
        <input type="text" name="confirm_delete" class="input-ui sm:max-w-xs" placeholder="Type DELETE" required>
        <button class="btn-danger min-h-[44px] rounded-xl px-6 font-bold bg-red-600 hover:bg-red-700 text-white transition transform hover:-translate-y-0.5">Delete My Account</button>
      </form>
    </section>

  </div>
</main>

<script>
  lucide.createIcons();

  const notesArea = document.getElementById('notesArea');
  const noteStatus = document.getElementById('noteStatus');
  const noteIndicator = document.getElementById('noteIndicator');
  let timer = null;

  async function saveNotes() {
    try {
      const fd = new FormData();
      fd.append('csrf_token', <?= json_encode($_SESSION['csrf_token'] ?? '') ?>);
      fd.append('content', notesArea.value);
      
      noteStatus.textContent = 'Saving...';
      noteIndicator.className = 'w-2 h-2 rounded-full bg-indigo-500 animate-pulse';

      const res = await fetch('<?= BASE_URL ?>/settings/saveNotes', { method: 'POST', body: fd });
      const data = await res.json();
      
      if (!res.ok || !data.ok) throw new Error(data.message || 'Save failed');
      
      noteStatus.textContent = 'Synced to cloud';
      noteIndicator.className = 'w-2 h-2 rounded-full bg-emerald-500';
      setTimeout(() => {
        noteStatus.textContent = 'Idle';
        noteIndicator.className = 'w-2 h-2 rounded-full bg-slate-300';
      }, 2000);
    } catch (err) {
      noteStatus.textContent = 'Sync failed';
      noteIndicator.className = 'w-2 h-2 rounded-full bg-rose-500';
    }
  }

  notesArea?.addEventListener('input', () => {
    noteStatus.textContent = 'Typing...';
    noteIndicator.className = 'w-2 h-2 rounded-full bg-amber-400';
    clearTimeout(timer);
    timer = setTimeout(saveNotes, 1000);
  });
</script>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>