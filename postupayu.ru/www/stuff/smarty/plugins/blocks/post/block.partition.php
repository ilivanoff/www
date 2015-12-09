<?php

/*
 * Разделы занятий кружка.
 * Могут быть переданы в виде 'MAKROS' или 'MAKROS:VALUE'.
 */

function smarty_block_partition($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return;
    }

    $type = PostFetchingContext::getInstance()->getFoldedEntity()->getFolding()->getFoldingSubType();

    $aa = ArrayAdapter::inst(explode(':', $content));

    $MAKROS = $aa->str(0);
    $VALUE = $aa->str(1);

    switch ($type) {
        case POST_TYPE_TRAINING:
            //УПРАЖНЕНИЯ
            switch ($MAKROS) {
                case 'PLAN':
                    echo '<h3>План урока:</h3>';
                    break;
                case 'VIDEO':
                    echo '<h3>Видео урок:</h3>';
                    break;
                case 'CONSPECT':
                    echo '<h3>Конспект занятия:</h3>';
                    break;
                case 'TASKS':
                    if (!$VALUE) {
                        echo '<h3>Контрольные вопросы и задачи:</h3>';
                    } else {
                        echo "<h3 class=\"section\">$VALUE</h4>";
                    }
                    break;
                case 'FINAL':
                    echo '<h3>Заключение:</h3>';
                    break;

                default:
                    echo "<h3>$content</h3>";
                    break;
            }

            break;
        default:
            check_condition(false, __FUNCTION__ . ' function cannot be used with post type ' . $type);
    }
}

?>