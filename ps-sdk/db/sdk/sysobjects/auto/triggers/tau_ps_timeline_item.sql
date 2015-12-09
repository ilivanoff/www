delimiter |

DROP TRIGGER IF EXISTS tau_ps_timeline_item;

CREATE TRIGGER tau_ps_timeline_item AFTER UPDATE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;
