<?php

ExternalPluginsManager::PhpMailer();

/**
 * Базовый класс для отправки почты
 *
 * @author azazello
 */
final class PsMailSender extends PHPMailer {

    /** @var PsMailSender */
    private static $inst;

    /**
     * В конструкторе выполним все необходимые подготовки.
     * Доступ public как в PHPMailer.
     */
    public function __construct() {
        check_condition(!self::$inst, 'Экземпляр ' . __CLASS__ . ' должен быть единственным');

        $this->exceptions = true;
        $this->CharSet = 'UTF-8';
        $this->SetLanguage('ru');
        $this->IsSMTP();
        $this->SMTPKeepAlive = true;                           //Need SmtpClose in __destruct()
        $this->SMTPAuth = true;                               // Enable SMTP authentication
        $this->Host = SMTP_HOST;                              // Specify main and backup server
        $this->Sender = SMTP_USERNAME;
        $this->Username = SMTP_USERNAME;
        $this->Password = SMTP_PASSWORD;
        //Enable encryption, 'ssl' also accepted
        //$mail->SMTPSecure = 'tls';
        $this->IsHTML(true);
        //$mail->WordWrap = 50;

        $this->reset();
    }

    /**
     * В деструкторе класса закроем СМТП соединение
     */
    function __destruct() {
        $this->SmtpClose();
    }

    /**
     * Установка темы письма
     */
    public function SetSubject($Subject) {
        $this->Subject = $Subject;
    }

    /**
     * Установка тела письма
     */
    public function SetBody($Body) {
        $this->Body = $Body;
    }

    /**
     * Возвращает код получателя
     */
    public function getUserIdTo() {
        foreach ($this->to as $addr) {
            $userId = UserBean::inst()->getUserIdByMail($addr[0]);
            if (is_integer($userId)) {
                return $userId; //---
            }
        }
        return null;
    }

    /**
     * Метод отправляет письмо пользователю
     * 
     * @throws Exception
     */
    public function Send() {
        $PROFILER = PsProfiler::inst(__CLASS__);

        $PROFILER->start('Send');
        try {
            //Отправляем письмо
            $result = parent::Send();
            //Останавливаем профайлер
            $PROFILER->stop();
            //Сделаем дамп отправленного письма
            $this->dumpEmail();
            //Запишем аудит
            MailAudit::inst()->afterSended($this);
            //Вернём то, что вернул оригинальный Send метод
            return $result;
        } catch (Exception $ex) {
            //Останавливаем профайлер без сохранения
            $PROFILER->stop(false);
            //Если возникла ошибка отправки письма - сделаем её дамп вместе с письмом
            if ($ex instanceof phpmailerException) {
                ExceptionHandler::dumpError($ex, $this);
            }
            throw $ex;
        }
    }

    /**
     * Метод сохраняет последний удачно отправленный email
     */
    private function dumpEmail() {
        $DM = DirManager::autogen('emails');
        if ($DM->getDirContentCnt() >= EMAILS_MAX_FILES_COUNT) {
            $DM->clearDir();
        }
        $DM->getDirItem(null, PsUtil::fileUniqueTime(), 'mail')->putToFile($this);
    }

    /**
     * Метод полностью очищает состояние отправщика писем, подготовливая его к отправке нового письма
     * 
     * @return PsMailSender
     */
    public function reset() {
        $this->ClearAllRecipients();
        $this->ClearAttachments();
        $this->ClearCustomHeaders();
        $this->SetSubject('');
        $this->SetBody('');
        $this->SetFrom(PS_MAIL_NO_REPLY . '@' . ServerArrayAdapter::HTTP_HOST(), PS_MAIL_NO_REPLY);
        return $this;
    }

    /** @return PsMailSender */
    public static function inst() {
        return self::$inst ? self::$inst->reset() : self::$inst = new PsMailSender();
    }

    /**
     * Быстрая отправка письма с темой и сообщением на указанный адрес.
     */
    public static function fastSend($Subject, $Body, $addressTo, $nameTo = '') {
        $sender = self::inst();
        $sender->SetSubject($Subject);
        $sender->SetBody($Body);
        $sender->AddAddress($addressTo, $nameTo);
        $sender->Send();
    }

    /**
     * Метод собирает всю информацию о письме для дампа или записи в лог ошибки
     */
    public function __toString() {
        $RESULT[] = '<EMAIL START>';
        $RESULT[] = 'DATE: ' . self::RFCDate();
        foreach (array('to', 'cc', 'bcc') as $kind) {
            foreach ($this->$kind as $addr) {
                $name = $addr[1];
                $address = $addr[0];
                $RESULT[] = strtoupper($kind) . ": <$name> $address";
            }
        }
        if ($this->attachment) {
            $RESULT[] = 'ATTACHES:' . array_to_string($this->attachment);
        }
        $RESULT[] = 'THEME: ' . $this->Subject;
        $RESULT[] = 'CONTENT: ' . "\n" . $this->Body;
        $RESULT[] = '<EMAIL END>';
        return join("\n", $RESULT);
    }

}

?>