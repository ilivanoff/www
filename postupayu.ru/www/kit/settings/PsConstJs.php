<?php

/**
 * Константы системы, которые будут экспортированы в javascript
 * и доступны в нём через глобальную переменную CONST.
 *
 * @author azazello
 */
final class PsConstJs {
    /**
     * Группа параметров javascript - панели для фолдингов
     */

    const PAGE_JS_GROUP_PANELS = 'panels';

    /**
     * Максимальный размер загружаемых файлов через post
     */
    const UPLOAD_MAX_FILE_SIZE = UPLOAD_MAX_FILE_SIZE;

    /**
     * Класс ссылки, загружающей информацию в bubble.
     */
    const BUBBLE_HREF_MOVE_CLASS = 'ps-bubblehref-move';
    const BUBBLE_HREF_STICK_CLASS = 'ps-bubblehref-stick';

    /*
     * Загрузчики bubble`оф
     */
    const BUBBLE_LOADER_IMAGE = 'image';
    const BUBBLE_LOADER_FOLDING = 'folding';
    const BUBBLE_LOADER_FOLDING_DATA = 'folded-item-unique';
    const BUBBLE_LOADER_FOLDING_STORE_ID = 'ps-folded-items-store';

    /*
     * Лупа - увеличение картинки
     */
    const IMG_PREVIEW = '/resources/images/icons/preview.png';
    /*
     * Длинная картинка-прогресс
     */
    const IMG_LOADING_LONG = '/resources/images/icons/page_loading.gif';

    /*
     * Длинная картинка-прогресс
     */
    const IMG_LOADING_PROGRESS = '/resources/images/icons/loading/loading.gif';

    /*
     * Значёк формулы
     */
    const IMG_FORMULA = '/resources/images/icons/controls/formula.png';

    /*
     * Путь к скрипту, который строит и отдаёт капчу
     */
    const CAPTCHA_FIELD = FORM_PARAM_PSCAPTURE;
    const CAPTCHA_LENGTH = 6;
    const CAPTCHA_IMG_ID = 'captchaimage';
    const CAPTCHA_SCRIPT = '/pscaptcha/image.php';

    /**
     * Css классы - нумераторы.
     * Внутри дивов, имеющих данный класс, все элементы и ссылки на эти элементы, имеющие тот-же класс с 
     * суффиксом -index и -href-index, будут пронумерованы "насквозь".
     * 
     * Превикс CSS_NUMERATOR_ можно использовать только в том случае, если константа описывает класс
     * для сквозной нумерации.
     */
    const CSS_NUMERATOR_IMG = 'ps-numerator-img';
    const CSS_NUMERATOR_FORMULA = 'ps-numerator-formula';

    /**
     * Класс, добавляемый диву с кнопками управления постом (доп печать и т.д.)
     */
    const POST_HEAD = 'ps-post-head';
    const POST_HEAD_CONTROLS = 'ps-post-head-controls';

    /**
     * Различные классы для работы галерей
     */
    const GALLERY_BOX = 'ps-gallery-box';       //Сам блок с картинками (открыть/закрыть)
    const GALLERY_LIST = 'ps-gallery-list';     //Картинки, которые с помощью js будут преобразованы с галерею
    const GALLERY_IMAGES = 'ps-gallery-images'; //Картинки галереи в виде списка

    /**
     * Префикс для кодов аватаров пользователя
     */
    const AVATAR_ID_PREFIX = 'av-';
    const AVATAR_NO_SUFFIX = 'no';

    /**
     * Метод возвращает span с номером для элемента нумерации
     */
    public static function numeratorItemIndex($css, $num) {
        return PsHtml::span(array('class' => $css . '-index'), $num);
    }

    /**
     * Метод возвращает span с номером для ссылки на элемент нумерации
     */
    public static function numeratorHrefIndex($css, $num) {
        return PsHtml::span(array('class' => $css . '-href-index'), $num);
    }

    /**
     * Переносит все константы данного класса в поле констант через define().
     * Дальше можно использовать в смарти:
     * {$smarty.const.JS_GALLERY_LIST}
     * или
     * constant('JS_GALLERY_LIST').
     */
    public static function defineAllConsts() {
        PsUtil::defineClassConsts(__CLASS__, 'JS');
    }

}

?>