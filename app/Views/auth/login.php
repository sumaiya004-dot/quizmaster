<?php $pageTitle = 'Login'; ?>
<?php include APP_PATH . '/Views/partials/head.php'; ?>

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 bg-slate-50 text-slate-900">
  <div class="w-full max-w-lg mx-auto">

    <!-- Logo Section -->
    <div class="flex justify-center mb-8">
      <div class="flex items-center gap-2">
        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100">
          <i data-lucide="zap" class="w-6 h-6 text-white fill-white"></i>
        </div>
        <span class="font-bold text-2xl tracking-tight text-slate-900">QuizMaster</span>
      </div>
    </div>

    <!-- Title & Subtitle -->
    <div class="mb-8 text-center">
      <h2 class="text-3xl font-bold tracking-tight text-slate-900">Welcome back</h2>
      <p class="text-slate-500 mt-2 text-sm sm:text-base">Enter your credentials to access your dashboard.</p>
    </div>

    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
      <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700 animate-in fade-in">
        <?php foreach ($errors as $e): ?>
          <div class="flex items-center gap-2 mb-1">
            <i data-lucide="alert-circle" class="w-4 h-4"></i> <?= xss($e) ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Login Form Card -->
    <div class="bg-white rounded-3xl p-6 md:p-10 shadow-sm border border-slate-200 w-full">
      <form method="POST" action="<?= BASE_URL ?>/auth/login" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= xss($csrf) ?>">

        <div class="space-y-1">
          <label class="text-xs font-bold text-slate-500 uppercase ml-1">Email Address</label>
          <input type="email" name="email" value="<?= xss($old['email'] ?? '') ?>" required 
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition outline-none text-sm sm:text-base"
          placeholder="name@example.com">
        </div>

        <div class="space-y-1">
          <div class="flex justify-between items-center px-1">
            <label class="text-xs font-bold text-slate-500 uppercase">Password</label>
            <a href="#" class="text-xs font-semibold text-indigo-600 hover:underline">Forgot?</a>
          </div>
          <input type="password" name="password" required 
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition outline-none text-sm sm:text-base"
          placeholder="••••••••">
        </div>

        <button type="submit" class="w-full rounded-xl bg-indigo-600 py-4 font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-none transition active:scale-[0.98]">
          Sign In
        </button>
      </form>
    </div>

    <!-- Sign-up Link -->
    <p class="mt-8 text-center text-sm text-slate-500">
      Don't have an account? 
      <a href="<?= BASE_URL ?>/auth/signup" class="text-indigo-600 font-bold hover:underline">Create one for free</a>
    </p>

  </div>
</div>

<script>lucide.createIcons();</script>
<?php include APP_PATH . '/Views/partials/footer.php'; ?>