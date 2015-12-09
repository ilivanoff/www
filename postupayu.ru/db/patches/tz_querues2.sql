SET time_zone = '+0:00';
select unix_timestamp();
select unix_timestamp('2012-02-24 12:00:00');


SET time_zone = '+4:00';
select unix_timestamp();
select unix_timestamp('2012-02-24 12:00:00');

SET time_zone = '+8:00';
select unix_timestamp();
select unix_timestamp('2012-02-24 12:00:00');



-- SET time_zone = 'SYSTEM';