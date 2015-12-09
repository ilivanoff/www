delimiter |

DROP TRIGGER IF EXISTS tai_train_rubric;

CREATE TRIGGER tai_train_rubric AFTER INSERT
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
END
|

delimiter ;
