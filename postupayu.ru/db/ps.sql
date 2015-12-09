DROP DATABASE IF EXISTS ps;
CREATE DATABASE ps CHARACTER SET utf8 COLLATE utf8_general_ci;
USE ps;

/*
 * INCLUDE FILE [script.sql]
 */
/*
Created: 14.08.2010
Modified: 04.12.2014
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
  b_can_login Bool NOT NULL DEFAULT 1,
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
  id_rubric Int UNSIGNED NOT NULL AUTO_INCREMENT,
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
  id_rubric Int UNSIGNED NOT NULL AUTO_INCREMENT,
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
  id_rubric Int UNSIGNED NOT NULL,
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
  id_root Int UNSIGNED NOT NULL,
  id_parent Int UNSIGNED,
  id_post Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED NOT NULL,
  id_user_to Int UNSIGNED,
  dt_event Int UNSIGNED NOT NULL,
  content Text,
  b_deleted Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
  n_deep Tinyint UNSIGNED NOT NULL,
  b_known Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_comment)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table issue_post_comments

CREATE TABLE issue_post_comments
(
  id_comment Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_root Int UNSIGNED NOT NULL,
  id_parent Int UNSIGNED,
  id_post Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED NOT NULL,
  id_user_to Int UNSIGNED,
  dt_event Int UNSIGNED NOT NULL,
  content Text,
  b_deleted Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
  n_deep Tinyint UNSIGNED NOT NULL,
  b_known Bool NOT NULL DEFAULT 0
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
  id_root Int UNSIGNED NOT NULL,
  id_parent Int UNSIGNED,
  id_user Int UNSIGNED NOT NULL,
  id_user_to Int UNSIGNED,
  id_owner Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  theme Varchar(255),
  content Text,
  id_template Int UNSIGNED,
  v_template Varchar(255),
  b_deleted Bool NOT NULL DEFAULT 0,
  b_new Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
  n_deep Tinyint UNSIGNED NOT NULL,
  b_known Bool NOT NULL DEFAULT 0,
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
  id_rubric Int UNSIGNED NOT NULL,
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
  id_root Int UNSIGNED NOT NULL,
  id_parent Int UNSIGNED,
  id_post Int UNSIGNED NOT NULL,
  id_user Int UNSIGNED NOT NULL,
  id_user_to Int UNSIGNED,
  dt_event Int UNSIGNED NOT NULL,
  content Text,
  b_deleted Bool NOT NULL DEFAULT 0,
  b_confirmed Bool NOT NULL DEFAULT 0,
  n_deep Tinyint UNSIGNED NOT NULL,
  b_known Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_comment)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
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
  v_params Text,
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
  id_user Int UNSIGNED NOT NULL,
  id_post Int UNSIGNED NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

CREATE UNIQUE INDEX idx_userlessons_user_post ON user_lessons (id_user,id_post)
;

-- Table ps_testing

CREATE TABLE ps_testing
(
  id_testing Int UNSIGNED NOT NULL,
  name Varchar(255),
  n_time Tinyint UNSIGNED NOT NULL,
  n_tasks Tinyint UNSIGNED NOT NULL DEFAULT 0
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE ps_testing ADD PRIMARY KEY (id_testing)
;

ALTER TABLE ps_testing ADD UNIQUE id_testing (id_testing)
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
  v_answer Text NOT NULL,
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
  v_data Varchar(255),
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

-- Table ps_user_codes

CREATE TABLE ps_user_codes
(
  id_user Int UNSIGNED NOT NULL,
  v_type Char(1) NOT NULL,
  v_code Char(32) NOT NULL,
  dt_add Int UNSIGNED NOT NULL,
  dt_used Int UNSIGNED,
  n_status Tinyint UNSIGNED NOT NULL DEFAULT 0
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

CREATE UNIQUE INDEX idx_usercodes_type_code ON ps_user_codes (v_type,v_code)
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

-- Table ps_lib_item

CREATE TABLE ps_lib_item
(
  id Int UNSIGNED NOT NULL AUTO_INCREMENT,
  ident Char(80) NOT NULL,
  grup Char(1) NOT NULL,
  name Varchar(255) NOT NULL,
  content Text,
  dt_start Varchar(20),
  dt_stop Varchar(20),
  b_show Bool NOT NULL DEFAULT 1,
 PRIMARY KEY (id),
 UNIQUE id (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Таблица хранит информацию об элементах библиотеки. grOup - зарезервированное слово:('
;

CREATE UNIQUE INDEX idx_libitem_ident_grup ON ps_lib_item (ident,grup)
;

-- Table ps_table_settings

CREATE TABLE ps_table_settings
(
  v_table Varchar(255) NOT NULL
  COMMENT 'Название таблицы',
  v_column Varchar(255)
  COMMENT 'Название столбца',
  v_prop_type Char(1) NOT NULL
  COMMENT 'Тип настройки. {@see AdminDbBean}',
  v_type Varchar(255)
  COMMENT 'Тип (для конкретной настройки)',
  v_value Varchar(255)
  COMMENT 'Ткстовое значение',
  n_value Int
  COMMENT 'Числовое значение'
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Настройки импорта/экспорта табличных данных в проекте'
;

-- Table ps_props

CREATE TABLE ps_props
(
  v_prop Char(80) NOT NULL,
  v_val Varchar(255),
  n_val Int UNSIGNED
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Свойства, хранимые в базе'
;

ALTER TABLE ps_props ADD UNIQUE v_prop (v_prop)
;

-- Table ps_gallery

CREATE TABLE ps_gallery
(
  id_gallery Int UNSIGNED NOT NULL AUTO_INCREMENT,
  v_dir Varchar(255) NOT NULL,
  v_name Varchar(255),
 PRIMARY KEY (id_gallery)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

ALTER TABLE ps_gallery ADD UNIQUE v_dir (v_dir)
;

-- Table ps_gallery_images

CREATE TABLE ps_gallery_images
(
  id_image Int UNSIGNED NOT NULL AUTO_INCREMENT,
  v_dir Varchar(255) NOT NULL,
  v_file Varchar(255) NOT NULL,
  v_name Varchar(255),
  v_descr Varchar(255),
  n_order Tinyint UNSIGNED NOT NULL,
  b_show Bool NOT NULL DEFAULT 0,
  b_web Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_image)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

CREATE UNIQUE INDEX idx_galleryimages_dir_file ON ps_gallery_images (v_dir,v_file)
;

-- Table ps_mappings

CREATE TABLE ps_mappings
(
  id_mapping Int NOT NULL AUTO_INCREMENT,
  mhash Char(32) NOT NULL,
  lident Varchar(255) NOT NULL,
  rident Varchar(255) NOT NULL,
  ord Int NOT NULL,
 PRIMARY KEY (id_mapping)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_db_changes

CREATE TABLE ps_db_changes
(
  v_entity Varchar(255) NOT NULL,
  v_type Char(1) NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_views_state

CREATE TABLE ps_views_state
(
  v_view Varchar(255) NOT NULL,
  n_cnt Int NOT NULL
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_discussion_backup

CREATE TABLE ps_discussion_backup
(
  v_table Varchar(255) NOT NULL,
  id_msg Int UNSIGNED NOT NULL,
  id_parent Int UNSIGNED,
  id_root Int UNSIGNED,
  id_thread Int UNSIGNED,
  id_user Int UNSIGNED NOT NULL,
  id_user_to Int UNSIGNED,
  id_user_delete Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  dt_event_delete Int UNSIGNED NOT NULL,
  n_deep Tinyint UNSIGNED NOT NULL,
  theme Varchar(255),
  content Text
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
;

-- Table ps_folded_codes

CREATE TABLE ps_folded_codes
(
  id Int UNSIGNED NOT NULL AUTO_INCREMENT,
  v_unique Char(80) NOT NULL,
 PRIMARY KEY (id)
)
;

ALTER TABLE ps_folded_codes ADD UNIQUE v_unique (v_unique)
;

-- Table ps_votes

CREATE TABLE ps_votes
(
  id_user Int UNSIGNED NOT NULL,
  id_user_to Int UNSIGNED,
  v_group Varchar(255) NOT NULL,
  id_inst Int UNSIGNED NOT NULL,
  n_votes Tinyint NOT NULL
)
;

CREATE UNIQUE INDEX idx_votes_gr_user_inst ON ps_votes (v_group,id_user,id_inst)
;

-- Table ps_audit

CREATE TABLE ps_audit
(
  id_rec Int UNSIGNED NOT NULL AUTO_INCREMENT,
  id_rec_parent Int UNSIGNED,
  id_user Int UNSIGNED,
  id_user_authed Int UNSIGNED,
  id_process Int UNSIGNED NOT NULL,
  dt_event Int UNSIGNED NOT NULL,
  n_action Tinyint UNSIGNED NOT NULL,
  v_data Text,
  b_encoded Bool NOT NULL DEFAULT 0,
 PRIMARY KEY (id_rec)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
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

ALTER TABLE train_post_comments ADD CONSTRAINT Relationship83 FOREIGN KEY (id_user_to) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE blog_post_comments ADD CONSTRAINT Relationship84 FOREIGN KEY (id_user_to) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE issue_post_comments ADD CONSTRAINT Relationship85 FOREIGN KEY (id_user_to) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE feedback_user ADD CONSTRAINT Relationship86 FOREIGN KEY (id_user_to) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_votes ADD CONSTRAINT Relationship87 FOREIGN KEY (id_user_to) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_votes ADD CONSTRAINT Relationship88 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_user_codes ADD CONSTRAINT Relationship89 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_audit ADD CONSTRAINT Relationship91 FOREIGN KEY (id_user_authed) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_audit ADD CONSTRAINT Relationship92 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

/*
 * INCLUDE FILE [views.sql]
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

CREATE OR REPLACE VIEW v_ps_lib_item
AS
   SELECT *
     FROM ps_lib_item
    WHERE     b_show = 1;

/*
 * INCLUDE FILE [procedures.sql]
 */
