select * from test;

delete from test;

insert into test (
   dt, ut
) VALUES (
   now(),
   UNIX_TIMESTAMP()
);


SET time_zone = '-8:00';

SET time_zone = 'SYSTEM';

select now();

SELECT @@global.time_zone, @@session.time_zone;