<?php

/**
 * Хранилище всех WebPage, которые только есть в системе.
 * Перед началом работы мы пробегаемся по всем "построителям страниц" и просим их зарегистрировать
 * страницы, которые они могут строить, в хранилище.
 */
class WebPages {

    private static $CURPAGE;
    private static $CURPAGE_SETTED = false;

    /**
     * Метод загружает текущую страницу.
     * При этом он имеет ввиду, что страница может и не быть зарегистрирована.
     * Все методы, вне методов WebPages, должны работать в предположении, что текущая 
     * страница существует и установлена. Данный метод нужен только для нужд этого класса.
     * 
     * @return WebPage - текущая страница или null
     */
    private static function loadCurPage($ensure) {
        if (!self::$CURPAGE_SETTED) {
            self::$CURPAGE_SETTED = true;
            self::$CURPAGE = self::getPage(ServerArrayAdapter::PHP_SELF(), false);
        }
        check_condition(!$ensure || (self::$CURPAGE instanceof WebPage), 'Текущая страница не установлена');
        return self::$CURPAGE;
    }

    /**
     * Возвращает текущую страницу.
     * 
     * @return WebPage
     */
    public static function getCurPage() {
        return self::loadCurPage(true);
    }

    /**
     * Проверяет, является ли переданная страница - текущей
     * 
     * @param type $page
     */
    public static function isCurPage($page) {
        $curPage = self::loadCurPage(false);
        if (!($curPage instanceof WebPage)) {
            //Текущей вообще нет
            return false;
        }
        foreach (to_array($page) as $page) {
            if ($curPage->isIt($page)) {
                return true;
            }
        }
        return false;
    }

    public static function reloadCurPage() {
        self::getCurPage()->redirectHere();
    }

    /**
     * Метод определяет и строит текущую Web страницу.
     * Если у пользователя нет к ней доступа, то он будет перенаправлен.
     */
    public static function buildCurrent() {
        $page = self::loadCurPage(false);
        if (!($page instanceof WebPage)) {
            self::getPage(BASE_PAGE_INDEX)->redirectHere();
        }
        $page->buildPage();
    }

    /**
     * Статика
     */
    private static $storeState = 0; //0 - не наполнена, 1 - идёт наполнение, 2 - наполнено
    private static $page2code = array();
    private static $page2pathBase = array();

    /**
     * Метод получения зарегистрированной страницы
     * 
     * @return WebPage
     */
    public final static function getPage($page, $ensure = true) {
        if (self::$storeState === 0) {
            self::$storeState = 1;
            PageBuilder::inst()->registerAllHtmlPages();
            self::$storeState = 2;

            //Проверим, чтобыла зарегистрирована страница index и пользователь имеет доступ к ней.
            check_condition(self::getPage(BASE_PAGE_INDEX)->hasAccess(), 'Пользователь не имеет доступа к индексной странице');
        }

        check_condition(self::$storeState === 2, 'Хранилище страниц ещё не наполнено');

        if ($page instanceof WebPage) {
            return $page;
        }

        if (is_numeric($page)) {
            if (array_key_exists($page, self::$page2code)) {
                return self::$page2code[$page];
            }
            check_condition(!$ensure, "Страница с кодом [$page] не зарегистрирована");
            return null;
        }

        if (is_string($page)) {
            $pathBase = get_file_name($page);
            if (array_key_exists($pathBase, self::$page2pathBase)) {
                return self::$page2pathBase[$pathBase];
            }
            check_condition(!$ensure, "Страница с адресом [$page] не зарегистрирована");
            return null;
        }

        check_condition(!$ensure, "Страница не зарегистрирована");
        return null;
    }

    /**
     * Список возможных скриптов (которые не будут исключены при сборке проекта)
     */
    private static $allowedScripts = array();

    public static function allowedScripts() {
        //Инициируем сбор данных
        self::getPage(BASE_PAGE_INDEX);
        return self::$allowedScripts;
    }

    /**
     * Функция для регистрации страниц
     * 
     * @param type $path
     * @param type $name
     * @param type $code
     * @param AbstractPageBuilder $builderIdent
     * @param type $authType
     * @param type $basePageCode
     * @param type $pageCodeNoAccess
     * @param type $allovedInProduction
     * @return type
     */
    public final static function register(
    $path, //
            $name, //
            $code, //
            $builderIdent, //
            $authType, //
            $basePageCode = null, //
            $pageCodeNoAccess = null, //
            $allovedInProduction = true//
    ) {

        self::$allowedScripts[] = $path;

        if (!$allovedInProduction && PsDefines::isProduction()) {
            return; //----
        }

        check_condition(self::$storeState === 1, 'Страница не может быть заретистирована, хранилище находится в состоянии [' . self::$storeState . ']');

        check_condition(is_string($path) && !isEmpty($path), "Некорректный путь для страницы [$path]");
        check_condition(is_integer($code), "Некорректный код для страницы [$code]");

        $pathBase = get_file_name($path);
        check_condition(!array_key_exists($pathBase, self::$page2pathBase) && !array_key_exists($code, self::$page2code), "Страница [$name ($code)] не может быть зарегистрирована дважды");

        $page = new WebPage($path, $name, $code, $authType, $basePageCode, $pageCodeNoAccess, $builderIdent);

        self::$page2code[$code] = $page;
        self::$page2pathBase[$pathBase] = $page;
    }

}

?>