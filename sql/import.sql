CREATE
    DATABASE pollgram;
USE
    pollgram;

CREATE TABLE Users
(
    name      varchar(255) NOT NULL,
    surname   varchar(255) NOT NULL,
    birthdate date         NOT NULL,
    username  varchar(255) NOT NULL,
    password  varchar(255) NOT NULL,
    is_admin  tinyint(1)   NOT NULL DEFAULT '0',
    PRIMARY KEY (username),
    UNIQUE KEY username (username)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE polls
(
    id               int        NOT NULL AUTO_INCREMENT,
    question         text       NOT NULL,
    min_rating       int                 DEFAULT NULL,
    max_rating       int                 DEFAULT NULL,
    is_active        int        NOT NULL,
    date_created     datetime   NOT NULL,
    deadline         datetime   NOT NULL,
    multiple_options tinyint(1) NOT NULL DEFAULT '0',
    voters           varchar(255)        DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE = InnoDB
  AUTO_INCREMENT = 8
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE votes
(
    id      int          NOT NULL AUTO_INCREMENT,
    poll_id int          NOT NULL,
    user_id varchar(255) NOT NULL,
    choices varchar(255) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY poll_id_user_id_unique (poll_id, user_id),
    KEY fk_votes_user_id (user_id),
    CONSTRAINT fk_votes_poll_id FOREIGN KEY (poll_id) REFERENCES polls (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_votes_user_id FOREIGN KEY (user_id) REFERENCES Users (username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE choices
(
    id               int  NOT NULL AUTO_INCREMENT,
    poll_id          int  NOT NULL,
    choice           text NOT NULL,
    votes            int          DEFAULT '0',
    selected_choices varchar(255) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY fk_choices_poll_id (poll_id),
    CONSTRAINT fk_choices_poll_id FOREIGN KEY (poll_id) REFERENCES polls (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 78
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;
