<?php

namespace Session;

use Database\Database;
use Database\DatabaseException;
use PDOStatement;

class SessionHandler implements \SessionHandlerInterface {
    public function __construct() {
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
        register_shutdown_function('session_write_close');
        session_start();
    }

    public function __destruct() {
        $this->close();
    }

    public function close(): bool {
        Database::getInstance()->disconnect();
        return true;
    }

    public function destroy(string $id): bool {
        try {
            $pdo = Database::getInstance()->getPDO();
            /** @var PDOStatement|false $stmt */
            $stmt = $pdo->prepare("DELETE FROM sessions WHERE id = ? LIMIT 1");
            if ($stmt === false) return false;
            if ($stmt->execute([$id])) return true;
        } catch (\PDOException | DatabaseException) { }
        return false;
    }

    public function gc(int $max_lifetime): int|false {
        try {
            $pdo = Database::getInstance()->getPDO();
            /** @var PDOStatement|false $stmt */
            $stmt = $pdo->prepare("DELETE FROM sessions WHERE access < ?");
            if ($stmt === false) return false;
            if ($stmt->execute([time() - $max_lifetime])) return $stmt->rowCount();
        } catch (\PDOException | DatabaseException) { }
        return false;
    }

    public function open(string $path, string $name): bool {
        try {
            // Check if database connection is open and connect if necessary.
            return Database::getInstance()->getPDO() !== null;
        } catch (\PDOException | DatabaseException) { }
        return false;
    }

    public function read(string $id): string|false {
        try {
            $pdo = Database::getInstance()->getPDO();
            /** @var PDOStatement|false $stmt */
            $stmt = $pdo->prepare("SELECT data FROM sessions WHERE id = ? LIMIT 1");
            if ($stmt === false) return false;
            if ($stmt->execute([$id])) {
                $data = $stmt->fetchColumn();
                return $data !== false ? $data : "";
            }
        } catch (\PDOException | DatabaseException) { }
        return false;
    }

    public function write(string $id, string $data): bool {
        try {
            $pdo = Database::getInstance()->getPDO();
            /** @var PDOStatement|false $stmt */
            $stmt = $pdo->prepare(<<<SQL
                INSERT INTO sessions (id, access, data) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                    access = VALUES(access), 
                    data = VALUES(data)
            SQL);
            if ($stmt === false) return false;
            return $stmt->execute([$id, time(), $data]);
        } catch (\PDOException | DatabaseException) { }
        return false;
    }
}