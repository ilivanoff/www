<?php

/**
 * Description of SearchResults
 *
 * @author azazello
 */
final class SearchResults {

    const COL_PRE = 'pre';
    const COL_DATE = 'date';
    const COL_NOWRAP = 'class-nowrap';

    private $data;
    private $query;
    private $settings = array();
    private $columns = array();

    public static function convert($in) {
        return $in instanceof SearchResults ? $in : new SearchResults($in);
    }

    public function __construct(array $data = null, Query $query = null) {
        $this->data = $data;
        $this->columns = $data ? array_keys($data[0]) : null;
        $this->query = $query;
    }

    /**
     * Добавляет настройку для столбца
     * 
     * @return \SearchResults
     */
    public function addSetting($column, $setting) {
        $this->settings[$column][] = $setting;
        $this->settings[$column] = array_values(array_unique($this->settings[$column]));
        return $this;
    }

    public function setColumns(array $columns) {
        $this->columns = $columns;
    }

    public function toAttay() {
        $REQULT = array();
        if ($this->query && AuthManager::isAuthorizedAsAdmin()) {
            $REQULT['query'] = $this->query->build($params);
            $REQULT['params'] = array_to_string($params);
        }

        if (!$this->data) {
            return $REQULT;
        }

        //Добавим стандартные обработчики для колонок
        foreach ($this->columns as $column) {
            if (starts_with($column, 'dt_')) {
                $this->addSetting($column, self::COL_DATE);
                $this->addSetting($column, self::COL_NOWRAP);
            }
        }

        $REQULT['data'] = $this->data;
        $REQULT['columns'] = $this->columns;
        $REQULT['settings'] = $this->settings;

        return $REQULT;
    }

}

?>