delimiter |

-- Процедура вставляет запись об изменении сущности БД
DROP PROCEDURE IF EXISTS onDbChange|

CREATE PROCEDURE onDbChange (IN ventity VARCHAR(255), IN vtype CHAR(1))
SQL SECURITY DEFINER
BEGIN
    delete from ps_db_changes where v_entity=ventity and v_type=vtype;
    insert into ps_db_changes (v_entity, v_type) values (ventity, vtype);
END
|


-- Процедура проверяет предыдущее и текущее состояние представлений (views) и, если нужно, рождает событие изменения
DROP PROCEDURE IF EXISTS checkViewsState|

CREATE PROCEDURE checkViewsState()
SQL SECURITY DEFINER
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE pViewName VARCHAR(255);
  DECLARE cur1 CURSOR FOR SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA=DATABASE();
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur1;
  
  WHILE not done DO
    -- View name
    FETCH cur1 INTO pViewName;
    
    -- Variables
    SET @OLD_CNT = 1;
    SET @NEW_CNT = 0;
    SET @NOW_QUERY = CONCAT('select count(1) into @NEW_CNT from ', pViewName);

    -- NEW_CNT
    PREPARE stmt1 FROM @NOW_QUERY; 
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1; 
    
    -- OLD_CNT
    select n_cnt into @OLD_CNT from (select s.n_cnt from ps_views_state s where s.v_view=pViewName union select 0) w limit 1;

    -- Change insert
    if @OLD_CNT != @NEW_CNT then
       delete from ps_views_state where v_view = pViewName;
       insert into ps_views_state (v_view, n_cnt) values (pViewName, @NEW_CNT);
       call onDbChange(pViewName, 'V');
    end if;
    
  END WHILE;
  
  CLOSE cur1;
END
|

delimiter ;

/*
 * INCLUDE FILE [users.sql]
 */
/*
  Пользователи системы
*/

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg,
                  b_admin,
				  b_can_login)
VALUES (1,
        'Администратор системы',
        '1',
        'admin@mail.ru',
        '96e79218965eb72c92a549dd5a330112',
        UNIX_TIMESTAMP(),
        1,
		1);

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg,
                  b_admin,
				  b_can_login)
VALUES (2,
        'Система',
        '1',
        'system@postupayu.ru',
        '-',
        UNIX_TIMESTAMP(),
        1,
		0);

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg,
				  b_can_login)
VALUES (100,
        'Илья',
        '1',
        'azaz@mail.ru',
        '96e79218965eb72c92a549dd5a330112',
        UNIX_TIMESTAMP(),
		1);

/*
  Пользователи MySql
*/
/*grant all on ps.* to 'ps'@'localhost' identified by 'ps';*/

/*
 * INCLUDE FILE [gym.sql]
 */
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
 * INCLUDE FILE [mosaic1.sql]
 */
/*
 *	???????? ??????? ???????? 1
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

/*
 * INCLUDE FILE [mosaic3.sql]
 */
