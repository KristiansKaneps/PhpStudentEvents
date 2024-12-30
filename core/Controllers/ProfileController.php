<?php

namespace Controllers;

use Router\Request;
use Services\Auth;
use Services\ProfileService;
use Validation\Pattern;

class ProfileController extends Controller {
    protected ProfileService $profileService;

    public function __construct() {
        parent::__construct();
        $this->profileService = resolve(ProfileService::class);
    }

    public function profile(Auth $auth, Request $request, ?int $userId = null): void {
        $userId = $userId === null ? $auth->getUserId() : $userId;

        if (empty($userId)) {
            $this->redirect('login');
        } else if (!$auth->hasAdminRole() && $userId !== $auth->getUserId()) {
            $this->unauthorized();
        }

        $user = $auth->getUser($userId);

        if (empty($user)) {
            $this->notFound();
        }

        if ($request->isPost()) {
            $validationError = false;

            $data = [];

            if (!$request->empty('name') && $user['name'] !== $request->input('name')) {
                $data['name'] = $request->input('name');
            }
            if (!$request->empty('surname') && $user['surname'] !== $request->input('surname')) {
                $data['surname'] = $request->input('surname');
            }
            if (!$request->empty('email') && $user['email'] !== $request->input('email')) {
                if (!Pattern::matches($request->input('email'), Pattern::EMAIL)) {
                    $validationError = true;
                    $this->flash('error_email', t('validation.profile.update.invalid_email'));
                } else if (!$auth->checkEmailAvailability($request->input('email'))) {
                    $validationError = true;
                    $this->flash('error_email', t('validation.profile.update.taken_email'));
                } else {
                    $data['email'] = $request->input('email');
                }
            }
            if (!$auth->hasAdminRole() || $userId === $auth->getUserId()) {
                if ($request->empty('password')) {
                    $validationError = true;
                    $this->flash('error_password', t('validation.required', ['name' => t('form.label.password')]));
                } else if (!$auth->verifyPassword($user['email'], $request->input('password'))) {
                    $validationError = true;
                    $this->flash('error_password', t('validation.profile.update.invalid_password'));
                }
            }
            if ($request->input('password_new') !== $request->input('password_new_confirm')) {
                $validationError = true;
                $this->flash('error_password_new_confirm', t('validation.profile.update.invalid_password_new_confirm'));
            }
            if (!$request->empty('password_new') && strlen($request->input('password_new')) < 8) {
                $validationError = true;
                $this->flash('error_password_new', t('validation.profile.update.invalid_password_new'));
            } else if (!$request->empty('password_new')) {
                if (!$validationError && $auth->verifyPassword($user['email'], $request->input('password_new'))) {
                    $validationError = true;
                    $this->flash('error_password_new', t('validation.profile.update.new_password_matches_old_password'));
                } else {
                    $data['password'] = $request->input('password_new');
                }
            }
            if ($user['phone'] !== $request->input('phone')) {
                if ($request->empty('phone')) {
                    $data['phone'] = null;
                } else {
                    $trimmedPhone = str_replace(' ', '', $request->input('phone'));
                    $request->setInput('phone', $trimmedPhone);
                    if (!Pattern::matches($trimmedPhone, Pattern::PHONE)) {
                        $validationError = true;
                        $this->flash('error_phone', t('validation.profile.update.invalid_phone'));
                    } else {
                        $data['phone'] = $trimmedPhone;
                    }
                }
            }
            if ($user['student_id'] !== $request->input('student_id')) {
                $data['student_id'] = $request->input('student_id');
            }

            // Update user role if current user is admin.
            if ($request->isset('role') && $auth->hasAdminRole()) {
                $role = intval($request->input('role'));
                if ($role < Auth::USER_ROLE_USER) $role = 0;
                else if ($role > Auth::USER_ROLE_ADMIN) $role = Auth::USER_ROLE_ADMIN;
                $data['role'] = $role;
            }

            if ($validationError) {
                $this->flash($request);
            } else if (!empty($data)) {
                $result = $this->profileService->updateUser($userId, $data);
                switch ($result) {
                    case ProfileService::UPDATE_RESULT_SUCCESS:
                        if ($userId === $auth->getUserId()) $auth->refreshCurrentUser();
                        $this->toast('success', t('toast.success.profile_updated'));
                        break;
                    case ProfileService::UPDATE_RESULT_EXCEPTION:
                        $this->toast('error', t('toast.error.profile_updated'));
                        break;
                }
            }
        }

        $this->render('pages/profile/view', ['user' => $auth->getUser($userId)]);
    }
}