DROP DATABASE IF EXISTS ps;
CREATE DATABASE ps CHARACTER SET utf8 COLLATE utf8_general_ci;
USE ps;
/*
Created: 14.08.2010
Modified: 17.12.2012
Model: MySQL 5.1
Database: MySQL 5.1
*/

-- Create tables section -------------------------------------------------

-- Table users

CREATE TABLE users
(
  id_user Int UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Мой комментарий',
  user_name Varchar(255) NOT NULL,
  dt_reg Int UNSIGNED NOT NULL,
  b_sex Tinyint UNSIGNED NOT NULL DEFAULT 0,
  email Varchar(80) NOT NULL,
  passwd Char(32) NOT NULL,
  b_admin Bool NOT NULL DEFAULT 0,
  id_avatar Int UNSIGNED
  COMMENT 'Идентификатор аватара. Может быть загруженным файлом или аватаром по умолчанию',
  about Text,
  about_src Text,
  contacts Text,
  contacts_src Text,
  msg Text,
  msg_src Text,
  timezone Varchar(50),
 PRIMARY KEY (id_user)
)
  AUTO_INCREMENT = 100
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE users ADD UNIQUE email (email)
;

-- Table blog_rubric

CREATE TABLE blog_rubric
(
  id_rubric Tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  name Varchar(255) NOT NULL,
  ident Varchar(64),
  content Text,
  b_tpl Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_rubric)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table train_rubric

CREATE TABLE train_rubric
(
  id_rubric Tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  name Varchar(255) NOT NULL,
  content Text,
  ident Varchar(64),
  b_tpl Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_rubric)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table blog_post

