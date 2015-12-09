/*
Created: 14.08.2010
Modified: 20.01.2015
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

-- Table ps_inflects

CREATE TABLE  IF NOT EXISTS ps_inflects
(
  id_inflect Int UNSIGNED NOT NULL AUTO_INCREMENT,
  v_word Varchar(255) NOT NULL,
  v_var1 Varchar(255) NOT NULL,
  v_var2 Varchar(255) NOT NULL,
  v_var3 Varchar(255) NOT NULL,
  v_var4 Varchar(255) NOT NULL,
  v_var5 Varchar(255) NOT NULL,
  v_var6 Varchar(255) NOT NULL,
 PRIMARY KEY (id_inflect)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_bin
  COMMENT = 'Склонение слов'
;

ALTER TABLE ps_inflects ADD UNIQUE v_word (v_word)
;

-- Create relationships section ------------------------------------------------- 

ALTER TABLE page_watch ADD CONSTRAINT Relationship28 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_misprint ADD CONSTRAINT Relationship40 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_upload ADD CONSTRAINT Relationship41 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_timeline_item ADD CONSTRAINT Relationship44 FOREIGN KEY (id_timeline) REFERENCES ps_timeline (id_timeline) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_user_popups ADD CONSTRAINT Relationship82 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_user_codes ADD CONSTRAINT Relationship89 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_audit ADD CONSTRAINT Relationship91 FOREIGN KEY (id_user_authed) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;

ALTER TABLE ps_audit ADD CONSTRAINT Relationship92 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE NO ACTION ON UPDATE NO ACTION
;


