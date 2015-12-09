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
