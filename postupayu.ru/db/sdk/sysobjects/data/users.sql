/*
  Пользователи системы
*/

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg,
                  b_admin,
				  b_can_login)
VALUES (1,
        'Администратор системы',
        '1',
        'admin@mail.ru',
        '96e79218965eb72c92a549dd5a330112',
        UNIX_TIMESTAMP(),
        1,
		1);

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg,
                  b_admin,
				  b_can_login)
VALUES (2,
        'Система',
        '1',
        'system@postupayu.ru',
        '-',
        UNIX_TIMESTAMP(),
        1,
		0);

INSERT INTO users(id_user,
                  user_name,
                  b_sex,
                  email,
                  passwd,
                  dt_reg,
				  b_can_login)
VALUES (100,
        'Илья',
        '1',
        'azaz@mail.ru',
        '96e79218965eb72c92a549dd5a330112',
        UNIX_TIMESTAMP(),
		1);