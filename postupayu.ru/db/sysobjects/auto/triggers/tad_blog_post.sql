delimiter |

DROP TRIGGER IF EXISTS tad_blog_post;

CREATE TRIGGER tad_blog_post AFTER DELETE
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
END
|

delimiter ;
