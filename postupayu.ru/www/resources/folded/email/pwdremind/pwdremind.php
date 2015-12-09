<?php

/**
 * Форма pwdremind
 *
 * @author Admin
 */
class EMAIL_pwdremind extends BaseEmail {

    public function send($email) {
        //Загружаем код пользователя по e-mail
        $userId = PsUser::instByMail($email)->getId();

        //Генерируем код
        $CODE = PsUserCode::passRecover()->generateAndSave($userId);

        //Отправляем письмо
        try {
            $content = $this->foldedEntity->fetchTpl(array('code' => $CODE->getCode()));
            PsMailSender::fastSend('Восстановление пароля на ' . ServerArrayAdapter::HTTP_HOST(), $content, $email);
        } catch (Exception $ex) {
            //Коды, высланные пользователю, нужно удалить
            $CODE->dropUnusedCodes($userId);
            throw $ex;
        }
    }

    /** @return EMAIL_pwdremind */
    public static function inst() {
        return parent::inst();
    }

}

?>