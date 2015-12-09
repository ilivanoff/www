<?php

/**
 * Интерфейс для тех контекстов, в рамках которых проходит нумерация задач
 */
interface TasksNumeratorContext {

    public function resetTasksNumber();

    public function getNextTaskNumber();

    public function getTasksCount();
}

?>