CREATE TABLE blog_post
(
  id_post Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_rubric Tinyint UNSIGNED NOT NULL,
  name Varchar(255) NOT NULL,
  ident Char(80) NOT NULL,
  b_show Bool NOT NULL DEFAULT 0,
  dt_publication Int UNSIGNED,
  rev_count Int UNSIGNED NOT NULL DEFAULT 0,
  b_tpl Bool NOT NULL DEFAULT 1,
  content Text,
  content_showcase Text,
 PRIMARY KEY (id_post)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE blog_post ADD UNIQUE ident (ident)
;

-- Table blog_post_comments

CREATE TABLE blog_post_comments
(
  id_comment Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_parent Int UNSIGNED,
  id_post Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED,
  dt_event Int UNSIGNED NOT NULL,
  content Text NOT NULL,
  b_deleted Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
  n_deep Tinyint UNSIGNED NOT NULL,
  b_know Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_comment)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table issue_post_comments

CREATE TABLE issue_post_comments
(
  id_comment Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_parent Int UNSIGNED,
  id_post Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED,
  dt_event Int UNSIGNED NOT NULL,
  content Text NOT NULL,
  b_deleted Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
  n_deep Tinyint UNSIGNED NOT NULL,
  b_know Bool NOT NULL DEFAULT 0
  COMMENT 'Признак - оповещён ли автор сообщения о том, что на него есть ответ',
 PRIMARY KEY (id_comment)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table feedback

CREATE TABLE feedback
(
  id_feedback Int UNSIGNED NOT NULL AUTO_INCREMENT,
  user_name Varchar(255) NOT NULL,
  contacts Varchar(255),
  dt_event Int UNSIGNED NOT NULL,
  theme Varchar(255) NOT NULL,
  content Text NOT NULL,
  b_deleted Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_feedback)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table feedback_user

CREATE TABLE feedback_user
(
  id_feedback Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_parent Int UNSIGNED,
  id_user Int UNSIGNED NOT NULL,
  id_owner Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  theme Varchar(255),
  content Text,
  b_deleted Bool NOT NULL DEFAULT 0,
  id_template Tinyint UNSIGNED,
  b_new Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_feedback)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Сообщения обратной связи'
;

-- Table train_post

CREATE TABLE train_post
(
  id_post Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_rubric Tinyint UNSIGNED NOT NULL,
  name Varchar(255) NOT NULL,
  ident Char(80) NOT NULL,
  b_show Bool NOT NULL DEFAULT 0,
  dt_publication Int UNSIGNED,
  rev_count Int UNSIGNED NOT NULL DEFAULT 0,
  b_tpl Bool NOT NULL DEFAULT 1,
  content Text,
  content_showcase Text,
 PRIMARY KEY (id_post)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE train_post ADD UNIQUE ident (ident)
;

-- Table train_post_comments

CREATE TABLE train_post_comments
(
  id_comment Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_parent Int UNSIGNED,
  id_post Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED,
  dt_event Int UNSIGNED NOT NULL,
  content Text NOT NULL,
  b_deleted Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
  n_deep Tinyint UNSIGNED NOT NULL,
  b_know Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_comment)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table message_template

CREATE TABLE message_template
(
  id_template Tinyint UNSIGNED NOT NULL,
  content Text NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE message_template ADD PRIMARY KEY (id_template)
;

-- Table page_watch

CREATE TABLE page_watch
(
  id_user Int UNSIGNED,
  dt_event Int UNSIGNED NOT NULL,
  page_ident Char(80) NOT NULL,
  watch_count Int UNSIGNED NOT NULL DEFAULT 0
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_misprint

CREATE TABLE ps_misprint
(
  id_missprint Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user Int UNSIGNED,
  ident Char(32) NOT NULL,
  text Varchar(255) NOT NULL,
  note Text,
  url Char(80) NOT NULL,
  b_deleted Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_missprint),
 UNIQUE id_missprint (id_missprint)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_upload

CREATE TABLE ps_upload
(
  id_upload Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user Int UNSIGNED,
  name Varchar(255) NOT NULL,
  original_name Varchar(255) NOT NULL,
  mime_type Varchar(255) NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  type Char(1) NOT NULL
  COMMENT 'Тип файла:
A-аватар',
  b_deleted Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_upload),
 UNIQUE id_upload (id_upload)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Таблица загрузок файлов пользователями'
;

-- Table ps_timeline

CREATE TABLE ps_timeline
(
  id_timeline Tinyint UNSIGNED NOT NULL,
  v_name Varchar(255) NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE ps_timeline ADD PRIMARY KEY (id_timeline)
;

ALTER TABLE ps_timeline ADD UNIQUE id_timeline (id_timeline)
;

-- Table ps_timeline_item

CREATE TABLE ps_timeline_item
(
  id_timeline_item Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_timeline Tinyint UNSIGNED NOT NULL,
  v_title Varchar(255),
  content Text,
  date_start Varchar(20) NOT NULL,
  date_end Varchar(20),
  id_master_inst Int UNSIGNED,
  v_master_ident Char(80),
 PRIMARY KEY (id_timeline_item),
 UNIQUE id_timeline_item (id_timeline_item)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table gym_exercises

CREATE TABLE gym_exercises
(
  id_gym_ex Tinyint UNSIGNED NOT NULL,
  name Varchar(255) NOT NULL,
  n_order Tinyint UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE gym_exercises ADD PRIMARY KEY (id_gym_ex)
;

ALTER TABLE gym_exercises ADD UNIQUE gym_ex_id (id_gym_ex)
;

-- Table muscle_group

CREATE TABLE muscle_group
(
  id_muscle_group Tinyint UNSIGNED NOT NULL,
  name Varchar(255) NOT NULL,
  n_order Int UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE muscle_group ADD PRIMARY KEY (id_muscle_group)
;

ALTER TABLE muscle_group ADD UNIQUE id_muscle_group (id_muscle_group)
;

-- Table gym_exercises2muscle_group

CREATE TABLE gym_exercises2muscle_group
(
  id_gym_ex Tinyint UNSIGNED NOT NULL,
  id_muscle_group Tinyint UNSIGNED NOT NULL,
  n_order Int UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE gym_exercises2muscle_group ADD PRIMARY KEY (id_gym_ex,id_muscle_group)
;

-- Table gym_programm

CREATE TABLE gym_programm
(
  id_gym_programm Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user Int UNSIGNED,
  name Varchar(255),
  description Text,
 PRIMARY KEY (id_gym_programm)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table gym_sets

CREATE TABLE gym_sets
(
  id_set Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_gym_programm Int UNSIGNED NOT NULL,
  id_gym_programm_exercise Int UNSIGNED NOT NULL,
  value Varchar(255) NOT NULL,
  n_order Int UNSIGNED NOT NULL,
 PRIMARY KEY (id_set),
 UNIQUE id_set (id_set)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table gym_programm_exercises

CREATE TABLE gym_programm_exercises
(
  id_gym_programm_exercise Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_gym_programm Int UNSIGNED NOT NULL,
  id_gym_ex Tinyint UNSIGNED,
  n_order Int UNSIGNED NOT NULL,
  name Varchar(255),
  description Text,
 PRIMARY KEY (id_gym_programm_exercise),
 UNIQUE id_rec (id_gym_programm_exercise)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table user_lessons

CREATE TABLE user_lessons
(
  id_user_lesson Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user Int UNSIGNED NOT NULL,
  id_post Int UNSIGNED NOT NULL,
  b_passed Bool NOT NULL DEFAULT 0,
  comment Text,
 PRIMARY KEY (id_user_lesson),
 UNIQUE id_user_lesson (id_user_lesson)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

CREATE UNIQUE INDEX idx_userlessons_user_post ON user_lessons (id_user,id_post)
;

-- Table ps_testing

CREATE TABLE ps_testing
(
  id_testing Int UNSIGNED NOT NULL AUTO_INCREMENT,
  name Varchar(255),
  n_time Tinyint UNSIGNED NOT NULL,
  n_tasks Tinyint UNSIGNED NOT NULL,
 PRIMARY KEY (id_testing),
 UNIQUE id_testing (id_testing)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_testing_results

CREATE TABLE ps_testing_results
(
  id_testing_result Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_testing Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED NOT NULL,
  n_time Int UNSIGNED NOT NULL,
 PRIMARY KEY (id_testing_result),
 UNIQUE id_testing_result (id_testing_result)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_testing_result_content

CREATE TABLE ps_testing_result_content
(
  id_testing_result Int UNSIGNED,
  n_task Tinyint UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_img_mosaic_parts

CREATE TABLE ps_img_mosaic_parts
(
  id_img Int UNSIGNED NOT NULL,
  n_part Int NOT NULL,
  id_user Int UNSIGNED,
  dt_event Int UNSIGNED,
  x_cell Int UNSIGNED NOT NULL,
  y_cell Int UNSIGNED NOT NULL,
  owned Bool NOT NULL DEFAULT 0
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

CREATE UNIQUE INDEX Index1 ON ps_img_mosaic_parts (id_img,n_part)
;

-- Table ps_img_mosaic

CREATE TABLE ps_img_mosaic
(
  id_img Int UNSIGNED NOT NULL AUTO_INCREMENT,
  w Int UNSIGNED NOT NULL,
  h Int UNSIGNED NOT NULL,
  cx Int UNSIGNED NOT NULL,
  cy Int UNSIGNED NOT NULL,
  cw Int UNSIGNED NOT NULL,
  ch Int UNSIGNED NOT NULL,
 PRIMARY KEY (id_img),
 UNIQUE id_img (id_img)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_img_mosaic_answers

CREATE TABLE ps_img_mosaic_answers
(
  id_answer Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_img Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED,
  v_answer Varchar(255) NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  b_winner Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_answer),
 UNIQUE id_answer (id_answer)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

CREATE UNIQUE INDEX Index1 ON ps_img_mosaic_answers (id_img,id_user)
;

-- Table ps_user_points

CREATE TABLE ps_user_points
(
  id_point Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user Int UNSIGNED,
  id_reason Tinyint UNSIGNED NOT NULL,
  id_inst Int UNSIGNED,
  n_cnt Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  b_shown Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_point),
 UNIQUE id_point (id_point)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_chess_knight

CREATE TABLE ps_chess_knight
(
  id_solution Int UNSIGNED NOT NULL AUTO_INCREMENT,
  v_solution Char(128) NOT NULL,
  b_system Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_solution),
 UNIQUE id_solution (id_solution)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE ps_chess_knight ADD UNIQUE v_solution (v_solution)
;

-- Table ps_chess_knight2user

CREATE TABLE ps_chess_knight2user
(
  id_user Int UNSIGNED NOT NULL,
  id_solution Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE ps_chess_knight2user ADD PRIMARY KEY (id_user,id_solution)
;

-- Table issue_post

CREATE TABLE issue_post
(
  id_post Int UNSIGNED NOT NULL,
  ident Char(80) NOT NULL,
  name Varchar(255) NOT NULL,
  b_show Bool NOT NULL DEFAULT 0,
  dt_publication Int UNSIGNED,
  rev_count Int NOT NULL DEFAULT 0
)
;

ALTER TABLE issue_post ADD PRIMARY KEY (id_post)
;

ALTER TABLE issue_post ADD UNIQUE ident (ident)
;

-- Table pass_remind

CREATE TABLE pass_remind
(
  id_user Int UNSIGNED NOT NULL,
  v_code Char(32) NOT NULL,
  dt_add Int UNSIGNED NOT NULL,
  dt_used Int UNSIGNED,
  n_status Tinyint UNSIGNED NOT NULL DEFAULT 0
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE pass_remind MODIFY v_code Char(32) BINARY NOT NULL;

ALTER TABLE pass_remind ADD UNIQUE v_code (v_code)
;

-- Table ps_user_logins

CREATE TABLE ps_user_logins
(
  id_action Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_parent Int UNSIGNED,
  id_user Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  n_action Tinyint UNSIGNED NOT NULL,
  v_ip Varchar(30),
  v_agent Varchar(255),
 PRIMARY KEY (id_action),
 UNIQUE id_action (id_action)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_matches_ans

CREATE TABLE ps_matches_ans
(
  id_solution Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_task Int UNSIGNED,
  v_solution Varchar(255) NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
 PRIMARY KEY (id_solution),
 UNIQUE id_solution (id_solution)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_matches_task

CREATE TABLE ps_matches_task
(
  id_task Int UNSIGNED NOT NULL AUTO_INCREMENT,
  ident Char(80) NOT NULL,
 PRIMARY KEY (id_task),
 UNIQUE id_task (id_task)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE ps_matches_task ADD UNIQUE ident (ident)
;

-- Table ps_matches_ans2user

CREATE TABLE ps_matches_ans2user
(
  id_user Int UNSIGNED NOT NULL,
  id_solution Int UNSIGNED NOT NULL,
  id_task Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE ps_matches_ans2user ADD PRIMARY KEY (id_user,id_solution)
;

-- Table ps_user_popups

CREATE TABLE ps_user_popups
(
  id_user Int UNSIGNED NOT NULL,
  v_type Char(1) NOT NULL,
  v_ident Varchar(255) NOT NULL,
  n_order Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Избранные всплывающие страницы пользователя'
;

-- Create relationships section ------------------------------------------------- 

ALTER TABLE blog_post ADD CONSTRAINT Relationship2 FOREIGN KEY (id_rubric) REFERENCES blog_rubric (id_rubric) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE blog_post_comments ADD CONSTRAINT Relationship3 FOREIGN KEY (id_post) REFERENCES blog_post (id_post) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE blog_post_comments ADD CONSTRAINT Relationship4 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE train_post ADD CONSTRAINT Relationship5 FOREIGN KEY (id_rubric) REFERENCES train_rubric (id_rubric) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE train_post_comments ADD CONSTRAINT Relationship6 FOREIGN KEY (id_post) REFERENCES train_post (id_post) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE train_post_comments ADD CONSTRAINT Relationship7 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE issue_post_comments ADD CONSTRAINT Relationship21 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE page_watch ADD CONSTRAINT Relationship28 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE train_post_comments ADD CONSTRAINT Relationship32 FOREIGN KEY (id_parent) REFERENCES train_post_comments (id_comment) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE blog_post_comments ADD CONSTRAINT Relationship33 FOREIGN KEY (id_parent) REFERENCES blog_post_comments (id_comment) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE issue_post_comments ADD CONSTRAINT Relationship34 FOREIGN KEY (id_parent) REFERENCES issue_post_comments (id_comment) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE feedback_user ADD CONSTRAINT Relationship35 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE feedback_user ADD CONSTRAINT Relationship36 FOREIGN KEY (id_parent) REFERENCES feedback_user (id_feedback) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE feedback_user ADD CONSTRAINT Relationship37 FOREIGN KEY (id_template) REFERENCES message_template (id_template) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE feedback_user ADD CONSTRAINT Relationship38 FOREIGN KEY (id_owner) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_misprint ADD CONSTRAINT Relationship40 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_upload ADD CONSTRAINT Relationship41 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_timeline_item ADD CONSTRAINT Relationship44 FOREIGN KEY (id_timeline) REFERENCES ps_timeline (id_timeline) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE gym_exercises2muscle_group ADD CONSTRAINT Relationship45 FOREIGN KEY (id_gym_ex) REFERENCES gym_exercises (id_gym_ex) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE gym_exercises2muscle_group ADD CONSTRAINT Relationship46 FOREIGN KEY (id_muscle_group) REFERENCES muscle_group (id_muscle_group) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE gym_programm ADD CONSTRAINT Relationship47 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE gym_programm_exercises ADD CONSTRAINT Relationship50 FOREIGN KEY (id_gym_programm) REFERENCES gym_programm (id_gym_programm) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE gym_programm_exercises ADD CONSTRAINT Relationship51 FOREIGN KEY (id_gym_ex) REFERENCES gym_exercises (id_gym_ex) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE gym_sets ADD CONSTRAINT Relationship53 FOREIGN KEY (id_gym_programm_exercise) REFERENCES gym_programm_exercises (id_gym_programm_exercise) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE gym_sets ADD CONSTRAINT Relationship54 FOREIGN KEY (id_gym_programm) REFERENCES gym_programm (id_gym_programm) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE user_lessons ADD CONSTRAINT Relationship55 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE user_lessons ADD CONSTRAINT Relationship56 FOREIGN KEY (id_post) REFERENCES train_post (id_post) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_testing_results ADD CONSTRAINT Relationship60 FOREIGN KEY (id_testing) REFERENCES ps_testing (id_testing) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_testing_results ADD CONSTRAINT Relationship62 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_testing_result_content ADD CONSTRAINT Relationship63 FOREIGN KEY (id_testing_result) REFERENCES ps_testing_results (id_testing_result) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_img_mosaic_parts ADD CONSTRAINT Relationship64 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_img_mosaic_parts ADD CONSTRAINT Relationship65 FOREIGN KEY (id_img) REFERENCES ps_img_mosaic (id_img) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_img_mosaic_answers ADD CONSTRAINT Relationship66 FOREIGN KEY (id_img) REFERENCES ps_img_mosaic (id_img) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_img_mosaic_answers ADD CONSTRAINT Relationship67 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_user_points ADD CONSTRAINT Relationship71 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_chess_knight2user ADD CONSTRAINT Relationship72 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_chess_knight2user ADD CONSTRAINT Relationship73 FOREIGN KEY (id_solution) REFERENCES ps_chess_knight (id_solution) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE issue_post_comments ADD CONSTRAINT Relationship74 FOREIGN KEY (id_post) REFERENCES issue_post (id_post) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE pass_remind ADD CONSTRAINT Relationship75 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_user_logins ADD CONSTRAINT Relationship76 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_user_logins ADD CONSTRAINT Relationship77 FOREIGN KEY (id_parent) REFERENCES ps_user_logins (id_action) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_matches_ans ADD CONSTRAINT Relationship78 FOREIGN KEY (id_task) REFERENCES ps_matches_task (id_task) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_matches_ans2user ADD CONSTRAINT Relationship79 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_matches_ans2user ADD CONSTRAINT Relationship80 FOREIGN KEY (id_solution) REFERENCES ps_matches_ans (id_solution) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_matches_ans2user ADD CONSTRAINT Relationship81 FOREIGN KEY (id_task) REFERENCES ps_matches_task (id_task) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_user_popups ADD CONSTRAINT Relationship82 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;



/*
  Пользователи
*/

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg,
                  b_admin)
VALUES (1,
        'admin',
        '1',
        'admin@mail.ru',
        '96e79218965eb72c92a549dd5a330112',
        UNIX_TIMESTAMP(),
        1);

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg)
VALUES (100,
        'Илья',
        '1',
        'azaz@mail.ru',
        '96e79218965eb72c92a549dd5a330112',
        UNIX_TIMESTAMP());


/*
  == ЖУРНАЛ ==
*/

INSERT INTO issue_post(id_post,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (1,
        'Выпуск №1',
        'Issue1',
        1,
        UNIX_TIMESTAMP('2010-01-01'));

INSERT INTO issue_post(id_post,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (2,
        'Выпуск №2',
        'Issue2',
        1,
        UNIX_TIMESTAMP('2010-01-10'));

INSERT INTO issue_post(id_post,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (3,
        'Выпуск №3',
        'Issue3',
        1,
        UNIX_TIMESTAMP('2010-01-20'));


INSERT INTO issue_post(id_post,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (4,
        'Выпуск №4',
        'Issue4',
        1,
        UNIX_TIMESTAMP('2010-01-30'));


INSERT INTO issue_post(id_post,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (5,
        'Выпуск №5',
        'Issue5',
        1,
        UNIX_TIMESTAMP('2010-02-01'));


/*
  == БЛОГ ==
*/

-- РУБРИКИ --

INSERT INTO blog_rubric(id_rubric,
                        name,
                        content,
                        ident,
                        b_tpl)
VALUES (1,
        'Великие люди',
        '<p>Информация зыгружена из базы.</p>',
        'famous',
        1);

INSERT INTO blog_rubric(id_rubric,
                        name,
                        content,
                        ident,
                        b_tpl)
VALUES (
          2,
          'Спорт в жизни студента',
          '<p>В данном разделе блога собраны все заметки, касающиеся спорта в жизни студента и здоровья в целом. Я не стал разносить данные темы по отдельным рубрикам, т.к. они (как Вы понимаете) крайне тесно связаны...</p>',
          'sport',
          0);


-- ПОСТЫ --

INSERT INTO blog_post(id_rubric,
                      name,
                      ident,
                      b_show,
                      dt_publication)
VALUES (1,
        'Пифагор Самосский',
        'pifagor',
        1,
        UNIX_TIMESTAMP('2010-01-01'));

INSERT INTO blog_post(id_rubric,
                      name,
                      ident,
                      b_show,
                      dt_publication)
VALUES (1,
        'Евклид',
        'euclide',
        1,
        UNIX_TIMESTAMP('2010-01-05'));
        
INSERT INTO blog_post(id_rubric,
                      name,
                      ident,
                      b_show,
                      dt_publication)
VALUES (1,
        'Архимед',
        'arhimed',
        1,
        UNIX_TIMESTAMP('2010-01-10'));


-- 'Ричард Филлипс Фейнман', BP_FM1_feynman
-- 'Альберт Эйнштейн', BP_FM2_einstein

INSERT INTO blog_post(id_rubric,
                      name,
                      ident,
                      b_show,
                      dt_publication)
VALUES (2,
        'Турники - всё, что Вам нужно',
        'BP_SP1_turniki',
        1,
        UNIX_TIMESTAMP('2010-01-11'));


INSERT INTO blog_post(id_rubric,
                      name,
                      ident,
                      b_show,
                      dt_publication)
VALUES (2,
        'Как не посадить зрение',
        'BP_SP2_zrenie',
        1,
        UNIX_TIMESTAMP('2010-01-15'));




/*
  == ФИЗМАТ КРУЖОК ==
*/

-- РУБРИКИ --

INSERT INTO train_rubric(id_rubric,
                         name,
                         content,
                         ident,
                         b_tpl)
VALUES (
          1,
          'Математика',
          '<p>В данном разделе собраны материалы разборов различных школьных и институтских тем по математике.</p> <p>Если у Вас есть вопросы, предложение рассмотреть интересующую вас математическую задачку или раздел - <a href="feedback.php#feed">пишите</a>, буду рад помочь.</p><p>Желаю удачи в изучении!;)</p>',
          'math',
          0);

INSERT INTO train_rubric(id_rubric,
                         name,
                         content,
                         ident,
                         b_tpl)
VALUES (
          2,
          'Физика',
          '<p>В данном разделе собраны материалы разборов различных школьных и институтских тем по физике.</p> <p>Если у Вас есть вопросы, предложение рассмотреть интересующую Вас задачку по физике или тему&nbsp;&ndash; <a href="feedback.php#feed">пишите</a>, буду рад помочь.</p>',
          'phys',
          0);


-- ПОСТЫ --

INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (1,
        'Тригонометрия. Введение.',
        'trigonometry',
        1,
        UNIX_TIMESTAMP('2010-01-01'));

INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (
          1,
          'Тригонометрия. Тригонометрический круг.',
          'trigonometry_krug',
          1,
          UNIX_TIMESTAMP('2010-01-03'));

INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (1,
        'Тригонометрия. Вывод формул.',
        'trigonometry_formula',
        1,
        UNIX_TIMESTAMP('2010-01-06'));

INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       dt_publication)
VALUES (1,
        'Тест по тригонометрии.',
        'trigonometry_testing',
        UNIX_TIMESTAMP('2010-01-08'));

INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (1,
        'Векторы',
        'vectors',
        1,
        UNIX_TIMESTAMP('2010-01-10'));

INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (1,
        'Декартова система координат',
        'dekart_system',
        1,
        UNIX_TIMESTAMP('2010-01-30'));


INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (2,
        'Кинематика точки. Введение.',
        'kinemtochki',
        1,
        UNIX_TIMESTAMP('2010-02-05'));

INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (2,
        'Кинематика точки. Виды движений.',
        'kinemtochki2',
        1,
        UNIX_TIMESTAMP('2010-02-07'));


INSERT INTO train_post(id_rubric,
                       name,
                       ident,
                       b_show,
                       dt_publication)
VALUES (1,
        'Матрицы',
        'matrix',
        1,
        UNIX_TIMESTAMP('2010-02-15'));






/*
 * Инфо сообщения
*/

INSERT INTO message_template(id_template, content)
VALUES (1, 'Базовое шаблонное сообщение.');

INSERT INTO message_template(id_template, content)
VALUES (
          5,
          'Спасибо за участие! Ваше сообщение учтено.');



/*
  == РЕШЕНИЕ ЗАДАЧИ О ХОДЕ КОНЯ ==
*/

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '11325172847688674827152331527183758768472816241233416281738577583718261422436456355466788674826142211325173857364463553446655345',
          1);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '58778573816241221426183756688775837152311224162847667886748261422113251738577688674827152311325172533446658463443655433554334564',
          1);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '81738577583718261422416283758768472816241231527163847688674827152311325172645635435536173857788674826142211325335466455334466544',
          1);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '81738577583718261422416283758768472816241231527163847688674827152311325172645635435536173857788674826142211325446546345345665433',
          0);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '83758768472816241231527163827486785738172513214261738162412214261837587785644351728476886748271523113253654634553644563554664533',
          0);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '18261422416281738577583716241231527183758768472836153453655745332113251738466788768472513211233527485644638261425466788674554364',
          0);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '18375877857381624122142638577886748261422113251736556371837587684728162412315264563543517284768867482746341523113244655345665433',
          0);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '88768472513211231527486786748261422113251738577866857381624122142618375877655334465433123152718375876847281624456443355644365563',
          0);

INSERT INTO ps_chess_knight(v_solution, b_system)
VALUES (
          '81738577583718261422416283758768472816241231527163847688674827152311325172645635435536173857788674826142211325446553344654664533',
          0);


/*
  == ХРОНОЛОГИЧЕСКИЕ ШКАЛЫ ==
*/

INSERT INTO ps_timeline(id_timeline, v_name)
VALUES (1, 'Хронология жизни великих людей');


INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Пифагор',
        '',
        '570 BC',
        '490 BC',
        'pifagor');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Евклид',
        '',
        '365 BC',
        '300 BC',
        'euclide');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Архимед',
        '',
        '287 BC',
        '212 BC',
        'arhimed');


INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Пётр Леонидович Капица',
        '<p>Капица, великий человек!</p>',
        '1894-07-08',
        '1984-04-08',
        'PetrKapica');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Мария Склодовская-Кюри',
        '<p>Мария Кюри, великий человек!</p>',
        '1867-11-07',
        '1934-05-04',
        'MarieCurie');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Ричард Филлипс Фейнман',
        '<p>Фейнман, великий человек!</p>',
        '1918-05-11',
        '1988-02-15',
        'BP_FM1_feynman');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Альберт Эйнштейн',
        '<p>Эйнштейн, великий человек!</p>',
        '1879-03-14',
        '1955-04-18',
        'BP_FM2_einstein');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Вернер Карл Гейзенберг',
        '<p>Эйнштейн, великий человек!</p>',
        '1901-12-05',
        '1976-02-01',
        'KarlHeisenberg');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Вольфганг Эрнст Паули',
        '<p>Эйнштейн, великий человек!</p>',
        '1900-04-25',
        '1958-12-15',
        'ErnstPauli');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Нильс Хенрик Давид Бор',
        '<p>Эйнштейн, великий человек!</p>',
        '1885-10-07',
        '1962-11-18',
        'NielsBohr');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Макс Карл Эрнст Людвиг Планк',
        '<p>Макс Карл Эрнст Людвиг Планк</p>',
        '1858-04-23',
        '1947-10-04',
        'MaxPlanck');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Ян Дидерик Ван-дер-Ваальс',
        '<p>Макс Карл Эрнст Людвиг Планк</p>',
        '1837-11-23',
        '1923-03-08',
        'VanDerWaals');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (1,
        'Галилео Галилей',
        '<p>Макс Карл Эрнст Людвиг Планк</p>',
        '1564-02-15',
        '1642-01-08',
        'GalileoGalilei');

INSERT INTO ps_timeline_item(id_timeline,
                             v_title,
                             content,
                             date_start,
                             date_end,
                             v_master_ident)
VALUES (
          1,
          'Исаак Ньютон',
          '<p>Английский математик, астроном, физик, механик, заложивший основы классической механики, он объяснил движение небесных тел – планет вокруг Солнца и Луны вокруг Земли. Самым известным его открытием был закон всемирного тяготения. </p>',
          '1642-12-25',
          '1727-03-20',
          'IsaacNewton');


/*
    !!!ПРЕДСТАВЛЕНИЯ!!!
 */

CREATE OR REPLACE VIEW v_issue_post
AS
   SELECT *
     FROM issue_post
    WHERE     b_show = 1
          AND dt_publication IS NOT NULL
          AND dt_publication < unix_timestamp()
   ORDER BY dt_publication DESC, id_post DESC;


CREATE OR REPLACE VIEW v_train_post
AS
   SELECT *
     FROM train_post
    WHERE     b_show = 1
          AND dt_publication IS NOT NULL
          AND dt_publication < unix_timestamp()
   ORDER BY dt_publication DESC, id_post DESC;

CREATE OR REPLACE VIEW v_train_rubric
AS
   SELECT *
     FROM train_rubric r
    WHERE EXISTS
             (SELECT 1
                FROM v_train_post p
               WHERE p.id_rubric = r.id_rubric)
   ORDER BY r.name ASC;

CREATE OR REPLACE VIEW v_blog_post
AS
   SELECT *
     FROM blog_post
    WHERE     b_show = 1
          AND dt_publication IS NOT NULL
          AND dt_publication < unix_timestamp()
   ORDER BY dt_publication DESC, id_post DESC;


CREATE OR REPLACE VIEW v_blog_rubric
AS
   SELECT *
     FROM blog_rubric r
    WHERE EXISTS
             (SELECT 1
                FROM v_blog_post p
               WHERE p.id_rubric = r.id_rubric)
   ORDER BY r.name ASC;
 /*
 	GYM
 */
/*
	Очистка
*/
 delete from gym_sets;
 delete from gym_programm_exercises;
 delete from gym_programm;
 delete from gym_exercises2muscle_group;
 delete from muscle_group;
 delete from gym_exercises;



/*ГРУППЫ МЫШЦ*/
insert into muscle_group (id_muscle_group, name, n_order) values (1, 'Трапеция', 1);
insert into muscle_group (id_muscle_group, name, n_order) values (2, 'Дельты', 2);
insert into muscle_group (id_muscle_group, name, n_order) values (3, 'Грудные', 3);
insert into muscle_group (id_muscle_group, name, n_order) values (4, 'Бицепс', 4);
insert into muscle_group (id_muscle_group, name, n_order) values (5, 'Предплечья', 5);
insert into muscle_group (id_muscle_group, name, n_order) values (6, 'Трицепс', 6);
insert into muscle_group (id_muscle_group, name, n_order) values (7, 'Прес', 7);
insert into muscle_group (id_muscle_group, name, n_order) values (8, 'Широчайшие', 8);
insert into muscle_group (id_muscle_group, name, n_order) values (9, 'Разгибатели спины', 9);
insert into muscle_group (id_muscle_group, name, n_order) values (10, 'Ягодичные', 10);
insert into muscle_group (id_muscle_group, name, n_order) values (11, 'Квадрицепсы', 11);
insert into muscle_group (id_muscle_group, name, n_order) values (12, 'Бицепс бедра', 12);
insert into muscle_group (id_muscle_group, name, n_order) values (13, 'Икры', 13);
 
 
/*УПРАЖНЕНИЯ*/
 
/*Трицепс*/
insert into gym_exercises (id_gym_ex, name, n_order) values (1, 'Жим штанги узким хватом', 1);
insert into gym_exercises (id_gym_ex, name, n_order) values (2, 'Отжимания в упоре сзади', 2);
insert into gym_exercises (id_gym_ex, name, n_order) values (3, 'Отжимания на параллельных брусьях', 3);
insert into gym_exercises (id_gym_ex, name, n_order) values (4, 'Разгибание руки с гантелью в наклоне', 4);
insert into gym_exercises (id_gym_ex, name, n_order) values (5, 'Разгибание гантели двумя руками из-за головы', 5);
insert into gym_exercises (id_gym_ex, name, n_order) values (6, 'Французский жим одной рукой', 6);
insert into gym_exercises (id_gym_ex, name, n_order) values (7, 'Разгибания рук на верхнем блоке', 7);
insert into gym_exercises (id_gym_ex, name, n_order) values (8, 'Разгибание одной руки с верхним блоком хватом снизу', 8);

/*Бицепс*/
insert into gym_exercises (id_gym_ex, name, n_order) values (10, 'Подъем штанги на бицепс', 10);
insert into gym_exercises (id_gym_ex, name, n_order) values (11, 'Поочередное сгибание рук на бицепс с гантелями', 11);
insert into gym_exercises (id_gym_ex, name, n_order) values (12, 'Поочередное сгибание рук на бицепс с гантелями хватом «молот»', 12);
insert into gym_exercises (id_gym_ex, name, n_order) values (13, 'Концентрированные сгибания рук на бицепс', 13);
insert into gym_exercises (id_gym_ex, name, n_order) values (14, 'Сгибание рук на бицепс со штангой обратным хватом', 14);

/*Грудь*/
insert into gym_exercises (id_gym_ex, name, n_order) values (21, 'Отжимания', 20);
insert into gym_exercises (id_gym_ex, name, n_order) values (22, 'Жим штанги лежа средним хватом на скамье с положит. наклоном', 21);
insert into gym_exercises (id_gym_ex, name, n_order) values (20, 'Жим лёжа средним хватом', 22);
insert into gym_exercises (id_gym_ex, name, n_order) values (25, 'Жим штанги лежа средним хватом на скамье с отрицат. наклоном', 23);
insert into gym_exercises (id_gym_ex, name, n_order) values (24, 'Жим гантелей лежа на скамье с положительным наклоном', 24);
insert into gym_exercises (id_gym_ex, name, n_order) values (23, 'Жим с гантелями лежа на горизонтальной скамье', 25);
insert into gym_exercises (id_gym_ex, name, n_order) values (26, 'Жим гантелей лежа на скамье с отрицательным наклоном', 26);
insert into gym_exercises (id_gym_ex, name, n_order) values (27, 'Пуловер с гантелей лежа на скамье', 27);
  
/*Трапеция*/
insert into gym_exercises (id_gym_ex, name, n_order) values (30, 'Шраги с гантелями', 30);
insert into gym_exercises (id_gym_ex, name, n_order) values (31, 'Тяга штанги к подбородку', 32);
insert into gym_exercises (id_gym_ex, name, n_order) values (32, 'Шраги со штангой', 31);

/*Широчайшие*/
insert into gym_exercises (id_gym_ex, name, n_order) values (40, 'Подтягивания прямым хватом', 40);
insert into gym_exercises (id_gym_ex, name, n_order) values (41, 'Подтягивания обратным хватом', 41);
insert into gym_exercises (id_gym_ex, name, n_order) values (42, 'Тяга верхнего блока перед собой', 42);
insert into gym_exercises (id_gym_ex, name, n_order) values (43, 'Тяга верхнего блока за шею', 43);
insert into gym_exercises (id_gym_ex, name, n_order) values (44, 'Тяга верхнего блока к груди с V образным грифом', 44);
insert into gym_exercises (id_gym_ex, name, n_order) values (45, 'Тяга верхнего блока прямыми руками к бёдрам', 45);
insert into gym_exercises (id_gym_ex, name, n_order) values (46, 'Тяга к груди в гребном тренажере', 46);
insert into gym_exercises (id_gym_ex, name, n_order) values (47, 'Тяга гантели в наклоне', 47);
insert into gym_exercises (id_gym_ex, name, n_order) values (48, 'Тяга штанги, стоя в наклоне', 48);
insert into gym_exercises (id_gym_ex, name, n_order) values (49, 'Тяга Т-образного грифа', 49);

/*Разгибатели спины*/
insert into gym_exercises (id_gym_ex, name, n_order) values (60, 'Гиперэкстензия', 60);
insert into gym_exercises (id_gym_ex, name, n_order) values (61, 'Наклоны вперёд со штангой на плечах («Доброе утро»)', 61);

/*Плечи (дельты)*/
insert into gym_exercises (id_gym_ex, name, n_order) values (70, 'Жим штанги с груди сидя', 70);
insert into gym_exercises (id_gym_ex, name, n_order) values (71, 'Жим гантелей сидя', 71);
insert into gym_exercises (id_gym_ex, name, n_order) values (72, 'Жим гантелей от Арнольда Шварценеггера', 72);
insert into gym_exercises (id_gym_ex, name, n_order) values (73, 'Подъёмы гантелей в стороны', 73);
insert into gym_exercises (id_gym_ex, name, n_order) values (74, 'Подъём гантелей перед собой', 74);
insert into gym_exercises (id_gym_ex, name, n_order) values (75, 'Подъём рук перед собой с гантелью', 75);
insert into gym_exercises (id_gym_ex, name, n_order) values (76, 'Подъём штанги перед собой на вытянутых руках', 76);
insert into gym_exercises (id_gym_ex, name, n_order) values (77, 'Подъём рук в стороны на тренажёре', 77);

/*Ягодичные*/
insert into gym_exercises (id_gym_ex, name, n_order) values (80, 'Выпады со штангой на плечах', 80);
insert into gym_exercises (id_gym_ex, name, n_order) values (81, 'Выпады с гантелями', 81);
insert into gym_exercises (id_gym_ex, name, n_order) values (82, 'Махи ногой назад на полу', 82);
insert into gym_exercises (id_gym_ex, name, n_order) values (83, 'Мостик лёжа', 83);
insert into gym_exercises (id_gym_ex, name, n_order) values (84, 'Разведение ног на тренажере', 84);
insert into gym_exercises (id_gym_ex, name, n_order) values (85, 'Махи ногой в стороны, лёжа на боку', 85);

/*Ноги*/
insert into gym_exercises (id_gym_ex, name, n_order) values (90, 'Приседания с гантелями', 90);
insert into gym_exercises (id_gym_ex, name, n_order) values (91, 'Приседания со штангой на груди', 91);
insert into gym_exercises (id_gym_ex, name, n_order) values (92, 'Приседания со штангой на плечах', 92);
insert into gym_exercises (id_gym_ex, name, n_order) values (93, 'Наклонный жим ногами', 93);
insert into gym_exercises (id_gym_ex, name, n_order) values (94, 'Разгибания ног в тренажёре', 94);
insert into gym_exercises (id_gym_ex, name, n_order) values (95, 'Сгибания ног в тренажёре', 95);

/*Икры*/
insert into gym_exercises (id_gym_ex, name, n_order) values (100, 'Подъём на носки стоя', 100);
insert into gym_exercises (id_gym_ex, name, n_order) values (101, 'Подъём на носок одной ноги стоя', 101);
insert into gym_exercises (id_gym_ex, name, n_order) values (102, 'Разгибание голени сидя', 102);

/*ПРИВЯЗКА УПРАЖНЕНИЙ К МЫШЦАМ*/
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (1, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (1, 3, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (2, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (3, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (3, 3, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (4, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (5, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (6, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (7, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (8, 6, 1);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (10, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (11, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (12, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (13, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (14, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (14, 5, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (20, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (21, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (21, 6, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (21, 2, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (22, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (23, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (24, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (25, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (26, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (27, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (27, 8, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (30, 1, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (31, 2, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (31, 1, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (32, 1, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (40, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (41, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (42, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (43, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (44, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (45, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (46, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (47, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (48, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (49, 8, 1);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (60, 9, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (60, 10, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (61, 9, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (61, 12, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (61, 10, 1);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (70, 2, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (71, 2, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (72, 2, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (73, 2, 4);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (74, 2, 5);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (75, 2, 6);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (76, 2, 7);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (77, 2, 8);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (80, 10, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (80, 11, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (81, 10, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (81, 11, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (82, 10, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (83, 10, 4);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (84, 10, 5);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (85, 10, 6);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (90, 10, 10);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (90, 11, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (91, 10, 11);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (91, 11, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (92, 10, 12);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (92, 11, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (93, 10, 13);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (93, 11, 4);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (94, 11, 5);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (95, 12, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (95, 13, 10);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (100, 13, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (101, 13, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (102, 13, 3);

/*
 *	   1
 */
delete from ps_img_mosaic_parts where id_img=1;
delete from ps_img_mosaic_answers where id_img=1;
delete from ps_img_mosaic where id_img=1;
insert into ps_img_mosaic (id_img, w, h, cx, cy, cw, ch) values (1, 1000, 740, 25, 20, 40, 37);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 1, 9, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 2, 17, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 3, 14, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 4, 2, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 5, 11, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 6, 13, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 7, 16, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 8, 6, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 9, 18, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 10, 20, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 11, 4, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 12, 5, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 13, 4, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 14, 20, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 15, 24, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 16, 10, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 17, 15, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 18, 25, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 19, 24, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 20, 4, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 21, 22, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 22, 18, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 23, 25, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 24, 22, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 25, 23, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 26, 8, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 27, 25, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 28, 24, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 29, 14, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 30, 5, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 31, 19, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 32, 18, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 33, 17, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 34, 18, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 35, 6, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 36, 15, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 37, 5, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 38, 6, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 39, 6, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 40, 23, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 41, 24, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 42, 5, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 43, 2, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 44, 20, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 45, 23, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 46, 11, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 47, 11, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 48, 17, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 49, 20, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 50, 10, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 51, 10, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 52, 10, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 53, 5, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 54, 10, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 55, 8, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 56, 22, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 57, 12, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 58, 7, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 59, 15, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 60, 24, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 61, 1, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 62, 1, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 63, 14, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 64, 13, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 65, 6, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 66, 11, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 67, 15, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 68, 12, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 69, 12, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 70, 9, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 71, 3, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 72, 17, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 73, 3, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 74, 19, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 75, 12, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 76, 24, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 77, 13, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 78, 11, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 79, 1, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 80, 7, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 81, 8, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 82, 4, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 83, 11, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 84, 4, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 85, 6, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 86, 7, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 87, 8, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 88, 4, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 89, 3, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 90, 18, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 91, 21, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 92, 10, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 93, 19, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 94, 22, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 95, 22, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 96, 7, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 97, 3, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 98, 5, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 99, 20, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 100, 22, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 101, 13, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 102, 8, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 103, 9, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 104, 16, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 105, 16, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 106, 9, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 107, 24, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 108, 16, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 109, 16, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 110, 18, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 111, 8, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 112, 8, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 113, 10, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 114, 9, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 115, 7, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 116, 6, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 117, 25, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 118, 18, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 119, 21, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 120, 3, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 121, 13, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 122, 3, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 123, 25, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 124, 11, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 125, 10, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 126, 19, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 127, 19, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 128, 17, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 129, 24, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 130, 4, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 131, 17, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 132, 16, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 133, 17, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 134, 19, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 135, 12, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 136, 19, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 137, 1, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 138, 1, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 139, 24, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 140, 16, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 141, 8, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 142, 4, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 143, 19, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 144, 18, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 145, 20, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 146, 22, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 147, 24, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 148, 11, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 149, 9, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 150, 11, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 151, 1, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 152, 3, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 153, 12, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 154, 13, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 155, 14, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 156, 11, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 157, 25, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 158, 21, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 159, 22, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 160, 11, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 161, 16, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 162, 10, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 163, 23, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 164, 13, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 165, 25, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 166, 11, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 167, 8, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 168, 2, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 169, 6, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 170, 2, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 171, 2, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 172, 5, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 173, 9, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 174, 7, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 175, 12, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 176, 20, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 177, 19, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 178, 6, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 179, 12, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 180, 21, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 181, 4, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 182, 9, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 183, 3, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 184, 15, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 185, 19, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 186, 14, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 187, 23, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 188, 6, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 189, 12, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 190, 2, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 191, 14, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 192, 3, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 193, 13, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 194, 4, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 195, 25, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 196, 23, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 197, 22, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 198, 2, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 199, 25, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 200, 1, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 201, 4, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 202, 16, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 203, 9, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 204, 15, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 205, 20, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 206, 3, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 207, 15, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 208, 10, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 209, 17, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 210, 23, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 211, 24, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 212, 8, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 213, 9, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 214, 6, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 215, 16, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 216, 1, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 217, 1, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 218, 19, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 219, 8, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 220, 1, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 221, 18, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 222, 17, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 223, 11, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 224, 2, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 225, 13, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 226, 14, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 227, 1, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 228, 1, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 229, 3, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 230, 22, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 231, 16, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 232, 1, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 233, 9, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 234, 23, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 235, 15, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 236, 22, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 237, 4, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 238, 6, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 239, 22, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 240, 17, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 241, 9, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 242, 10, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 243, 12, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 244, 5, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 245, 9, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 246, 20, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 247, 16, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 248, 25, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 249, 1, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 250, 14, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 251, 2, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 252, 6, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 253, 14, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 254, 5, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 255, 10, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 256, 21, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 257, 23, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 258, 2, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 259, 13, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 260, 4, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 261, 12, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 262, 16, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 263, 22, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 264, 11, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 265, 12, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 266, 25, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 267, 7, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 268, 19, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 269, 15, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 270, 21, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 271, 25, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 272, 2, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 273, 23, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 274, 7, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 275, 5, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 276, 12, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 277, 7, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 278, 8, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 279, 17, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 280, 18, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 281, 13, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 282, 5, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 283, 10, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 284, 16, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 285, 5, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 286, 9, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 287, 19, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 288, 8, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 289, 15, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 290, 14, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 291, 24, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 292, 18, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 293, 12, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 294, 25, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 295, 15, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 296, 20, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 297, 21, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 298, 3, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 299, 12, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 300, 9, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 301, 8, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 302, 23, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 303, 17, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 304, 4, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 305, 18, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 306, 20, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 307, 11, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 308, 14, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 309, 1, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 310, 22, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 311, 23, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 312, 14, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 313, 10, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 314, 2, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 315, 13, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 316, 24, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 317, 20, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 318, 7, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 319, 22, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 320, 10, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 321, 15, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 322, 13, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 323, 18, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 324, 4, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 325, 16, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 326, 5, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 327, 21, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 328, 13, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 329, 24, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 330, 23, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 331, 20, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 332, 4, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 333, 20, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 334, 18, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 335, 5, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 336, 5, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 337, 7, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 338, 5, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 339, 19, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 340, 21, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 341, 25, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 342, 17, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 343, 23, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 344, 8, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 345, 2, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 346, 8, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 347, 14, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 348, 17, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 349, 23, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 350, 14, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 351, 17, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 352, 24, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 353, 8, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 354, 3, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 355, 5, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 356, 4, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 357, 10, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 358, 21, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 359, 17, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 360, 4, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 361, 17, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 362, 12, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 363, 22, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 364, 20, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 365, 21, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 366, 3, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 367, 2, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 368, 1, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 369, 3, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 370, 23, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 371, 10, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 372, 24, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 373, 16, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 374, 11, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 375, 13, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 376, 9, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 377, 20, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 378, 2, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 379, 4, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 380, 6, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 381, 17, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 382, 9, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 383, 14, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 384, 21, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 385, 9, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 386, 18, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 387, 21, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 388, 18, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 389, 7, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 390, 3, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 391, 13, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 392, 22, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 393, 3, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 394, 7, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 395, 18, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 396, 13, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 397, 17, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 398, 16, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 399, 7, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 400, 15, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 401, 19, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 402, 22, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 403, 10, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 404, 5, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 405, 1, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 406, 20, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 407, 19, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 408, 12, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 409, 19, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 410, 5, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 411, 16, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 412, 23, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 413, 5, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 414, 16, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 415, 10, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 416, 6, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 417, 21, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 418, 10, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 419, 21, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 420, 19, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 421, 11, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 422, 13, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 423, 12, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 424, 19, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 425, 6, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 426, 24, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 427, 16, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 428, 3, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 429, 7, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 430, 14, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 431, 21, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 432, 7, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 433, 4, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 434, 23, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 435, 13, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 436, 3, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 437, 3, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 438, 18, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 439, 6, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 440, 25, 17);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 441, 2, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 442, 1, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 443, 2, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 444, 1, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 445, 25, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 446, 8, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 447, 13, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 448, 11, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 449, 21, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 450, 12, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 451, 20, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 452, 8, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 453, 9, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 454, 15, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 455, 7, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 456, 20, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 457, 22, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 458, 18, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 459, 11, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 460, 1, 16);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 461, 24, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 462, 25, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 463, 15, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 464, 15, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 465, 12, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 466, 15, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 467, 14, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 468, 6, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 469, 8, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 470, 15, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 471, 23, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 472, 25, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 473, 22, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 474, 24, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 475, 18, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 476, 11, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 477, 6, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 478, 7, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 479, 19, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 480, 2, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 481, 7, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 482, 23, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 483, 9, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 484, 21, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 485, 14, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 486, 14, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 487, 7, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 488, 17, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 489, 24, 19);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 490, 2, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 491, 21, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 492, 25, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 493, 15, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 494, 21, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 495, 6, 20);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 496, 25, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 497, 2, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 498, 20, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 499, 14, 18);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (1, 500, 15, 8);

