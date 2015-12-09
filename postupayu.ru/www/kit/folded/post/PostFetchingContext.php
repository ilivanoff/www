<?php

/**
 * Контекст, в котором выполняется фетч шаблона поста
 */
class PostFetchingContext extends FoldedContext implements ImageNumeratorContext, TasksNumeratorContext, FormulaNumeratorContext, SpritableContext {
    /*
     * ЗАМЕНЫ
     */

    const REPLACEMENT_ANONS = 'ANONS';

    /* Данные подключаемых плагинов */
    const PLUGINS_DATA = 'PLUGINS_DATA';

    /* Данные подключаемых плагинов */
    const VERSES = 'VERSES';

    /* примеры */
    const CURRENT_EX_NUM = 'CURRENT_EX_NUM';
    const EX_ID2NUM = 'EX_ID2NUM';

    /* теоремы */
    const CURRENT_TH_NUM = 'CURRENT_TH_NUM';
    const TH_ID2NUM = 'TH_ID2NUM';

    /* параграфы в уроках */
    const ANONS_PART = 'ANONS_PART';

    /* замены */
    const REPLACEMENTS = 'REPLACEMENTS';
    const HAS_VIDEO = 'HAS_VIDEO';

    /*
     * ВИДЕО
     */

    public function setHasVideo() {
        $this->setParam(self::HAS_VIDEO, true);
    }

    public function hasVideo() {
        return $this->hasParam(self::HAS_VIDEO);
    }

    /*
     * АНОНС
     */

    public function getAnons() {
        return $this->getParam(self::ANONS_PART, array());
    }

    public function addAnons($name) {
        $num = count($this->getAnons()) + 1;
        $this->setMappedParam(self::ANONS_PART, $num, $name);
        return $num;
    }

    /*
     * ПРИМЕРЫ
     */

    public function getExampleNum($exId, $doRegister) {
        $num = $exId ? $this->getMappedParam(self::EX_ID2NUM, $exId) : null;
        if (!is_numeric($num) && $doRegister) {
            $num = $this->getNumAndIncrease(self::CURRENT_EX_NUM);
            $this->LOGGER->info("Example registered: $exId -> $num");
            $this->setMappedParam(self::EX_ID2NUM, $exId, $num);
        }
        return $num;
    }

    public function getExampleElId($exId) {
        return $this->getFoldedEntity()->getUnique(IdHelper::exId($exId));
    }

    public function getExamplesCount() {
        return $this->getParam(self::CURRENT_EX_NUM, 0);
    }

    public function getNextExampleNum() {
        return $this->getNumAndIncrease(self::CURRENT_EX_NUM);
    }

    public function resetExamplesNum() {
        $this->resetParam(self::CURRENT_EX_NUM);
    }

    public function registerExample($exId, $num) {
        $this->LOGGER->info("Example registered: $exId -> $num");
        $this->setMappedParam(self::EX_ID2NUM, $exId, $num);
    }

    /*
     * ТЕОРЕМЫ
     */

    public function getThCount() {
        return $this->getParam(self::CURRENT_TH_NUM, 0);
    }

    public function getNextThNum($thId, $doRegister) {
        $num = $thId ? $this->getMappedParam(self::TH_ID2NUM, $thId) : null;
        if (!is_numeric($num) && $doRegister) {
            $num = $this->getNumAndIncrease(self::CURRENT_TH_NUM);
            $this->LOGGER->info("Theorem registered: $thId -> $num");
            $this->setMappedParam(self::TH_ID2NUM, $thId, $num);
        }
        return $num;
    }

    public function resetThNum() {
        $this->resetParam(self::CURRENT_TH_NUM);
    }

    public function getThElId($thId) {
        return $this->getFoldedEntity()->getUnique(IdHelper::thId($thId));
    }

    /*
     * СТИХИ
     */

    public function registerVerse(Verse $verse) {
        if ($verse->isValid()) {
            $this->LOGGER->info("Verse registered: $verse");
            $this->addParam(self::VERSES, $verse);
        }
    }

    public function getVerses() {
        return $this->getParam(self::VERSES, array());
    }

    public function getUsedVerses() {
        $result = array();
        /* @var $verse Verse */
        foreach ($this->getVerses() as $verse) {
            $poetIdent = $verse->getPoetIdent();
            $verseIdent = $verse->getVerseIdent();
            if (!array_key_exists($poetIdent, $result)) {
                $result[$poetIdent] = array();
            }
            if (!in_array($verseIdent, $result[$poetIdent])) {
                $result[$poetIdent][] = $verseIdent;
            }
        }
        return $result;
    }

