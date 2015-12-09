delimiter |

DROP TRIGGER IF EXISTS tau_blog_post;

CREATE TRIGGER tau_blog_post AFTER UPDATE
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
	CALL onDbChange(CONCAT('post-bp-', NEW.ident), 'F');
END
|

delimiter ;
