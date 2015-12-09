delimiter |

DROP TRIGGER IF EXISTS tad_ps_gallery_images;

CREATE TRIGGER tad_ps_gallery_images AFTER DELETE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;
