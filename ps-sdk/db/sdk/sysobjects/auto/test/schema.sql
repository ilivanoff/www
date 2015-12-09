/*
 * + FILE [schema.sql]
 */
/*
Created: 14.08.2010
Modified: 20.01.2015
Model: MySQL 5.1
Database: MySQL 5.1
*/

-- Create tables section -------------------------------------------------

-- Table ps_test_data_load

CREATE TABLE ps_test_data_load
(
  v_key Varchar(255) NOT NULL,
  v_value Varchar(255) NOT NULL
)
;

ALTER TABLE ps_test_data_load ADD UNIQUE v_key (v_key)
;

/*
 * + FILE [data.sql]
 */
-- ps_test_data_load
INSERT INTO ps_test_data_load (v_key, v_value) VALUES ('key1', 'val1');
INSERT INTO ps_test_data_load (v_key, v_value) VALUES ('key2', 'val2');
INSERT INTO ps_test_data_load (v_key, v_value) VALUES ('key3', 'val3');
INSERT INTO ps_test_data_load (v_key, v_value) VALUES ('key4', 'val4');