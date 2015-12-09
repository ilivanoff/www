<?php

abstract class BaseSearchForm extends BaseAjaxForm {

    const BUTTON_SEARCH = 'Найти';

    /** @var AjaxSuccess */
    public final function getData() {
        return parent::getData();
    }

    protected function processImpl(PostArrayAdapter $adapter, $button) {
        return new AjaxSuccess(SearchResults::convert($this->doSearch($adapter))->toAttay());
    }

    protected abstract function doSearch(PostArrayAdapter $params);
}

?>