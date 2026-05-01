<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        $stats = [
            'total_users' => (int) $db->fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'student'"),
            'total_quizzes' => (int) $db->fetchColumn("SELECT COUNT(*) FROM quizzes"),
            'total_attempts' => (int) $db->fetchColumn("SELECT COUNT(*) FROM results"),
            'accuracy_rate' => (float) ($db->fetchColumn("SELECT COALESCE(ROUND(AVG(score / NULLIF(total_marks,0) * 100),1), 0) FROM results") ?? 0),
        ];

        $subjects = $db->fetchAll(
            "SELECT name, icon, description
             FROM subjects
             ORDER BY id ASC
             LIMIT 6"
        );

        $this->render('home/index', compact('stats', 'subjects'));
    }
}

