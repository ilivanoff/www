<?php

/**
 * Панель справа с элементами управления и навигации
 *
 * @author azazello
 */
class ClientBoxPanel implements PluggablePanel {

    private $boxes;

    public function __construct($boxes) {
        $this->boxes = $boxes;
    }

    public function getHtml() {
        $contents = array();
        /* @var $box BaseClientBox */
        foreach ($this->boxes as $box) {
            $contents[] = $box->getContent()->getDiv();
        }
        return implode('', $contents);
    }

    public function getJsParams() {
        $jsParams = array();
        /* @var $box BaseClientBox */
        foreach ($this->boxes as $ident => $box) {
            $params = $box->getContent()->getJsParams();
            if (!empty($params)) {
                $jsParams[$ident] = $box->getContent()->getJsParams();
            }
        }
        return $jsParams;
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>
