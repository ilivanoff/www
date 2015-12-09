<?php

/**
 * Класс для аудита отправки почты
 *
 * @author azazello
 */
final class MailAudit extends BaseAudit {
    /**
     * Действия
     */

    const ACTION_SENDED = 1;

    public function getProcessCode() {
        return self::CODE_EMAILS;
    }

    public function getDescription() {
        return 'Отправка почты';
    }

    /**
     * Аудит отправки письма
     */
    public function afterSended(PsMailSender $sender) {
        $this->doAudit(self::ACTION_SENDED, $sender->getUserIdTo(), "$sender");
    }

    /** @return MailAudit */
    public static function inst() {
        return parent::inst();
    }

}

?>