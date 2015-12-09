delimiter |

DROP TRIGGER IF EXISTS tai_train_post;

CREATE TRIGGER tai_train_post AFTER INSERT
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
END
|

delimiter ;
