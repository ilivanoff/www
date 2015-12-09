delimiter |

DROP TRIGGER IF EXISTS tai_blog_rubric;

CREATE TRIGGER tai_blog_rubric AFTER INSERT
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
END
|

delimiter ;
