<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private string $host = 'localhost';
    private string $dbname = 'quizmaster';
    private string $user = 'root';
    private string $pass = '';
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
            // Keep connection alive — prevents MySQL error 2006 "server has gone away"
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION wait_timeout=28800, SESSION interactive_timeout=28800",
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            error_log('[QuizMaster] DB connection error: ' . $e->getMessage());
            http_response_code(500);
            die('Database connection failed.');
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // MySQL error 2006 = "server has gone away" — reconnect once and retry
            if ($e->errorInfo[1] === 2006 || $e->errorInfo[1] === 2013) {
                error_log('[QuizMaster] MySQL gone away — reconnecting…');
                $this->connect();
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                return $stmt;
            }
            throw $e;
        }
    }

    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    // Alias for fetchOne() — used throughout the app as ->fetch()
    public function fetch(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchColumn(string $sql, array $params = [], int $col = 0): mixed
    {
        return $this->query($sql, $params)->fetchColumn($col);
    }

    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}
