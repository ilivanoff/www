<?php

/**
 * Базовый бласс для фетчинга загруженного массива
 *
 * @author azazello
 */
abstract class ArrayQueryFetcher {

    private $excludeKeys;
    private $includeKeys;
    private $excludeValues;
    private $includeValues;
    private $isFilterKey;
    private $isFilterValue;

    /** @return BaseQuerySettings */
    public final function setExcludeKeys(array $excludeKeys = null) {
        check_condition($this->isFilterKey, 'Класс ' . get_called_class() . ' не фильтрует ключи');
        $this->excludeKeys = $excludeKeys;
        return $this;
    }

    /** @return BaseQuerySettings */
    public final function setIncludeKeys(array $includeKeys = null) {
        check_condition($this->isFilterKey, 'Класс ' . get_called_class() . ' не фильтрует ключи');
        $this->includeKeys = $includeKeys;
        return $this;
    }

    /** @return BaseQuerySettings */
    public final function setExcludeValues(array $excludeValues = null) {
        check_condition($this->isFilterValue, 'Класс ' . get_called_class() . ' не фильтрует значения');
        $this->excludeValues = $excludeValues;
        return $this;
    }

    /** @return BaseQuerySettings */
    public final function setIncludeValues(array $includeValues = null) {
        check_condition($this->isFilterValue, 'Класс ' . get_called_class() . ' не фильтрует значения');
        $this->includeValues = $includeValues;
        return $this;
    }

    protected final function filterKey($key) {
        return ($this->excludeKeys === null || !in_array($key, $this->excludeKeys)) &&
                ($this->includeKeys === null || in_array($key, $this->includeKeys));
    }

    protected final function filterValue($value) {
        return ($this->excludeValues === null || !in_array($value, $this->excludeValues)) &&
                ($this->includeValues === null || in_array($value, $this->includeValues));
    }

    public abstract function fetchResult(array $ROWS);

    protected function __construct($isFilterKey, $isFilterValue) {
        $this->isFilterKey = !!$isFilterKey;
        $this->isFilterValue = !!$isFilterValue;
    }

}

?>