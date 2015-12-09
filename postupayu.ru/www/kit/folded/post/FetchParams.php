<?php

class FetchParams extends FoldedTplFetchPrams {

    const PARAM_ANONS = 'anons';
    const PARAM_VERSES = 'verses';
    const PARAM_EX_CNT = 'ex_cnt';
    const PARAM_PLUGINS = 'plugins';
    const PARAM_HAS_VIDEO = 'has_video';
    const PARAM_TASKS_CNT = 'tasks_cnt';

    protected function _assertExists() {
        return false;
    }

    /*
     * Из PostFetchingContext
     */

    public function getAnons() {
        return to_array(parent::__get(self::PARAM_ANONS));
    }

    public function getTasksCnt() {
        return parent::__get(self::PARAM_TASKS_CNT);
    }

    public function getExamplesCnt() {
        return parent::__get(self::PARAM_EX_CNT);
    }

    public function hasVideo() {
        return !!parent::__get(self::PARAM_HAS_VIDEO);
    }

    /*
     * Плагины
     */

    public function getPluginsData() {
        return to_array(parent::__get(self::PARAM_PLUGINS));
    }

    private $pluginIdents;

    public function getUsedPlugins() {
        if (!is_array($this->pluginIdents)) {
            $this->pluginIdents = array();
            foreach ($this->getPluginsData() as $data) {
                $ident = $data[0];
                if (!in_array($ident, $this->pluginIdents)) {
                    $this->pluginIdents[] = $ident;
                }
            }
        }
        return $this->pluginIdents;
    }

    public function isUsingPlugin($ident) {
        return in_array($ident, $this->getUsedPlugins());
    }

    /*
     * Стихи
     * $this->verses хранит связку:
     * $poetIdent=>array($verseIdent1, $verseIdent2)
     */

    public function getUsedVerses() {
        return to_array(parent::__get(self::PARAM_VERSES));
    }

    public function isUsingPoet($poetIdent) {
        return array_key_exists($poetIdent, $this->getUsedVerses());
    }

}

?>