<?php

final class PSSmarty extends AbstractSingleton {

    /** @var Smarty */
    private $smarty;

    protected function __construct() {
        ExternalPluginsManager::Smarty();

        /**
         * Начиная с версии 5.4 в функции htmlentities параметр encoding был изменён на UTF-8, 
         * до этого момента после применения данного метода к тексту шаблона мы будем получать кракозябру.
         */
        SmartyCompilerException::$escape = is_phpver_is_or_greater(5, 4);

        $this->smarty = new Smarty();
        $this->smarty->compile_check = true;
        $this->smarty->force_compile = false;
//        $this->smarty->caching = TRUE;

        $SMARTY_BASE_PATH = DirManager::smarty()->absDirPath();

        /*
         * УПРАВЛЯЮЩИЕ ДИРЕКТОРИИ
         */
        $this->smarty->setTemplateDir($SMARTY_BASE_PATH . '/templates/');
        $this->smarty->setCompileDir($SMARTY_BASE_PATH . '/templates_c/');
        $this->smarty->setCacheDir($SMARTY_BASE_PATH . '/cache/');
        $this->smarty->setConfigDir($SMARTY_BASE_PATH . '/configs/');

        /*
         * ПЛАГИНЫ
         */
        //1. Функции
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/content/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/mmedia/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/gym/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/replacements/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/discussion/comments/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/discussion/feedback/';

        //2. Модификаторы
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/modifiers/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/modifiers/content/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/modifiers/discussion/comments/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/modifiers/discussion/feedback/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/modifiers/rubric/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/modifiers/post/';

        //3. Блочные функции
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/blocks/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/blocks/content/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/blocks/text/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/blocks/child/';
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/blocks/post/';

        /*
         * ADMIN
         */
        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/functions/admin/';

        $plugins_dir[] = $SMARTY_BASE_PATH . '/plugins/modifiers/admin/';

        $this->smarty->addPluginsDir($plugins_dir);

        /*
         * Импортируем константы некоторых классов, чтобы на них можно было ссылаться через 
         * {$smarty.const.CONST_NAME}
         */
        PsConstJs::defineAllConsts();

        /*
         * Подключим фильтры
         */
        new SmartyFilters($this->smarty);

        /*
         * Зарегистрируем наши функции
         */
        /* @var $plugin AbstractSmartyPlugin */
        foreach (Classes::getDirClasses(__DIR__, 'plugins/impl', 'AbstractSmartyPlugin') as $plugin) {
            $plugin->registerPlugins($this->smarty);
        }
    }

    /** @return Smarty */
    public static function smarty() {
        return parent::inst()->smarty;
    }

    /** @return Smarty_Internal_Template */
    public static function template($path, $data = null) {
        return self::smarty()->createTemplate($path instanceof DirItem ? $path->getAbsPath() : $path, $data);
    }

}

?>