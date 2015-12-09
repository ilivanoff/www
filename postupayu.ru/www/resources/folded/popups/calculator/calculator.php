<?php

class PP_calculator extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Калькулятор';
    }

    public function getDescr() {
        return "Приложение является заменой обычному калькулятору и имеет неограниченный список возможностей для вычисления произвольных выражений.";
    }

    public function doProcess(ArrayAdapter $params) {
        
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        return $this->getFoldedEntity()->fetchTpl();
    }

    public function getSmartyParams4Resources() {
        
    }

    public function getPopupVisibility() {
        return PopupVis::TRUE_DEFAULT;
    }

}

?>
