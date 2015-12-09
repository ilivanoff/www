<?php

abstract class RubricsBean extends PostsBean {

    /** @return Rubric */
    protected function fetchRubric(array $data) {
        return new Rubric($this->getPostType(), $data);
    }

    private function loadRubrics($what, $where = null) {
        return $this->getObjects(Query::select($what, $this->rubricsView, $where), null, Rubric::getClass(), 'id_rubric', array($this->getPostType()));
    }

    public function getRubrics() {
        return $this->loadRubrics('id_rubric, name, ident');
    }

    public function getRubricsContent(array $ids, $loadAll = false) {
        if ($loadAll) {
            return $this->loadRubrics('*');
        } else {
            $result = array();
            /** @var QueryParamAssoc */
            foreach (Query::assocParamsIn('id_rubric', $ids) as $param) {
                foreach ($this->loadRubrics('*', $param) as $rubricId => $rubric) {
                    $result[$rubricId] = $rubric;
                }
            }
            return $result;
        }
    }

    /**
     * Метод загружает виртуальный пост. Если он есть в базе, то будет возвращён пост из базы, если нет, то будет возвращён
     * виртуальный пост.
     * 
     * @param string $ident - идентификатор поста
     * @param array $cols4replace - столбцы, которые можно заменить своими значениями в случае, если строка не будет загружена из БД
     */
    public function getVirtualRubric($ident, array $cols4replace = array()) {
        check_condition($ident, 'Не передан идентификатор для загрузки виртуальной рубрики');

        //Проверим, возможно запись уже есть в таблице. Если так - вернём её.
        $row = $this->getRec("select * from $this->rubricsTable where ident=?", $ident);
        $virtual = !is_array($row);
        if ($virtual) {
            $cols4replace['ident'] = $ident;
            $cols4replace['id_rubric'] = TEST_ENTITY_ID;
            $row = $this->getEmptyRec($this->rubricsTable, $cols4replace);
        }

        return $this->fetchRubric($row);
    }

}

?>