<?php

namespace Database;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;

    public function __clone() {} // Disable cloning
    public function __wakeup() {} // Disable de-serialization

    private readonly string $host, $name, $username, $password;
    private readonly string $dsn;

    private ?PDO $pdo = null;

    private function __construct() {
        $this->host = config('DATABASE_HOST');
        $this->name = config('DATABASE_NAME');
        $this->username = config('DATABASE_USERNAME');
        $this->password = config('DATABASE_PASSWORD');
        $this->dsn = "mysql:host=$this->host;dbname=$this->name;charset=UTF8";
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
            try {
                self::$instance->connect();
            } catch (\Exception $e) {
                if (config('ENVIRONMENT') === 'production') {
                    die("Could not connect to the database.");
                } else {
                    die("Could not connect to the database: " . $e->getMessage());
                }
            }
        }
        return self::$instance;
    }

    public function __destruct() {
        $this->disconnect();
    }

    /**
     * @throws DatabaseException
     */
    public function connect(): PDO {
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->pdo;
        } catch (PDOException $e) {
            $this->pdo = null;
            throw DatabaseException::from($e);
        }
    }

    public function disconnect(): void {
        $this->pdo = null; // PHP automatically destructs this object and closes the connection.
    }

    /**
     * @throws DatabaseException
     */
    public function getPDO(): PDO {
        if (!$this->pdo) throw new DatabaseException('Database connection not established');
        return $this->pdo;
    }

    /**
     * Execute a prepared query with parameters.
     * @param string $sql
     * @param array $params
     * @return array
     * @throws DatabaseException
     */
    public function executeQuery(string $sql, array $params = []): array {
        try {
            $stmt = $this->getPDO()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw DatabaseException::from($e);
        }
    }

    /**
     * Start a database transaction.
     * @throws DatabaseException
     */
    public function beginTransaction(): void {
        try {
            $this->getPDO()->beginTransaction();
        } catch (PDOException $e) {
            throw DatabaseException::from($e);
        }
    }

    /**
     * Commit the current transaction.
     * @throws DatabaseException
     */
    public function commitTransaction(): void {
        try {
            $this->getPDO()->commit();
        } catch (PDOException $e) {
            throw DatabaseException::from($e);
        }
    }

    /**
     * Roll back the current transaction.
     * @throws DatabaseException
     */
    public function rollbackTransaction(): void {
        try {
            $this->getPDO()->rollBack();
        } catch (PDOException $e) {
            throw DatabaseException::from($e);
        }
    }
}
