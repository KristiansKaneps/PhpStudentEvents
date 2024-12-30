<?php

global $localizedMessages;

$localizedMessages = [
    'app' => [
        'name' => 'Studentu pasākumi',
        'locale' => [
            'en' => 'English',
            'lv' => 'Latviešu',
            'ru' => 'Русский',
        ],
    ],
    'nav' => [
        'logo_title' => 'PASĀKUMI',
        'home' => 'Sākums',
        'events' => 'Pasākumi',
        'about' => 'Par mums',
        'contact' => 'Kontakti',
        'login' => 'Ienākt',
        'register' => 'Reģistrēties',
        'logout' => 'Iziet',
        'profile' => 'Mans profils',
    ],
    'user' => [
        'role' => [
            'user' => 'Lietotājs',
            'organizer' => 'Organizators',
            'admin' => 'Administrators',
            '0' => 'Lietotājs',
            '1' => 'Organizators',
            '2' => 'Administrators',
        ],
    ],
    'form' => [
        'label' => [
            'email' => 'E-pasts',
            'password' => 'Parole',
            'password_old' => 'Vecā parole',
            'password_new' => 'Jaunā parole',
            'password_change' => 'Paroles maiņa',
            'student_id' => 'Studenta ID',
            'name' => 'Vārds',
            'surname' => 'Uzvārds',
            'phone' => 'Telefona Nr.',
            'role' => 'Loma',
        ],
        'placeholder' => [
            'email' => 'E-pasts',
            'password' => 'Parole',
            'password_confirm' => 'Parole atkārtoti',
            'password_old' => 'Vecā parole',
            'password_new' => 'Jaunā parole',
            'password_new_confirm' => 'Jaunā parole atkārtoti',
            'student_id' => 'Studenta ID',
            'name' => 'Vārds',
            'surname' => 'Uzvārds',
            'phone' => 'Tālruņa Nr.',
            'role' => 'Loma',
        ],
        'btn' => [
            'login' => 'Pieslēgties',
            'register' => 'Reģistrēties',
            'save_changes' => 'Saglabāt izmaiņas',
        ],
        'alt' => [
            'no_account' => 'Nav konta?',
            'is_account' => 'Jau esi reģistrējies?',
        ],
    ],
    'section' => [
        'home' => [
            'title' => 'Atklāj pasākumus',
            'subtitle' => 'Iesaistieties, piedalieties un attīstieties, izmantojot iespējas mūsu universitātē!',
            'btn' => [
                'explore' => 'Izpētīt pasākumus',
            ],
        ],
        'events' => [
            'title' => 'Gaidāmie pasākumi',
        ],
        'about' => [
            'title' => 'Par studentu pasākumiem',
        ],
        'contact' => [
            'title' => 'Kontakti',
        ],
        'register' => [
            'title' => 'Reģistrēties',
        ],
        'login' => [
            'title' => 'Pieslēgties',
        ],
        'profile' => [
            'title' => 'Mans profils',
            'subtitle' => [
                'my_profile' => 'Tavi profila dati',
                'other_profile' => 'Lietotāja :name :surname profila dati',
            ],
        ],
    ],
    'toast' => [
        'success' => [
            'registered' => 'Jūs esat veiksmīgi reģistrējies!',
            'profile_updated' => 'Profils atjaunināts!',
        ],
        'info' => [],
        'error' => [
            'registered' => 'Neizdevās reģistrēties. Lūdzu, vēlāk mēģiniet vēlreiz.',
            'profile_updated' => 'Neizdevās atjaunināt profilu. Lūdzu, vēlāk mēģiniet vēlreiz.',
        ],
    ],
    'validation' => [
        'auth' => [
            'login' => [
                'invalid_email' => 'Nezināma e-pasta adrese.',
                'invalid_password' => 'Nepareiza parole.',
            ],
            'register' => [
                'invalid_email' => 'Nederīga e-pasta address.',
                'taken_email' => 'E-pasta adrese ir aizņemta.',
                'invalid_password' => 'Nederīga parole. Tai jābūt vismaz 8 rakstzīmēm.',
                'invalid_password_confirm' => 'Paroles nesakrīt.',
                'invalid_phone' => 'Nederīgs telefona numurs.',
                'invalid_student_id' => 'Nederīgs studenta ID.',
            ],
        ],
        'profile' => [
            'update' => [
                'invalid_email' => 'Nederīga e-pasta address.',
                'taken_email' => 'E-pasta adrese ir aizņemta.',
                'invalid_password' => 'Nepareiza parole.',
                'invalid_password_new' => 'Nederīga parole. Tai jābūt vismaz 8 rakstzīmēm.',
                'invalid_password_new_confirm' => 'Paroles nesakrīt.',
                'invalid_phone' => 'Nederīgs telefona numurs.',
                'invalid_student_id' => 'Nederīgs studenta ID.',
                'new_password_matches_old_password' => 'Jaunā parole ir tāda pati kā vecā parorle.',
            ],
        ],
        'required' => 'Lauks ":name" ir obligāts.',
        'email' => 'Laukam ":name" ir jābūt korektai e-pasta adresei.',
        'phone' => 'Laukam ":name" ir jābūt korektam tālr. nr.',
    ],
];
