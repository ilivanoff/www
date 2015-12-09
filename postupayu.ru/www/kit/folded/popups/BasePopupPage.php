<?php

abstract class BasePopupPage extends FoldedClass {

    protected function _construct() {
        //do nothing...
    }

    public abstract function doProcess(ArrayAdapter $params);

    public abstract function getTitle();

    public abstract function getDescr();

    public abstract function getJsParams();

    /**
     * Наша задача - только построить содержимое страницы.
     * Ресурсы буду подключены к ней позднее, в менеджере страниц, нам об этом думать не нужно.
     */
    public abstract function buildContent();

    public abstract function getSmartyParams4Resources();

    //Видимость страницы
    public function getPopupVisibility() {
        //По умолчанию считается, что на страницу нельзя перейти как на плагин
        return PopupVis::FALSE;
    }

    /*
     * Утилитные методы
     */

    protected function getPageUrl(array $params = array()) {
        return PopupPagesManager::inst()->getPageUrl($this, $params);
    }

}

?>