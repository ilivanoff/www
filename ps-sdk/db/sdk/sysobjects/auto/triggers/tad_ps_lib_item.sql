delimiter |

DROP TRIGGER IF EXISTS tad_ps_lib_item;

CREATE TRIGGER tad_ps_lib_item AFTER DELETE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;
