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