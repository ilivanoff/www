<?php

function smarty_function_gallery($params, Smarty_Internal_Template & $smarty) {
    $params = ArrayAdapter::inst($params);
    $gallery = $params->str(array('dir', 'name'));

    echo PsGallery::inst($gallery)->getGalleryBox($params->bool('lazy'));
}

?>
