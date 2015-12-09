<?php

/**
 * Description of MosaicImgBean
 *
 * @author Admin
 */
class MosaicImgBean extends BaseBean {

    /**
     * Возвращает информацию о картинке, сразу проверяя её наличие.
     * 
     * @param int $imgId - код картинки
     * @return array
     */
    public function getImgInfo($imgId) {
        return $this->getRecEnsure('SELECT * FROM ps_img_mosaic where id_img=?', $imgId);
    }

    /**
     * Загружает ячейки мозайки из базы.
     * 
     * @param type $imgId - код картинки
     * @param type $userId - код пользователя, ячейки которого загружаются
     * @param boolean $owned - признак, загружать ли только ячейки, принадлежащие кому-либо
     * @param type $limit - ограничивать ли выбор ячеек
     * @param type $cntOnly - признак работы в режиме "извлечь только количество"
     */
    private function getCellsImpl($imgId, $userId, $owned, $limit, $cntOnly) {
        $params[] = $imgId;

        $sqlFrom = 'from ps_img_mosaic_parts where id_img=?';

        if (is_numeric($userId)) {
            $owned = true;
            $params[] = $userId;
            $sqlFrom .= ' and id_user=?';
        }

        if ($owned === true) {
            $sqlFrom .= ' and owned=1';
        } else
        if ($owned === false) {
            $sqlFrom .= ' and owned=0';
        }

        if ($cntOnly) {
            return $this->getCnt("select count(1) as cnt $sqlFrom", $params);
        } else {
            $sqlFrom .= ' order by n_part';
            if (is_numeric($limit)) {
                $params[] = $limit;
                $sqlFrom .= ' limit ?';
            }
            return $this->getArray("select * $sqlFrom", $params);
        }
    }

    /**
     * Загрузка ячеек. Описание параметров см. выше.
     */
    private function getCells($imgId, $userId = null, $owned = null, $limit = null) {
        return $this->getCellsImpl($imgId, $userId, $owned, $limit, false);
    }

    /**
     * Загрузка кол-ва ячеек. Описание параметров см. выше.
     */
    private function getCellsCnt($imgId, $userId = null, $owned = null) {
        return $this->getCellsImpl($imgId, $userId, $owned, null, true);
    }

    /*
     * ==================
     * = PUBLIC METHODS =
     * ==================
     */

    /**
     * Возвращает кол-во свободных ячеек
     */
    public function getFreeCellsCnt($imgId) {
        return $this->getCellsCnt($imgId, null, false);
    }

    /**
     * Возвращает занятые ячейки
     */
    public function getOwnedCells($imgId, $userId = null) {
        return $this->getCells($imgId, $userId, true);
    }

    /**
     * Возвращает кол-во занятых ячеек
     */
    public function getOwnedCellsCnt($imgId, $userId = null) {
        return $this->getCellsCnt($imgId, $userId, true);
    }

    /**
     * Возвращает пользователей, у которых есть ячейки в мозайке
     */
    public function getImgUsers($imgId) {
        $usersArr = $this->getArray('select * from users where id_user in (select distinct id_user from ps_img_mosaic_parts where id_img=? and id_user is not null and owned=1)', $imgId);
        $users = array();
        foreach ($usersArr as $user) {
            $users[$user['id_user']] = PsUser::inst($user);
        }
        return $users;
    }

    /**
     * Возвращают порцию не занятых ячейк, которые могут быть привязаны к пользователю.
     * 
     * @param type $imgId - код картинки
     * @param type $bindCnt - размер порции
     */
    public function getCells4UserBind($imgId, $bindCnt) {
        $freeCnt = $this->getFreeCellsCnt($imgId);
        $canBind = min($freeCnt, $bindCnt);
        $this->LOGGER->info("Binding $canBind cells ($bindCnt requested, $freeCnt is free) at mosaic img $imgId.");
        return $canBind > 0 ? $this->getCells($imgId, null, false, $canBind) : array();
    }

    /**
     * Отмечает ячейку, как занятую пользователем
     * 
     * @param type $userId - код пользователя
     * @param type $imgId - код картинки
     * @param type $nPart - номер занимаемой ячейки
     */
    public function markAsOwned($userId, $imgId, $nPart) {
        $this->update('update ps_img_mosaic_parts set owned=1, dt_event=unix_timestamp(), id_user=? where id_img=? and n_part=?', array($userId, $imgId, $nPart));
    }

    /**
     * Возвращает статискику по кол-ву пользователей, занявших одинаковое кол-во ячеек.
     * Возвращается array('Кол-во ячеек' => 'Кол-во пользователей').
     * Если 2 пользователя набрали по 10 ячеек, вернётся array(10 => 2).
     */
    public function getStatictic($imgId) {
        $dataArr = $this->getArray('
select count(1) as usercnt, points
  from (select count(1) as points, id_user
          from ps_img_mosaic_parts
         where id_img = ?
           and owned = 1
         group by id_user) w
 group by points
 order by points desc;
', $imgId);

        $res = array();
        foreach ($dataArr as $data) {
            $res[$data['points']] = $data['usercnt'];
        }
        return $res;
    }

    /*
     * ANSWERS
     */

    /**
     * Возвращает ответ пользователя на головоломку. Если ответа от пользователя нет - вернётся null.
     * 
     * @return UserAnsDO
     */
    public function getUserAnswer($imgId, $userId) {
        $rec = $this->getRec('SELECT id_answer, v_answer FROM ps_img_mosaic_answers where id_img=? and id_user=?', array($imgId, $userId));
        return $rec ? new UserAnsDO($rec) : null;
    }

    /**
     * Сохраняет ответ пользователя
     */
    public function saveImgAnswer($imgId, $userId, $answer) {
        $ans = $this->getRec('SELECT id_img, v_answer FROM ps_img_mosaic_answers where id_img=? and id_user=?', array($imgId, $userId));
        if ($ans == null) {
            $this->getImgInfo($imgId);
            $this->insert('INSERT INTO ps_img_mosaic_answers (id_img, id_user, v_answer, dt_event) VALUES (?, ?, ?, unix_timestamp())', array($imgId, $userId, $answer));
        } else {
            if ($answer != $ans['v_answer']) {
                $this->update('UPDATE ps_img_mosaic_answers SET v_answer=?, dt_event=unix_timestamp() WHERE id_img=? and id_user=?', array($answer, $imgId, $userId));
            }
        }
    }

    /**
     * Удаляет ответ пользователя на головоломку
     */
    public function delImgAnswer($ansId, $imgId, $userId) {
        $this->update('delete from ps_img_mosaic_answers where id_answer=? and id_user=? and id_img=?', array($ansId, $userId, $imgId));
    }

    /**
     * Возвращает ответ победителя.
     * Победным считается ответ, для которого ps_img_mosaic_answers.b_winner=1
     * 
     * @return UserAnsDO
     */
    public function getWinnerAnswer($imgId) {
        $rec = $this->getRec('select a.id_answer, a.v_answer, u.id_user, u.user_name from ps_img_mosaic_answers a, users u where id_img=? and b_winner=1 and a.id_user = u.id_user limit 1', $imgId);
        return $rec ? new UserAnsDO($rec) : null;
    }

    /*
     * СИНГЛТОН
     */

    /** @return MosaicImgBean */
    public static function inst() {
        return parent::inst();
    }

}

?>