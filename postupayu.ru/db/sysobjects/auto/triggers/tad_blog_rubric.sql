delimiter |

DROP TRIGGER IF EXISTS tad_blog_rubric;

CREATE TRIGGER tad_blog_rubric AFTER DELETE
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
END
|

delimiter ;
