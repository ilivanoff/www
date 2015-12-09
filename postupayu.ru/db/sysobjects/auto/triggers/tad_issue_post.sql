delimiter |

DROP TRIGGER IF EXISTS tad_issue_post;

CREATE TRIGGER tad_issue_post AFTER DELETE
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
END
|

delimiter ;
