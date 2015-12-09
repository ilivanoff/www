<?php

final class Handlers {

    private $postTypes = array();
    private $rubricsProcessors = array();
    private $postsProcessors = array();
    private $commentProcessors = array();
    private $discussionControllers = array();
    private $pagePreloadListeners = array();
    private $foldings = array();
    private $libs = array();
    private $bubbles = array();
    private $panels = array();
    private $folding2unique = array();
    private $folding2smartyPrefix = array();
    private $folding2classPrefix = array();
    private $postProcessorFoldings = array();
    private $newsProviders = array();
    private $pageFinaliseFoldings = array();

    private function __construct() {
        PsProfiler::inst(__CLASS__)->start(__FUNCTION__);

        $managers = array(
            MagManager::inst(),
            BlogManager::inst(),
            TrainManager::inst()
        );

        foreach ($managers as $manager) {
            //Соберём типы постов
            $this->postTypes[] = $manager->getPostType();

            if ($manager instanceof RubricsProcessor) {
                $this->rubricsProcessors[$manager->getPostType()] = $manager;
                $this->foldings[] = $manager->getRubricsFolding();
            }
            if ($manager instanceof PostsProcessor) {
                $this->postsProcessors[$manager->getPostType()] = $manager;
                $this->foldings[] = $manager->getFolding();
            }
            if ($manager instanceof CommentsProcessor) {
                $this->commentProcessors[$manager->getPostType()] = $manager;
                $this->discussionControllers[$manager->getDiscussionController()->getDiscussionUnique()] = $manager->getDiscussionController();
            }
            if ($manager instanceof PagePreloadListener) {
                $this->pagePreloadListeners[] = $manager;
            }
            if ($manager instanceof NewsProvider) {
                $this->newsProviders[$manager->getNewsEventType()] = $manager;
            }
        }

        //Контроллеры дискуссий
        $this->discussionControllers[FeedbackManager::inst()->getDiscussionUnique()] = FeedbackManager::inst();

        //Фолдинги
        $this->foldings[] = PopupPagesManager::inst();
        $this->foldings[] = PluginsManager::inst();
        $this->foldings[] = IdentPagesManager::inst();
        $this->foldings[] = TimeLineManager::inst();
        $this->foldings[] = TemplateMessages::inst();
        $this->foldings[] = UserPointsManager::inst();
        $this->foldings[] = StockManager::inst();
        $this->foldings[] = HelpManager::inst();
        $this->foldings[] = EmailManager::inst();
        $this->foldings[] = PSForm::inst();
        $this->foldings[] = DialogManager::inst();
        //Библиотеки
        $this->foldings[] = PoetsManager::inst();
        $this->foldings[] = ScientistsManager::inst();
        //Админские страницы
        $this->foldings[] = APagesResources::inst();
        //Базовые страницы
        $this->foldings[] = BasicPagesManager::inst();
        //Построитель страниц
        $this->foldings[] = PageBuilder::inst();
        //Управление списком предпросмотра постов
        $this->foldings[] = ShowcasesCtrlManager::inst();
        //Элементы в правой панели навигации
        $this->foldings[] = ClientBoxManager::inst();

        /*
         * Выделим различные подклассы фолдингов
         */
        foreach ($this->foldings as $folding) {
            //Фолдинги библиотек
            if ($folding instanceof LibResources) {
                $this->libs[] = $folding;
            }
            //Фолдинги обработчиков постов
            if ($folding instanceof PostFoldedResources) {
                $this->postProcessorFoldings[] = $folding;
            }
            //Фолдинги для баблов
            if ($folding instanceof BubbledFolding) {
                $this->bubbles[] = $folding;
            }
            //Фолдинги, предоставляющие панели
            if ($folding instanceof PanelFolding) {
                $this->panels[] = $folding;
            }
            //Фолдинги, финализирующие контент страницы
            if ($folding instanceof PageFinalizerFolding) {
                $this->pageFinaliseFoldings[] = $folding;
            }
            //Индексированный список фолдингов
            $this->folding2unique[$folding->getUnique()] = $folding;
            //Префиксы smarty к фолдингам
            $this->folding2smartyPrefix[$folding->getSmartyPrefix()] = $folding;
            //Префиксы классов к фолдингам
            if ($folding->getClassPrefix()) {
                $this->folding2classPrefix[$folding->getClassPrefix()] = $folding;
            }
        }

        PsProfiler::inst(__CLASS__)->stop();
    }

