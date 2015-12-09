<?php

/**
 * Элемент панели управления предпросмотров постов. Базовый класс для фолдингов - плагинов.
 *
 * @author azazello
 */
abstract class ShowcasesControllerItem extends FoldedClass {

    protected function _construct() {
        //do nothing...
    }

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    /** @var ShowcasesControllerCtxt */
    protected $ctxt;

    public final function doProcess(ShowcasesControllerCtxt $ctxt) {
        $this->ctxt = $ctxt;
        $this->init();
    }

    /**
     * Инициализация плагина - самый первый вызываемый метод
     */
    public abstract function init();

    /**
     * Название плагина - то самое, которое будет отображено с помощью hint
     */
    public abstract function getName();

    /**
     * Параметры javascript, которые будут доступны в .js обработчике.
     * Должен вернуться массив.
     */
    public abstract function getJsParams();

    /**
     * Ресурсы, передаваемые в page_resources и позволяющие подключить нужные скрипты
     */
    public abstract function getSmartyParams4Resources();

    /**
     * Поскольку плагин всегда должен иметь свой <div>, мы позволим только передать 
     * массив SmartyParams в фаблон, фетчинг которого производится.
     */
    protected abstract function tplSmartyParams();

    /**
     * Плагины для встраивания на страницу - array('calendar', 'Календарь').
     */
    public abstract function getPlugins();

    /**
     * Метод получает фактический контект для плагина
     */
    public final function getContent() {
        return $this->getFoldedEntity()->fetchTplWithResources(to_array($this->tplSmartyParams()));
    }

}

?>