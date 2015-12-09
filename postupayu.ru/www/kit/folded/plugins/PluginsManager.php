<?php

/**
 * Менеджер плагинов, добавляемых на страницу.
 * Плагином является элемент, умеющий сам по себе выполнять какие-либо действия и 
 * который может быть использован на странице несколько раз.
 * 
 * Например - приложение для определения слов, буквы в которых перемешаны.
 * 
 * Ресурсы (css, js) всегда подключаются на страницу один раз.
 * Содержимое плагина может вставляться на страницу многократно.
 */
final class PluginsManager extends PluginResources {

    const PLUGIN_TESTING = 'testing';

    /** @return BasePlugin */
    public function getPlugin($ident) {
        return $this->getEntityClassInst($ident);
    }

    /*
     * БИЛДЕРЫ
     */

    /**
     * Метод строит плагин при вызове Smarty-функции. Пример:
     * {psplugin name='$ident' param1='value1'}$content{/psplugin}
     */
    public function buildFromTag($ident, $content, ArrayAdapter $params) {
        $plugin = $this->getPlugin($ident);

        $return = '';
        if (!$content) {
            /*
             * Установим контекст до выполнения блока, заключённого в psplugin,
             * чтобы, например, вести подсчёт задач или других сущностей.
             */
            PluginFetchingContext::getInstance()->setContext($plugin->getIdent());
        } else {
            $return = $this->buildImpl($plugin, $content, $params);
            PluginFetchingContext::getInstance()->dropContext();
            FoldedContextWatcher::getInstance()->setDependsOnEntity($this->getFoldedEntity($ident));
        }
        return $return;
    }

    /**
     * Метод строит плагин для отображения в popup окне
     */
    public function buildAsPopup($ident, ArrayAdapter $params) {
        $plugin = $this->getPlugin($ident);
        PluginFetchingContext::getInstance()->setContext($plugin->getIdent());
        $return = $this->buildImpl($plugin, null, $params);
        PluginFetchingContext::getInstance()->dropContext();
        FoldedContextWatcher::getInstance()->setDependsOnEntity($this->getFoldedEntity($ident));
        return $return;
    }

    /**
     * Основной метод, выполняющий всю работу.
     * К этому моменту мы уже определили плагин и установили контекст.
     * Остаётся только построить сам плагин.
     */
    private function buildImpl(BasePlugin $plugin, $content, ArrayAdapter $params) {
        //Если $content === null, то мы отображаем плагин в popup окне
        //В противном случае запросим УРЛ для перехода к popup-виду плагина
        $popupUrl = $content === null ? null : PopupPagesManager::inst()->getPluginUrl($plugin);

        $ident = $plugin->getIdent();
        $content = trim($content);

        try {
            $pluginContent = $plugin->getPluginContent($content, $params, PluginFetchingContext::getInstance());

            if (PostFetchingContext::getInstance()->isSetted()) {
                PostFetchingContext::getInstance()->registerPlugin($ident, $pluginContent->getPostData());
            }

            $tpl = PSSmarty::template('psplugins/BASE.tpl');
            $tpl->assign('url', $popupUrl);
            $tpl->assign('ident', $ident);
            $tpl->assign('content', $this->getResourcesLinks($ident, $pluginContent->getContent()));

            return $tpl->fetch();
        } catch (Exception $e) {
            //Произошла ошибка... От нас требуется вернуть её текстовое представление,
            //так как самое важное - отключить контекст выполнения плагина.
            return ExceptionHandler::getHtml($e);
        }
    }

    public function getFoldedEntityPreview($ident) {
        $plugin = $this->getPlugin($ident);
        PluginFetchingContext::getInstance()->setContext($plugin->getIdent());
        $return = $this->buildImpl($plugin, 'token1 token2 token3', ArrayAdapter::inst());
        PluginFetchingContext::getInstance()->dropContext();

        return array(
            'info' => $plugin->getName(),
            'content' => $return
        );
    }

    protected function getFoldedContext() {
        return PluginFetchingContext::getInstance();
    }

    /** @return PluginsManager */
    public static function inst() {
        return parent::inst();
    }

}

?>