<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
$csrf = generateCSRF();
$pageTitle = $pageTitle ?? 'QuizMaster Pro';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — QuizMaster Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        .gradient-brand { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); }
        .card-hover { transition: all .25s ease; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(99,102,241,.15); }
        .diff-low    { background:#dcfce7; color:#166534; }
        .diff-medium { background:#fef9c3; color:#854d0e; }
        .diff-high   { background:#fee2e2; color:#991b1b; }
        @keyframes pulse-ring { 0%,100%{opacity:1} 50%{opacity:.4} }
        .timer-warning { animation: pulse-ring 1s infinite; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">
<?php if (isLoggedIn()): ?>
<nav class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <a href="<?= $_SESSION['user_role']==='admin' ? '/admin_manage.php' : '/dashboard.php' ?>"
               class="font-display text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                ⚡ QuizMaster Pro
            </a>
            <div class="flex items-center gap-4">
                <?php if ($_SESSION['user_role'] === 'student'): ?>
                <a href="/dashboard.php" class="text-sm text-gray-600 hover:text-indigo-600 font-medium transition">Dashboard</a>
                <?php else: ?>
                <a href="/admin_manage.php" class="text-sm text-gray-600 hover:text-indigo-600 font-medium transition">Manage</a>
                <?php endif; ?>
                <div class="flex items-center gap-2 bg-gray-100 rounded-full px-3 py-1.5">
                    <span class="text-base"><?= htmlspecialchars($_SESSION['user_avatar'] ?? '🎓') ?></span>
                    <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                    <?php if ($_SESSION['user_role']==='admin'): ?>
                    <span class="text-xs bg-indigo-100 text-indigo-700 rounded-full px-2 py-0.5 font-semibold">Admin</span>
                    <?php endif; ?>
                </div>
                <a href="/logout.php" class="text-sm text-red-500 hover:text-red-700 font-medium transition">Logout</a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>
<input type="hidden" id="csrf_token" value="<?= $csrf ?>">
