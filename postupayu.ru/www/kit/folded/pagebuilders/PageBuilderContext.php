<?php

/**
 * Контекст построения страницы. Может быть использован для передачи на страницу определённых параметров.
 */
class PageBuilderContext extends FoldedContext {

    public function tplFetchParamsClass() {
        return PageParams::getClassName();
    }

    /*
     * Параметры javascript
     */

    public function setJsParam($key, $val) {
        $this->setMappedParam(PageParams::PARAM_JS, $key, $val);
    }

    public function setJsParamsGroup($group, $key, $val) {
        $this->setMappedParam2(PageParams::PARAM_JS, $group, $key, $val);
    }

    public function setJsParams($params) {
        if (is_array($params)) {
            $this->setMappedParams(PageParams::PARAM_JS, $params);
        }
    }

    private function getJsParams() {
        return $this->getParam(PageParams::PARAM_JS, array());
    }

    /*
     * Параметры javascript
     */

    public function setSmartyParam4Resources($key, $val) {
        $this->setMappedParam(PageParams::PARAM_RESOURCES, $key, $val);
    }

    public function setSmartyParams4Resources($params) {
        if (is_array($params)) {
            $this->setMappedParams(PageParams::PARAM_RESOURCES, $params);
        }
    }

    private function getSmartyParams4Resources() {
        return $this->getParam(PageParams::PARAM_RESOURCES, array());
    }

    /*
     * Заголовок страницы
     */

    public function setTitle($title) {
        $this->setParam(PageParams::PARAM_TITLE, $title);
    }

    private function getTitle() {
        return $this->getParam(PageParams::PARAM_TITLE);
    }

    /**
     * ФИНАЛИЗАЦИЯ
     */
    public function finalizeTplContent($content) {
        $PARAMS[PageParams::PARAM_JS] = $this->getJsParams();
        $PARAMS[PageParams::PARAM_TITLE] = $this->getTitle();
        $PARAMS[PageParams::PARAM_RESOURCES] = $this->getSmartyParams4Resources();
        $PARAMS[PageParams::PARAM_CONTENT] = $content;
        return $PARAMS;
    }

    /** @return PageBuilderContext */
    public static function getInstance() {
        return parent::inst();
    }

}

?>