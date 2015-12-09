<?php

/*
 * =====================================
 * ========== ОБРАТНАЯ СВЯЗЬ ===========
 * =====================================
 */

class FeedBean extends BaseBean {

    /**
     * Сохранение сообщения анонимного пользователя
     */
    public function saveAnonimousFeedback($userName, $contacts, $theme, $comment) {
        $this->update('insert into feedback (user_name, contacts, dt_event, theme, content) values (?, ?, UNIX_TIMESTAMP(), ?, ?)', array(
            $userName,
            $contacts,
            $theme,
            $comment));
    }

    /*
     * СИНГЛТОН
     */

    /** @return FeedBean */
    public static function inst() {
        return parent::inst();
    }

}

?>
