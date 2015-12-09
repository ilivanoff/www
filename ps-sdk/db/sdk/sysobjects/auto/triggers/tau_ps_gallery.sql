delimiter |

DROP TRIGGER IF EXISTS tau_ps_gallery;

CREATE TRIGGER tau_ps_gallery AFTER UPDATE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;
