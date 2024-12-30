<?php

namespace Controllers;

use JetBrains\PhpStorm\NoReturn;
use Router\Request;
use Services\Auth;
use Validation\Pattern;

class AuthController extends Controller {
    public function login(Auth $auth, #[\SensitiveParameter] Request $request): void {
        if (auth()) $this->redirect('home');

        if ($request->isPost()) {
            $validationError = false;

            if ($request->empty('email')) {
                $validationError = true;
                $this->flash('error_email', t('validation.required', ['name', t('form.label.email')]));
            } else if (!Pattern::matches($request->input('email'), Pattern::EMAIL)) {
                $validationError = true;
                $this->flash('error_email', t('validation.auth.login.invalid_email'));
            }
            if ($request->empty('password')) {
                $validationError = true;
                $this->flash('error_password', t('validation.required', ['name', t('form.label.password')]));
            }

            if ($validationError) {
                $this->render('pages/auth/register');
                return;
            }

            $status = $auth->authenticateByEmail($request->input('email'), $request->input('password'));

            switch ($status) {
                case Auth::LOGIN_RESULT_SUCCESS:
                    $this->redirect('home');
                case Auth::LOGIN_RESULT_INVALID_PASSWORD:
                    $this->flash('error_password', t('validation.auth.login.invalid_password'));
                    $this->flash('email', $request->input('email'));
                    break;
                case Auth::LOGIN_RESULT_INVALID_USER:
                    $this->flash('error_email', t('validation.auth.login.invalid_email'));
                    break;
                default:
                    $this->error(500);
            }
        }

        $this->render('pages/auth/login');
    }

    public function register(Auth $auth, #[\SensitiveParameter] Request $request): void {
        if (auth()) $this->redirect('home');

        if ($request->isPost()) {
            $validationError = false;

            if ($request->empty('email')) {
                $validationError = true;
                $this->flash('error_email', t('validation.required', ['name', t('form.label.email')]));
            } else if (!Pattern::matches($request->input('email'), Pattern::EMAIL)) {
                $validationError = true;
                $this->flash('error_email', t('validation.auth.register.invalid_email'));
            }
            if ($request->empty('password')) {
                $validationError = true;
                $this->flash('error_password', t('validation.required', ['name' => t('form.label.password')]));
            } else if (strlen($request->input('password')) < 8) {
                $validationError = true;
                $this->flash('error_password', t('validation.auth.register.invalid_password'));
            }
            if ($request->input('password') !== $request->input('password_confirm')) {
                $validationError = true;
                $this->flash('error_password_confirm', t('validation.auth.register.invalid_password_confirm'));
            }
            if (!$request->empty('phone')) {
                $trimmedPhone = str_replace(' ', '', $request->input('phone'));
                $request->setInput('phone', $trimmedPhone);
                if (!Pattern::matches($trimmedPhone, Pattern::PHONE)) {
                    $validationError = true;
                    $this->flash('error_phone', t('validation.auth.register.invalid_phone'));
                }
            }

            if ($validationError) {
                $this->flash($request->removeInput(['password', 'password_confirm']));
                $this->render('pages/auth/register');
                return;
            }

            $status = $auth->registerUser($request->retainInput(['name', 'surname', 'email', 'password', 'phone', 'student_id']));

            switch ($status) {
                case Auth::REGISTER_RESULT_SUCCESS:
                    $this->redirect('home');
                case Auth::REGISTER_RESULT_EXCEPTION:
                    $this->toast('error', t('toast.error.registered'));
                    $this->flash($request);
                    break;
                case Auth::REGISTER_RESULT_USER_EXISTS:
                    $this->flash('error_email', t('validation.auth.register.taken_email'));
                    $this->flash($request);
                    break;
                case Auth::REGISTER_RESULT_SUCCESS_COULD_NOT_AUTOLOGIN:
                    $this->redirect('login');
                default:
                    $this->error(500);
            }
        }

        $this->render('pages/auth/register');
    }

    #[NoReturn] public function logout(Auth $auth): void {
        $auth->logout();
        $this->redirect(route('login'));
    }
}