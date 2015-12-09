delimiter |

DROP TRIGGER IF EXISTS tai_ps_timeline;

CREATE TRIGGER tai_ps_timeline AFTER INSERT
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;