delete from ps_img_mosaic_parts where id_img=3;
delete from ps_img_mosaic_answers where id_img=3;
delete from ps_img_mosaic where id_img=3;
insert into ps_img_mosaic (id_img, w, h, cx, cy, cw, ch) values (3, 720, 420, 24, 15, 30, 28);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 1, 18, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 2, 2, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 3, 16, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 4, 12, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 5, 15, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 6, 7, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 7, 6, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 8, 16, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 9, 17, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 10, 22, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 11, 23, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 12, 22, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 13, 10, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 14, 24, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 15, 18, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 16, 3, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 17, 21, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 18, 12, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 19, 19, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 20, 3, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 21, 18, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 22, 8, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 23, 11, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 24, 16, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 25, 4, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 26, 10, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 27, 18, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 28, 17, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 29, 14, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 30, 15, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 31, 16, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 32, 17, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 33, 8, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 34, 5, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 35, 12, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 36, 3, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 37, 18, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 38, 20, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 39, 19, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 40, 21, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 41, 15, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 42, 15, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 43, 7, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 44, 4, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 45, 7, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 46, 11, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 47, 9, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 48, 4, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 49, 23, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 50, 9, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 51, 24, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 52, 22, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 53, 11, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 54, 8, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 55, 1, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 56, 20, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 57, 12, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 58, 3, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 59, 8, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 60, 10, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 61, 7, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 62, 20, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 63, 16, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 64, 24, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 65, 23, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 66, 1, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 67, 11, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 68, 8, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 69, 24, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 70, 6, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 71, 16, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 72, 2, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 73, 5, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 74, 21, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 75, 12, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 76, 19, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 77, 19, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 78, 19, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 79, 4, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 80, 4, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 81, 13, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 82, 14, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 83, 6, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 84, 3, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 85, 18, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 86, 10, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 87, 5, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 88, 6, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 89, 19, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 90, 9, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 91, 9, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 92, 6, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 93, 23, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 94, 11, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 95, 7, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 96, 13, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 97, 2, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 98, 24, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 99, 9, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 100, 15, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 101, 7, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 102, 18, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 103, 17, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 104, 14, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 105, 22, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 106, 2, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 107, 11, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 108, 24, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 109, 24, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 110, 16, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 111, 20, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 112, 14, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 113, 20, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 114, 4, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 115, 20, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 116, 14, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 117, 2, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 118, 4, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 119, 9, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 120, 8, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 121, 14, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 122, 16, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 123, 9, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 124, 22, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 125, 9, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 126, 3, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 127, 6, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 128, 23, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 129, 18, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 130, 10, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 131, 19, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 132, 23, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 133, 5, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 134, 3, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 135, 22, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 136, 21, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 137, 13, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 138, 14, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 139, 11, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 140, 20, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 141, 3, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 142, 15, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 143, 15, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 144, 5, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 145, 11, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 146, 2, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 147, 13, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 148, 9, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 149, 21, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 150, 13, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 151, 19, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 152, 2, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 153, 9, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 154, 18, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 155, 4, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 156, 15, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 157, 6, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 158, 1, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 159, 7, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 160, 20, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 161, 13, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 162, 19, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 163, 6, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 164, 10, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 165, 14, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 166, 12, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 167, 10, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 168, 7, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 169, 2, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 170, 21, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 171, 10, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 172, 15, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 173, 21, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 174, 5, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 175, 1, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 176, 18, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 177, 17, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 178, 9, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 179, 24, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 180, 12, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 181, 6, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 182, 17, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 183, 22, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 184, 2, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 185, 7, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 186, 13, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 187, 16, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 188, 17, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 189, 22, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 190, 17, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 191, 11, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 192, 4, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 193, 3, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 194, 2, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 195, 14, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 196, 7, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 197, 19, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 198, 13, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 199, 7, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 200, 22, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 201, 23, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 202, 21, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 203, 6, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 204, 14, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 205, 11, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 206, 12, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 207, 21, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 208, 17, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 209, 8, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 210, 7, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 211, 2, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 212, 16, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 213, 10, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 214, 21, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 215, 16, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 216, 16, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 217, 24, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 218, 23, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 219, 18, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 220, 11, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 221, 13, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 222, 24, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 223, 18, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 224, 8, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 225, 23, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 226, 12, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 227, 13, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 228, 2, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 229, 7, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 230, 17, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 231, 19, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 232, 5, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 233, 22, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 234, 12, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 235, 19, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 236, 5, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 237, 11, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 238, 8, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 239, 6, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 240, 20, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 241, 7, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 242, 20, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 243, 14, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 244, 6, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 245, 20, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 246, 16, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 247, 8, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 248, 23, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 249, 20, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 250, 10, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 251, 8, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 252, 3, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 253, 24, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 254, 23, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 255, 21, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 256, 3, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 257, 5, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 258, 4, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 259, 21, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 260, 4, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 261, 12, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 262, 22, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 263, 4, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 264, 1, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 265, 3, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 266, 24, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 267, 13, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 268, 17, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 269, 17, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 270, 1, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 271, 24, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 272, 11, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 273, 1, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 274, 1, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 275, 7, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 276, 2, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 277, 6, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 278, 4, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 279, 13, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 280, 10, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 281, 6, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 282, 9, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 283, 19, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 284, 1, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 285, 19, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 286, 1, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 287, 22, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 288, 14, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 289, 5, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 290, 1, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 291, 3, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 292, 15, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 293, 12, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 294, 23, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 295, 15, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 296, 2, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 297, 6, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 298, 23, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 299, 16, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 300, 1, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 301, 2, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 302, 18, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 303, 8, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 304, 5, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 305, 12, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 306, 14, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 307, 5, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 308, 14, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 309, 21, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 310, 17, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 311, 12, 12);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 312, 23, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 313, 15, 5);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 314, 13, 13);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 315, 10, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 316, 22, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 317, 8, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 318, 12, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 319, 17, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 320, 14, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 321, 18, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 322, 20, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 323, 24, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 324, 23, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 325, 15, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 326, 1, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 327, 1, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 328, 4, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 329, 8, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 330, 24, 15);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 331, 19, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 332, 10, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 333, 15, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 334, 8, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 335, 9, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 336, 1, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 337, 15, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 338, 20, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 339, 18, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 340, 21, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 341, 9, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 342, 5, 10);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 343, 3, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 344, 10, 14);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 345, 20, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 346, 10, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 347, 5, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 348, 9, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 349, 22, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 350, 21, 11);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 351, 13, 1);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 352, 3, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 353, 5, 8);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 354, 11, 4);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 355, 11, 6);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 356, 22, 3);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 357, 17, 7);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 358, 4, 2);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 359, 13, 9);
insert into ps_img_mosaic_parts (id_img, n_part, x_cell, y_cell) values (3, 360, 16, 6);

/*
 * TRIGGERS
 */
delimiter |

CREATE TRIGGER tau_issue_post AFTER UPDATE
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange(CONCAT('post-is-', NEW.ident), 'F');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_blog_rubric AFTER UPDATE
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange(CONCAT('rubric-bp-', NEW.ident), 'F');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_blog_post AFTER UPDATE
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange(CONCAT('post-bp-', NEW.ident), 'F');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_train_rubric AFTER UPDATE
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange(CONCAT('rubric-tr-', NEW.ident), 'F');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_train_post AFTER UPDATE
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange(CONCAT('post-tr-', NEW.ident), 'F');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tai_ps_lib_item AFTER INSERT
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_ps_lib_item AFTER UPDATE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
	CALL onDbChange(CONCAT('lib-', NEW.grup, '-', NEW.ident), 'F');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tad_ps_lib_item AFTER DELETE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tai_ps_timeline AFTER INSERT
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_ps_timeline AFTER UPDATE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tad_ps_timeline AFTER DELETE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tai_ps_timeline_item AFTER INSERT
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_ps_timeline_item AFTER UPDATE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tad_ps_timeline_item AFTER DELETE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tai_ps_gallery AFTER INSERT
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_ps_gallery AFTER UPDATE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tad_ps_gallery AFTER DELETE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tai_ps_gallery_images AFTER INSERT
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tau_ps_gallery_images AFTER UPDATE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;

delimiter |

CREATE TRIGGER tad_ps_gallery_images AFTER DELETE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;


/*
 * INCLUDE TABLE [issue_post]
 */
insert into issue_post (id_post, ident, name, b_show, dt_publication) values (1, 'Issue1', 'Выпуск №1', 1, 1357070400);
insert into issue_post (id_post, ident, name, b_show, dt_publication) values (2, 'Issue2', 'Выпуск №2', 1, 1357761600);
insert into issue_post (id_post, ident, name, b_show, dt_publication) values (3, 'Issue3', 'Выпуск №3', 1, 1358625600);
insert into issue_post (id_post, ident, name, b_show, dt_publication) values (4, 'Issue4', 'Выпуск №4', 1, 1359489600);
insert into issue_post (id_post, ident, name, b_show, dt_publication) values (5, 'Issue5', 'Выпуск №5', 1, 1359662400);


/*
 * INCLUDE TABLE [blog_rubric]
 */
insert into blog_rubric (name, ident, content, b_tpl) values ('Великие люди', 'famous', null, 1);
insert into blog_rubric (name, ident, content, b_tpl) values ('Спорт в жизни студента', 'sport', '<p>В данном разделе блога собраны все заметки, касающиеся спорта в жизни студента и здоровья в целом. Я не стал разносить данные темы по отдельным рубрикам, т.к. они (как Вы понимаете) крайне тесно связаны...</p><br />', 0);


/*
 * INCLUDE TABLE [blog_post]
 */
insert into blog_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Пифагор Самосский', 'pifagor', 1, 1356984000, 1, null, null);
insert into blog_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Евклид', 'euclide', 1, 1357329600, 1, null, null);
insert into blog_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Архимед', 'arhimed', 1, 1357934400, 1, null, null);
insert into blog_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (2, 'Турники - всё, что Вам нужно', 'BP_SP1_turniki', 1, 1357675200, 1, null, null);
insert into blog_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (2, 'Как не посадить зрение', 'BP_SP2_zrenie', 1, 1361023500, 0, null, null);


/*
 * INCLUDE TABLE [train_rubric]
 */
insert into train_rubric (name, content, ident, b_tpl) values ('Математика', null, 'math', 1);
insert into train_rubric (name, content, ident, b_tpl) values ('Физика', null, 'phys', 1);


