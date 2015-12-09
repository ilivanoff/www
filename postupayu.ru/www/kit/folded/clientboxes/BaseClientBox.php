<?php

/**
 * Базовый элемент для всех сущностей фолдинга этого типа
 */
abstract class BaseClientBox extends FoldedClass {

    protected function _construct() {
        //do nothing...
    }

    /**
     * Основной метод, занимающийся построением содержимого
     * 
     * @return ClientBoxFilling
     */
    protected abstract function getClientBoxFilling();

    private $cbContent = null;

    /**
     * Метод получает фактический контект для всплывающей страницы.
     * Сама страница может вернуть или IdentPageFilling, и тогда содержимое 
     * будет обработано за неё. Или непосредственно IdentPageContent,
     * если ей самой нужно обработать содержимое (например - акции).
     * 
     * @return ClientBoxContent
     */
    public final function getContent() {
        if ($this->cbContent) {
            return $this->cbContent;
        }

        $this->checkAccess();

        $this->profilerStart(__FUNCTION__);

        $filling = null;
        try {
            $filling = $this->getClientBoxFilling();
            check_condition($filling instanceof ClientBoxFilling, "Элемент [{$this->ident}] обработан некорректно");
        } catch (Exception $ex) {
            $this->profilerStop(false);
            return $this->cbContent = new ClientBoxContent(PsHtml::divErr(ExceptionHandler::getHtml($ex)));
        }

        //Построим заголовок
        $HEAD_PARAMS['class'][] = 'box-header';
        if ($filling->isCover()) {
            $HEAD_PARAMS['class'][] = 'covered';
            $HEAD_PARAMS['style']['background-image'] = 'url(' . $this->foldedEntity->getCover()->getRelPath() . ')';
        }
        $HEAD_CONTENT = $filling->getHref() ? PsHtml::a(array('href' => $filling->getHref()), $filling->getTitle()) : $filling->getTitle();
        $HEAD = PsHtml::html2('h3', $HEAD_PARAMS, $HEAD_CONTENT);

        $BOX_CONTENT = $this->foldedEntity->fetchTplWithResources($filling->getSmartyParams());

        $BOX = PsHtml::div(array(), $HEAD . $BOX_CONTENT);

        $this->profilerStop();

        return $this->cbContent = new ClientBoxContent($BOX, $filling->getJsParams());
    }

}

?>