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
            'event' => [
                'name' => 'Pasākuma nosaukums',
                'description' => 'Apraksts',
                'category' => 'Kategorija',
                'max_participant_count' => 'Maksimālais dalībnieku skaits',
                'start_date' => 'Sākuma datums un laiks',
                'end_date' => 'Beigu datums un laiks',
            ],
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
            'event' => [
                'name' => 'Ievadiet pasākuma nosaukumu',
                'description' => 'Ievadiet pasākuma aprakstu',
                'category' => 'Atlasīt kategoriju',
                'max_participant_count' => 'Ievadiet maksimālo dalībnieku skaitu (0 - neierobežotam skaitam)',
            ],
        ],
        'btn' => [
            'login' => 'Pieslēgties',
            'register' => 'Reģistrēties',
            'save_changes' => 'Saglabāt izmaiņas',
            'create' => 'Izveidot',
            'cancel' => 'Atcelt',
            'cancel_event' => 'Atcelt pasākumu',
            'delete' => 'Izdzēst',
            'delete_event' => 'Izdzēst pasākumu',
            'join_event' => 'Pievienoties pasākumam',
            'leave_event' => 'Pamest pasākumu',
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
            'subtitle' => [
                'user' => 'Atklājiet un izpētiet jums pielāgotus gaidāmos pasākumus.',
                'organizer' => 'Pārvaldiet savus pasākumus un izveidojiet jaunus, lai piesaistītu auditoriju.',
            ],
            'no_events' => 'Pašlaik nav pasākumu, ko parādīt. Lūdzu, pārbaudiet vēlāk!',
            'create_title' => 'Izveidojiet jaunu pasākumu',
            'participants_limited' => 'Dalībnieki: :current/:max',
            'participants_unlimited' => 'Dalībnieki: :current',
            'cancelled' => 'Atcelts',
            'category' => 'Kategorija: :category',
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
            'login' => 'Jūs esat veiksmīgi pieteicies sistēmā.',
            'registered' => 'Jūs esat veiksmīgi reģistrējies!',
            'profile_updated' => 'Profils atjaunināts!',
            'event_created' => 'Pasākums izveidots!',
            'event_cancelled' => 'Pasākums ":name" atcelts!',
            'event_deleted' => 'Pasākums ":name" izdzēsts!',
            'participation_created' => 'Jūs esat veiksmīgi pievienojies pasākumam ":name".',
            'participation_removed' => 'Jūs esat veiksmīgi pametis pasākumu ":name".',
        ],
        'info' => [
            'logout' => 'Jūs esat atteicies sistēmā.',
            'no_profile_changes' => 'Nav izmaiņu profila datos.',
            'event_participant_left' => 'Dalībnieks ":user_name" atstāja notikumu ":event_name". Pašreizējais dalībnieku skaits: :current_participant_count.',
            'event_participant_joined' => 'Dalībnieks ":user_name" pievienojās pasākumam ":event_name". Pašreizējais dalībnieku skaits: :current_participant_count.',
            'event_participant_cancelled' => 'Pasākums ":name" tika atcelts.',
        ],
        'error' => [
            'registered' => 'Neizdevās reģistrēties. Lūdzu, vēlāk mēģiniet vēlreiz.',
            'profile_updated' => 'Neizdevās atjaunināt profilu. Lūdzu, vēlāk mēģiniet vēlreiz.',
            'event_created' => 'Neizdevās izveidot pasākumu. Lūdzu vēlāk mēģiniet vēlreiz.',
            'event_cancelled' => 'Neizdevās atcelt pasākumu ":name".',
            'event_deleted' => 'Neizdevās izdzēst pasākumu ":name".',
            'event_not_found' => 'Pasākumu ar ID ":id" nevarēja atrast.',
            'user_not_found' => 'Lietotāju ar ID ":id" nevarēja atrast.',
            'participation_created' => 'Jūs jau esat pasākuma ":name" dalībnieks.',
            'participation_removed' => 'Jūs neesat pasākuma ":name" dalībnieks.',
            'participation_create_error' => 'Nevarēja pievienoties pasākumam ":name". Lūdzu, vēlāk mēģiniet vēlreiz.',
            'participation_remove_error' => 'Nevarēja atstāt pasākumu ":name". Lūdzu, vēlāk mēģiniet vēlreiz.',
            'participation_max_reached' => 'Sasniegts pasākuma ":name" maksimālais dalībnieku skaits. Jūs nevarat pievienoties šim pasākumam.',
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
        'event' => [
            'end_before_start' => 'Sākuma datumam ir jābūt pirms beigu datuma.',
            'start_in_past' => 'Sākuma datumam ir jābūt nākotnē.',
            'max_participant_count_negative' => 'Maksimālajam dalībnieku skaitam jābūt 0 vai pozitīvam skaitlim.',
        ],
        'required' => 'Lauks ":name" ir obligāts.',
        'email' => 'Laukam ":name" ir jābūt korektai e-pasta adresei.',
        'phone' => 'Laukam ":name" ir jābūt korektam tālr. nr.',
    ],
];
