<?php

class PageFinaliserRegExp extends AbstractPageFinalizer {

    //Пути, по которым лежат файлы, которые можно обфусцировать
    private $OBFUSCATABLE;

    protected function doFinalize($html) {
        /*
         * ИНИЦИАЛИЗАЦИЯ
         */
        $this->OBFUSCATABLE[] = DirManager::resources()->relDirPath('folded');
        $this->OBFUSCATABLE[] = DirManager::resources()->relDirPath('scripts/ps');
        //Расширим для предотвращения PREG_BACKTRACK_LIMIT_ERROR
        ini_set('pcre.backtrack_limit', 10 * 1000 * 1000);


        /*
         * НАЧАЛО РЫБОТЫ
         */

        /* Удалим комментарии */
        $pattern = "/<!--(.*?)-->/si";
        $html = preg_replace($pattern, '', $html);

        $resources = '';


        /*
         * JAVASCRIPT
         * <script ...>...</script>
         */

        $linked = array();
        $matches = array();
        $pattern = "/<script ([^>]*)>(.*?)<\/script>/si";
        $cnt = preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);
        $attributes = $matches[1];
        $contents = $matches[2];

        for ($index = 0; $index < $cnt; $index++) {
            $attrs = $this->parseAttributesString($attributes[$index]);
            $content = trim($contents[$index]);

            if ($content) {
                //Есть содержимое, этот тег включаем сразу
                $tmp = PsHtml::linkJs(null, $content, $attrs);
                $resources .= $tmp . "\n";
                $this->LOGGER->info($tmp);
                continue;
            }

            $src = array_get_value('src', $attrs);
            if (!$src || in_array($src, $linked)) {
                //Нет пути или путь уже включён - пропускаем
                continue;
            }
            $linked[] = $src;

            $newSrc = $this->tryReplaceResource($src);

            if (!$newSrc) {
                if ($this->LOGGER->isEnabled()) {
                    $tmp = PsHtml::linkJs(null, null, $attrs);
                    $this->LOGGER->info($tmp . '  [EXCLUDED]');
                }
                continue;
            }

            $replaced = $src != $newSrc;

            if ($replaced) {
                $linked[] = $newSrc;
                $attrs['src'] = $newSrc;
            }

            $tmp = PsHtml::linkJs(null, null, $attrs);
            $resources .= $tmp . "\n";

            if ($this->LOGGER->isEnabled()) {
                $attrs['src'] = $src;
                $this->LOGGER->info(($replaced ? PsHtml::linkJs(null, null, $attrs) . '  [REPLACED]  ' : '') . $tmp);
            }
        }
        $html = preg_replace($pattern, '', $html);


        /*
         * FAVICON, CSS
         * <link .../>
         */

        /* Вырежем css и другие "линки" */
        $matches = array();
        $pattern = "/<link ([^>]*)\/>/si";
        $cnt = preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);
        $attributes = $matches[1];
        for ($index = 0; $index < $cnt; $index++) {
            $attrs = $this->parseAttributesString($attributes[$index]);

            $src = array_get_value('href', $attrs);
            if (!$src || in_array($src, $linked)) {
                //Нет пути или путь уже включён - пропускаем
                continue;
            }
            $linked[] = $src;

            $newSrc = $this->tryReplaceResource($src);

            if (!$newSrc) {
                if ($this->LOGGER->isEnabled()) {
                    $tmp = PsHtml::linkLink($attrs);
                    $this->LOGGER->info($tmp . '  [EXCLUDED]');
                }
                continue;
            }

            $replaced = $src != $newSrc;

            if ($replaced) {
                $linked[] = $newSrc;
                $attrs['href'] = $newSrc;
            }

            $tmp = PsHtml::linkLink($attrs);
            $resources .= $tmp . "\n";

            if ($this->LOGGER->isEnabled()) {
                $attrs['href'] = $src;
                $this->LOGGER->info(($replaced ? PsHtml::linkLink($attrs) . '  [REPLACED]  ' : '') . $tmp);
            }
        }
        $html = preg_replace($pattern, '', $html);


        $resources = "\n" . trim($resources) . "\n";

        /*
         * Удалим пробелы
         */
        $matches = array();
        $cnt = preg_match("/<head>(.*?)<\/head>/si", $html, $matches);
        if ($cnt == 1) {
            /**
             * $headOld - ресурсы страницы, находящиеся в блоке <head ...>...</head> и 
             * оставшиеся после вырезания .js и .css. Обычно там остаётся два блока:
             * <meta...>...</meta> и <title>...</title>
             */
            $headOld = $matches[1];
            $headNew = normalize_string($headOld);
            $headNew = "$headNew $resources";
            $html = str_replace_first($headOld, $headNew, $html);
        } else {
            //Вставляем ресурсы в <head>
            $html = str_replace_first('</head>', $resources . '</head>', $html);
        }


        $this->LOGGER->infoBox('PAGE FINALISED', $html);

        return $html;
    }

    /**
     * На вход: type="text/javascript" src="/a/b/c.js"
     * На выход: array(type=>text/javascript, src=>/a/b/c.js)
     * Навеяно: http://programming-tut.blogspot.ru/2010/12/php-store-image-tag-attributes-into.html
     */
    private function parseAttributesString($attributes) {
        preg_match_all('/([a-z][[a-z0-9]*]?)=["|\'](.*?)["|\']/is', $attributes, $pairs);
        return array_combine($pairs[1], $pairs[2]);
    }

    /**
     * Метод получает на вход url ресурса и пытается его обфусцировать, если это возможно.
     *      
     * @return: путь к ресурсу, который должнет быть подключен, или null, если ресурс подключать не нужно
     */
    private function tryReplaceResource($url) {
        if (!starts_with($url, DIR_SEPARATOR)) {
            //Это внешний ресурс: http:// и т.д. - подключаем.
            return $url;
        }

        $di = DirItem::inst($url);

        if (!$di->isFile() || $di->getSize() <= 0) {
            return null;
        }

        if (!PsDefines::isProduction()) {
            //Не будем пытаться обфусцировать в девелопменте
            //return $url;
        }

        if (!ends_with($url, '.js')) {
            //Мы не можем обфусцировать .css файлы, так как они используют относительные пути к ресурсам.
            return $url;
        }
        return $url;

        $url = $di->getRelPath();

        if (!starts_with($url, $this->OBFUSCATABLE)) {
            //Не стоит обфусцировать не наши .js файлы, так как они могут внутри использовать ссылки на рядом лежащие ресурсы
            return $url;
        }

        $type = 'js';

        $obfuscatedDi = DirManager::autogen("resources/$type")->getDirItem(null, md5($url), $type);
        $mtyme = $obfuscatedDi->getModificationTime();
        if (!$mtyme || ($mtyme < $di->getModificationTime())) {
            $obfuscatedDi->writeToFile(StringUtils::normalizeResourceFile($type, $di->getFileContents()), true);
        }
        return $obfuscatedDi->getRelPath();
    }

}

?>