<?php

/**
 * Description of UserBindDO
 *
 * @author azazello
 */
class UserPointDO extends BaseDataStore {

    public function getPointId() {
        return (int) $this->id_point;
    }

    public function getReasonId() {
        return (int) $this->id_reason;
    }

    public function getCnt() {
        return (int) $this->n_cnt;
    }

    public function getData() {
        return $this->v_data;
    }

    public function getDtEvent($format = DF_USER_POINTS) {
        return DatesTools::inst()->uts2dateInCurTZ($this->dt_event, $format);
    }

    /**
     * Метод возвращает экземпляр класса, расшифровывающего причину выдачи данных очков пользователю.
     * 
     * @return UserPointDescriber
     */
    public function getDescriber() {
        return UserPointDescriber::inst($this);
    }

}

?>
