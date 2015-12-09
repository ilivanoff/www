/*
 * + FILE [tai_issue_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_issue_post;

CREATE TRIGGER tai_issue_post AFTER INSERT
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_issue_post.sql]
 */
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

/*
 * + FILE [tad_issue_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_issue_post;

CREATE TRIGGER tad_issue_post AFTER DELETE
  ON issue_post FOR EACH ROW 
BEGIN
	CALL onDbChange('issue_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_blog_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_blog_rubric;

CREATE TRIGGER tai_blog_rubric AFTER INSERT
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_blog_rubric.sql]
 */
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

/*
 * + FILE [tad_blog_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_blog_rubric;

CREATE TRIGGER tad_blog_rubric AFTER DELETE
  ON blog_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_blog_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_blog_post;

CREATE TRIGGER tai_blog_post AFTER INSERT
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_blog_post.sql]
 */
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

/*
 * + FILE [tad_blog_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_blog_post;

CREATE TRIGGER tad_blog_post AFTER DELETE
  ON blog_post FOR EACH ROW 
BEGIN
	CALL onDbChange('blog_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_train_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_train_rubric;

CREATE TRIGGER tai_train_rubric AFTER INSERT
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_train_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_train_rubric;

CREATE TRIGGER tau_train_rubric AFTER UPDATE
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
	CALL onDbChange(CONCAT('rubric-tr-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * + FILE [tad_train_rubric.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_train_rubric;

CREATE TRIGGER tad_train_rubric AFTER DELETE
  ON train_rubric FOR EACH ROW 
BEGIN
	CALL onDbChange('train_rubric', 'T');
END
|

delimiter ;

/*
 * + FILE [tai_train_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tai_train_post;

CREATE TRIGGER tai_train_post AFTER INSERT
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_train_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_train_post;

CREATE TRIGGER tau_train_post AFTER UPDATE
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
	CALL onDbChange(CONCAT('post-tr-', NEW.ident), 'F');
END
|

delimiter ;

/*
 * + FILE [tad_train_post.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tad_train_post;

CREATE TRIGGER tad_train_post AFTER DELETE
  ON train_post FOR EACH ROW 
BEGIN
	CALL onDbChange('train_post', 'T');
END
|

delimiter ;

/*
 * + FILE [tau_ps_lib_item.sql]
 */
delimiter |

DROP TRIGGER IF EXISTS tau_ps_lib_item;

CREATE TRIGGER tau_ps_lib_item AFTER UPDATE
  ON ps_lib_item FOR EACH ROW 
BEGIN
	CALL onDbChange('ps_lib_item', 'T');
	CALL onDbChange(CONCAT('lib-', NEW.grup, '-', NEW.ident), 'F');
	CALL onDbChange(CONCAT('lib-', NEW.grup, '-', NEW.ident), 'F');
END
|

delimiter ;