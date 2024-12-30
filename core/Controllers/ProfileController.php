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

    public function profile(Auth $auth, Request $request): void {
        if ($request->isPost()) {
            $validationError = false;

            $data = [];

            if (!$request->empty('name') && $auth->getUser()['name'] !== $request->input('name')) {
                $data['name'] = $request->input('name');
            }
            if (!$request->empty('surname') && $auth->getUser()['surname'] !== $request->input('surname')) {
                $data['surname'] = $request->input('surname');
            }
            if (!$request->empty('email') && $auth->getUser()['email'] !== $request->input('email')) {
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
            if ($request->empty('password')) {
                $validationError = true;
                $this->flash('error_password', t('validation.required', ['name' => t('form.label.password')]));
            } else if (!$auth->verifyPassword($request->input('email'), $request->input('password'))) {
                $validationError = true;
                $this->flash('error_password', t('validation.profile.update.invalid_password'));
            }
            if ($request->input('password_new') !== $request->input('password_new_confirm')) {
                $validationError = true;
                $this->flash('error_password_new_confirm', t('validation.profile.update.invalid_password_new_confirm'));
            }
            if (!$request->empty('password_new') && strlen($request->input('password_new')) < 8) {
                $validationError = true;
                $this->flash('error_password_new', t('validation.profile.update.invalid_password_new'));
            } else if (!$request->empty('password_new')) {
                $data['password'] = $request->input('password_new');
            }
            if (!$request->empty('phone') && $auth->getUser()['phone'] !== $request->input('phone')) {
                $trimmedPhone = str_replace(' ', '', $request->input('phone'));
                $request->setInput('phone', $trimmedPhone);
                if (!Pattern::matches($trimmedPhone, Pattern::PHONE)) {
                    $validationError = true;
                    $this->flash('error_phone', t('validation.profile.update.invalid_phone'));
                } else {
                    $data['phone'] = $trimmedPhone;
                }
            }
            if (!$request->empty('student_id') && $auth->getUser()['student_id'] !== $request->input('student_id')) {
                $data['student_id'] = $request->input('student_id');
            }

            // Update user role if current user is admin.
            if ($request->isset('role') && $auth->getUser()['role'] >= Auth::USER_ROLE_ADMIN) {
                $role = intval($request->input('role'));
                if ($role < Auth::USER_ROLE_USER) $role = 0;
                else if ($role > Auth::USER_ROLE_ADMIN) $role = Auth::USER_ROLE_ADMIN;
                $data['role'] = $role;
            }

            if ($validationError) {
                $this->flash($request);
            } else if (!empty($data)) {
                $result = $this->profileService->updateUser($auth->getUserId(), $data);
                switch ($result) {
                    case ProfileService::UPDATE_RESULT_SUCCESS:
                        $auth->refreshCurrentUser();
                        $this->toast('success', t('toast.success.profile_updated'));
                        break;
                    case ProfileService::UPDATE_RESULT_EXCEPTION:
                        $this->toast('error', t('toast.error.profile_updated'));
                        break;
                }
            }
        }

        $this->render('pages/profile/index', ['user' => $auth->getUser()]);
    }
}