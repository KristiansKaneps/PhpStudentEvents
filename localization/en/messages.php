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
            'btn' => [
                'login' => 'Login',
                'register' => 'Register',
            ],
        ],
        'login' => [
            'title' => 'Login',
            'btn' => [
                'login' => 'Login',
                'register' => 'Register',
            ],
        ],
    ],
];
