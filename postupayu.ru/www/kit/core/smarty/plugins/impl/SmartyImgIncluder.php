<?php

class SmartyImgIncluder extends AbstractSmartyPlugin {

    private function insert($FNAME, ArrayAdapter $params, $content) {
        $IMG['data'] = $params->has('data') ? $params->get('data') : $params->getByKeyPrefix('data_', true);
        $IMG['class'] = $params->get(array('cl', 'class'));
        $IMG['style'] = $params->get(array('st', 'style'));
        $IMG['title'] = $params->get('title');
        /*
         * Если alt задан, но пуст - таким его и оставим. 
         * Скорее всего нам такое поведение и нужно. Например в галлереях alt используется для подсказки.
         * Нам не нужно в качестве подсказки показывать название картинки.
         */
        if ($params->has('alt')) {
            $IMG['alt'] = $params->get('alt');
        }
        if ($params->hasNoEmpty('map')) {
            $IMG['usemap'] = '#' . $params->get('map');
        }

        $isPopup = ends_with($FNAME, 'imgp');

        //Массив фактических картинок
        $IMAGES = array();
        if ($content && !$isPopup) {
            //Есть содержимое
        } else {
            $IMAGES = to_array($this->getImages($params, $FNAME));
            check_condition($IMAGES, "В блок [$FNAME] не переданы картинки для отображения.");
        }

        if ($isPopup) {
            //Всплывающая картинка
            $popupImgSrc = $IMAGES[0] instanceof DirItem ? $IMAGES[0]->getRelPath() : $IMAGES[0];
            if ($content == '.') {
                echo PsBubble::previewImgBubble($popupImgSrc);
            } else {
                echo PsBubble::spanImgBubble($content, $popupImgSrc);
            }
            return; //---
        }


        //Параметры
        $id = $params->str(array('id', 'num'));
        $text = $params->str('text');

        $isBlock = $content || count($IMAGES) > 1 || $id || $text || ends_with($FNAME, array('imgb', 'imgn'));

        if (!$isBlock) {
            $IMG['src'] = $IMAGES[0];
            echo PsHtml::img($IMG);
            return; //---
        }

        //Блочная картинка
        //Определим номер картинки. Если это imgn, то нумерация форсированно отключена.
        $inc = ends_with($FNAME, 'imgn') ? null : FoldedContextWatcher::getInstance()->getImageNumeratorContext(false);

        //Определим текст подписи под картинкой.
        $num = $inc ? ' ' . PsConst::NUM_REPLCASE_MACROS : '';
        $text = trim($num || $text ? "<b>Рис.$num</b> $text" : '');
        $text = $text ? "<p>$text</p>" : '';

        //Строим див с картинками
        $DIV['class'] = array('block_img');

        if (!$content) {
            foreach ($IMAGES as $src) {
                $IMG['src'] = $src;
                $content .= PsHtml::img($IMG);
            }
        }
        $content .= $text;

        echo $inc ? $inc->wrapBlockImgBox($id, $DIV, $content) : PsHtml::div($DIV, $content);
    }

