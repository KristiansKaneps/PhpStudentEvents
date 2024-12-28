<?php

namespace Services;

use Database\DatabaseException;

class Auth extends Service {
    private ?array $user;

    /**
     * @throws DatabaseException
     */
    public function __construct() {
        parent::__construct();
        if (isset($_SESSION['id'])) {
            $this->user = $this->db->executeQuery("SELECT * FROM users WHERE id = ? LIMIT 1", [$_SESSION['id']]);
        } else {
            $this->user = null;
        }
    }

    public function isAuthenticated(): bool {
        return !empty($this->user);
    }

    public function getUser(): ?array {
        return $this->user;
    }

    public function logout(): void {
        unset($_SESSION['id']);
        $this->user = null;
    }

    public static function getInstance(): self {
        return resolve(self::class);
    }

    public static function auth(): bool {
        return self::getInstance()->isAuthenticated();
    }

    public static function user(): ?array {
        return self::getInstance()->getUser();
    }
}