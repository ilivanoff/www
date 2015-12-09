<?php

class PP_plugin extends BasePopupPage {

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /** @var BasePlugin */
    private $plugin;
    private $params;

    public function doProcess(ArrayAdapter $params) {
        $this->plugin = PluginsManager::inst()->getPlugin($params->str(GET_PARAM_PLUGIN_IDENT));
        $this->params = $params;
    }

    public function getTitle() {
        return $this->plugin->getName();
    }

    public function getDescr() {
        return 'Отображение плагина в сплывающем окне.';
    }

    public function getJsParams() {
        
    }

    public function buildContent() {
        return PluginsManager::inst()->buildAsPopup($this->plugin->getIdent(), $this->params);
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>