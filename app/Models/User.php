<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database;

final class User
{
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1';
        return Database::getInstance()->selectOne($sql, ['email' => $email]);
    }

    public function create(string $name, string $email, string $passwordHash): bool
    {
        $sql = 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)';
        return Database::getInstance()->execute($sql, [
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
        ]);
    }
}
