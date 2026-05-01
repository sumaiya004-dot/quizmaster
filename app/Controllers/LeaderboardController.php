<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use Core\Controller;

class LeaderboardController extends Controller
{
    public function __construct()
    {
        requireLogin();
    }

    public function index(): void
    {
        $leaderboard = (new UserModel())->getLeaderboard(50);
        $this->render('leaderboard/index', compact('leaderboard'));
    }
}

