<?php

/**
 * Панель с плагинами предпросмотра
 */
function smarty_function_showcases_list_view_mode($params, Smarty_Internal_Template &$template) {
    return ShowcasesCtrlManager::inst()->includePanel(ShowcasesCtrlManager::PANEL_SCCONTROLS);
}

?>