    public function getRubricsProcessors() {
        return $this->rubricsProcessors;
    }

    public function walkRubricsProcessors($callback) {
        $this->walk($this->rubricsProcessors, $callback);
    }

    public function getPostsProcessors() {
        return $this->postsProcessors;
    }

    public function getNewsProviders() {
        return $this->newsProviders;
    }

    public function getPostProcessorFoldings() {
        return $this->postProcessorFoldings;
    }

    public function walkPostsProcessors($callback) {
        $this->walk($this->postsProcessors, $callback);
    }

    public function getCommentProcessors() {
        return $this->commentProcessors;
    }

    public function walkCommentProcessors($callback) {
        $this->walk($this->commentProcessors, $callback);
    }

    public function getPagePreloadListeners() {
        return $this->pagePreloadListeners;
    }

    /*
     * Обход всех классов
     */

    private function walk(array $what, $callback) {
        foreach ($what as $ob) {
            call_user_func($callback, $ob);
        }
    }

    /*
     * Фолдинги
     */

    public function getFoldings() {
        return $this->foldings;
    }

    public function getBubbles() {
        return $this->bubbles;
    }

    public function getPanelProviders() {
        return $this->panels;
    }

    public function getFoldingsIndexed() {
        return $this->folding2unique;
    }

    /** @return FoldedResources */
    public function getFoldingByUnique($unique, $assert = true) {
        $folding = array_get_value($unique, $this->folding2unique);
        check_condition(!$assert || $folding, "Фолдинг [$unique] не существует.");
        return $folding;
    }

    /** @return FoldedResources */
    public function getFolding($type, $subtype = null, $assert = true) {
        return $this->getFoldingByUnique(FoldedResources::unique($type, $subtype), $assert);
    }

    /** @return FoldedResources */
    public function getFoldingByClassPrefix($prefix, $assert = true) {
        $folding = array_get_value($prefix, $this->folding2classPrefix);
        check_condition(!$assert || !!$folding, "Фолдинг с префиксом классов [$prefix] не существует.");
        return $folding;
    }

    /**
     * Метод патыется получить путь к сущности фолдинга по названию класса.
     * Все классы для сущностей фолдинга начинаются на префикс с подчёркиванием,
     * например PL_, на этом и основан способ подключени класса.
     * 
     * Метод должен быть статическим, так как если мы попытаемся получить путь к
     * классу фолидна, создаваемому Handlers, то никогда его не загрузим.
     */
    public static function tryGetFoldedEntityClassPath($className) {
        $prefix = FoldedResources::extractPrefixFromClass($className);
        $folding = $prefix ? self::getInstance()->getFoldingByClassPrefix($prefix, false) : null;
        if ($folding) {
            $ident = FoldedResources::extractIdentFormClass($className);
            return $folding->getClassPath($ident);
        }
        return null;
    }

    /** @return FoldedEntity */
    public function getFoldedEntityByUnique($unique, $assert = true) {
        $parts = explode('-', trim($unique));
        $count = count($parts);
        if ($count < 2) {
            check_condition(!$assert, "Некорректный идентификатор сущности фолдинга: [$unique].");
            return null;
        }

        $type = $parts[0];
        $hasSubType = $this->isFoldingHasSubtype($type, false);
        if ($hasSubType === null) {
            //Фолдинга с таким типом вообще не существует
            check_condition(!$assert, "Сущность фолдинга [$unique] не существует.");
            return null;
        }

        if ($hasSubType && ($count == 2)) {
            check_condition(!$assert, "Некорректный идентификатор сущности фолдинга: [$unique].");
            return null;
        }

        $subtype = $hasSubType ? $parts[1] : null;
        $folding = $this->getFolding($type, $subtype, $assert);

        if (!$folding) {
            return null;
        }

        array_shift($parts);
        if ($hasSubType) {
            array_shift($parts);
        }

        //TODO '-' вынести на константы
        $ident = implode('-', $parts);

        return $folding->getFoldedEntity($ident, $assert);
    }

