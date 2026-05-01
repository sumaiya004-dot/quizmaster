<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use Core\Controller;

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        if (isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        $this->redirect('/auth/login');
    }

    public function login(): void
    {
        if (isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $error = '';
        $email = '';

        if ($this->isPost()) {
            if (!verifyCsrf((string) ($_POST['csrf_token'] ?? ''))) {
                $error = 'Security check failed. Please refresh and try again.';
            } else {
                $email = sanitizeInput((string) ($_POST['email'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');

                if ($email === '' || $password === '') {
                    $error = 'Both email and password are required.';
                } else {
                    $user = $this->userModel->findByEmail($email);
                    if ($user && verifyPassword($password, (string) $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = (int) $user['id'];
                        $_SESSION['user_name'] = (string) $user['name'];
                        $_SESSION['user_email'] = (string) $user['email'];
                        $_SESSION['user_role'] = (string) ($user['role'] ?? 'student');
                        $_SESSION['user_avatar'] = (string) ($user['avatar'] ?? '🎓');
                        $this->redirect('/dashboard');
                    } else {
                        $error = 'Invalid email or password. Please try again.';
                    }
                }
            }
        }

        $this->render('auth/login', ['error' => $error, 'email' => $email, 'csrf' => generateCsrf()]);
    }

    public function signup(): void
    {
        if (isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        $errors = [];
        $success = false;
        $old = [];

        if ($this->isPost()) {
            if (!verifyCsrf((string) ($_POST['csrf_token'] ?? ''))) {
                $errors[] = 'Security check failed. Please refresh.';
            } else {
                $name = sanitizeInput((string) ($_POST['name'] ?? ''));
                $email = sanitizeInput((string) ($_POST['email'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');
                $confirm = (string) ($_POST['confirm'] ?? '');
                $old = compact('name', 'email');

                if ($name === '') {
                    $errors[] = 'Full name is required.';
                }
                if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'A valid email address is required.';
                }
                if (strlen($password) < 6) {
                    $errors[] = 'Password must be at least 6 characters.';
                }
                if ($password !== $confirm) {
                    $errors[] = 'Passwords do not match.';
                }
                if (empty($errors) && $this->userModel->emailExists($email)) {
                    $errors[] = 'That email is already registered.';
                }

                if (empty($errors)) {
                    $this->userModel->create($name, $email, hashPassword($password));
                    $success = true;
                }
            }
        }

        $this->render('auth/signup', ['errors' => $errors, 'success' => $success, 'old' => $old, 'csrf' => generateCsrf()]);
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('/auth/login');
    }
}
