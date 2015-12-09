<?php

/**
 * Реализация интерфейса для тех контекстов, в рамках которых проходит нумерация задач
 */
class TasksNumeratorContextImpl extends FoldedContexAdapter implements TasksNumeratorContext {

    const CURRENT_TASK_NUM = 'CURRENT_TASK_NUM';

    public function resetTasksNumber() {
        $this->ctxt->resetParam(self::CURRENT_TASK_NUM);
    }

    public function getNextTaskNumber() {
        return $this->ctxt->getNumAndIncrease(self::CURRENT_TASK_NUM);
    }

    public function getTasksCount() {
        return $this->ctxt->getParam(self::CURRENT_TASK_NUM, 0);
    }

}

?>
