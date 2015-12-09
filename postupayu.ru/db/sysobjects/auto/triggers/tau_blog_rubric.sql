delimiter |

DROP TRIGGER IF EXISTS tau_blog_rubric;

CREATE TRIGGER tau_blog_rubric AFTER UPDATE
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
	CALL onDbChange(CONCAT('rubric-bp-', NEW.ident), 'F');
END
|

delimiter ;
