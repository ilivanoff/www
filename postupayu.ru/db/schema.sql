/*
Created: 14.08.2010
Modified: 25.12.2014
Model: MySQL 5.1
Database: MySQL 5.1
*/

-- Create tables section -------------------------------------------------

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


