<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/quizmaster');

require_once __DIR__ . '/Core/Database.php';
require_once __DIR__ . '/app/helpers.php';

use Core\Database;

if (empty($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}
if (($_SESSION['user_role'] ?? 'student') !== 'admin') {
    http_response_code(403);
    echo '<h2 style="font-family:sans-serif;padding:2rem;color:#b91c1c">Admin access required.</h2>';
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$db = Database::getInstance();

if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $action = sanitizeInput((string) $_GET['action']);
        if ($action === 'list') {
            $rows = $db->fetchAll(
                "SELECT q.id, q.quiz_id, q.question_text, q.question_type, q.explanation, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option, q.marks
                 FROM questions q
                 ORDER BY q.id DESC
                 LIMIT 200"
            );
            echo json_encode(['ok' => true, 'rows' => $rows], JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new RuntimeException('Invalid method.');
        }

        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!verifyCsrf($token)) {
            throw new RuntimeException('CSRF verification failed.');
        }

        if ($action === 'create') {
            $db->execute(
                "INSERT INTO questions
                 (quiz_id, question_text, question_type, explanation, option_a, option_b, option_c, option_d, correct_option, marks)
                 VALUES (:quiz_id, :question_text, :question_type, :explanation, :option_a, :option_b, :option_c, :option_d, :correct_option, :marks)",
                [
                    ':quiz_id' => (int) $_POST['quiz_id'],
                    ':question_text' => (string) $_POST['question_text'],
                    ':question_type' => (string) $_POST['question_type'],
                    ':explanation' => (string) ($_POST['explanation'] ?? ''),
                    ':option_a' => (string) $_POST['option_a'],
                    ':option_b' => (string) $_POST['option_b'],
                    ':option_c' => (string) $_POST['option_c'],
                    ':option_d' => (string) $_POST['option_d'],
                    ':correct_option' => (string) $_POST['correct_option'],
                    ':marks' => max(1, (int) $_POST['marks']),
                ]
            );
            echo json_encode(['ok' => true]);
            exit;
        }

        if ($action === 'update') {
            $db->execute(
                "UPDATE questions
                 SET quiz_id = :quiz_id, question_text = :question_text, question_type = :question_type, explanation = :explanation,
                     option_a = :option_a, option_b = :option_b, option_c = :option_c, option_d = :option_d,
                     correct_option = :correct_option, marks = :marks
                 WHERE id = :id",
                [
                    ':id' => (int) $_POST['id'],
                    ':quiz_id' => (int) $_POST['quiz_id'],
                    ':question_text' => (string) $_POST['question_text'],
                    ':question_type' => (string) $_POST['question_type'],
                    ':explanation' => (string) ($_POST['explanation'] ?? ''),
                    ':option_a' => (string) $_POST['option_a'],
                    ':option_b' => (string) $_POST['option_b'],
                    ':option_c' => (string) $_POST['option_c'],
                    ':option_d' => (string) $_POST['option_d'],
                    ':correct_option' => (string) $_POST['correct_option'],
                    ':marks' => max(1, (int) $_POST['marks']),
                ]
            );
            echo json_encode(['ok' => true]);
            exit;
        }

        if ($action === 'delete') {
            $db->execute("DELETE FROM questions WHERE id = :id", [':id' => (int) $_POST['id']]);
            echo json_encode(['ok' => true]);
            exit;
        }

        throw new RuntimeException('Unknown action.');
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — QuizMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    <main class="max-w-7xl mx-auto px-4 py-8">
        <button onclick="history.back()" class="min-h-[44px] mb-4 rounded border bg-white px-3 py-2 text-sm">Back</button>
        <div class="bg-gradient-to-r from-indigo-700 to-violet-700 text-white rounded-2xl p-6 mb-4">
            <h1 class="text-2xl md:text-3xl font-bold">Admin Panel — Question CRUD</h1>
            <p class="text-indigo-100">Manage question_type and explanation fields with AJAX.</p>
        </div>

        <section class="bg-white border rounded-xl p-4">
            <h2 class="font-bold mb-3">Create / Update Question</h2>
            <form id="qForm" class="grid md:grid-cols-2 gap-3">
                <input type="hidden" name="csrf_token" value="<?= xss($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="id" id="id">
                <input name="quiz_id" placeholder="Quiz ID" class="min-h-[44px] rounded border px-3 py-2" required>
                <select name="question_type" class="min-h-[44px] rounded border px-3 py-2" required>
                    <option value="mcq">MCQ</option>
                    <option value="tf">TF</option>
                    <option value="short">Short</option>
                </select>
                <textarea name="question_text" placeholder="Question text" class="md:col-span-2 rounded border px-3 py-2" required></textarea>
                <input name="option_a" placeholder="Option A" class="min-h-[44px] rounded border px-3 py-2" required>
                <input name="option_b" placeholder="Option B" class="min-h-[44px] rounded border px-3 py-2" required>
                <input name="option_c" placeholder="Option C" class="min-h-[44px] rounded border px-3 py-2" required>
                <input name="option_d" placeholder="Option D" class="min-h-[44px] rounded border px-3 py-2" required>
                <input name="correct_option" placeholder="Correct option (a/b/c/d)" class="min-h-[44px] rounded border px-3 py-2" required>
                <input name="marks" type="number" min="1" value="1" class="min-h-[44px] rounded border px-3 py-2" required>
                <textarea name="explanation" placeholder="Explanation" class="md:col-span-2 rounded border px-3 py-2"></textarea>
                <div class="md:col-span-2 flex gap-2">
                    <button id="saveBtn" type="submit" class="min-h-[44px] px-4 py-2 rounded bg-indigo-600 text-white font-semibold">Save</button>
                    <button id="resetBtn" type="button" class="min-h-[44px] px-4 py-2 rounded border">Reset</button>
                </div>
            </form>
        </section>

        <section class="mt-4 bg-white border rounded-xl p-4">
            <h2 class="font-bold mb-3">Questions</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-3">ID</th><th class="py-2 pr-3">Quiz</th><th class="py-2 pr-3">Type</th><th class="py-2 pr-3">Question</th><th class="py-2 pr-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rows"></tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        const rowsEl = document.getElementById('rows');
        const form = document.getElementById('qForm');
        const saveBtn = document.getElementById('saveBtn');

        async function loadRows() {
            try {
                const res = await fetch('admin_panel.php?action=list');
                const data = await res.json();
                if (!res.ok || !data.ok) throw new Error(data.message || 'Failed to load');
                rowsEl.innerHTML = '';
                data.rows.forEach((r) => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b';
                    tr.innerHTML = `
                        <td class="py-2 pr-3">${r.id}</td>
                        <td class="py-2 pr-3">${r.quiz_id}</td>
                        <td class="py-2 pr-3">${r.question_type}</td>
                        <td class="py-2 pr-3">${(r.question_text || '').slice(0, 70)}</td>
                        <td class="py-2 pr-3">
                            <button class="min-h-[44px] px-2 py-1 rounded border text-xs mr-1 editBtn">Edit</button>
                            <button class="min-h-[44px] px-2 py-1 rounded border text-xs text-rose-600 delBtn">Delete</button>
                        </td>`;
                    tr.querySelector('.editBtn').addEventListener('click', () => fillForm(r));
                    tr.querySelector('.delBtn').addEventListener('click', () => removeRow(r.id, form.csrf_token.value));
                    rowsEl.appendChild(tr);
                });
            } catch (err) {
                alert(err.message);
            }
        }

        function fillForm(r) {
            form.id.value = r.id;
            form.quiz_id.value = r.quiz_id;
            form.question_type.value = r.question_type;
            form.question_text.value = r.question_text;
            form.option_a.value = r.option_a;
            form.option_b.value = r.option_b;
            form.option_c.value = r.option_c;
            form.option_d.value = r.option_d;
            form.correct_option.value = r.correct_option;
            form.marks.value = r.marks;
            form.explanation.value = r.explanation || '';
            saveBtn.textContent = 'Update';
        }

        async function removeRow(id, token) {
            if (!confirm('Delete this question?')) return;
            try {
                const fd = new FormData();
                fd.append('id', id);
                fd.append('csrf_token', token);
                const res = await fetch('admin_panel.php?action=delete', { method: 'POST', body: fd });
                const data = await res.json();
                if (!res.ok || !data.ok) throw new Error(data.message || 'Delete failed');
                loadRows();
            } catch (err) {
                alert(err.message);
            }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const fd = new FormData(form);
                const action = form.id.value ? 'update' : 'create';
                const res = await fetch(`admin_panel.php?action=${action}`, { method: 'POST', body: fd });
                const data = await res.json();
                if (!res.ok || !data.ok) throw new Error(data.message || 'Save failed');
                form.reset();
                form.id.value = '';
                saveBtn.textContent = 'Save';
                loadRows();
            } catch (err) {
                alert(err.message);
            }
        });

        document.getElementById('resetBtn').addEventListener('click', () => {
            form.reset();
            form.id.value = '';
            saveBtn.textContent = 'Save';
        });

        loadRows();
    </script>
</body>
</html>

