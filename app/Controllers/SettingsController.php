<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use Core\Controller;

class SettingsController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        requireLogin();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        $uid = (int) $_SESSION['user_id'];
        $user = $this->userModel->findById($uid);
        if (!$user) {
            $this->redirect('/auth/logout');
        }

        $pwdError = '';
        $pwdSuccess = '';
        $dangerError = '';
        $notes = $this->userModel->getNotes($uid);

        if ($this->isPost()) {
            $action = sanitizeInput((string) ($_POST['action'] ?? ''));
            if (!verifyCsrf((string) ($_POST['csrf_token'] ?? ''))) {
                $dangerError = 'Security check failed.';
            } elseif ($action === 'password') {
                $current = (string) ($_POST['current_password'] ?? '');
                $new = (string) ($_POST['new_password'] ?? '');
                $confirm = (string) ($_POST['confirm_password'] ?? '');

                if (!verifyPassword($current, (string) $user['password'])) {
                    $pwdError = 'Current password is incorrect.';
                } elseif (strlen($new) < 6) {
                    $pwdError = 'New password must be at least 6 characters.';
                } elseif ($new !== $confirm) {
                    $pwdError = 'New password and confirm password do not match.';
                } else {
                    $this->userModel->updatePassword($uid, hashPassword($new));
                    $pwdSuccess = 'Password changed successfully.';
                    $user = $this->userModel->findById($uid);
                }
            } elseif ($action === 'delete_account') {
                $confirmDelete = (string) ($_POST['confirm_delete'] ?? '');
                if ($confirmDelete !== 'DELETE') {
                    $dangerError = 'Type DELETE exactly to confirm account deletion.';
                } else {
                    $this->userModel->deleteUser($uid);
                    session_unset();
                    session_destroy();
                    header('Location: ' . BASE_URL . '/');
                    exit;
                }
            }
        }

        $this->render('settings/index', compact('user', 'pwdError', 'pwdSuccess', 'dangerError', 'notes'));
    }

    public function saveNotes(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \RuntimeException('Invalid method.');
            }
            if (!verifyCsrf((string) ($_POST['csrf_token'] ?? ''))) {
                throw new \RuntimeException('CSRF verification failed.');
            }

            $content = (string) ($_POST['content'] ?? '');
            $this->userModel->saveNotes((int) $_SESSION['user_id'], $content);
            echo json_encode(['ok' => true, 'message' => 'Notes auto-saved']);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

