<?php
declare(strict_types=1);

function hashPassword(string $plain): string
{
    return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword(string $plain, string $hash): bool
{
    return password_verify($plain, $hash);
}

function xss(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitizeInput(string $value): string
{
    return trim(strip_tags($value));
}

function generateCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}

function difficultyBadge(string $level): string
{
    $map = [
        'low' => ['Easy', 'bg-green-100 text-green-800'],
        'medium' => ['Medium', 'bg-yellow-100 text-yellow-800'],
        'high' => ['Hard', 'bg-red-100 text-red-800'],
    ];
    [$label, $cls] = $map[$level] ?? ['Unknown', 'bg-gray-100 text-gray-700'];
    return '<span class="inline-block text-xs px-2 py-1 rounded ' . $cls . '">' . xss($label) . '</span>';
}

function typeBadge(string $type): string
{
    $map = [
        'all' => 'All',
        'mcq' => 'MCQ',
        'tf' => 'True/False',
        'short' => 'Short',
        'short_answer' => 'Short',
    ];
    return '<span class="inline-block text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-700">' . xss($map[$type] ?? 'Unknown') . '</span>';
}

function percentColor(int $pct): string
{
    if ($pct >= 80) return 'text-green-600';
    if ($pct >= 60) return 'text-yellow-600';
    if ($pct >= 40) return 'text-orange-600';
    return 'text-red-600';
}
