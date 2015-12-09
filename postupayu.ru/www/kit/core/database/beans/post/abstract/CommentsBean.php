<?php

abstract class CommentsBean extends BaseBean {

    /** @var PostBeanSettings */
    private $SETTINGS;

    /**
     * Таблица с рубриками
     */
    protected $rubricsTable;

    /**
     * Представление с рубриками
     */
    protected $rubricsView;

    /**
     * Таблица с постами
     */
    protected $postsTable;

    /**
     * Представление с постами
     */
    protected $postsView;

    /**
     * Таблица с комментариями
     */
    protected $commentsTable;

    /**
     * Тип поста для данного бина
     */
    public final function getPostType() {
        return $this->SETTINGS->getPostType();
    }

    /**
     * Таблица с рубриками
     */
    public function getRubricsTable() {
        return $this->rubricsTable;
    }

    /**
     * Представление с рубриками
     */
    public function getRubricsView() {
        return $this->rubricsView;
    }

    /**
     * Таблица с постами
     */
    public final function getPostsTable() {
        return $this->postsTable;
    }

    /**
     * Представление с постами
     */
    public final function getPostsView() {
        return $this->postsView;
    }

    /**
     * Таблица с комментариями
     */
    public final function getCommentsTable() {
        return $this->commentsTable;
    }

    /**
     * Настройки бина для работы с БД
     * 
     * @return PostBeanSettings
     */
    protected abstract function PostBeanSettings();

    /**
     * В конструкторе мы провалидируем настройки бина и проинициализируем protected поля
     */
    protected final function __construct() {
        parent::__construct();
        $this->SETTINGS = PsUtil::assertInstanceOf($this->PostBeanSettings(), PostBeanSettings::getClass());
        //Инициализируем поля для быстрого доступа внутри бина
        $this->rubricsTable = $this->SETTINGS->getRubricsTable();
        $this->rubricsView = $this->SETTINGS->getRubricsView();
        $this->postsTable = $this->SETTINGS->getPostsTable();
        $this->postsView = $this->SETTINGS->getPostsView();
        $this->commentsTable = $this->SETTINGS->getCommentsTable();
    }

}

?>