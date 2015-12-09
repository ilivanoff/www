<?php

/**
 * Базовый бин для работы с фолдингами. Чтобы не плодить экземпляры,
 * мы будем использовать один единственный бин, а фолдинги, с которыми мы работаем,
 * передавать внутрь.
 *
 * @author Admin
 */
final class FoldingBean extends BaseBean {

    /**
     * Метод возвращает идентификаторы данного фолдинга, хранимые в базе.
     * 
     * @param type $all - признак, возвращать:
     *  * все сущности (выбираются из table)
     *  * видимые сущности (выбираются из view)
     */
    public function getIdents(FoldedResources $folded, $all) {
        $folded->assertWorkWithTable();
        $cacheKey = $folded->getUnique('idents-' . ($all ? 'all' : 'vis'));
        if (!$this->CACHE->has($cacheKey)) {
            $view = $all ? $folded->getTableName() : $folded->getTableView();
            $colIdent = $folded->getTableColumnIdent();
            $colStype = $folded->getTableColumnStype();

            $params = array();
            $query = "select $colIdent as value from $view";

            if ($colStype) {
                $params[] = $folded->getFoldingSubType();
                $query .= " where $colStype=?";
            }
            $this->CACHE->set($cacheKey, $this->getValues($query, $params));
        }
        return $this->CACHE->get($cacheKey);
    }

    /**
     * Метод возвращает видимые пользователю объекты из базы
     */
    public function getVisibleObjects(FoldedResources $folded, $objName, array $visibleIdents) {
        $folded->assertWorkWithTable();
        $cacheKey = $folded->getUnique("visible-$objName");
        if (!$this->CACHE->has($cacheKey)) {
            $view = $folded->getTableView();
            $colIdent = $folded->getTableColumnIdent();
            $colStype = $folded->getTableColumnStype();

            $where = array();
            if ($colStype) {
                $where[$colStype] = $folded->getFoldingSubType();
            }
            $this->CACHE->set($cacheKey, $this->getArray(Query::select('*', $view, $where), null, ObjectQueryFetcher::inst($objName, $colIdent)->setIncludeKeys($visibleIdents)));
        }
        return $this->CACHE->get($cacheKey);
    }

    /**
     * Метод возвращает код для сущности фолдинга.
     * Этот код нужен для того, чтобы другие сущности могли ссылаться на него через базу.
     */
    public function getEntityCode(FoldedEntity $entity) {
        $code = array_get_value('id', to_array($this->getRec('select id from ps_folded_codes where v_unique=?', $entity->getUnique())));
        return 1 * (is_numeric($code) ? $code : $this->insert('insert into ps_folded_codes (v_unique) values (?)', $entity->getUnique()));
    }

    /**
     * Возвращает идентификатор сущности фолдинга по её коду.
     * Если сущности нет, то будет ошибка, так как если мы её ищём, то на неё уже кто-то состался.
     * 
     * @param type $code
     */
    public function getUniqueByCode($code) {
        $unique = array_get_value('v_unique', to_array($this->getRec('select v_unique from ps_folded_codes where id=?', $code)));
        return check_condition($unique, "Не найдена сущность фолдина с кодом [$code]");
    }

    /** @return FoldingBean */
    public static function inst() {
        return parent::inst();
    }

}

?>