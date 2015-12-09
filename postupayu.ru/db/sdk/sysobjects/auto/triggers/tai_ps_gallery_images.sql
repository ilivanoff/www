delimiter |

DROP TRIGGER IF EXISTS tai_ps_gallery_images;

CREATE TRIGGER tai_ps_gallery_images AFTER INSERT
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;
