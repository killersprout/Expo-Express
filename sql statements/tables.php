create table categories
(
    organization varchar(50) not null,
    event_name   varchar(50) not null,
    index_cat    int auto_increment,
    parent_id    int         not null,
    category     varchar(50) not null,
    has_children int         not null
);

create index index_cat
    on categories (index_cat);
 engine: InnoDB collation: latin1_swedish_ci


 create table comments
(
    comment_id      int(3) auto_increment
        primary key,
    comment_post_id int(3)       not null,
    comment_author  varchar(255) not null,
    comment_context text         not null,
    comment_status  varchar(255) not null,
    comment_date    date         not null
);
 engine: InnoDB collation: latin1_swedish_ci


 create table exhibits
(
    is_published tinyint(1) default 0 not null,
    exhibitor    text                 not null,
    event        text                 not null,
    title        text                 null,
    exhibit_id   int                  null,
    visits       int                  null
);
 engine: InnoDB collation: latin1_swedish_ci


 create table judging
(
    id           int(10)      not null,
    exhibitname  varchar(100) not null,
    rating       varchar(100) not null,
    comments     varchar(256) not null,
    event        varchar(255) null,
    organization varchar(255) null
);
 engine: InnoDB collation: latin1_swedish_ci

 create table templates
(
    title text not null,
    link  text not null
);
engine: InnoDB collation: latin1_swedish_ci

 create table users
(
    user_id         int(3) auto_increment
        primary key,
    username        varchar(255)                           not null,
    user_firstname  varchar(255)                           not null,
    user_lastname   varchar(255)                           not null,
    user_password   varchar(255)                           not null,
    user_email      varchar(255)                           not null,
    template_layout varchar(255)                           null,
    user_role       varchar(255) default 'Attendee'        not null,
    exhibit_name    varchar(255)                           not null,
    division_name   varchar(255)                           not null,
    event_name      varchar(255)                           not null,
    organization    varchar(255)                           null,
    head_count      int(3)                                 not null,
    date            datetime     default CURRENT_TIMESTAMP not null,
    hash            varchar(255)                           not null,
    active          int(3)       default 1                 not null
);
engine: InnoDB collation: latin1_swedish_ci

 create table voting
(
    id           int           not null,
    exhibitname  varchar(100)  not null,
    event        varchar(300)  not null,
    organization varchar(100)  not null,
    votes        varchar(1000) not null
);
 engine: InnoDB collation: latin1_swedish_ci

 create table voting_questions
(
    organization   varchar(255)  not null,
    event          varchar(50)   not null,
    voting         varchar(1000) not null,
    judging        varchar(1000) not null,
    voting_visible int(100)      not null
);
engine: InnoDB collation: latin1_swedish_ci