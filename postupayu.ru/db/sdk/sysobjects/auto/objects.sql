/*
 * AUTO TRIGGERS SECTION
 */

/*
 * + FILE [tad_ps_gallery.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_gallery;

CREATE TRIGGER tad_ps_gallery AFTER DELETE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_gallery_images.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_gallery_images;

CREATE TRIGGER tad_ps_gallery_images AFTER DELETE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_lib_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_lib_item;

CREATE TRIGGER tad_ps_lib_item AFTER DELETE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_timeline.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_timeline;

CREATE TRIGGER tad_ps_timeline AFTER DELETE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

/*
 * + FILE [tad_ps_timeline_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_ps_timeline_item;

CREATE TRIGGER tad_ps_timeline_item AFTER DELETE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_gallery.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_gallery;

CREATE TRIGGER tai_ps_gallery AFTER INSERT
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_gallery_images.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_gallery_images;

CREATE TRIGGER tai_ps_gallery_images AFTER INSERT
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_lib_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_lib_item;

CREATE TRIGGER tai_ps_lib_item AFTER INSERT
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_timeline.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_timeline;

CREATE TRIGGER tai_ps_timeline AFTER INSERT
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_ps_timeline_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_ps_timeline_item;

CREATE TRIGGER tai_ps_timeline_item AFTER INSERT
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_gallery.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_gallery;

CREATE TRIGGER tau_ps_gallery AFTER UPDATE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_gallery_images.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_gallery_images;

CREATE TRIGGER tau_ps_gallery_images AFTER UPDATE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
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
END
|

delimiter ;

/*
 * + FILE [tau_ps_timeline.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_timeline;

CREATE TRIGGER tau_ps_timeline AFTER UPDATE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_timeline_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_timeline_item;

CREATE TRIGGER tau_ps_timeline_item AFTER UPDATE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;

/*
 * INCLUDES SECTION
 */

/*
 * + FILE [v_ps_lib_item.sql]
 */
CREATE OR REPLACE VIEW v_ps_lib_item
AS
   SELECT *
     FROM ps_lib_item
    WHERE     b_show = 1;

/*
 * + FILE [onDbChange.sql]
 */
-- Процедура вставляет запись об изменении сущности БД
delimiter |

DROP PROCEDURE IF EXISTS onDbChange|

CREATE PROCEDURE onDbChange (IN ventity VARCHAR(255), IN vtype CHAR(1))
SQL SECURITY DEFINER
BEGIN
    delete from ps_db_changes where v_entity=ventity and v_type=vtype;
    insert into ps_db_changes (v_entity, v_type) values (ventity, vtype);
END
|

delimiter ;

/*
 * + FILE [checkViewsState.sql]
 */
-- Процедура проверяет предыдущее и текущее состояние представлений (views) и, если нужно, рождает событие изменения
delimiter |

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
 * + FILE [users.sql]
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