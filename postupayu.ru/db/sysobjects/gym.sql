 /*
 	GYM
 */
/*
	Очистка
*/
 delete from gym_sets;
 delete from gym_programm_exercises;
 delete from gym_programm;
 delete from gym_exercises2muscle_group;
 delete from muscle_group;
 delete from gym_exercises;



/*ГРУППЫ МЫШЦ*/
insert into muscle_group (id_muscle_group, name, n_order) values (1, 'Трапеция', 1);
insert into muscle_group (id_muscle_group, name, n_order) values (2, 'Дельты', 2);
insert into muscle_group (id_muscle_group, name, n_order) values (3, 'Грудные', 3);
insert into muscle_group (id_muscle_group, name, n_order) values (4, 'Бицепс', 4);
insert into muscle_group (id_muscle_group, name, n_order) values (5, 'Предплечья', 5);
insert into muscle_group (id_muscle_group, name, n_order) values (6, 'Трицепс', 6);
insert into muscle_group (id_muscle_group, name, n_order) values (7, 'Прес', 7);
insert into muscle_group (id_muscle_group, name, n_order) values (8, 'Широчайшие', 8);
insert into muscle_group (id_muscle_group, name, n_order) values (9, 'Разгибатели спины', 9);
insert into muscle_group (id_muscle_group, name, n_order) values (10, 'Ягодичные', 10);
insert into muscle_group (id_muscle_group, name, n_order) values (11, 'Квадрицепсы', 11);
insert into muscle_group (id_muscle_group, name, n_order) values (12, 'Бицепс бедра', 12);
insert into muscle_group (id_muscle_group, name, n_order) values (13, 'Икры', 13);
 
 
/*УПРАЖНЕНИЯ*/
 
/*Трицепс*/
insert into gym_exercises (id_gym_ex, name, n_order) values (1, 'Жим штанги узким хватом', 1);
insert into gym_exercises (id_gym_ex, name, n_order) values (2, 'Отжимания в упоре сзади', 2);
insert into gym_exercises (id_gym_ex, name, n_order) values (3, 'Отжимания на параллельных брусьях', 3);
insert into gym_exercises (id_gym_ex, name, n_order) values (4, 'Разгибание руки с гантелью в наклоне', 4);
insert into gym_exercises (id_gym_ex, name, n_order) values (5, 'Разгибание гантели двумя руками из-за головы', 5);
insert into gym_exercises (id_gym_ex, name, n_order) values (6, 'Французский жим одной рукой', 6);
insert into gym_exercises (id_gym_ex, name, n_order) values (7, 'Разгибания рук на верхнем блоке', 7);
insert into gym_exercises (id_gym_ex, name, n_order) values (8, 'Разгибание одной руки с верхним блоком хватом снизу', 8);

/*Бицепс*/
insert into gym_exercises (id_gym_ex, name, n_order) values (10, 'Подъем штанги на бицепс', 10);
insert into gym_exercises (id_gym_ex, name, n_order) values (11, 'Поочередное сгибание рук на бицепс с гантелями', 11);
insert into gym_exercises (id_gym_ex, name, n_order) values (12, 'Поочередное сгибание рук на бицепс с гантелями хватом «молот»', 12);
insert into gym_exercises (id_gym_ex, name, n_order) values (13, 'Концентрированные сгибания рук на бицепс', 13);
insert into gym_exercises (id_gym_ex, name, n_order) values (14, 'Сгибание рук на бицепс со штангой обратным хватом', 14);

/*Грудь*/
insert into gym_exercises (id_gym_ex, name, n_order) values (21, 'Отжимания', 20);
insert into gym_exercises (id_gym_ex, name, n_order) values (22, 'Жим штанги лежа средним хватом на скамье с положит. наклоном', 21);
insert into gym_exercises (id_gym_ex, name, n_order) values (20, 'Жим лёжа средним хватом', 22);
insert into gym_exercises (id_gym_ex, name, n_order) values (25, 'Жим штанги лежа средним хватом на скамье с отрицат. наклоном', 23);
insert into gym_exercises (id_gym_ex, name, n_order) values (24, 'Жим гантелей лежа на скамье с положительным наклоном', 24);
insert into gym_exercises (id_gym_ex, name, n_order) values (23, 'Жим с гантелями лежа на горизонтальной скамье', 25);
insert into gym_exercises (id_gym_ex, name, n_order) values (26, 'Жим гантелей лежа на скамье с отрицательным наклоном', 26);
insert into gym_exercises (id_gym_ex, name, n_order) values (27, 'Пуловер с гантелей лежа на скамье', 27);
  
/*Трапеция*/
insert into gym_exercises (id_gym_ex, name, n_order) values (30, 'Шраги с гантелями', 30);
insert into gym_exercises (id_gym_ex, name, n_order) values (31, 'Тяга штанги к подбородку', 32);
insert into gym_exercises (id_gym_ex, name, n_order) values (32, 'Шраги со штангой', 31);

