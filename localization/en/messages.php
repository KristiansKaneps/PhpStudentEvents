<?php

global $localizedMessages;

$localizedMessages = [
    'app' => [
        'name' => config('APP.NAME'),
        'locale' => [
            'en' => 'English',
            'lv' => 'Latviešu',
            'ru' => 'Русский',
        ],
    ],
    'nav' => [
        'logo_title' => 'EVENTS',
        'home' => 'Home',
        'events' => 'Events',
        'about' => 'About',
        'contact' => 'Contact',
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'profile' => 'My Profile',
    ],
    'user' => [
        'role' => [
            'user' => 'User',
            'organizer' => 'Organizer',
            'admin' => 'Admin',
            '0' => 'User',
            '1' => 'Organizer',
            '2' => 'Admin',
        ],
    ],
    'form' => [
        'label' => [
            'email' => 'E-mail',
            'password' => 'Password',
            'password_old' => 'Old Password',
            'password_new' => 'New Password',
            'password_change' => 'Change Password',
            'student_id' => 'Student ID',
            'name' => 'Name',
            'surname' => 'Surname',
            'phone' => 'Phone Number',
            'role' => 'Role',
        ],
        'placeholder' => [
            'email' => 'E-mail',
            'password' => 'Password',
            'password_confirm' => 'Repeat password',
            'password_old' => 'Old password',
            'password_new' => 'New password',
            'password_new_confirm' => 'Repeat new password',
            'student_id' => 'Student ID',
            'name' => 'Name',
            'surname' => 'Surname',
            'phone' => 'Phone Number',
            'role' => 'Role',
        ],
        'btn' => [
            'login' => 'Login',
            'register' => 'Register',
            'save_changes' => 'Save Changes',
        ],
        'alt' => [
            'no_account' => 'No account?',
            'is_account' => 'Have already registered?',
        ],
    ],
    'section' => [
        'home' => [
            'title' => 'Discover Events',
            'subtitle' => 'Engage, participate, and grow with opportunities at our university!',
            'btn' => [
                'explore' => 'Explore Events',
            ],
        ],
        'events' => [
            'title' => 'Upcoming Events',
        ],
        'about' => [
            'title' => 'About Student Events',
        ],
        'contact' => [
            'title' => 'Contact Us',
        ],
        'register' => [
            'title' => 'Register',
        ],
        'login' => [
            'title' => 'Login',
        ],
        'profile' => [
            'title' => 'My Profile',
            'subtitle' => [
                'my_profile' => 'Your details',
                'other_profile' => 'User\'s :name :surname details',
            ],
        ],
    ],
    'toast' => [
        'success' => [
            'registered' => 'You have successfully registered!',
            'profile_updated' => 'Profile updated!',
        ],
        'info' => [],
        'error' => [
            'registered' => 'Could not register. Please try again later.',
            'profile_updated' => 'Could not update profile. Please try again later.',
        ],
    ],
    'validation' => [
        'auth' => [
            'login' => [
                'invalid_email' => 'Unknown e-mail address.',
                'invalid_password' => 'Invalid password.',
            ],
            'register' => [
                'invalid_email' => 'Invalid e-mail address.',
                'taken_email' => 'E-mail address is already taken.',
                'invalid_password' => 'Invalid password. It must be at least 8 characters.',
                'invalid_password_confirm' => 'Passwords do not match.',
                'invalid_phone' => 'Invalid phone number.',
                'invalid_student_id' => 'Invalid student ID.',
            ],
        ],
        'profile' => [
            'update' => [
                'invalid_email' => 'Invalid e-mail address.',
                'taken_email' => 'E-mail address is already taken.',
                'invalid_password' => 'Invalid password.',
                'invalid_password_new' => 'Invalid password. It must be at least 8 characters.',
                'invalid_password_new_confirm' => 'Passwords do not match.',
                'invalid_phone' => 'Invalid phone number.',
                'invalid_student_id' => 'Invalid student ID.',
                'new_password_matches_old_password' => 'New password is the same as old password.',
            ],
        ],
        'required' => 'Field ":name" is required.',
        'email' => 'Field ":name" must be a valid e-mail address.',
        'phone' => 'Field ":name" must be a valid phone number.',
    ],
];
