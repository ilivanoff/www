delimiter |

DROP TRIGGER IF EXISTS tau_train_post;

CREATE TRIGGER tau_train_post AFTER UPDATE
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
	CALL onDbChange(CONCAT('post-tr-', NEW.ident), 'F');
END
|

delimiter ;
