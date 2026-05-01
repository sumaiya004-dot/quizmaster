<nav class="sticky top-0 z-50 border-b border-slate-200 bg-white/80 backdrop-blur-md text-slate-900">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">
      
      <!-- Brand Logo -->
      <a href="<?= BASE_URL ?>" class="font-bold text-indigo-600 no-underline flex items-center gap-2">
        <span class="bg-indigo-600 text-white p-1 rounded-lg">⚡</span>
        <span>QuizMaster <span class="text-[10px] text-slate-400 font-normal border border-slate-200 px-1 rounded">PRO</span></span>
      </a>

      <!-- Desktop Menu (Hidden on Mobile) -->
      <div class="hidden md:flex items-center gap-4">
        <a href="<?= BASE_URL ?>#features" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition no-underline">Features</a>
        <a href="<?= BASE_URL ?>/leaderboard/index" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition no-underline">Leaderboard</a>
        
        <div class="h-4 w-[1px] bg-slate-200 mx-2"></div>

        <?php if (!empty($_SESSION['user_id'])): ?>
          <a href="<?= BASE_URL ?>/dashboard" class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 text-sm font-medium no-underline hover:bg-slate-50 transition">
            <span><?= xss((string)($_SESSION['user_avatar'] ?? '🎓')) ?></span>
            <span><?= xss(explode(' ', (string)($_SESSION['user_name'] ?? 'User'))[0]) ?></span>
          </a>
          <a href="<?= BASE_URL ?>/profile/index" class="text-sm font-medium text-slate-600 hover:text-indigo-600 no-underline">Profile</a>
          <a href="<?= BASE_URL ?>/auth/logout" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 text-sm font-bold no-underline hover:bg-rose-100 transition">Logout</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/auth/login" class="text-sm font-medium text-slate-600 hover:text-indigo-600 no-underline px-3">Sign In</a>
          <a href="<?= BASE_URL ?>/auth/signup" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold no-underline hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition">Get Started</a>
        <?php endif; ?>
      </div>

      <!-- Mobile Menu Button (Lucide Icon used) -->
      <button id="menuBtn" class="md:hidden p-2 rounded-xl hover:bg-slate-100 transition">
        <i data-lucide="menu" class="w-6 h-6 text-slate-600"></i>
      </button>
    </div>

    <!-- Mobile Menu (Dropdown) -->
    <div id="mobileMenu" class="hidden md:hidden pb-6 pt-2 border-t border-slate-100 animate-in fade-in slide-in-from-top-4 duration-200">
      <div class="flex flex-col gap-2">
        <a href="<?= BASE_URL ?>#features" class="px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 no-underline font-medium">Features</a>
        <a href="<?= BASE_URL ?>/leaderboard/index" class="px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 no-underline font-medium">Leaderboard</a>
        
        <?php if (!empty($_SESSION['user_id'])): ?>
          <div class="h-[1px] bg-slate-100 my-1"></div>
          <a href="<?= BASE_URL ?>/dashboard" class="px-4 py-3 rounded-xl bg-indigo-50 text-indigo-700 no-underline font-bold flex items-center gap-2">
            <span><?= xss((string)($_SESSION['user_avatar'] ?? '🎓')) ?></span> Dashboard
          </a>
          <a href="<?= BASE_URL ?>/profile/index" class="px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 no-underline font-medium">Profile</a>
          <a href="<?= BASE_URL ?>/settings/index" class="px-4 py-3 rounded-xl hover:bg-slate-50 text-slate-700 no-underline font-medium">Settings</a>
          <a href="<?= BASE_URL ?>/auth/logout" class="mx-4 mt-2 px-4 py-3 rounded-xl bg-rose-600 text-white text-center no-underline font-bold shadow-md">Logout</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/auth/login" class="px-4 py-3 rounded-xl border border-slate-200 text-center text-slate-700 no-underline font-bold mt-2">Sign In</a>
          <a href="<?= BASE_URL ?>/auth/signup" class="px-4 py-3 rounded-xl bg-indigo-600 text-white text-center no-underline font-bold shadow-md shadow-indigo-100">Get Started</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<script>
  // Mobile Menu Toggle Logic
  const menuBtn = document.getElementById('menuBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  menuBtn.onclick = () => {
    mobileMenu.classList.toggle('hidden');
    // Change icon between menu and x
    const icon = menuBtn.querySelector('i');
    const isHidden = mobileMenu.classList.contains('hidden');
    icon.setAttribute('data-lucide', isHidden ? 'menu' : 'x');
    if (typeof lucide !== 'undefined') lucide.createIcons();
  };
</script>