<?php

/*
  {task}
  <p>Условие задачи</p>
  {hint}
  <p>Подсказка</p>
  {/hint}
  {solut}
  <p>Решение</p>
  {/solut}
  {ans}
  <p>Ответ</p>
  {/ans}
  {proof}
  <p>Доказательство</p>
  {/proof}
  {/task}
 */

function smarty_block_task($params, $content, Smarty_Internal_Template &$template) {
    $ctxtParams = SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);

    if (!$content) {
        return; //---
    }

    /* @var $taskTpl Smarty_Internal_Template */
    $taskTpl = PSSmarty::template('common/task.tpl', $ctxtParams);

    $from = value_Array(array('from', 'c_from'), $params);

    $taskTpl->assign('from', $from);
    $taskTpl->assign('body', $content);

    $isSubTask = SmartyBlockContext::getInstance()->hasParentBlock('tasks');
    $taskTpl->assign('sub_task', $isSubTask);

    $taskNum = null;
    if (!$isSubTask) {
        $taskNumber = null;
        //Допускается вывод задач не в контексте
        $ctxt = FoldedContextWatcher::getInstance()->getTasksNumeratorContext(false);
        if ($ctxt) {
            $taskNumber = $ctxt->getNextTaskNumber();
        }
        $taskNum = $taskNumber ? "Задача № $taskNumber" : null;
    }
    $taskTpl->assign('task_num', $taskNum);

    $taskTpl->display();
}

?>
