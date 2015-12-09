<?php

function smarty_function_ctrl_button($params, Smarty_Internal_Template &$template) {

    $adapter = ArrayAdapter::inst($params);
    $data = array();

    if ($adapter->has('states')) {
        $type = 'states';
        $data['states'] = explode(' ', $adapter->str('states'));
    } else {
        $data['hoverable'] = $adapter->bool('hoverable');
        $data['gray'] = $adapter->bool('gray');
        $data['popup'] = $adapter->bool('popup');
        $data['blank'] = $adapter->bool('blank');
        $data['title'] = $adapter->str('title');
        $data['href'] = $adapter->str('href');
        $data['action'] = $adapter->str('action');
        $data['class'] = $adapter->str('class');
        $data['name'] = $adapter->str(array('name', 'img', 'action'));

        $type = $adapter->str('type');
        $type = $type ? $type : 'button';
    }

    /* @var $buttonTpl Smarty_Internal_Template */
    $buttonTpl = $template->smarty->createTemplate("discussion/controls/$type.tpl");
    $buttonTpl->assign($data);
    $buttonTpl->display();
}

?>
