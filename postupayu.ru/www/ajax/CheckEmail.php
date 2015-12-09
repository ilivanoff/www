<?php

require_once 'AjaxTools.php';

$email = RequestArrayAdapter::inst()->str(FORM_PARAM_REG_MAIL);
$mustPresent = RequestArrayAdapter::inst()->bool('mp');

$invalid = true;
if ($email) {
    $email = strtolower($email);
    $invalid = UserInputValidator::validateEmail($email, $mustPresent);
}

echo $invalid ? 'false' : 'true';
?>
