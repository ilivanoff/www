<?php

/**
 * Базовый интерфейс для всех фолдингов, хранящих свои сущности в базе
 *
 * @author azazello
 */
interface DatabasedFolding {

    /**
     * Таблица для хранения сущностей фолдинга.
     * Должна задаваться в виде [table.column.stype].
     * 
     * table - таблица, хранящая сущности фолдинга. Если начинается с v_, 
     *         то считается, что сущности, видимые пользователю, хранятся в представлении.
     * column - столбец, хранящий идентификатор фолдинга
     * stype - столбец, хранящий подтип фолдинга
     */
    function foldingTable();

    /**
     * "Виртуальная" запись в таблице для данной сущности фолдинга.
     * Используется для заполнения формы при создании записи и для поиска записи, если она существует.
     * $ident может быть и null, в таком случае необходимо попытаться получить следующий идентификатор записи.
     */
    function dbRec4Entity($ident);
}

?>