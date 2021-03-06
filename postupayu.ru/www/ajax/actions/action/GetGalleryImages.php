<?php

class GetGalleryImages extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('id');
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(PsGallery::inst($params->str('id'))->getBoxImages());
    }

}

?>
