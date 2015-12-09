delimiter |

DROP TRIGGER IF EXISTS tad_train_rubric;

CREATE TRIGGER tad_train_rubric AFTER DELETE
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
END
|

delimiter ;
