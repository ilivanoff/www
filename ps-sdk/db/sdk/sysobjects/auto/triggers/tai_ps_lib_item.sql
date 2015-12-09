delimiter |

DROP TRIGGER IF EXISTS tai_ps_lib_item;

CREATE TRIGGER tai_ps_lib_item AFTER INSERT
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
END
|

delimiter ;