/*Широчайшие*/
insert into gym_exercises (id_gym_ex, name, n_order) values (40, 'Подтягивания прямым хватом', 40);
insert into gym_exercises (id_gym_ex, name, n_order) values (41, 'Подтягивания обратным хватом', 41);
insert into gym_exercises (id_gym_ex, name, n_order) values (42, 'Тяга верхнего блока перед собой', 42);
insert into gym_exercises (id_gym_ex, name, n_order) values (43, 'Тяга верхнего блока за шею', 43);
insert into gym_exercises (id_gym_ex, name, n_order) values (44, 'Тяга верхнего блока к груди с V образным грифом', 44);
insert into gym_exercises (id_gym_ex, name, n_order) values (45, 'Тяга верхнего блока прямыми руками к бёдрам', 45);
insert into gym_exercises (id_gym_ex, name, n_order) values (46, 'Тяга к груди в гребном тренажере', 46);
insert into gym_exercises (id_gym_ex, name, n_order) values (47, 'Тяга гантели в наклоне', 47);
insert into gym_exercises (id_gym_ex, name, n_order) values (48, 'Тяга штанги, стоя в наклоне', 48);
insert into gym_exercises (id_gym_ex, name, n_order) values (49, 'Тяга Т-образного грифа', 49);

/*Разгибатели спины*/
insert into gym_exercises (id_gym_ex, name, n_order) values (60, 'Гиперэкстензия', 60);
insert into gym_exercises (id_gym_ex, name, n_order) values (61, 'Наклоны вперёд со штангой на плечах («Доброе утро»)', 61);

/*Плечи (дельты)*/
insert into gym_exercises (id_gym_ex, name, n_order) values (70, 'Жим штанги с груди сидя', 70);
insert into gym_exercises (id_gym_ex, name, n_order) values (71, 'Жим гантелей сидя', 71);
insert into gym_exercises (id_gym_ex, name, n_order) values (72, 'Жим гантелей от Арнольда Шварценеггера', 72);
insert into gym_exercises (id_gym_ex, name, n_order) values (73, 'Подъёмы гантелей в стороны', 73);
insert into gym_exercises (id_gym_ex, name, n_order) values (74, 'Подъём гантелей перед собой', 74);
insert into gym_exercises (id_gym_ex, name, n_order) values (75, 'Подъём рук перед собой с гантелью', 75);
insert into gym_exercises (id_gym_ex, name, n_order) values (76, 'Подъём штанги перед собой на вытянутых руках', 76);
insert into gym_exercises (id_gym_ex, name, n_order) values (77, 'Подъём рук в стороны на тренажёре', 77);

/*Ягодичные*/
insert into gym_exercises (id_gym_ex, name, n_order) values (80, 'Выпады со штангой на плечах', 80);
insert into gym_exercises (id_gym_ex, name, n_order) values (81, 'Выпады с гантелями', 81);
insert into gym_exercises (id_gym_ex, name, n_order) values (82, 'Махи ногой назад на полу', 82);
insert into gym_exercises (id_gym_ex, name, n_order) values (83, 'Мостик лёжа', 83);
insert into gym_exercises (id_gym_ex, name, n_order) values (84, 'Разведение ног на тренажере', 84);
insert into gym_exercises (id_gym_ex, name, n_order) values (85, 'Махи ногой в стороны, лёжа на боку', 85);

/*Ноги*/
insert into gym_exercises (id_gym_ex, name, n_order) values (90, 'Приседания с гантелями', 90);
insert into gym_exercises (id_gym_ex, name, n_order) values (91, 'Приседания со штангой на груди', 91);
insert into gym_exercises (id_gym_ex, name, n_order) values (92, 'Приседания со штангой на плечах', 92);
insert into gym_exercises (id_gym_ex, name, n_order) values (93, 'Наклонный жим ногами', 93);
insert into gym_exercises (id_gym_ex, name, n_order) values (94, 'Разгибания ног в тренажёре', 94);
insert into gym_exercises (id_gym_ex, name, n_order) values (95, 'Сгибания ног в тренажёре', 95);

/*Икры*/
insert into gym_exercises (id_gym_ex, name, n_order) values (100, 'Подъём на носки стоя', 100);
insert into gym_exercises (id_gym_ex, name, n_order) values (101, 'Подъём на носок одной ноги стоя', 101);
insert into gym_exercises (id_gym_ex, name, n_order) values (102, 'Разгибание голени сидя', 102);

/*ПРИВЯЗКА УПРАЖНЕНИЙ К МЫШЦАМ*/
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (1, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (1, 3, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (2, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (3, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (3, 3, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (4, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (5, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (6, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (7, 6, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (8, 6, 1);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (10, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (11, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (12, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (13, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (14, 4, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (14, 5, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (20, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (21, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (21, 6, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (21, 2, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (22, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (23, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (24, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (25, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (26, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (27, 3, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (27, 8, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (30, 1, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (31, 2, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (31, 1, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (32, 1, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (40, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (41, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (42, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (43, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (44, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (45, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (46, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (47, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (48, 8, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (49, 8, 1);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (60, 9, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (60, 10, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (61, 9, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (61, 12, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (61, 10, 1);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (70, 2, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (71, 2, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (72, 2, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (73, 2, 4);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (74, 2, 5);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (75, 2, 6);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (76, 2, 7);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (77, 2, 8);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (80, 10, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (80, 11, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (81, 10, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (81, 11, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (82, 10, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (83, 10, 4);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (84, 10, 5);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (85, 10, 6);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (90, 10, 10);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (90, 11, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (91, 10, 11);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (91, 11, 2);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (92, 10, 12);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (92, 11, 3);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (93, 10, 13);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (93, 11, 4);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (94, 11, 5);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (95, 12, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (95, 13, 10);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (100, 13, 1);
insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (101, 13, 2);

insert into gym_exercises2muscle_group (id_gym_ex, id_muscle_group, n_order) values (102, 13, 3);