    private function getImages(ArrayAdapter $params, $FNAME) {
        $dir = $params->str('dir');
        $name = $params->get('name');

        //НЕПОСРЕДСТВЕННО КАРТИНКА
        if ($name instanceof DirItem) {
            return $name;
        }

        /*
         * FOLDING
         */
        $FCW = FoldedContextWatcher::getInstance();

        $folding = null;

        $ident = $params->get('ident');

        //Тип фолдинга. Он может быть передан либо в параметре 'group', либо по префиксу смарти-функции, например: postimg (post-название типа фолдинга).
        $foldingType = $params->get('group');
        $foldingType = $foldingType ? $foldingType : (starts_with($FNAME, 'img') ? null : array_get_value(0, explode('img', $FNAME)));

        //Подтип фолдинга, например: is - подтип фолдингов для фолдингов с типом post (выпуск журнала среди всех постов).
        $foldingSubType = $params->get('type');

        if ($params->has('post')) {
            /* @var $post AbstractPost */
            $post = $params->get('post');
            $ident = $post->getIdent();
            $folding = Handlers::getInstance()->getPostsProcessorByPostType($post->getPostType())->getFolding();
        } else if ($foldingType) {
            $hasSubtype = Handlers::getInstance()->isFoldingHasSubtype($foldingType);
            if ($hasSubtype && !$foldingSubType) {
                //У фолдинга есть подтип, но в параметрах он не передан - определим фолдинг по контексту
                $folding = $FCW->getFoldedEntityEnsureType($foldingType)->getFolding();
            } else {
                $folding = Handlers::getInstance()->getFolding($foldingType, $foldingSubType);
            }
        } else if ($foldingSubType) {
            //Если передан только тип, то считаем, что имеется ввиду фолдинг поста
            $folding = Handlers::getInstance()->getPostsProcessorByPostType($foldingSubType)->getFolding();
        }

        if ($folding && !$ident) {
            //У нас есть фолдинг, но нет идентификатора сущности - определим её из контекста
            $ident = $FCW->getFoldedEntityEnsureType($folding->getFoldingType())->getIdent();
        }

        if (!$dir && !$name && $ident && $folding) {
            /*
             * Не передано название картинки, но передан идентификатор сущности - показываем cover.
             * TODO - подумать, возможно имеет смысл сделать возможность показывать любую картинку в заданном размере.
             */
            return $folding->getCover($ident, $params->str('dim'));
        }

        /*
         * Берём путь "как есть", если: 
         * 1. Передан специальный параметр asis
         * 2. Передана dir, и она начинается с '/'
         * 3. Не передана dir, но при этом name начинается с '/'
         * 4. dir или name указывают на адрес в интернете
         */
        $asis = $params->bool('asis') || starts_with($dir, DIR_SEPARATOR) || (!$dir && starts_with($name, DIR_SEPARATOR)) || PsUrl::isHttp($dir) || PsUrl::isHttp($name);

        if ($asis) {
            if (!$dir) {
                return $name;
            }
            if (starts_with($name, DIR_SEPARATOR)) {
                return cut_string_end($dir, DIR_SEPARATOR) . $name;
            }
            return ($name ? ensure_ends_with($dir, DIR_SEPARATOR) : $dir) . $name;
        }

        /** @var DirManager */
        $DM = null;
        /*
         * Теперь определим DirManager. Мы его можем взять:
         */
        if ($folding) {
            //1. Из ресурсов фолдинга
            $DM = $folding->getResourcesDm($ident, 'src');
        } else {
            //2. Обычный resources->images, если фолдинг не установлен
            $DM = DirManager::images();
        }

        /*
         * Определим список показываемых картинок по атрибуту $name. Пример тега:
         * {postimg type='tr' ident='matrix' name='mao.gif mu.png mu.png'}
         * Просто разделим значение атрибута $name по точкам и пробелам и склеим в названия картинок.
         */

        $NAMES = preg_split("/[. ]/", $name);
        $DI = $DM ? $DM->getDirItem($dir, $name) : DirItem::inst($dir, $name);
        if ((count($NAMES) % 2) != 0 || $DI->isImg()) {
            //Указано что-то непонятное - не чётное кол-во составных элементов
            return $DI;
        }

        $IMAGES = array();
        for ($i = 0; $i < count($NAMES); $i+=2) {
            $imgName = $NAMES[$i] . '.' . $NAMES[$i + 1];
            $IMAGES[] = $DM ? $DM->getDirItem($dir, $imgName) : DirItem::inst($dir, $imgName);
        }
        return $IMAGES;
    }

    /*
     * @override
     */

    protected function do_block($tagName, $params, $content, Smarty_Internal_Template $smarty) {
        if ($content) {
            $this->insert($tagName, ArrayAdapter::inst($params), $content);
        }
    }

    protected function do_function($tagName, $params, Smarty_Internal_Template $smarty) {
        $this->insert($tagName, ArrayAdapter::inst($params), null);
    }

    private function registerPluginImpl(array &$result, $type) {
        $result[$type . 'img'] = Smarty::PLUGIN_FUNCTION;  //Обычная картинка (может быть блочной, если есть текст или id)
        $result[$type . 'imgb'] = Smarty::PLUGIN_FUNCTION; //Блочная картинка 
        $result[$type . 'imgn'] = Smarty::PLUGIN_FUNCTION; //Блочная картинка без нумерации
        $result[$type . 'imgc'] = Smarty::PLUGIN_BLOCK; //Картинка на содержимым
        $result[$type . 'imgp'] = Smarty::PLUGIN_BLOCK; //Всплывающая картинка (popup img)
    }

    protected function getPlugins() {
        $result = array();
        $foldings = Handlers::getInstance()->getFoldings();
        /* @var $folding FoldedResources */
        foreach ($foldings as $folding) {
            $this->registerPluginImpl($result, $folding->getFoldingType());
        }
        $this->registerPluginImpl($result, '');
        return $result;
    }

}

?>