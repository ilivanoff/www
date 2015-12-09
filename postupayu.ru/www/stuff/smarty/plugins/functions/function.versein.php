<?php

/*
 * Вставляет текст стиха для поэта
 */

function smarty_function_versein($params, Smarty_Internal_Template &$template) {

    $params = ArrayAdapter::inst($params);

    $poetIdent = $params->str('poet');
    $verseIdent = $params->str('verse');

    $verse = PoetsManager::inst()->getVerse($poetIdent, $verseIdent);
    echo $verse->getContent();

    FoldedContextWatcher::getInstance()->setDependsOnEntity($verse->getFoldedEntity());
}

?>