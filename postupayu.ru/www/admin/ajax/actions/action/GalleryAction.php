<?php

class GalleryAction extends AbstractAdminAjaxAction {

    protected function getRequiredParamKeys() {
        return array('action', 'gallery');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $action = $params->str('action');
        $gallery = $params->str('gallery');

        switch ($action) {
            case 'creategall':
                PsGallery::makeNew($gallery, $params->str('name'));
                break;
            case 'save':
                PsGallery::inst($gallery)->saveGallery($params->str('name'), $params->arr('images'));
                break;
            case 'imgadd':
                PsGallery::inst($gallery)->addWebImg($params->arr('img'));
                break;
            case 'imgdel':
                if ($params->bool('web')) {
                    PsGallery::inst($gallery)->deleteWebImg($params->str('file'));
                } else {
                    PsGallery::inst($gallery)->deleteLocalImg($params->str('file'));
                }
                break;
            default:
                json_error("Unknown action [$action].");
        }

        return new AjaxSuccess();
    }

}

?>
