<?php

function smarty_function_form($params, Smarty_Internal_Template & $smarty) {
    $formId = value_Array('form_id', $params);

    $hiddens = array();
    foreach ($params as $key => $value) {
        if ($key == 'form_id') {
            continue;
        }
        $hiddens[$key] = $value;
    }

    PSForm::inst()->getForm($formId)->display($hiddens);
}

?>
