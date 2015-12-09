<?php

/**
 * Функция вставляет текст всплывающей подсказки
 */
function smarty_block_help($params, $content, Smarty_Internal_Template &$template) {
    if (!$content) {
        return; //---
    }

    //Параметры
    $ident = $params->get(array('name', 'ident'));
    //Текст
    $text = $text == '.' ? null : $text;


    $entity = HelpManager::inst()->getFoldedEntity($ident);
    if ($entity) {
        return $entity->getFolding()->getLibItemBubbleHref($entity->getIdent(), $text);
    } else {
        if ($text) {
            return $text;
        } else {
            $info = ($libType ? $libType . '-' : '') . $ident;
            return PsHtml::spanErr("Не найден библиотечный элемент [$info]");
        }
    }
}

?>
