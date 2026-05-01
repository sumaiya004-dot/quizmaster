<?php
// classes/User.php
require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register(string $name, string $email, string $password): array {
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered.'];
        }
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'student')"
        );
        $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hash]);
        return ['success' => true, 'message' => 'Account created successfully!'];
    }

    public function login(string $email, string $password): array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    public function getUserById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getAllStudents(): array {
        $stmt = $this->db->query("SELECT id, name, email, avatar, created_at FROM users WHERE role='student' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return (bool)$stmt->fetch();
    }

    public function getNotes(int $userId): string {
        $stmt = $this->db->prepare("SELECT content FROM user_notes WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();
        return $row ? $row['content'] : '';
    }

    public function saveNotes(int $userId, string $content): void {
        $stmt = $this->db->prepare(
            "INSERT INTO user_notes (user_id, content) VALUES (:uid, :content)
             ON DUPLICATE KEY UPDATE content = :content2, updated_at = NOW()"
        );
        $stmt->execute([':uid' => $userId, ':content' => $content, ':content2' => $content]);
    }

    public function getLeaderboard(int $limit = 10): array {
        $stmt = $this->db->prepare("
            SELECT u.name, u.avatar,
                   COUNT(r.id) AS total_quizzes,
                   SUM(r.score) AS total_score,
                   ROUND(AVG(r.score / NULLIF(r.total_marks,0) * 100), 1) AS avg_pct
            FROM users u
            JOIN results r ON r.user_id = u.id
            WHERE u.role = 'student'
            GROUP BY u.id
            ORDER BY total_score DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
