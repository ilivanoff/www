<?php

/**
 * Description of LibBean
 *
 * @author Admin
 */
class LibBean extends BaseBean {

    /**
     * Метод извлекает сущности библиотек, среди которых можно найти искомую
     */
    public function getLibItemsSearchAmong($grup, $ident, $text) {
        if (!$ident && !$text) {
            //не идентификатора, ни группы - по чём определять то?
            return null;
        }

        $sql = 'select i.grup, i.ident, i.name from v_ps_lib_item i where 1=1 ';

        $where = array();
        $params = array();

        if ($grup) {
            $where[] = 'grup=?';
            $params[] = $grup;
        }

        if ($ident) {
            $where[] = 'ident=?';
            $params[] = $ident;
        } else {
            $where['name'] = "name like ?";
            $params['name'] = "%$text%";
            $res = $this->getArray($sql . implode(' and ', $where), $params);
            if (count($res)) {
                //Ничего не нашли по совпадению текста... Прийдётся извне искать среди всех элементов.
                return $res;
            }
            unset($where['name']);
            unset($params['name']);
        }
        return $this->getArray($sql . implode(' and ', $where), $params);
    }

    /*
     * СИНГЛТОН
     */

    /** @return LibBean */
    public static function inst() {
        return parent::inst();
    }

}

?>