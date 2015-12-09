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