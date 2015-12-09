<?php

class PB_test extends AbstractPageBuilder {

    public static function registerWebPages() {
        WebPages::register('test.php', 'Тестовая страница', PAGE_TEST, self::getIdent(), AuthManager::AUTH_TYPE_NO_MATTER, null, null, false);
    }

    protected function doProcess(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        PsDefines::assertProductionOff(__CLASS__);
        PsUtil::startUnlimitedMode();
    }

    protected function doBuild(PageContext $ctxt, PageBuilderContext $builderCtxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        //1. ЗАГОЛОВОК
        $builderCtxt->setTitle('Тестовая страница');


        //2. NO JAVASCRIPT
        //3. SMARTY RESOURCES
        $builderCtxt->setSmartyParam4Resources('MATHJAX_DISABLE', true);
        $builderCtxt->setSmartyParam4Resources('TIMELINE_ENABE', true);


        //4. GET SMARTY PARAMS FOR TPL
        //Подсчитаем кол-во тестовых страниц по кол-ву шаблонов
        $SM = PSSmarty::smarty();
        $cnt = 0;
        do {
            ++$cnt;
        } while ($SM->templateExists("test/page$cnt.tpl"));

        $content = $this->getContentImpl($requestParams, $SM);
        $galls = DirManager::mmedia()->getDirContent('gallery', DirItemFilter::DIRS);

        $smartyParams['cnt'] = $cnt;
        $smartyParams['galls'] = $galls;
        $smartyParams['content'] = $content;
        $smartyParams['processors'] = Handlers::getInstance()->getPostsProcessors();

        return $smartyParams;
    }

    /**
     * Основной метод, выполняющий загрузку содержимого тестовой страницы
     */
    private function getContentImpl(RequestArrayAdapter $params, Smarty $smarty) {
        //Силовые упражнения
        $exId = $params->int('ex_id');
        if ($exId) {
            //$ex = GymManager::getInstance()->getExercise($exId);
            $tplPath = "gym/exercises/$exId.tpl";
            return $smarty->templateExists($tplPath) ? $smarty->fetch($tplPath) : null;
        }

        //Специальные страницы
        $pageType = $params->str('pagetype');
        if ($pageType) {
            $smParams = array();
            switch ($pageType) {
                case 'smarty':

                    foreach (array('blocks', 'functions', 'modifiers') as $type) {

                        $items = DirManager::smarty('plugins/' . $type)->getDirContentFull(null, PsConst::EXT_PHP);
                        /* @var $item DirItem */
                        foreach ($items as $item) {
                            //Название
                            $name = explode('.', $item->getName());
                            $name = $name[1];

                            //Первый комментарий
                            $tokens = token_get_all($item->getFileContents());
                            $comment = array(
                                T_COMMENT, // All comments since PHP5
                                T_DOC_COMMENT   // PHPDoc comments      
                            );
                            $fileComment = '';
                            foreach ($tokens as $token) {
                                if (in_array($token[0], $comment)) {
                                    $fileComment = trim($token[1]);
                                    break;
                                }
                            }
                            $smParams[$type][] = array(
                                'name' => $name,
                                'comment' => $fileComment
                            );
                        }
                    }
                    break;

                case 'doubleimg':
                    $images = DirManager::images()->getDirContentFull(null, DirItemFilter::IMAGES);

                    $sorted = array();
                    /* @var $img DirItem */
                    foreach ($images as $img) {
                        $ident = $img->getSize() . 'x' . $img->getImageAdapter()->getWidth() . 'x' . $img->getImageAdapter()->getHeight();
                        $sorted[$ident][] = $img;
                    }

                    $result = array();
                    /* @var $img DirItem */
                    foreach ($sorted as $ident => $imgs) {
                        if (count($imgs) > 1) {
                            $result[$ident] = $imgs;
                        }
                    }
                    $smParams = array(
                        'images' => $result
                    );
                    break;

                case 'testmethods':
                    $smParams['methods'] = TestManagerCaller::getMethodsList();
                    break;

                case 'imgbysize':
                    $images = DirManager::images()->getDirContentFull(null, DirItemFilter::IMAGES, array('GymExercises'));
                    DirItemSorter::inst()->sort($images, DirItemSorter::BY_SIZE);
                    $smParams = array(
                        'images' => $images
                    );
                    break;

                case 'formules':
                    $formules = TexImager::inst()->getAllFormules();

                    $totalSize = 0;
                    /* @var $formula DirItem */
                    foreach ($formules as $formula) {
                        $totalSize += $formula->getSize();
                        $formula->setData('class', 'TeX');
                    }

                    DirItemSorter::inst()->sort($formules, DirItemSorter::BY_SIZE);
                    $smParams = array(
                        'formules' => $formules,
                        'formules_size' => $totalSize
                    );
                    break;
            }

            $content = $smarty->fetch("test/page_{$pageType}.tpl", $smParams);

            if ($pageType) {
                switch ($pageType) {
                    case 'patterns':
                        $out = array();
                        preg_match_all("/===(.*?)===/", $content, $out, PREG_PATTERN_ORDER);

                        $params = array();
                        for ($i = 0; $i < count($out[0]); $i++) {
                            $full = $out[0][$i];
                            $ctt = $out[1][$i];
                            $params[$full] = "<div class=\"demo-head\">$ctt</div>";
                        }

                        $content = PsStrings::replaceMap($content, $params);
                }
            }

            return $content;
        }

        $post_type = Handlers::getInstance()->extractPostType($params->str(GET_PARAM_TYPE), false);
        $post_id = $params->int(GET_PARAM_POST_ID);

        if ($post_type && $post_id) {
            return Handlers::getInstance()->getPostsProcessorByPostType($post_type)->getPostContentProvider($post_id)->getPostContent(false)->getContent();
        }

        //Тестовая страница
        $num = $params->int('num');
        $num = $num ? $num : 1;

        return $smarty->fetch("test/page$num.tpl");
    }

    public function getProfiler() {
        return null;
    }

}

?>