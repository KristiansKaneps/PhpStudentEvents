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
            'cancel' => 'Cancel',
            'cancel_event' => 'Cancel Event',
            'delete' => 'Delete',
            'delete_event' => 'Delete Event',
            'join_event' => 'Join Event',
            'leave_event' => 'Leave Event',
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
            'create_title' => 'Create a New Event',
            'participants_limited' => 'Participants: :current/:max',
            'participants_unlimited' => 'Participants: :current',
            'cancelled' => 'Cancelled',
            'category' => 'Category: :category',
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
            'login' => 'You have successfully logged in.',
            'registered' => 'You have successfully registered!',
            'profile_updated' => 'Profile updated!',
            'event_created' => 'Event created!',
            'event_cancelled' => 'Event ":name" cancelled!',
            'event_deleted' => 'Event ":name" deleted!',
            'participation_created' => 'You have successfully joined the event ":name".',
            'participation_removed' => 'You have successfully left the event ":name".',
        ],
        'info' => [
            'logout' => 'You have logged out.',
            'no_profile_changes' => 'No changes in profile data.',
            'event_participant_left' => 'A participant left the event ":name". Current participant count: :current_participant_count.',
            'event_participant_joined' => 'A participant joined the event ":name". Current participant count: :current_participant_count.',
            'event_participant_cancelled' => 'The event ":name" was cancelled.',
        ],
        'error' => [
            'registered' => 'Could not register. Please try again later.',
            'profile_updated' => 'Could not update profile. Please try again later.',
            'event_created' => 'Could not create event. Please try again later.',
            'event_cancelled' => 'Could not delete event ":name".',
            'event_deleted' => 'Could not delete event ":name".',
            'event_not_found' => 'The event with ID ":id" could not be found.',
            'user_not_found' => 'The user with ID ":id" could not be found.',
            'participation_created' => 'You are already a participant in event ":name".',
            'participation_removed' => 'You are not a participant in event ":name".',
            'participation_create_error' => 'Could not join the event ":name". Please try again later.',
            'participation_remove_error' => 'Could not leave the event ":name". Please try again later.',
            'participation_max_reached' => 'The maximum number of participants for the event ":name" has been reached. You cannot join this event.',
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
