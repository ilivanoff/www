/*
 * AUTO TRIGGERS SECTION
 */

/*
 * + FILE [tad_blog_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_blog_post;

CREATE TRIGGER tad_blog_post AFTER DELETE
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_blog_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_blog_rubric;

CREATE TRIGGER tad_blog_rubric AFTER DELETE
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_issue_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_issue_post;

CREATE TRIGGER tad_issue_post AFTER DELETE
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_train_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_train_post;

CREATE TRIGGER tad_train_post AFTER DELETE
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_train_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_train_rubric;

CREATE TRIGGER tad_train_rubric AFTER DELETE
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_blog_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_blog_post;

CREATE TRIGGER tai_blog_post AFTER INSERT
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_blog_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_blog_rubric;

CREATE TRIGGER tai_blog_rubric AFTER INSERT
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_issue_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_issue_post;

CREATE TRIGGER tai_issue_post AFTER INSERT
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_train_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_train_post;

CREATE TRIGGER tai_train_post AFTER INSERT
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_train_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_train_rubric;

CREATE TRIGGER tai_train_rubric AFTER INSERT
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_blog_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_blog_post;

CREATE TRIGGER tau_blog_post AFTER UPDATE
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
	CALL onDbChange(CONCAT('post-bp-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * + FILE [tau_blog_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_blog_rubric;

CREATE TRIGGER tau_blog_rubric AFTER UPDATE
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
	CALL onDbChange(CONCAT('rubric-bp-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * + FILE [tau_issue_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_issue_post;

CREATE TRIGGER tau_issue_post AFTER UPDATE
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
	CALL onDbChange(CONCAT('post-is-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * + FILE [tau_ps_lib_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_lib_item;

CREATE TRIGGER tau_ps_lib_item AFTER UPDATE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
	CALL onDbChange(CONCAT('lib-', NEW.grup, '-', NEW.ident), 'F');
	CALL onDbChange(CONCAT('lib-', NEW.grup, '-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * + FILE [tau_train_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_train_post;

CREATE TRIGGER tau_train_post AFTER UPDATE
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
	CALL onDbChange(CONCAT('post-tr-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * + FILE [tau_train_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_train_rubric;

CREATE TRIGGER tau_train_rubric AFTER UPDATE
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
	CALL onDbChange(CONCAT('rubric-tr-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * INCLUDES SECTION
 */

/*
 * + FILE [views.sql]
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
 * + FILE [gym.sql]
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
 * + FILE [mosaic1.sql]
 */
/*
 *	Разбивка мозайки картинки 1
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
 * + FILE [mosaic3.sql]
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