<?php

class PageFinaliserSimpleDom extends AbstractPageFinalizer {

    /**
     * Метод, вызываемый извне.
     * Главная его задача - проследить за освобождением ресурсов, 
     * т.к. создаются тяжёлые объекты.
     */
    protected function doFinalize($pattern) {
        ExternalPluginsManager::SimpleHtmlDom();
        /* http://simplehtmldom.sourceforge.net/ */
        $html = str_get_html($pattern);
        try {
            $this->finaliseImpl($html);
        } catch (Exception $e) {
            if ($html) {
                $html->clear();
                unset($html);
            }
            throw $e;
        }

        if ($html) {
            $pattern = $html->save();
            $html->clear();
            unset($html);
        }

        return $pattern;
    }

    private function finaliseImpl(simple_html_dom &$html) {
        // Удаляем комментарии
        foreach ($html->find('comment') as $e) {
            $e->outertext = '';
        }

        $head = $html->find('head', 0);
        // Если нет тега <head> - пропускаем обработку
        if ($head) {
            $tmp;
            $resources = '';
            $linked = array();
            //$jsToObfuscate = array();

            foreach ($html->find('link, script') as $e) {
                switch ($e->tag) {
                    case 'link':
                        // <link ...>
                        $tmp = $e->outertext;
                        if (!array_key_exists($tmp, $linked)) {
                            $linked[$tmp] = true;
                            $resources .= $tmp;
                            $this->LOGGER->info($tmp);
                        }
                        break;
                    case 'script':
                        // <script ...></script>
                        $tmp = PsHtml::linkJs(null, $e->innertext, $e->attr);
                        if (!array_key_exists($tmp, $linked)) {
                            $linked[$tmp] = true;
                            $this->LOGGER->info($tmp);
                        }
                        break;
                }
                $e->outertext = '';
            }

            $head->innertext .= $resources;
        }
    }

}

?>