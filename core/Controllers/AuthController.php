<?php

namespace Controllers;

use Database\DatabaseException;
use Services\Auth;

class AuthController extends Controller {
    /**
     * @throws DatabaseException
     */
    function authenticateUser(string $email, string $password): ?array {
        $query = "SELECT id, name, surname, email, role, password FROM users WHERE email = :email";
        $user = $this->db->executeQuery($query, ['email' => $email]);
        if (!$user) {
            return null; // User not found
        }

        $user = $user[0]; // Since executeQuery returns an array of rows
        if (!password_verify($password, $user['password'])) {
            return null; // Invalid password
        }

        unset($user['password']); // Remove password hash for safety
        return $user;
    }

    public function login(): void {
        $this->render('pages/auth/login');
    }

    public function register(): void {
        $this->render('pages/auth/register');
    }

    public function logout(Auth $auth): void {
        $auth->logout();
        $this->redirect(route('home'));
    }
}