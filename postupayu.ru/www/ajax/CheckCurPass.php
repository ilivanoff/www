<?php

require_once 'AjaxTools.php';

$pass = RequestArrayAdapter::inst()->str(FORM_PARAM_REG_OLD_PASS);

echo $pass && PsUser::inst()->checkPassword($pass) ? 'true' : 'false';
?>
