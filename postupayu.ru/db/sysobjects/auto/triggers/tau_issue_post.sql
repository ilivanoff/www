delimiter |

DROP TRIGGER IF EXISTS tau_issue_post;

CREATE TRIGGER tau_issue_post AFTER UPDATE
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
	CALL onDbChange(CONCAT('post-is-', NEW.ident), 'F');
END
|

delimiter ;
