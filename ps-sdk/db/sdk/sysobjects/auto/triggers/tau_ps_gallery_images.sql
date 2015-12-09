delimiter |

DROP TRIGGER IF EXISTS tau_ps_gallery_images;

CREATE TRIGGER tau_ps_gallery_images AFTER UPDATE
  ON ps_gallery_images FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_gallery_images', 'T');
END
|

delimiter ;
