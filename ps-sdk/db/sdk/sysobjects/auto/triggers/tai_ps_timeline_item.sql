delimiter |

DROP TRIGGER IF EXISTS tai_ps_timeline_item;

CREATE TRIGGER tai_ps_timeline_item AFTER INSERT
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;
