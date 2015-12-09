delimiter |

DROP TRIGGER IF EXISTS tad_ps_timeline;

CREATE TRIGGER tad_ps_timeline AFTER DELETE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;
