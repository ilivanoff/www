delimiter |

DROP TRIGGER IF EXISTS tad_train_post;

CREATE TRIGGER tad_train_post AFTER DELETE
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
END
|

delimiter ;
