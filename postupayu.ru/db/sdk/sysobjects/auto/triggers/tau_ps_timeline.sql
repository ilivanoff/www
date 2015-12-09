delimiter |

DROP TRIGGER IF EXISTS tau_ps_timeline;

CREATE TRIGGER tau_ps_timeline AFTER UPDATE
  ON ps_timeline FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_timeline', 'T');
END
|

delimiter ;
