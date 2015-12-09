delimiter |

DROP TRIGGER IF EXISTS tai_ps_gallery;

CREATE TRIGGER tai_ps_gallery AFTER INSERT
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;
