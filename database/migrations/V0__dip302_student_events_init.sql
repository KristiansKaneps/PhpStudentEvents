create table users
(
    id         bigint           not null auto_increment primary key,
    name       varchar(63)      not null,
    surname    varchar(63)      not null,
    email      varchar(320)     not null unique,
    student_id varchar(9)       null,
    phone      varchar(23)      null,
    role       tinyint unsigned not null default 0, -- 0 - user; 1 - organizer; 2 - admin
    password   binary(60)       not null,
    created_at datetime         not null default now(),
    updated_at datetime         not null default now()
);

create table event_categories
(
    id         bigint       not null auto_increment primary key,
    name       varchar(127) not null,
    sort_order int          not null default 0,
    created_at datetime     not null default now(),
    updated_at datetime     not null default now()
);

insert into event_categories(name, sort_order)
values ('Izglītība', 0);
insert into event_categories(name, sort_order)
values ('Seminārs', 1);
insert into event_categories(name, sort_order)
values ('Izklaide', 2);
insert into event_categories(name, sort_order)
values ('Cits', 3);

create table events
(
    id                        bigint       not null auto_increment primary key,
    user_id                   bigint       not null references users (id) on update cascade,
    category_id               bigint       not null references event_categories (id) on update cascade,
    name                      varchar(127) not null,
    description               text         not null default '',
    max_participant_count     int unsigned not null default 0, -- 0 - no limit
    current_participant_count int unsigned not null default 0,
    start_date                datetime     not null,
    end_date                  datetime     not null,
    cancelled                 bool         not null default false,
    created_at                datetime     not null default now(),
    updated_at                datetime     not null default now()
);

create table notifications
(
    event_id     bigint            not null references events (id) on update cascade,
    user_id      bigint            not null references users (id) on update cascade,
    type         tinyint unsigned  not null,
    type_counter smallint unsigned not null default 0,
    message      varchar(255)      not null,
    description  text              not null default '',
    status       tinyint unsigned  not null default 0, -- 0 - not sent; 1 - sent
    viewed_at    datetime          null,
    created_at   datetime          not null default now(),
    primary key (event_id, user_id, type, type_counter)
);

create table event_participants
(
    event_id   bigint   not null references events (id) on update cascade on delete cascade,
    user_id    bigint   not null references users (id) on update cascade on delete cascade,
    created_at datetime not null default now(),
    primary key (event_id, user_id)
);