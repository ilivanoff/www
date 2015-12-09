<?php

/**
 * Класс для работы с CSS спрайтами
 *
 * @author azazello
 */
final class CssSprite {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var Spritable */
    private $spritable;

    /** Название спрайта */
    private $name;

    /** @var DirItem */
    private $cssDi;

    /** @var DirItem */
    private $imgDi;

    /** Признак - был ли спрайт перестроен */
    private $rebuilded = false;

    private function __construct($name, Spritable $spritable) {
        $this->LOGGER = PsLogger::inst(__CLASS__);

        $this->spritable = $spritable;

        $this->name = $name;
        $this->cssDi = self::autogenWs($name)->getDirItem(null, $name, 'css');
        $this->imgDi = self::autogenWs($name)->getDirItem(null, $name, PsImg::getExt(SYSTEM_IMG_TYPE));

        $this->LOGGER->info("INSTANCE CREATED FOR [$name]");

        $this->rebuild(false);
    }

    private static $sprites = array();

    /**
     * Пусть к воркспейсу, в котором производится работа со спрайтом
     * 
     * @return DirManager
     */
    public static function autogenWs($subDirs = null) {
        return DirManager::autogen(array('sprites', $subDirs));
    }

    /** @return CssSprite */
    public static function inst($spritable) {
        //Если строка, значит передано название поддиректории в папке www/resources/sprites
        $spritable = is_string($spritable) ? DirManager::sprites($spritable)->getDirItem() : $spritable;

        check_condition($spritable instanceof Spritable, 'Элемент для построения спрайта не является подклассом Spritable');

        $name = $spritable->getSpriteName();

        if (array_key_exists($name, self::$sprites)) {
            return self::$sprites[$name];
        }

        if ($spritable instanceof DirItem) {
            check_condition($spritable->isDir(), "Некорректная директория $spritable для построения спрайта");
        }

        return self::$sprites[$name] = new CssSprite($name, $spritable);
    }

    /**
     * Основной метод - возвращает <span> для элемента спрайта.
     * Если спрайт не содержит данного элемента, то будет возвращена пустая строка.
     * 
     * @param type $withGray - параметр, означающий - нежно ли добавлять "серое" представление элемента
     */
    public function getSpriteSpan($itemName, array $attrs = array(), $withGray = false) {
        if (!$this->hasSpriteItem($itemName)) {
            return '';
        }

        if ($withGray) {
            return $this->spanImpl($itemName, $attrs, 'sprite-c') . $this->spanImpl($itemName . '_nc', $attrs, 'sprite-nc');
        } else {
            return $this->spanImpl($itemName, $attrs);
        }
    }

    private function spanImpl($itemName, array $attrs = array(), $class = null) {
        $attrs['class'] = to_array(array_get_value('class', $attrs));
        $attrs['class'][] = $class;
        $attrs['class'][] = 'sprite';
        $attrs['class'][] = 'sprite-' . $this->name;
        $attrs['class'][] = 'sprite-' . $this->name . '-' . $itemName;

        return PsHtml::span($attrs);
    }

    /*
     * ====================
     * =  PUBLIC METHODS  =
     * ====================
     * 
     */

    public function getName() {
        return $this->name;
    }

    /** @return DirItem */
    public function getCssDi() {
        return $this->cssDi;
    }

    /** @return DirItem */
    public function getImgDi() {
        return $this->imgDi;
    }

    /**
     * Проверка существования спрайта
     */
    public function exists() {
        return $this->cssDi->isFile();
    }

    /**
     * Метод возвращает все картинки, которые должны быть помещены в спрайт.
     * Можно смело считать, что за время работы скрипта кол-во картинок в 
     * директории останется неизменным, поэтому просто закешируем их на уровне данного класса.
     */
    private $images;

    public function getImages() {
        if (!is_array($this->images)) {
            $this->images = $this->spritable->getSpriteImages();
        }
        return $this->images;
    }

    /**
     * Элементы css, входящие в спрайт
     */
    private $items;

    public function getSpriteItems() {
        if (!is_array($this->items)) {
            $this->items = array();

            $contents = $this->cssDi->getFileContents();
            if ($contents) {
                $pattern = "/\.sprite-{$this->name}-([^{|^ ]*)/si";
                $matches = array();
                preg_match_all($pattern, $contents, $matches, PREG_PATTERN_ORDER);
                $this->items = $matches[1];
            }
        }
        return $this->items;
    }

    /**
     * Проверка наличия спрайта для картинки
     */
    private function hasSpriteItem($itemName) {
        return in_array($itemName, $this->getSpriteItems());
    }

    /**
     * Перестроение спрайта
     * 
     * @return CssSprite
     */
    public function rebuild($force = true) {
        if (!$this->rebuilded && ($force || !$this->exists())) {
            //Отлогируем
            $this->LOGGER->info("REBUILDING SPRITE FOR [{$this->name}]");
            //Поставим признак перестроенносьти
            $this->rebuilded = true;
            //Сбросим закешированные элементы, так как css файл мог поменяться
            $this->items = null;
            //Удалим .css файл
            $this->cssDi->remove();
            //Перестроим
            CssSpritesCreator::inst()->CreateSprite($this);
            //Создадим .css файл, даже если он не был создан в процессе построения. Просто у нас нет картинок в $spritable.
            $this->cssDi->touch();
        }

        return $this; //---
    }

    public function __toString() {
        return __CLASS__ . ' [' . $this->name . ']';
    }

}

?>