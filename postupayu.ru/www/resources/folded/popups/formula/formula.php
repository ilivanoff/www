<?php

class PP_formula extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    public function getTitle() {
        return 'Работа с формулами';
    }

    public function getDescr() {
        return "В проекте для работы с формулами используется макроязык LaTeX.\nДанное приложение представляет собой «песочницу» для написания и отладки формул.";
    }

    private $params = array();

    public function doProcess(ArrayAdapter $params) {
        $texHash = $params->str('hash');
        $this->params['formula'] = $texHash ? '\[' . TexImager::inst()->decodeTexFromHash($texHash) . '\]' : '';
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        PsDefines::setReplaceFormulesWithImages(false);
        return $this->getFoldedEntity()->fetchTpl($this->params);
    }

    public function getSmartyParams4Resources() {
        $RESOURCES['MATHJAX_DISABLE'] = false;
        $RESOURCES['ATOOL_ENABLE'] = true;
        return $RESOURCES;
    }

    public function getPopupVisibility() {
        return PopupVis::TRUE_DEFAULT;
    }

}

?>
