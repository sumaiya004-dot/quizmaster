<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= isset($pageTitle) ? xss($pageTitle) . ' — QuizMaster' : 'QuizMaster Pro' ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>

<body class="min-h-screen flex flex-col text-slate-900 bg-slate-50">