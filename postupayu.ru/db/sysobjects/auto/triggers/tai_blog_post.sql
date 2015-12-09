delimiter |

DROP TRIGGER IF EXISTS tai_blog_post;

CREATE TRIGGER tai_blog_post AFTER INSERT
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
END
|

delimiter ;