    /** @return FoldedResources */
    public function getFoldingBySmartyPrefix($smartyPrefix, $assert = true) {
        $folding = array_get_value($smartyPrefix, $this->folding2smartyPrefix);
        check_condition(!$assert || $folding, "Не удалось определить фолдинг для smaty-функции с префиксом [$smartyPrefix]");
        return $folding;
    }

    /**
     * Метод проверяет, имеет ли фолдинг с данным типом - подтип.
     * Например, все фолдинги для постов объединены в один фолдинг с типом post и разными подтипами [is, bp, tr].
     */
    public function isFoldingHasSubtype($type, $errIfNotFound = true) {
        /** @var FoldedResources */
        foreach ($this->foldings as $folding) {
            if ($folding->isItByType($type)) {
                return $folding->hasSubType();
            }
        }
        check_condition($errIfNotFound, "Не удалось найти folding с типом [$type]");
        return null;
    }

    /*
     * Библиотеки
     */

    public function getLibManagers() {
        return $this->libs;
    }

    /** @return LibResources */
    public function getLibManager($libType, $assert = true) {
        return $this->getFolding(LibResources::LIB_FOLDING_TYPE, $libType, $assert);
    }

    /**
     * Метод валидирует и извлекает тип поста
     */
    public function extractPostType($postType, $assert = true) {
        $type = lowertrim($postType);
        if (!$type) {
            check_condition(!$assert, 'Передан пустой тип поста');
            return null; //---
        }
        if (!in_array($type, $this->postTypes)) {
            check_condition(!$assert, "Некорректный тип поста: [$postType]");
            return null; //---
        }
        return $type;
    }

    /**
     * Получение обработчика
     */
    private function getHandlerImpl(array $handlers, $postType, $isEnsure) {
        $postType = $this->extractPostType($postType);
        if (array_key_exists($postType, $handlers)) {
            return $handlers[$postType];
        } else {
            check_condition(!$isEnsure, "Неизвестный тип поста: [$postType]");
        }
        return null;
    }

    /** @return RubricsProcessor */
    public function getRubricsProcessorByPostType($postType, $isEnsure = true) {
        return $this->getHandlerImpl($this->rubricsProcessors, $postType, $isEnsure);
    }

    /** @return PostsProcessor */
    public function getPostsProcessorByPostType($postType, $isEnsure = true) {
        return $this->getHandlerImpl($this->postsProcessors, $postType, $isEnsure);
    }

    /** @return CommentsProcessor */
    public function getCommentsProcessorByPostType($postType, $isEnsure = true) {
        return $this->getHandlerImpl($this->commentProcessors, $postType, $isEnsure);
    }

    /** @return DiscussionController Контроллер дискуссии */
    public function getDiscussionController($unique) {
        check_condition(array_key_exists($unique, $this->discussionControllers), "Неизвестный тип дискуссии: [$unique]");
        return $this->discussionControllers[$unique];
    }

    /** @return DiscussionController Контроллер дискуссии */
    public function getDiscussionControllers() {
        return $this->discussionControllers;
    }

    /** Фолдинги, производящие финализацию страницы */
    public function getPageFinaliseFoldings() {
        return $this->pageFinaliseFoldings;
    }

    /** @return NewsProvider */
    public function getNewsProviderByNewsType($newsType) {
        check_condition(array_key_exists($newsType, $this->newsProviders), "Неизвестный тип новости: [$newsType]");
        return $this->newsProviders[$newsType];
    }

    private static $inst;

    /** @return Handlers */
    public static function getInstance() {
        return self::$inst ? self::$inst : self::$inst = new Handlers();
    }

}

?>
