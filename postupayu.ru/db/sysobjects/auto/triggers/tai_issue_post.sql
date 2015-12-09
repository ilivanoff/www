delimiter |

DROP TRIGGER IF EXISTS tai_issue_post;

CREATE TRIGGER tai_issue_post AFTER INSERT
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
END
|

delimiter ;
