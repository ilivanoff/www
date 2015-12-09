<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.header_links.php
 * Type:     function
 * Name:     header_links
 * Purpose:  выводит картинки заголовка
 * -------------------------------------------------------------
 */

function basePageHref($basePageCode, $image) {
    $basePage = WebPage::inst($basePageCode);
    $sprites = CssSpritesManager::getDirSprite(CssSpritesManager::DIR_HEADER, $image, true);
    $href = $basePage->getHref($sprites . $basePage->getName());

    echo PsHtml::html2('li', array('class' => WebPages::getCurPage()->isMyBasePage($basePage) ? 'current' : null), $href);
}

function smarty_function_header_links($params, $smarty) {
    basePageHref(BASE_PAGE_INDEX, 'about');
    basePageHref(BASE_PAGE_MAGAZINE, 'magazine');
    basePageHref(BASE_PAGE_BLOG, 'blog');
    basePageHref(BASE_PAGE_TRAININGS, 'training');
    basePageHref(BASE_PAGE_FEEDBACK, 'feedback');
}

?>