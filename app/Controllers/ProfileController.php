<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use Core\Controller;

class ProfileController extends Controller
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

        $errors = [];
        $success = '';

        if ($this->isPost()) {
            if (!verifyCsrf((string) ($_POST['csrf_token'] ?? ''))) {
                $errors[] = 'Security check failed. Please refresh.';
            } else {
                $name = sanitizeInput((string) ($_POST['name'] ?? ''));
                $email = sanitizeInput((string) ($_POST['email'] ?? ''));
                $avatar = sanitizeInput((string) ($_POST['avatar'] ?? '🎓'));

                if ($name === '') $errors[] = 'Name is required.';
                if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
                if ($this->userModel->emailExistsForOtherUser($email, $uid)) $errors[] = 'Email already in use.';

                if (empty($errors)) {
                    $this->userModel->updateProfile($uid, $name, $email, $avatar);
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_avatar'] = $avatar;
                    $success = 'Profile updated successfully.';
                    $user = $this->userModel->findById($uid);
                }
            }
        }

        $this->render('profile/index', compact('user', 'errors', 'success'));
    }
}