/*
 * INCLUDE TABLE [train_post]
 */
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Тригонометрия. Введение.', 'trigonometry', 1, 1356984000, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Тригонометрия. Тригонометрический круг.', 'trigonometry_krug', 1, 1357156800, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Тригонометрия. Вывод формул.', 'trigonometry_formula', 1, 1357416000, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Тест по тригонометрии.', 'trigonometry_testing', 1, 1357588800, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Векторы', 'vectors', 1, 1357761600, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Декартова система координат', 'dekart_system', 1, 1359489600, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (2, 'Кинематика точки. Введение.', 'kinemtochki', 1, 1360008000, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (2, 'Кинематика точки. Виды движений.', 'kinemtochki2', 1, 1360180800, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (1, 'Матрицы', 'matrix', 1, 1360872000, 1, null, null);
insert into train_post (id_rubric, name, ident, b_show, dt_publication, b_tpl, content, content_showcase) values (2, 'Кинематика точки. Заключение', 'kinemtochki3', 0, 1390312200, 1, null, null);


/*
 * INCLUDE TABLE [ps_lib_item]
 * Таблица хранит информацию об элементах библиотеки. grOup - зарезервированное слово:(
 */
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('pushkin', 'p', 'Александр Сергеевич Пушкин', 'content', '26-05-1799', '29-01-1837', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('esenin', 'p', 'Сергей Александрович Есенин', 'content', '21-09-1895', '28-12-1925', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('ahmatova', 'p', 'Анна Андреевна Ахматова', 'content', '11-06-1889', '05-03-1966', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('pasternak', 'p', 'Борис Леонидович Пастернак', '<p>Будущий поэт родился в Москве в творческой еврейской семье.</p>\n<p>Родители Пастернака, отец — художник, академик Петербургской Академии художеств Леонид Осипович (Исаак Иосифович) Пастернак и мать — пианистка Розалия Исидоровна Пастернак (урождённая Кауфман, 1868—1939), переехали в Москву из Одессы в 1889 году, за год до его рождения.</p>\n<p>Борис появился на свет в доме на пересечении Оружейного переулка и Второй Тверской-Ямской улицы, где они поселились. Кроме старшего, Бориса, в семье Пастернак родились Александр (1893—1982), Жозефина (1900—1993) и Лидия (1902—1989). В некоторых официальных документах ещё начала 1900-х годов Б. Л. Пастернак фигурировал как «Борис Исаакович (он же Леонидович)»</p>', '29-01-1890', '30-05-1960', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('gogol', 'p', 'Николай Васильевич Гоголь', null, '20-03-1809', '21-02-1852', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('griboedov', 'p', 'Александр Сергеевич Грибоедов', null, '04-01-1795', '30-01-1829', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('krylov', 'p', 'Иван Андреевич Крылов', null, '02-02-1769', '09-11-1844', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('lermontov', 'p', 'Михаил Юрьевич Лермонтов', null, '03-10-1814', '15-07-1841', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('nekrasov', 'p', 'Николай Алексеевич Некрасов', null, '28-11-1821', '27-12-1877', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('turgenev', 'p', 'Иван Сергеевич Тургенев', null, '28-10-1818', '22-08-1883', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('tutchev', 'p', 'Фёдор Иванович Тютчев', null, '23-11-1803', '15-07-1873', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('fet', 'p', 'Афанасий Афанасьевич Фет', 'Великий русский поэт', '05-12-1820', '03-12-1892', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('cvetaeva', 'p', 'Марина Ивановна Цветаева', null, '26-09-1892', '31-08-1941', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('mandelshtam', 'p', 'Осип Эмильевич Мандельштам', null, '03-01-1891', '27-12-1938', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('pifagor', 's', 'Пифагор', '<p>\nПифагор Самосский (др.-греч. Πυθαγόρας ὁ Σάμιος, лат. Pythagoras; 570—490 гг. до н. э.) — древнегреческий философ, математик и мистик, создатель религиозно-философской школы пифагорейцев.\n</p>\n\n<p>\nИсторию жизни Пифагора трудно отделить от легенд, представляющих его в качестве совершенного мудреца и великого посвящённого во все таинства греков и варваров. Ещё Геродот называл его «величайшим эллинским мудрецом»[1].\n</p>\n\n<p>\nОсновными источниками по жизни и учению Пифагора являются сочинения философа-неоплатоника Ямвлиха (242—306 гг.) «О Пифагоровой жизни»; Порфирия (234—305 гг.) «Жизнь Пифагора»; Диогена Лаэртского (200—250 гг.) кн. 8, «Пифагор». Эти авторы опирались на сочинения более ранних авторов, из которых следует отметить ученика Аристотеля Аристоксена (370—300 гг. до н. э.) родом из Тарента, где сильны были позиции пифагорейцев.\n</p>', '570 BC', '490 BC', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('KarlHeisenberg', 's', 'Вернер Карл Гейзенберг', '<p>Эйнштейн, великий человек!</p>', '1901-12-05', '1976-02-01', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('BP_FM2_einstein', 's', 'Альберт Эйнштейн', '<p>Эйнштейн, великий человек!</p>', '1879-03-14', '1955-04-18', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('euclide', 's', 'Евклид', null, '365 BC', '300 BC', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('arhimed', 's', 'Архимед', null, '287 BC', '212 BC', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('GalileoGalilei', 's', 'Галилео Галилей', '<p>Макс Карл Эрнст Людвиг Планк</p>', '1564-02-15', '1642-01-08', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('MaxPlanck', 's', 'Макс Карл Эрнст Людвиг Планк', '<p>Макс Карл Эрнст Людвиг Планк</p>', '1858-04-23', '1947-10-04', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('PetrKapica', 's', 'Пётр Леонидович Капица', '<p>Капица, великий человек!</p>', '1894-07-08', '1984-04-08', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('MarieCurie', 's', 'Мария Склодовская-Кюри', '<p>Мария Кюри, великий человек!</p>', '1867-11-07', '1934-05-04', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('BP_FM1_feynman', 's', 'Ричард Филлипс Фейнман', 'Выдающийся американский учёный. Основные достижения относятся к области теоретической физики. Один из создателей квантовой электродинамики. В 1943—1945 годах входил в число разработчиков атомной бомбы в Лос-Аламосе. Разработал метод интегрирования по траекториям в квантовой механике (1948), а также т. н. метод диаграмм Фейнмана (1949) в квантовой теории поля, с помощью которых можно объяснять превращения элементарных частиц. Предложил партонную модель нуклона (1969), теорию квантованных вихрей. Реформатор методов преподавания физики в вузе.[1] Лауреат Нобелевской премии по физике (1965, совместно с С. Томонагой и Дж. Швингером).', '1918-05-11', '1988-02-15', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('ErnstPauli', 's', 'Вольфганг Эрнст Паули', '<p>Эйнштейн, великий человек!</p>', '1900-04-25', '1958-12-15', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('NielsBohr', 's', 'Нильс Хенрик Давид Бор', '<p>Эйнштейн, великий человек!</p>', '1885-10-07', '1962-11-18', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('VanDerWaals', 's', 'Ян Дидерик Ван-дер-Ваальс', '<p>Макс Карл Эрнст Людвиг Планк</p>', '1837-11-23', '1923-03-08', 1);
insert into ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) values ('IsaacNewton', 's', 'Исаак Ньютон', '<p>\r\nАнглийский математик, астроном, физик, механик, заложивший основы классической механики, он объяснил движение небесных тел – планет вокруг Солнца и Луны вокруг Земли. Самым известным его открытием был закон всемирного тяготения.\r\n</p>', '1642-12-25', '1727-03-20', 1);


/*
 * INCLUDE TABLE [ps_testing]
 */
insert into ps_testing (id_testing, name, n_time) values (1, 'Тест по тригонометрии', 45);


/*
 * INCLUDE TABLE [ps_chess_knight]
 */
insert into ps_chess_knight (v_solution, b_system) values ('11325172847688674827152331527183758768472816241233416281738577583718261422436456355466788674826142211325173857364463553446655345', 1);
insert into ps_chess_knight (v_solution, b_system) values ('58778573816241221426183756688775837152311224162847667886748261422113251738577688674827152311325172533446658463443655433554334564', 1);
insert into ps_chess_knight (v_solution, b_system) values ('81738577583718261422416283758768472816241231527163847688674827152311325172645635435536173857788674826142211325335466455334466544', 1);
insert into ps_chess_knight (v_solution, b_system) values ('81738577583718261422416283758768472816241231527163847688674827152311325172645635435536173857788674826142211325446546345345665433', 0);
insert into ps_chess_knight (v_solution, b_system) values ('83758768472816241231527163827486785738172513214261738162412214261837587785644351728476886748271523113253654634553644563554664533', 0);
insert into ps_chess_knight (v_solution, b_system) values ('18261422416281738577583716241231527183758768472836153453655745332113251738466788768472513211233527485644638261425466788674554364', 0);
insert into ps_chess_knight (v_solution, b_system) values ('18375877857381624122142638577886748261422113251736556371837587684728162412315264563543517284768867482746341523113244655345665433', 0);
insert into ps_chess_knight (v_solution, b_system) values ('88768472513211231527486786748261422113251738577866857381624122142618375877655334465433123152718375876847281624456443355644365563', 0);
insert into ps_chess_knight (v_solution, b_system) values ('81738577583718261422416283758768472816241231527163847688674827152311325172645635435536173857788674826142211325446553344654664533', 0);
insert into ps_chess_knight (v_solution, b_system) values ('11325172847688674827152331527183758768472816241233416281738577583718261422436456355466788674826142211325173857455365463455364463', 0);
insert into ps_chess_knight (v_solution, b_system) values ('11325172847688674827152331527183758768472816241233416281738577583718261422436456355466788674826142211325173857365563446546345345', 0);


/*
 * INCLUDE TABLE [ps_timeline]
 */
insert into ps_timeline (id_timeline, v_name) values (1, 'Хронология жизни великих людей');


/*
 * INCLUDE TABLE [ps_timeline_item]
 */
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Пифагор', null, '570 BC', '490 BC', null, 'pifagor');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Евклид', null, '365 BC', '300 BC', null, 'euclide');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Архимед', null, '287 BC', '212 BC', null, 'arhimed');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Пётр Леонидович Капица', '<p>Капица, великий человек!</p>', '1894-07-08', '1984-04-08', null, 'PetrKapica');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Мария Склодовская-Кюри', '<p>Мария Кюри, великий человек!</p>', '1867-11-07', '1934-05-04', null, 'MarieCurie');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Ричард Филлипс Фейнман', '<p>Фейнман, великий человек!</p>', '1918-05-11', '1988-02-15', null, 'BP_FM1_feynman');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Альберт Эйнштейн', '<p>Эйнштейн, великий человек!</p>', '1879-03-14', '1955-04-18', null, 'BP_FM2_einstein');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Вернер Карл Гейзенберг', '<p>Эйнштейн, великий человек!</p>', '1901-12-05', '1976-02-01', null, 'KarlHeisenberg');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Вольфганг Эрнст Паули', '<p>Эйнштейн, великий человек!</p>', '1900-04-25', '1958-12-15', null, 'ErnstPauli');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Нильс Хенрик Давид Бор', '<p>Эйнштейн, великий человек!</p>', '1885-10-07', '1962-11-18', null, 'NielsBohr');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Макс Карл Эрнст Людвиг Планк', '<p>Макс Карл Эрнст Людвиг Планк</p>', '1858-04-23', '1947-10-04', null, 'MaxPlanck');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Ян Дидерик Ван-дер-Ваальс', '<p>Макс Карл Эрнст Людвиг Планк</p>', '1837-11-23', '1923-03-08', null, 'VanDerWaals');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Галилео Галилей', '<p>Макс Карл Эрнст Людвиг Планк</p>', '1564-02-15', '1642-01-08', null, 'GalileoGalilei');
insert into ps_timeline_item (id_timeline, v_title, content, date_start, date_end, id_master_inst, v_master_ident) values (1, 'Исаак Ньютон', '<p>Английский математик, астроном, физик, механик, заложивший основы классической механики, он объяснил движение небесных тел – планет вокруг Солнца и Луны вокруг Земли. Самым известным его открытием был закон всемирного тяготения. </p>', '1642-12-25', '1727-03-20', null, 'IsaacNewton');


/*
 * INCLUDE TABLE [ps_gallery]
 */
insert into ps_gallery (v_dir, v_name) values ('steinic', 'Вильгельм Стейниц');
insert into ps_gallery (v_dir, v_name) values ('bohrneils', 'Нильс Хенрик Давид Бор');
insert into ps_gallery (v_dir, v_name) values ('anand', 'Вишванатан Ананд');
insert into ps_gallery (v_dir, v_name) values ('kramnik', 'Владимир Борисович Крамник');
insert into ps_gallery (v_dir, v_name) values ('feynman', 'Ричард Филлипс Фейнман');
insert into ps_gallery (v_dir, v_name) values ('chess', 'Необычные шахматы');
insert into ps_gallery (v_dir, v_name) values ('capablanca', 'Хосе Рауль Капабланка');
insert into ps_gallery (v_dir, v_name) values ('alekhin', 'Александр Александрович Алехин');
insert into ps_gallery (v_dir, v_name) values ('lasker', 'Эмануэль Ласкер');
insert into ps_gallery (v_dir, v_name) values ('botvinnik', 'Михаил Моисеевич Ботвинник');
insert into ps_gallery (v_dir, v_name) values ('euwe', 'Макс Эйве');
insert into ps_gallery (v_dir, v_name) values ('fischer', 'Роберт Джеймс Фишер');
insert into ps_gallery (v_dir, v_name) values ('karpov', 'Анатолий Евгеньевич Карпов');
insert into ps_gallery (v_dir, v_name) values ('kasparov', 'Гарри Кимович Каспаров');
insert into ps_gallery (v_dir, v_name) values ('smyslov', 'Василий Васильевич Смыслов');
insert into ps_gallery (v_dir, v_name) values ('spasskiy', 'Борис Васильевич Спасский');
insert into ps_gallery (v_dir, v_name) values ('tal', 'Михаил Нехемьевич Таль');
insert into ps_gallery (v_dir, v_name) values ('petrosyan', 'Тигран Вартанович Петросян');
insert into ps_gallery (v_dir, v_name) values ('newton', 'Исаак Ньютон');
insert into ps_gallery (v_dir, v_name) values ('pifagor', 'Пифагор');
insert into ps_gallery (v_dir, v_name) values ('schwarzenegger', 'Арнольд Шварценеггер');
insert into ps_gallery (v_dir, v_name) values ('einstein', 'Альберт Эйнштейн');
insert into ps_gallery (v_dir, v_name) values ('arhimed', 'Архимед');
insert into ps_gallery (v_dir, v_name) values ('euclide', 'Евклид');


/*
 * INCLUDE TABLE [ps_gallery_images]
 */
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', '1.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', 'Steinic.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', '1020-1.JPG', null, null, 3, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', '3474.jpg', null, null, 4, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', '220px-Lasker-Steinitz.jpg', 'Игра с Ласкером', null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', '852929247.JPG', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', 'SteinitzMarker.jpg', null, null, 7, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('steinic', 'steinitzPort.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', '200px-Niels_Bohr.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', '3f237f91-699e-35e0-967a-28c276b852a1.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'Img_Kvant_H-1997-02-002.jpg', null, null, 3, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'Bor.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'ba-huir-duik.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', '723a3626-c2f3-313d-863b-0d5cb6f1e82e.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'migdal-16546-15871.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'Niels_Bohr_Albert_Einstein3_by_Ehrenfest.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', '015.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', '97e34974-cf44-357d-859e-6b547a6540f9.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'a0500919-4d46-3531-b314-b178080f5c36.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'Altshuler4.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'BOHR2.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'bohr_explanation200.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'borland.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'borland2.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'image001.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'image002.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'o094.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', '1840.jpeg', null, null, 20, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', 'rgskryd7gms92ova.jpg', null, null, 21, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('bohrneils', '[IS11VI_1-01]_[GA_03]b.jpg', null, null, 22, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', '18-54-5b.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', '04-anand-topalov-02.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', '20110329140503.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', '373075.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'a3cf931599af247b61222d877a0f6ef7.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'anand-topalov_b2.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'anand.gif', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'anand.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'anand08.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'anand1.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'anand2-cb.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'AnandWithBoard.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'i4_146180_143.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'images.jpeg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'picture.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'picture1.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'picture11.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'vishi.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('anand', 'viswanathan-anand.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', '168076288.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'Kr1.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', '0184.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', '3192.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', '331x252_agMJu5d6FAwgO1xXekqTPXxfeiwWYACd.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', '8B9ADC79-0854-4D32-B98E-38EEA018B6B8_mw800_mh600_s.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'af3c04aad2b4eea9dfee74440df3bfc9.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'kramnik.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'Kramnik1.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'kramnik14.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'Kramnik3.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'kramnik_200810201305150.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'kramnik_robot.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kramnik', 'simpatyaga.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', '57540776_richard.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'Ruzhansky_1.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'image35.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'Ruzhansky_3.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'Feynman_and_Oppenheimer_at_Los_Alamos.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'feynman.gif', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'FEYN02.JPG', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'Feynman.JPG', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'feynman12.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'feynman1.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'feynman11.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'feynman2.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'Feynman22.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'td-feynman-thumb.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'feynman3.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'feynman_quality.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'Richard_Feynman_-_Fermilab.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', '59397184_ffffff.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('feynman', 'Feynman_IceDunk.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1-1.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '10.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '11.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '12.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1269989969_26.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113085_cool_chg.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113088_cool_cht.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113090_cool_chu.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113093_cool_ci0.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113103_cool_chj.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113115_cool_chk.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113122_cool_chl.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113146_cool_ci8.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113176_cool_ci3.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113179_cool_ci7.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1270113232_cool_cic.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '1305289918_chess_02.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '2158.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '3.jpg', null, null, 20, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '30.jpg', null, null, 21, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '31351.jpg', null, null, 22, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '4.jpg', null, null, 23, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '4224_big.jpg', null, null, 24, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '5.jpg', null, null, 25, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '51qolCksJiL.jpg', null, null, 26, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '7.jpg', null, null, 27, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '73614400_1.jpg', null, null, 28, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '73614432_1.jpg', null, null, 29, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '73614432_2.jpg', null, null, 30, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '73614455_5.jpg', null, null, 31, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '73614518_8.jpg', null, null, 32, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '8.jpg', null, null, 33, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', '9.jpg', null, null, 34, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'alice-in-wonderland-chess-set.jpg', null, null, 35, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'alice-in-wonderland.jpg', null, null, 36, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'aliens-chess-set-board.jpg', null, null, 37, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'aliens-chess-set.jpg', null, null, 38, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'Anymal-chess.jpg', null, null, 39, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'avpchess1.jpg', null, null, 40, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'avpchess2.jpg', null, null, 41, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'b5fd77d6eb.jpg', null, null, 42, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'b5fd77d6ec.jpg', null, null, 43, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'Bspk-56SG Silver Jubilee Chess set -side detail shot B&W.jpg', null, null, 44, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'calatrava-chess-set_01_mtEjm_17621.jpg', null, null, 45, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'calatrava-chess-set_05_2w5ts_17621.jpg', null, null, 46, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'cher1.png', null, null, 47, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'chess01.jpg', null, null, 48, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'chesshouse1.jpg', null, null, 49, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'ChessSets.jpg', null, null, 50, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'civil-war-chess-set-2.jpg', null, null, 51, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'civil-war-chess-set.jpg', null, null, 52, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'contemporary_metal_chess_setB600.jpg', null, null, 53, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'demo4.jpg', null, null, 54, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'ESB_Lego_Chess.jpg', null, null, 55, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'firefighter-chess-set.jpg', null, null, 56, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'g-m1.jpg', null, null, 57, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'greek-mythology-chess-set1.jpg', null, null, 58, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'greek-mythology-chess-set2.jpg', null, null, 59, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'image001.jpg', null, null, 60, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'lego.jpg', null, null, 61, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'lrg_tribal001(1).jpg', null, null, 62, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'm1-alabaster-chess-set.jpg', null, null, 63, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'm2-marble-chess-sets.jpg', null, null, 64, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'master CB124.jpg', null, null, 65, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'police-chess-set1.jpg', null, null, 66, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'police-chess-set2.jpg', null, null, 67, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'post-2-12478516555590.jpg', null, null, 68, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'PSHK.jpg', null, null, 69, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'recycled-chess-set_2_bE4tl_5638.jpg', null, null, 70, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'recycled-chess-set_8_ZnzFK_5638.jpg', null, null, 71, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'revolutionary-war-chess-set.jpg', null, null, 72, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'starwars_chess.jpg', null, null, 73, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'travel-chess-set.jpg', null, null, 74, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'unique-chess-set.jpg', null, null, 75, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'unusual_chess_1.jpg', null, null, 76, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'unusual_chess_39.jpg', null, null, 77, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'unusual_chess_40.jpg', null, null, 78, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'vacuumchess3.jpg', null, null, 79, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'veronachessset1.jpg', null, null, 80, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'veronachessset2.jpg', null, null, 81, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'veronachessset3.jpg', null, null, 82, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'waterloo-chess.jpg', null, null, 83, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('chess', 'xgent9617.jpg', null, null, 84, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', '1.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', '60808.thw.jpeg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'capablanca012.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', '16gd0b.jpeg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', '220px-Chess_Fever_Kapablanka.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'Alekhine_Capablanca_WCC_1927.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'Berlin.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'Capablanca-Botvinnik_1936.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'capablanca.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'cn4092_capablanca2.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', '264897338.JPG', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'capablanca04-ew.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'capablanca17-olga-ew.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'capablanca3.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'Kapablanka.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'thumbig1642450.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('capablanca', 'StampTajikistanSc168c.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'mikhail-botvinnik_3-t.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'botvinnik.jpg', null, null, 2, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', '047582246_Botvinnik.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', '1303216536_botvinnik.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', '999747266.JPG', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'botvinnik01-ew.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'botvinnik1.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'botvinnik2.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'botvinnik_mikhail.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'image_big.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'h_7b7e68fed1992a346fbefe19fdf49933.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'mikhail-botvinnik_4.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'npid_15619.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'rc33-14.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'smyslov_botvinnik58.jpg', null, null, 15, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'botvinnik.gif', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', 'z9215d8_d93fdd68_M.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('botvinnik', '12363302792dd1.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', '797880422.JPG', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'euve.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'euwe.gif', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'Euwe.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'euwe1.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'vlast_42_071dsrfe.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'maks_eyve_max_eyve_main.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'euw.gif', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euwe', 'Max_Euwe_1973.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '1962_Bobby_Fischer_by_Ralph_Ginzburg.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '0001qb18.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '0527113.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '1044722.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '12932280012.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '12932280014.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '33.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '411144-2599164-317-238.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', '416px-Bundesarchiv_Bild_183-76052-0053_XIV__Schacholympiade_in_Leipzig.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'bobby_fischer_2.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'fischer-bobby.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'Fischer-Tal.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'Fischer.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'fischer1.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'Fisher-2.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'fisher-300x198.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'fisher.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'Fisher_Robert.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'image.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'PAR38509.jpg', null, null, 20, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'Spassky-Fischer.jpg', null, null, 21, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('fischer', 'zfischer06.jpg', null, null, 22, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', '00afdae3.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'karpov.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', '0150355_42849278100_7489_n.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'akarpov.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', '03--f16ed.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', '302893390.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', '378484030.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', '378484031.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', '74610428_4330087_Anatolii_Karpov.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'I-05-STORY-karp-f20_640.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'Karpov (1).jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'karpov (2).jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'karpov-anatoliy-evgenevich_173_1268012707.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'karpov_ae.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'karpov_school.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'kasparov-2300.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'kasparov-2309.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'kasparov_karpov_579.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('karpov', 'kasparov_karpov_580.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '02.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '03.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '01.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '06.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '1695737.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '1981_08.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '761_1_max.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', '8.5.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'azarov_kasparov3.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'garry-kasparov-by-cool-sports-players-3-.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'Garry-Kasparov-by-cool-sports-players-4.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'garry-kasparov_4078.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'Garry_Kasparov_1980_Malta.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'kasparov (1).jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'kasparov (2).jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'Kasparov-11.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'kasparov.jpg', null, null, 17, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'Kasparov1.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('kasparov', 'Kasparov10.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'emanuel-lasker.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', '1.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', '14355.gif', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', '145825792.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', '2.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', '50044933_Lasker.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', '220px-Lasker.png', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', '819794276.JPG', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'cn4995_lasker1.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'go305.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'Emanuel Lasker.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'lasker.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'lasker-janovsky.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'lasker01-rf.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'lasker02.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'lasker08.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'lasker2.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'Lasker4.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'zFiles.aspx.jpeg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('lasker', 'R24_12_03.jpg', null, null, 20, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'smyslov1-246x300.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'smy.gif', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', '96ae06422366.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'cn4977_smyslov.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'images.jpeg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'n809541.PNG', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'smyslov.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'smyslov01.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'smyslov1.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'SmyslovSasha-768092.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'SmyslovVV.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'SmyslovVV2.JPG', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'Utrennik_Smyslov.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('smyslov', 'Smyslov2002.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', '54506432_Spasskiy.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'spassky.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', '157604355.JPG', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'Boris.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'Boris_Spassky_1983.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', '16.JPG', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', '29021_72452.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', '29021_72453.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', '29021_72454.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', '29021_72455.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'Boris_Spassky.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'spasskiy_201009231954540.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'spasskiy_boris.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'Spasski_1984_Saloniki.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'Spassky_Boris.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('spasskiy', 'spasskiy_2.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'Mikhail_Tal_1968.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'amazing_fun_weird_cool_bobby-fisher-mikhail-tal_200907240536322783.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'bot_tal1.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'DATA_PREV1998_pt7854.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'diner.m.talj.big.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'images.jpeg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'Michail Tal.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'MikhailTal143.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'sz12363304536af7.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'ta1l.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'Tal.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'tal1.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'talhist1.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'talhist2.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'talhist3.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'talhist4.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'tal_1960.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'tal_1971.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('tal', 'Tal_Mikhael.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'petrosian.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '1229250993_46.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '16.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'potrosyan.jpg', null, null, 4, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '17.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '174562404.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '174619102.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '18.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '3254001_0.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '359761028.JPG', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', '9721.gif', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'petrosian1.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'petrosyan-04122009150916H93_middle.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'h_f1dfe1776102828ca2049567b8e7cd6d.jpeg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'petrosyan.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'post-31580-1247479724.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'RIAN_683498.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'tigranpetrosyan.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('petrosyan', 'tigranpetrosyan1.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', '000076.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', '51242377.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', '525.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'b0e751a5576a443b051ef5858fcdd7be.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'image.axd.jpeg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'image034.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'Isaac Newton.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'Isaac-newton_1.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'isaac_newton.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'isaac_newton_22428_lg.gif', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'newton.article.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'Sir_Isaac_Newton.png', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('newton', 'vol3-401-Sir-Isaac-Newton-q75-484x500.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', '1pifagor01.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', '2pifagor.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', '3pifagor.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', '4.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', '6a00d8345228bf69e200e551d3c3ac8834-800wi.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'H4160201.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'mathematicians.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'Pifagor.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'pifagor3.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'pifagor[6].gif', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'Pythagoras.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'pythagoras3.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'Pythagoras_.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'Pythagore-chartres.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'pyth_theano.gif', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'statue_of_pythagoras_pythagorian_samos_greece_photo_dan_beamer.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('pifagor', 'universum.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '0.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '1.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '2.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '6.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '21.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '5.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '3.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '30.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '31.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '4.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '425613_310234802363160_100001299281715_802820_341209736_n.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '431082_2773442010493_1091313571_2030253_1913633619_n.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '6492_264295490104_261314600104_8190600_6922428_n.jpg', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '7.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '777711.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '8.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '88ccd1eeedc4ab20-large.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '9.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '90352259.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'AllDay.ru_46.jpg', null, null, 20, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'ar381.jpg', null, null, 21, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arn113bg.jpg', null, null, 22, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arn13.jpg', null, null, 23, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arni.jpg', null, null, 24, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold Schwarzenegger (7).jpg', null, null, 25, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold schwarzenegger bodybuilding photos_on3.jpg', null, null, 26, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold schwarzenegger bodybuilding photos_on4.png', null, null, 27, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold Schwarzenegger Bodybuilding Wallpaper-26.JPG', null, null, 28, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold Schwarzenegger Bodybuilding Wallpapers-8.jpg', null, null, 29, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold+Schwarzenegger.jpg', null, null, 30, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold-03.jpg', null, null, 31, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold-Classic-Workout-06.jpg', null, null, 32, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold-Classic-Workout-Dont-even-try-to-catch-me-09.jpg', null, null, 33, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-010.jpg', null, null, 34, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-019.jpg', null, null, 35, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-037.jpg', null, null, 36, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-041.jpg', null, null, 37, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-060.jpg', null, null, 38, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-068.jpg', null, null, 39, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-073.jpg', null, null, 40, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-089.jpg', null, null, 41, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-big.jpg', null, null, 42, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-body.jpg', null, null, 43, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-bodybuilding-pic.jpg', null, null, 44, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold-Schwarzenegger-Bodybuilding-Wallpaper-34.jpg', null, null, 45, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-commercials-1.png', null, null, 46, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-commercials-2.png', null, null, 47, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-fitness-legend.jpg', null, null, 48, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold-Schwarzenegger-predator.jpg', null, null, 49, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-training-photos-11.png', null, null, 50, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger-training-photos-9.png', null, null, 51, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger.jpg', null, null, 52, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold-Schwarzenegger1.jpg', null, null, 53, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger1715s.jpg', null, null, 54, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-schwarzenegger5.jpg', null, null, 55, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__10.jpg', null, null, 56, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__11.jpg', null, null, 57, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__13.jpg', null, null, 58, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__15.jpg', null, null, 59, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__16.jpg', null, null, 60, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__17.jpg', null, null, 61, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__19.jpg', null, null, 62, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__20.jpg', null, null, 63, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__21.jpg', null, null, 64, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__22.jpg', null, null, 65, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__23.jpg', null, null, 66, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__25.jpg', null, null, 67, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__26.jpg', null, null, 68, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__27.jpg', null, null, 69, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__28.jpg', null, null, 70, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__29.jpg', null, null, 71, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__3.jpg', null, null, 72, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__30.jpg', null, null, 73, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__31.jpg', null, null, 74, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__32.jpg', null, null, 75, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__33.jpg', null, null, 76, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__34.jpg', null, null, 77, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__35.jpg', null, null, 78, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__36.jpg', null, null, 79, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__37.jpg', null, null, 80, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__38.jpg', null, null, 81, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__39.jpg', null, null, 82, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__4.jpg', null, null, 83, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__40.jpg', null, null, 84, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__42.jpg', null, null, 85, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__43.jpg', null, null, 86, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__44.jpg', null, null, 87, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__47.jpg', null, null, 88, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__48.jpg', null, null, 89, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__49.jpg', null, null, 90, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__50.jpg', null, null, 91, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__51.jpg', null, null, 92, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__52.jpg', null, null, 93, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__53.jpg', null, null, 94, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__54.jpg', null, null, 95, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__56.jpg', null, null, 96, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__57.jpg', null, null, 97, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__59.jpg', null, null, 98, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__60.jpg', null, null, 99, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__61.jpg', null, null, 100, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__62.jpg', null, null, 101, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__63.jpg', null, null, 102, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__64.jpg', null, null, 103, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__66.jpg', null, null, 104, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__68.jpg', null, null, 105, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__7.jpg', null, null, 106, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__70.jpg', null, null, 107, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__72.jpg', null, null, 108, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__73.jpg', null, null, 109, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__74.jpg', null, null, 110, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__75.jpg', null, null, 111, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__76.jpg', null, null, 112, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__77.jpg', null, null, 113, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__78.jpg', null, null, 114, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__8.jpg', null, null, 115, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__81.jpg', null, null, 116, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__82.jpg', null, null, 117, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__83.jpg', null, null, 118, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__84.jpg', null, null, 119, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__85.jpg', null, null, 120, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__87.jpg', null, null, 121, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__89.jpg', null, null, 122, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__90.jpg', null, null, 123, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__93.jpg', null, null, 124, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__94.jpg', null, null, 125, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__95.jpg', null, null, 126, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__96.jpg', null, null, 127, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold-v-molodosti_26421_s__97.jpg', null, null, 128, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold.jpg', null, null, 129, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold12.jpg', null, null, 130, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold13.jpg', null, null, 131, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold18.jpg', null, null, 132, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold20.jpg', null, null, 133, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold6.jpg', null, null, 134, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold61.jpg', null, null, 135, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'ArnoldSchwarzeneggerWorkout.jpg', null, null, 136, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'ArnoldSchwarznegger-eMMP-Unk-459.jpg', null, null, 137, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold_2005.jpg', null, null, 138, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Body_Building.jpg', null, null, 139, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Schwarzenegger_1918.jpg', null, null, 140, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Schwarzenegger_4.jpg', null, null, 141, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Schwarzenegger_657.jpg', null, null, 142, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Schwarzenegger_660.jpg', null, null, 143, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Schwarzenegger_661.jpg', null, null, 144, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Schwarzenegger_662.jpg', null, null, 145, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Arnold_Schwarzenegger_663.jpg', null, null, 146, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'arnold_shvartsenegger37.jpg', null, null, 147, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'bodybuilding-pics-arnold-schwarzenegger-1379.jpg', null, null, 148, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'bodybuildinghistory_arnold1.jpg', null, null, 149, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Body_Building_Arnold_Schwarzenegger_14.jpg', null, null, 150, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'b_23531.jpg', null, null, 151, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'b_23536.jpg', null, null, 152, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'conan-arnold-schwarzenegger.jpg', null, null, 153, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'f_18245663.jpg', null, null, 154, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'lvyfsi.jpg', null, null, 155, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Schawrzy5.jpg', null, null, 156, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'schw.jpg', null, null, 157, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Schwarzenegger-Wallpaper.jpg', null, null, 158, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'schwarzenegger14.jpg', null, null, 159, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'sport247.jpg', null, null, 160, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'split_arni.jpg', null, null, 161, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'm383.jpg', null, null, 166, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'albert-einstein.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein_580x.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Albert-Einstein_large.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'AlbertEinstein.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Albert_Einstein.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'apollo-einstein3.png', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'd2a2695a2e08ff404de6c4b0bc3b0634.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Einstein staring picture.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Einstein-739570.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Einstein-at-blackboard-chalk-in-hand.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein-on-bikes.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein.png', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein010.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Einstein05.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Einstein11.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein111.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein12.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein2.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein3.jpg', null, null, 20, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein4.jpg', null, null, 21, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einstein460x276.jpg', null, null, 22, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'einsteinyoung.jpg', null, null, 23, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'Einstein_portrait.jpg', null, null, 24, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', '1102157000_einstein-albert.jpg', null, null, 25, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'en.gif', null, null, 26, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('einstein', 'yUZ85WWPD6Q.jpg', null, null, 27, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', '04-03.gif', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'Archimed.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'Archimedes.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'Archimedes1.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'archimedesscrewwiki.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'Archimedes_and_burning_mirror.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'Archimedes_lever_(Small).jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'Arhimed.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'Domenico-Fetti_Archimedes_1620.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'FieldsMedalFront.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'image024.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', 'o033.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('arhimed', '10-05.gif', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', '3.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'e1.JPG', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'e2.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'e3.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'eevklid.jpg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'euclide.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'EuclidStatueOxford.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'Euklid-von-Alexandria_1.jpg', null, null, 8, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'Euklid2.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'evklid.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'evklid_evklid_iz_megari3.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('euclide', 'w1225723606_2.jpg', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', '42539051661.jpg', null, null, 162, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'd5a2d20a496eb38c2d385129b0236ad2.jpg', null, null, 163, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'FranchGymShvarc.jpg', null, null, 164, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('schwarzenegger', 'Kak-nakachat-biceps-bystro-metodika-Arnolda-Shvarceneggera.jpg', null, null, 165, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('NielsBohr', 'i9loge9JSOQ.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('NielsBohr', 'http://alexandr4784.narod.ru/images_p/bor_nils.jpg', null, null, 2, 1, 1);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'Aliochin_A.A._1909_Karl_Bulla.jpg', null, null, 1, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'voronkov22.jpg', null, null, 2, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'Alexandre_Alekhine_Color.jpg', null, null, 3, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '28bb919fde9d.jpg', null, null, 4, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '0016htze.jpeg', null, null, 5, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '01Alehkine--.jpg', null, null, 6, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '02Alekhin-Muenchen-1941.jpg', null, null, 7, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '05alekhine3.jpg', null, null, 8, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '36816814_hhhPamKotSHahmatuyAlyohinchess_1a.jpg', null, null, 9, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '06AlekhineChess.jpg', null, null, 10, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '45116254_Alekhin_Alexander.jpg', null, null, 11, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', '616022096.JPG', null, null, 12, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'alekhine.gif', null, null, 13, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'Alekhine_Euwe_1937.jpg', null, null, 14, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'alekhine.jpg', null, null, 15, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'alexandr_aliohin.jpg', null, null, 16, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'alexandr_aliohin_with_chess.jpg', null, null, 17, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'voronkov21.jpg', null, null, 18, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'voronkov23.jpg', null, null, 19, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'voronkov30.jpg', null, null, 20, 0, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'voronkov24.jpg', 'пвапва', null, 21, 1, 0);
insert into ps_gallery_images (v_dir, v_file, v_name, v_descr, n_order, b_show, b_web) values ('alekhin', 'voronkov25.jpg', null, null, 22, 0, 0);


/*
 * INCLUDE TABLE [ps_mappings]
 */
insert into ps_mappings (mhash, lident, rident, ord) values ('1628e4831845b9641d1b03e25554b12b', 'famous', 'timeline', 1);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'news', 'post-is', 1);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'news', 'rubric-bp', 2);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'news', 'post-bp', 3);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'news', 'rubric-tr', 4);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'news', 'post-tr', 5);
insert into ps_mappings (mhash, lident, rident, ord) values ('eb1f36eaa88868e7dbc455e93ced63b0', 'blog_post_comments', 'posts', 1);
insert into ps_mappings (mhash, lident, rident, ord) values ('eb1f36eaa88868e7dbc455e93ced63b0', 'blog_post_comments', 'popups', 2);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'posts', 'post-is', 1);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'posts', 'rubric-bp', 2);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'posts', 'post-bp', 3);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'posts', 'rubric-tr', 4);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'posts', 'post-tr', 5);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'popups', 'post-is', 1);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'popups', 'rubric-bp', 2);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'popups', 'post-bp', 3);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'popups', 'rubric-tr', 4);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'popups', 'post-tr', 5);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'popups', 'pl', 6);
insert into ps_mappings (mhash, lident, rident, ord) values ('40d78cb5d0360ef3c57e8ec23c98dcd3', 'popups', 'pp', 7);
insert into ps_mappings (mhash, lident, rident, ord) values ('402a0ad60aa2034ba74efab8aeb3366e', 'timelines', 'ps_lib_item', 1);
insert into ps_mappings (mhash, lident, rident, ord) values ('402a0ad60aa2034ba74efab8aeb3366e', 'timelines', 'v_ps_lib_item', 2);
insert into ps_mappings (mhash, lident, rident, ord) values ('402a0ad60aa2034ba74efab8aeb3366e', 'timelines', 'ps_timeline', 3);
insert into ps_mappings (mhash, lident, rident, ord) values ('402a0ad60aa2034ba74efab8aeb3366e', 'timelines', 'ps_timeline_item', 4);
insert into ps_mappings (mhash, lident, rident, ord) values ('402a0ad60aa2034ba74efab8aeb3366e', 'gallery', 'ps_gallery', 1);
insert into ps_mappings (mhash, lident, rident, ord) values ('402a0ad60aa2034ba74efab8aeb3366e', 'gallery', 'ps_gallery_images', 2);

