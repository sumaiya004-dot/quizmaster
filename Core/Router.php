<?php
declare(strict_types=1);

namespace Core;

class Router
{
    private string $controller = 'HomeController';
    private string $method = 'index';
    private array $params = [];

    private const CTRL_NAMESPACE = 'App\\Controllers\\';
    private const AUTH_REQUIRED = ['DashboardController', 'QuizController', 'LeaderboardController', 'ProfileController', 'SettingsController'];

    public function dispatch(): void
    {
        $url = $this->parseUrl();

        // ✅ 1. Controller detect
        if (!empty($url[0])) {
            $ctrlName = ucfirst(strtolower($url[0])) . 'Controller';
            $ctrlClass = self::CTRL_NAMESPACE . $ctrlName;

            if (class_exists($ctrlClass)) {
                $this->controller = $ctrlName;
                unset($url[0]);
            } else {
                $this->notFound();
                return;
            }
        }

        // ✅ 2. Method detect (FIXED CASE ISSUE)
        if (!empty($url[1])) {
            $methodInput = strtolower((string) $url[1]);

            $controllerClass = self::CTRL_NAMESPACE . $this->controller;
            $methods = get_class_methods($controllerClass);

            $foundMethod = null;

            foreach ($methods as $m) {
                if (strtolower($m) === $methodInput) {
                    $foundMethod = $m;
                    break;
                }
            }

            if ($foundMethod !== null) {
                $this->method = $foundMethod;
                unset($url[1]);
            } else {
                $this->notFound();
                return;
            }
        }

        // ✅ 3. Params
        $this->params = $url ? array_values($url) : [];

        // ✅ 4. Auth check
        if (in_array($this->controller, self::AUTH_REQUIRED, true) && empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // ✅ 5. Call controller
        $ctrlClass = self::CTRL_NAMESPACE . $this->controller;
        $controllerInstance = new $ctrlClass();

        call_user_func_array([$controllerInstance, $this->method], $this->params);
    }

    private function parseUrl(): array
    {
        // ✅ ?url= support
        if (!empty($_GET['url'])) {
            $url = rtrim((string) $_GET['url'], '/');
            return explode('/', filter_var($url, FILTER_SANITIZE_URL));
        }

        // ✅ Clean URL support (XAMPP FIX)
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        // remove query string
        $uri = strtok($uri, '?');

        // auto detect base folder
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDir = str_replace('\\', '/', $scriptDir);

        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = trim($uri, '/');

        if ($uri === '') {
            return [];
        }

        return explode('/', filter_var($uri, FILTER_SANITIZE_URL));
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — QuizMaster</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white flex items-center justify-center p-4">
  <div class="max-w-xl w-full text-center rounded-3xl border border-white/10 bg-white/5 p-8">
    <p class="text-6xl mb-3">404</p>
    <h1 class="text-2xl md:text-3xl font-bold">Page not found</h1>
    <p class="text-white/70 mt-2">The link may be broken or the page may have moved.</p>
    <div class="mt-6 flex justify-center gap-3">
      <a href="' . BASE_URL . '" class="inline-flex items-center rounded-xl bg-indigo-500 px-5 py-2.5 font-semibold">Go Home</a>
      <button onclick="history.back()" class="rounded-xl border border-white/20 px-5 py-2.5">←</button>
    </div>
  </div>
</body>
</html>';
    }
}