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
            'event' => [
                'name' => 'Event Name',
                'description' => 'Description',
                'category' => 'Category',
                'max_participant_count' => 'Maximum Participants',
                'start_date' => 'Start Date & Time',
                'end_date' => 'End Date & Time',
            ],
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
            'event' => [
                'name' => 'Enter event name',
                'description' => 'Enter event description',
                'category' => 'Select a category',
                'max_participant_count' => 'Enter max participants (0 for unlimited)',
            ],
        ],
        'btn' => [
            'login' => 'Login',
            'register' => 'Register',
            'save_changes' => 'Save Changes',
            'create' => 'Create',
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
            'subtitle' => [
                'user' => 'Discover and explore upcoming events tailored for you.',
                'organizer' => 'Manage your events and create new ones to engage your audience.',
            ],
            'no_events' => 'No events to display at the moment. Please check back later!',
            'list_title' => 'Event List',
            'create_title' => 'Create a New Event',
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
            'event_created' => 'Event created!',
        ],
        'info' => [],
        'error' => [
            'registered' => 'Could not register. Please try again later.',
            'profile_updated' => 'Could not update profile. Please try again later.',
            'event_created' => 'Could not create event. Please try again later.',
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
        'event' => [
            'end_before_start' => 'Start date must be before end date.',
            'start_in_past' => 'Start date must be in the future.',
            'max_participant_count_negative' => 'Max participant count must be 0 or positive number.',
        ],
        'required' => 'Field ":name" is required.',
        'email' => 'Field ":name" must be a valid e-mail address.',
        'phone' => 'Field ":name" must be a valid phone number.',
    ],
];
