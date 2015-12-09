CREATE OR REPLACE VIEW v_issue_post
AS
   SELECT *
     FROM issue_post
    WHERE     b_show = 1
          AND dt_publication IS NOT NULL
          AND dt_publication < unix_timestamp()
   ORDER BY dt_publication DESC, id_post DESC;


CREATE OR REPLACE VIEW v_train_post
AS
   SELECT *
     FROM train_post
    WHERE     b_show = 1
          AND dt_publication IS NOT NULL
          AND dt_publication < unix_timestamp()
   ORDER BY dt_publication DESC, id_post DESC;

CREATE OR REPLACE VIEW v_train_rubric
AS
   SELECT *
     FROM train_rubric r
    WHERE EXISTS
             (SELECT 1
                FROM v_train_post p
               WHERE p.id_rubric = r.id_rubric)
   ORDER BY r.name ASC;
   
CREATE OR REPLACE VIEW v_blog_post
AS
   SELECT *
     FROM blog_post
    WHERE     b_show = 1
          AND dt_publication IS NOT NULL
          AND dt_publication < unix_timestamp()
   ORDER BY dt_publication DESC, id_post DESC;


CREATE OR REPLACE VIEW v_blog_rubric
AS
   SELECT *
     FROM blog_rubric r
    WHERE EXISTS
             (SELECT 1
                FROM v_blog_post p
               WHERE p.id_rubric = r.id_rubric)
   ORDER BY r.name ASC;

CREATE OR REPLACE VIEW v_ps_lib_item
AS
   SELECT *
     FROM ps_lib_item
    WHERE     b_show = 1;