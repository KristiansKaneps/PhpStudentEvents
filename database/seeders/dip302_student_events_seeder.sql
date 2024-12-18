insert into users(id, name, surname, email, student_id, password)
values (1, 'Kristiāns Jānis', 'Kaņeps', 'kanepskristians@gmail.com', '191RDB092', 'bcrypt_hashed_password');
insert into users(id, name, surname, email, student_id, password)
values (2, 'Natālija', 'Prokofjeva', 'natalija.prokofjeva@rtu.lv', null, 'bcrypt_hashed_password');
insert into users(id, name, surname, email, student_id, password)
values (3, 'Vita', 'Šakele', 'vita.sakele@rtu.lv', null, 'bcrypt_hashed_password');
insert into users(id, name, surname, email, student_id, password)
values (4, 'Aleksandrs', 'Kovancovs', 'aleksandrs.kovancovs@rtu.lv', null, 'bcrypt_hashed_password');
insert into users(id, name, surname, email, student_id, password)
values (5, 'Nezināms', 'Students', 'nezinams.students@edu.rtu.lv', '000RDB000', 'bcrypt_hashed_password');

insert into event_categories(id, name, sort_order)
values (1, 'Izglītība', 0);
insert into event_categories(id, name, sort_order)
values (2, 'Seminārs', 1);
insert into event_categories(id, name, sort_order)
values (3, 'Izklaide', 2);
insert into event_categories(id, name, sort_order)
values (4, 'Cits', 3);

insert into events(id, cancelled, user_id, category_id, name, description, max_participant_count,
                   current_participant_count, start_date, end_date, created_at, updated_at)
values (1, false, 3, 2, 'Bakalaura darba tēmas', 'Seminārs par bakalaura darba tēmas izvēli.', 0, 3,
        '2024-10-04T10:15:00', '2024-10-04T11:50:00', '2024-09-20T09:02:34', '2024-09-20T09:02:34');
insert into events(id, cancelled, user_id, category_id, name, description, max_participant_count,
                   current_participant_count, start_date, end_date, created_at, updated_at)
values (2, true, 5, 3, 'Nezināms pasākums (atcelts)', 'Apraksts nav dots.', 10, 1, '2024-12-26T18:00:00',
        '2024-12-27T00:00:00', '2024-11-07T10:00:17', '2024-11-07T10:02:12');
insert into events(id, cancelled, user_id, category_id, name, description, max_participant_count,
                   current_participant_count, start_date, end_date, created_at, updated_at)
values (3, false, 5, 3, 'Nezināms pasākums', 'Apraksts nav dots.', 10, 1, '2024-12-27T18:00:00', '2024-12-28T00:00:00',
        '2024-11-07T11:44:51', '2024-11-07T11:44:51');

insert into event_participants(event_id, user_id, created_at)
values (1, 3, '2024-09-20T09:02:34');
insert into event_participants(event_id, user_id, created_at)
values (1, 1, '2024-10-01T14:54:10');
insert into event_participants(event_id, user_id, created_at)
values (1, 5, '2024-10-03T10:11:11');
insert into event_participants(event_id, user_id, created_at)
values (2, 5, '2024-11-07T10:00:17');
insert into event_participants(event_id, user_id, created_at)
values (3, 5, '2024-11-07T11:44:51');

insert into notifications(event_id, user_id, type, type_counter, message, description, status, viewed_at, created_at)
values (1, 3, 0, 1, 'Pasākums "Bakalaura darba tēmas" izveidots', '', 1, '2024-09-20T09:03:01', '2024-09-20T09:02:34');
insert into notifications(event_id, user_id, type, type_counter, message, description, status, viewed_at, created_at)
values (1, 1, 1, 1, 'Pieteikums pasākumam "Bakalaura darba tēmas" izveidots', '', 1, '2024-10-01T15:06:13',
        '2024-10-01T14:54:10');
insert into notifications(event_id, user_id, type, type_counter, message, description, status, viewed_at, created_at)
values (1, 5, 1, 1, 'Pieteikums pasākumam "Bakalaura darba tēmas" izveidots', '', 1, null, '2024-10-03T10:11:11');
insert into notifications(event_id, user_id, type, type_counter, message, description, status, viewed_at, created_at)
values (2, 5, 0, 1, 'Pasākums "Nezināms pasākums" izveidots', '', 1, '2024-11-07T10:00:21', '2024-11-07T10:00:17');
insert into notifications(event_id, user_id, type, type_counter, message, description, status, viewed_at, created_at)
values (2, 5, 2, 1, 'Pasākums "Nezināms pasākums" atjaunināts',
        'Pasākums tika atcelts. Jaunais nosaukums: "Nezināms pasākums (atcelts)"', 1, '2024-11-07T10:02:23',
        '2024-11-07T10:02:12');
insert into notifications(event_id, user_id, type, type_counter, message, description, status, viewed_at, created_at)
values (3, 5, 0, 1, 'Pasākums "Nezināms pasākums" izveidots', '', 1, null, '2024-11-07T11:44:51');