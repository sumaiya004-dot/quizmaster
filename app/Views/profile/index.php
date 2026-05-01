<?php $pageTitle = 'Profile'; ?>
<?php include APP_PATH . '/Views/partials/head.php'; ?>

<main class="min-h-screen flex flex-col bg-slate-50 text-slate-800 flex-1">
  
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 md:py-10 w-full space-y-5 md:space-y-6">
    
    <!-- Back Button -->
    <button onclick="history.back()" 
    class="inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition-colors mb-2 text-sm md:text-base group">
      <i data-lucide="arrow-left" class="w-4 h-4 md:w-5 md:h-5 group-hover:-translate-x-1 transition-transform"></i> Back
    </button>

    <!-- HERO SECTION -->
    <div class="rounded-3xl p-6 sm:p-8 text-white bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg shadow-indigo-100">
      <div class="flex flex-col sm:flex-row items-center sm:items-center gap-5 text-center sm:text-left">
        
        <!-- Avatar Display -->
        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-4xl border border-white/30 shadow-inner">
          <?= xss((string)($user['avatar'] ?? '🎓')) ?>
        </div>

        <div class="flex-1">
          <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">
            Hello, <?= xss((string)$user['name']) ?>!
          </h2>
          <p class="text-sm opacity-90 mt-1">
            Manage your profile and account settings from here.
          </p>
        </div>

      </div>
    </div>

    <!-- PROFILE FORM CARD -->
    <section class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 md:p-10">
      
      <div class="mb-8 border-b border-slate-100 pb-6">
        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
          <i data-lucide="user-cog" class="w-5 h-5 text-indigo-600"></i> Account Details
        </h3>
        <p class="text-slate-500 text-sm mt-1">
          Update your personal information and profile picture.
        </p>
      </div>

      <!-- SUCCESS / ERROR MESSAGES -->
      <?php if (!empty($success)): ?>
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 text-sm flex items-center gap-3 animate-in fade-in">
          <i data-lucide="check-circle" class="w-5 h-5"></i> 
          <span class="font-medium"><?= xss($success) ?></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700 text-sm animate-in zoom-in-95">
          <div class="font-bold mb-2 flex items-center gap-2">
             <i data-lucide="alert-triangle" class="w-4 h-4"></i> Please fix the following:
          </div>
          <ul class="list-disc list-inside space-y-1 ml-1">
            <?php foreach ($errors as $err): ?>
              <li><?= xss($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- PROFILE FORM -->
      <form method="POST" action="<?= BASE_URL ?>/profile/index" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= xss(generateCsrf()) ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          <!-- Full Name -->
          <div class="md:col-span-2 space-y-2">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">
              Full Name
            </label>
            <input type="text" name="name" value="<?= xss((string)$user['name']) ?>" required
            class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400"
            placeholder="Your full name">
          </div>

          <!-- Email Address -->
          <div class="space-y-2">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">
              Email Address
            </label>
            <input type="email" name="email" value="<?= xss((string)$user['email']) ?>" required
            class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all"
            placeholder="email@example.com">
          </div>

          <!-- Avatar Emoji -->
          <div class="space-y-2">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">
              Avatar (Emoji)
            </label>
            <input type="text" name="avatar" value="<?= xss((string)($user['avatar'] ?? '🎓')) ?>" 
            class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all"
            placeholder="Paste an emoji (e.g. 👨‍💻)">
          </div>

        </div>

        <!-- Action Buttons -->
        <div class="pt-4 flex flex-col sm:flex-row gap-3">
          <button type="submit" 
          class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-xl bg-indigo-600 px-10 py-4 font-bold text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:shadow-none transition-all active:scale-95">
            <i data-lucide="save" class="w-5 h-5 mr-2"></i> Save Changes
          </button>
          
          <button type="reset" 
          class="flex-1 sm:flex-none inline-flex items-center justify-center rounded-xl bg-slate-100 px-8 py-4 font-bold text-slate-600 hover:bg-slate-200 transition-all">
            Reset
          </button>
        </div>

      </form>
    </section>
  </div>
</main>

<script>
  // Initialize Lucide icons
  if (window.lucide) {
    lucide.createIcons();
  }
</script>

<?php include APP_PATH . '/Views/partials/footer.php'; ?>