<?php

/**
 * Панель для встраивания на странице
 *
 * @author azazello
 */
interface PluggablePanel {

    public function getHtml();

    public function getJsParams();

    public function getSmartyParams4Resources();
}

?>