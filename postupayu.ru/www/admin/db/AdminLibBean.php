<?php

/**
 * Description of AdminLibBean
 *
 * @author azazello
 */
class AdminLibBean extends LibBean {

    public function getAllNoFetch($group) {
        return $this->getArray('SELECT * FROM ps_lib_item where grup=?', $group);
    }

    //INSERT
    public function createLibItem(LibItemDb $item) {
        check_condition($item->getIdent(), 'Не передан идентификатор создаваемой сущности');
        return $this->insert('INSERT INTO ps_lib_item (ident, grup, name, content, dt_start, dt_stop, b_show) VALUES (?, ?, ?, ?, ?, ?, 0)', array(
                    $item->getIdent(), //
                    $item->getGroup(), //
                    $item->getName(), //
                    $item->getContent(), //
                    $item->getDtStart(), //
                    $item->getDtStop()
                ));
    }

    //UPDATE
    public function updateLibItem(LibItemDb $item) {
        check_condition($item->hasId(), 'Не передан код обновляемой сущности');
        $this->update('UPDATE ps_lib_item SET name=?, content=?, dt_start=?, dt_stop=?, b_show=? WHERE id=?', array(
            $item->getName(), //
            $item->getContent(), //
            $item->getDtStart(), //
            $item->getDtStop(), //
            $item->isShow() ? 1 : 0, //
            $item->getId()
        ));
    }

    //DELETE
    public function removeLibEntity($id) {
        if (is_numeric($id)) {
            $this->update('DELETE FROM ps_lib_item WHERE id=?', $id);
        }
    }

    /*
     * СИНГЛТОН
     */

    /** @return AdminLibBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
