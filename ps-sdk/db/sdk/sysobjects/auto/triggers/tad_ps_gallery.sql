delimiter |

DROP TRIGGER IF EXISTS tad_ps_gallery;

CREATE TRIGGER tad_ps_gallery AFTER DELETE
  ON ps_gallery FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery', 'T');
END
|

delimiter ;
