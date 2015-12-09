delimiter |

DROP TRIGGER IF EXISTS tau_train_rubric;

CREATE TRIGGER tau_train_rubric AFTER UPDATE
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
	CALL onDbChange(CONCAT('rubric-tr-', NEW.ident), 'F');
END
|

delimiter ;
