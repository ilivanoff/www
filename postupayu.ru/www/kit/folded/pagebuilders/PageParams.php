<?php

/**
 * Description of PageParams
 *
 * @author azazello
 */
class PageParams extends FoldedTplFetchPrams {

    const PARAM_JS = 'js';
    const PARAM_TITLE = 'title';
    const PARAM_RESOURCES = 'smres';

    public function getTitle() {
        return trim(parent::__get(self::PARAM_TITLE));
    }

    public function getJsParams() {
        return to_array(parent::__get(self::PARAM_JS));
    }

    public function getSmartyParams4Resources() {
        return to_array(parent::__get(self::PARAM_RESOURCES));
    }

}

?>
