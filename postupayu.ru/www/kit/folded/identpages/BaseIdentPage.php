<?php

/**
 * Базовый элемент для всех сущностей фолдинга этого типа
 */
abstract class BaseIdentPage extends FoldedClass {

    protected function _construct() {
        //do nothing...
    }

    public abstract function getTitle();

    /** @return IdentPageFilling or IdentPageContent */
    protected abstract function processRequest(ArrayAdapter $params);

    /**
     * Метод возвращает ссылку, открывающую загружаемую страницу.
     * Для работы загружаемой страницы нужны ресурсы фолдинга этой страницы - они также будут добавлены к ссылке.
     * 
     * <a title="Новости" class="ip-opener" href="#news"><img alt="news.png" src="/autogen/images/16x16/folding/idents/news.png"></a>
     * 
     * @param BaseIdentPage or String $item
     * @param array $params - параметры ссылки
     * @param type $content - содержимое ссылки. Если нет, то будет подставлена картинка.
     */
    public final function getIdentPageHref(array $params = array(), $content = null) {
        $this->checkAccess();

        $params['class'][] = 'ip-opener';
        $params['class'][] = $this->foldedEntity->getUnique();
        $params['title'] = $this->getTitle();
        $params['href'] = "#" . $this->foldedEntity->getIdent();

        //Если не передано содержимое, то добавим картинку
        $content = $content ? $content : PsHtml::img(array('src' => $this->foldedEntity->getCover()));
        //Добавим все необходимые ресурсы
        $content = $this->foldedEntity->getResourcesLinks($content);

        return PsHtml::a($params, $content);
    }

    /**
     * Метод получает фактический контект для всплывающей страницы.
     * Сама страница может вернуть или IdentPageFilling, и тогда содержимое 
     * будет обработано за неё. Или непосредственно IdentPageContent,
     * если ей самой нужно обработать содержимое (например - акции).
     * 
     * @return IdentPageContent
     */
    public final function getContent(ArrayAdapter $params) {
        $this->checkAccess();

        $this->profilerStart(__FUNCTION__);

        $fillingOrContent = $this->processRequest($params);

        /** @var IdentPageContent */
        $content = null;

        if (is_object($fillingOrContent)) {
            if ($fillingOrContent instanceof IdentPageFilling) {
                $div = $this->getFoldedEntity()->fetchTpl(to_array($fillingOrContent->getSmartyParams()));
                $content = new IdentPageContent($div, $fillingOrContent->getJsParams());
            }

            if ($fillingOrContent instanceof IdentPageContent) {
                $content = $fillingOrContent;
            }
        }

        $this->profilerStop();

        check_condition(is_object($content) &&
                ($content instanceof IdentPageContent), "Страница [$this] обработана некорректно");

        return $content;
    }

}

?>