    /*
     * ЗАМЕНЫ (replacements)
     */

    public function registerReplacement($id) {
        $this->setMappedParam(self::REPLACEMENTS, $id, $id);
    }

    public function getReplacements() {
        return $this->getParam(self::REPLACEMENTS, array());
    }

    /*
     * ПЛАГИНЫ
     */

    public function registerPlugin($pluginIdent, $pluginData) {
        //Регистрируем как массив, так как потом мы будем этот объект сериализовать
        $this->addParam(self::PLUGINS_DATA, array($pluginIdent, $pluginData));
        $this->LOGGER->info("+ PS plugin [{}] with data: [{}].", $pluginIdent, concat($pluginData));
    }

    public function getPluginsData() {
        return $this->getParam(self::PLUGINS_DATA, array());
    }

    /**
     * ImageNumeratorContext
     */
    public function wrapBlockImgBox($imageId, array $attrs, $content) {
        return $this->ImageNumeratorContext()->wrapBlockImgBox($imageId, $attrs, $content);
    }

    public function getBlockImgHref($imageId) {
        return $this->ImageNumeratorContext()->getBlockImgHref($imageId);
    }

    /**
     * TasksNumeratorContext
     */
    public function getNextTaskNumber() {
        return $this->TasksNumeratorContext()->getNextTaskNumber();
    }

    public function getTasksCount() {
        return $this->TasksNumeratorContext()->getTasksCount();
    }

    public function resetTasksNumber() {
        $this->TasksNumeratorContext()->resetTasksNumber();
    }

    /**
     * FormulaNumeratorContext
     */
    public function wrapFormulaBox($formulaId, $content) {
        return $this->FormulaNumeratorContext()->wrapFormulaBox($formulaId, $content);
    }

    public function getFormulaHref($formulaId) {
        return $this->FormulaNumeratorContext()->getFormulaHref($formulaId);
    }

    /**
     * SpritableContext
     */
    public function getSpritable() {
        return $this->SpritableContext()->getSpritable();
    }

    /*
     * ФОЛДИНГ
     */

    /**
     * Метод возвращает абстрактный пост для данного фолдинга.
     * Мы не возвращаем реальный потому, что его может не быть на момент фетчинга, например в админке.
     * 
     * @return AbstractPost
     */
    public function getPost() {
        //return $this->getFoldedEntity()->getFolding()->pp()->getVirtualPost($this->getFoldedEntity()->getIdent());
        return $this->getFoldedEntity()->getFolding()->pp()->getAbstractPost($this->getFoldedEntity()->getIdent());
    }

    /**
     * Используем свой класс для хранения параметров поста.
     * Кроме содержимого мы хотим ещё много чего знать:)
     */
    public function tplFetchParamsClass() {
        return FetchParams::getClassName();
    }

    /**
     * Перепишем финализацию содержимомго, чтобы добавить дополнительные параметры, сохраняемые в контекст.
     */
    public function finalizeTplContent($content) {
        $PARAMS[FetchParams::PARAM_ANONS] = $this->getAnons();
        $PARAMS[FetchParams::PARAM_VERSES] = $this->getUsedVerses();
        $PARAMS[FetchParams::PARAM_EX_CNT] = $this->getExamplesCount();
        $PARAMS[FetchParams::PARAM_PLUGINS] = $this->getPluginsData();
        $PARAMS[FetchParams::PARAM_TASKS_CNT] = $this->getTasksCount();
        $PARAMS[FetchParams::PARAM_HAS_VIDEO] = $this->hasVideo();

        /*
         * Выполним замены.
         */
        foreach ($this->getReplacements() as $replId) {
            switch ($replId) {
                case self::REPLACEMENT_ANONS:
                    $localHrefs = PSSmarty::template('post/anons_local_hrefs.tpl', $PARAMS)->fetch();
                    $content = str_replace($replId, $localHrefs, $content);
                    break;
                default:
                    $content = str_replace($replId, PsHtml::divErr("Unknown replacement [$replId]"), $content);
                    break;
            }
        }

        $PARAMS[FetchParams::PARAM_CONTENT] = $this->wrapContent($content);

        return $PARAMS;
    }

    /** @return PostFetchingContext */
    public static function getInstance() {
        return parent::inst();
    }

}

?>