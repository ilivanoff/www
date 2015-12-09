<?php

require_once 'AjaxTools.php';

$form = PSForm::inst()->getSubmittedForm();
if ($form == null) {
    json_error('Форма не была засабмичена');
}

if (!($form instanceof BaseAjaxForm)) {
    json_error('Засабмиченная форма не может быть обработана через ajax');
}

if ($form->isErrorOccurred()) {
    json_error($form->getError());
} else {
    json_success($form->getData()->getJsParams());
}
?>