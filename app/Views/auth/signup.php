<?php $pageTitle = 'Create Account'; ?>
<?php include APP_PATH . '/Views/partials/head.php'; ?>

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 bg-slate-50 text-slate-900">
  <div class="w-full max-w-lg mx-auto">

    <!-- Back Button -->
    <button onclick="history.back()" class="inline-flex items-center gap-2 text-slate-400 hover:text-indigo-600 transition text-sm font-medium mb-6">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
    </button>

    <!-- Title Section -->
    <div class="mb-8">
      <h2 class="text-3xl font-bold tracking-tight text-slate-900">Create account</h2>
      <p class="text-slate-500 mt-2 text-sm sm:text-base">Join QuizMaster now and start your journey.</p>
    </div>

    <!-- Success Message -->
    <?php if (!empty($success)): ?>
      <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700 animate-in fade-in">
        Account created successfully. <a class="font-bold underline" href="<?= BASE_URL ?>/auth/login">Sign in now</a>.
      </div>
    <?php endif; ?>

    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
      <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
        <?php foreach ($errors as $e): ?>
          <div class="flex items-center gap-2 mb-1">
            <i data-lucide="alert-circle" class="w-4 h-4"></i> <?= xss($e) ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Signup Form Card -->
    <div class="bg-white rounded-3xl p-6 md:p-10 shadow-sm border border-slate-200 w-full">
      <form method="POST" action="<?= BASE_URL ?>/auth/signup" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= xss($csrf) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <!-- Full Name (Full Width) -->
          <div class="space-y-1 md:col-span-2">
            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Full Name</label>
            <input type="text" name="name" value="<?= xss($old['name'] ?? '') ?>" required 
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition outline-none"
            placeholder="John Doe">
          </div>

          <!-- Email Address (Full Width) -->
          <div class="space-y-1 md:col-span-2">
            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Email Address</label>
            <input type="email" name="email" value="<?= xss($old['email'] ?? '') ?>" required 
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition outline-none"
            placeholder="name@example.com">
          </div>

          <!-- Password -->
          <div class="space-y-1">
            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Password</label>
            <input type="password" name="password" required 
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition outline-none"
            placeholder="••••••••">
          </div>

          <!-- Confirm Password -->
          <div class="space-y-1">
            <label class="text-xs font-bold text-slate-500 uppercase ml-1">Confirm</label>
            <input type="password" name="confirm" required 
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition outline-none"
            placeholder="••••••••">
          </div>
        </div>

        <button type="submit" class="w-full mt-4 rounded-xl bg-indigo-600 py-4 font-bold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition active:scale-[0.98]">
          Create Account
        </button>
      </form>
    </div>

    <!-- Login Link -->
    <p class="mt-8 text-center text-sm text-slate-500">
      Already have an account? 
      <a href="<?= BASE_URL ?>/auth/login" class="text-indigo-600 font-bold hover:underline">Sign in instead</a>
    </p>

  </div>
</div>

<script>lucide.createIcons();</script>
<?php include APP_PATH . '/Views/partials/footer.php'; ?>