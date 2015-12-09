delimiter |

DROP TRIGGER IF EXISTS tad_ps_timeline_item;

CREATE TRIGGER tad_ps_timeline_item AFTER DELETE
  ON ps_timeline_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline_item', 'T');
END
|

delimiter ;
