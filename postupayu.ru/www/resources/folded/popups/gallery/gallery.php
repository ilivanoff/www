<?php

class PP_gallery extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    private $galleryId;

    public function doProcess(ArrayAdapter $params) {
        $this->galleryId = $params->str('id');
    }

    public function getTitle() {
        return 'Галлерея ' . PsGallery::inst($this->galleryId)->getName();
    }

    public function getDescr() {
        return "Отображение галлереи картинок.";
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        return $this->getFoldedEntity()->fetchTpl(array('images' => PsGallery::inst($this->galleryId)->getListImages()));
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
