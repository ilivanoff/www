<?php

function smarty_block_citatas($params, $content, Smarty_Internal_Template &$template) {
    SmartyBlockContext::getInstance()->registerBlock($content, __FUNCTION__);
    if ($content) {
        PSSmarty::template('common/citatas.tpl', array('c_body' => trim($content)))->display();
    }
}

?>
