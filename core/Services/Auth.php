<?php

namespace Services;

use Database\DatabaseException;

class Auth extends Service {
    const USER_ROLE_USER = 0;
    const USER_ROLE_ORGANIZER = 1;
    const USER_ROLE_ADMIN = 2;

    private ?array $user;

    public function __construct() {
        parent::__construct();
        $this->refreshCurrentUser();
    }

    private function findUserBy(string $sqlWhereClause, array $params): ?array {
        try {
            $result = $this->db->query(<<<SQL
                SELECT id, name, surname, email, phone, role, student_id, created_at, updated_at FROM users
                WHERE $sqlWhereClause
                LIMIT 1
            SQL, $params);
            return empty($result) ? null : $result[0];
        } catch (DatabaseException) { }
        return null;
    }

    public function refreshCurrentUser(): void {
        $sessionId = session_id();
        $this->user = empty($sessionId)
            ? null
            : $this->findUserBy('users.id = (SELECT user_id FROM sessions WHERE sessions.id = ?)', [$sessionId]);
    }

    public function isAuthenticated(): bool {
        return !empty($this->user);
    }

    public function hasAdminRole(): bool {
        return $this->isAuthenticated() && $this->user['role'] >= self::USER_ROLE_ADMIN;
    }

    public function hasOrganizerRole(): bool {
        return $this->isAuthenticated() && $this->user['role'] >= self::USER_ROLE_ORGANIZER;
    }

    public function hasUserRole(): bool {
        return $this->isAuthenticated() && $this->user['role'] >= self::USER_ROLE_USER;
    }

    public function hasGuestRole(): bool {
        return !$this->isAuthenticated();
    }

    public function userExists(int $userId): bool {
        try {
            return !empty($this->db->query("SELECT 1 FROM users WHERE id = ?", [$userId]));
        } catch (\Exception) { }
        return false;
    }

    public function getUser(?int $userId = null): ?array {
        return $userId === null || (isset($this->user['id']) && $userId === $this->user['id'])
            ? $this->user
            : $this->findUserBy('id = ?', [$userId]);
    }

    public function getUserId(): ?int {
        return empty($this->user) ? null : ($this->user['id'] ?? null);
    }

    public function logout(): void {
        try {
            $sessionId = session_id();
            if (!empty($sessionId)) {
                $this->db->query('UPDATE sessions SET user_id = null WHERE id = ?', [$sessionId]);
            }
        } catch (DatabaseException) {}
        $this->user = null;
    }

    public function checkEmailAvailability(string $email): bool {
        try {
            $query = "SELECT 1 FROM users WHERE email = :email";
            $user = $this->db->query($query, ['email' => $email]);
            return empty($user);
        } catch (DatabaseException) { }
        return false;
    }

    public function verifyPassword(string $email, #[\SensitiveParameter] string $password): bool {
        try {
            $query = "SELECT password FROM users WHERE email = :email";
            $user = $this->db->query($query, ['email' => $email]);
            if (empty($user)) return false;
            $user = $user[0]; // Since executeQuery returns an array of rows
            return password_verify($password, $user['password']);
        } catch (DatabaseException) { }
        return false;
    }

    const LOGIN_RESULT_SUCCESS = 0;
    const LOGIN_RESULT_EXCEPTION = 1;
    const LOGIN_RESULT_INVALID_USER = 2;
    const LOGIN_RESULT_INVALID_PASSWORD = 3;

    /**
     * Tries to authenticate a user by email and password.
     * @param string $email User's email
     * @param string $password User's password
     * @return int Result (one of LOGIN_RESULT_SUCCESS, LOGIN_RESULT_EXCEPTION, LOGIN_INVALID_USER or LOGIN_RESULT_INVALID_PASSWORD)
     */
    public function authenticateByEmail(string $email, #[\SensitiveParameter] string $password): int {
        try {
            $query = "SELECT id, name, surname, email, role, password FROM users WHERE email = :email";
            $user = $this->db->query($query, ['email' => $email]);
            if (empty($user)) return self::LOGIN_RESULT_INVALID_USER;
            $user = $user[0]; // Since executeQuery returns an array of rows
            if (!password_verify($password, $user['password'])) {
                return self::LOGIN_RESULT_INVALID_PASSWORD;
            }
            unset($user['password']); // Unset password hash for safety
            $sessionId = session_id();
            if (!empty($sessionId)) {
                $this->db->query('UPDATE sessions SET user_id = ? WHERE id = ?', [$user['id'], $sessionId]);
                $this->user = $user;
            }
        } catch (DatabaseException) {
            return self::LOGIN_RESULT_EXCEPTION;
        }
        return self::LOGIN_RESULT_SUCCESS;
    }

    public function changePassword(string $userId, #[\SensitiveParameter] string $newPassword): bool {
        try {
            $query = 'UPDATE users SET password = ? WHERE id = ?';
            return $this->db->execute($query, [password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 13]), $userId]);
        } catch (DatabaseException) { }
        return false;
    }

    const REGISTER_RESULT_SUCCESS = 0;
    const REGISTER_RESULT_EXCEPTION = 1;
    const REGISTER_RESULT_USER_EXISTS = 2;
    const REGISTER_RESULT_SUCCESS_COULD_NOT_AUTOLOGIN = 3;

    public function registerUser(#[\SensitiveParameter] array $data, bool $autoLogin = true): int {
        if (empty($data) || !isset($data['email'])) return self::REGISTER_RESULT_EXCEPTION;
        try {
            if (!empty($this->db->query('SELECT 1 FROM users WHERE email = ? LIMIT 1', [$data['email']])))
                return self::REGISTER_RESULT_USER_EXISTS;
            $rawPassword = $data['password'];
            $data['password'] = password_hash($rawPassword, PASSWORD_BCRYPT, ['cost' => 13]);
            $query = <<<SQL
                INSERT INTO users (name, surname, email, student_id, phone, password)
                VALUES (:name, :surname, :email, :student_id, :phone, :password)
            SQL;
            if (!$this->db->execute($query, $data)) return self::REGISTER_RESULT_EXCEPTION;
            if ($autoLogin) {
                if ($this->authenticateByEmail($data['email'], $rawPassword) !== self::LOGIN_RESULT_SUCCESS)
                    return self::REGISTER_RESULT_SUCCESS_COULD_NOT_AUTOLOGIN;
            }
        } catch (DatabaseException) {
            return self::REGISTER_RESULT_EXCEPTION;
        }
        return self::REGISTER_RESULT_SUCCESS;
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

    public static function userId(): ?int {
        return self::getInstance()->getUserId();
    }

    public static function adminRole(): bool {
        return self::getInstance()->hasAdminRole();
    }

    public static function organizerRole(): bool {
        return self::getInstance()->hasOrganizerRole();
    }

    public static function userRole(): bool {
        return self::getInstance()->hasUserRole();
    }

    public static function guestRole(): bool {
        return self::getInstance()->hasGuestRole();
    }
}