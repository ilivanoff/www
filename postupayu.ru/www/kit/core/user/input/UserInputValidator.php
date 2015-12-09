<?php

class UserInputValidator {

    public static function validateEmail($mail, $mustPresent = false) {
        if (!$mail) {
            return 'required';
        }
        if (ps_strlen($mail) > EMAIL_MAXLEN) {
            return 'maxlength';
        }
        if (!PsCheck::isEmail($mail)) {
            return 'email';
        }

        $hasMail = UserBean::inst()->hasMail($mail);
        if (($hasMail && !$mustPresent) || (!$hasMail && $mustPresent)) {
            return 'remote';
        }

        return false;
    }

    public static function validateSex($sex) {
        return get_sex($sex) ? false : 'required';
    }

    public static function validatePass($pass, $passConfirm) {
        if (isEmpty($pass)) {
            return 'required';
        }
        if (strlen($pass) < 6 || strlen($pass) > 80) {
            return 'rangelength';
        }
        return false;
    }

    public static function validatePassConfirm($pass, $passConfirm) {
        if (isEmpty($passConfirm)) {
            return 'required';
        }
        if ($pass !== $passConfirm) {
            return 'equalTo';
        }
        return false;
    }

    public static function censure($text) {
        $censure = PsCensure::parse($text);
        if ($censure) {
            return "Текст содержит нецензурную лексику: $censure";
        }
        return false;
    }

    public static function validateShortText($data, $required = true, $maxLen = SHORT_TEXT_MAXLEN) {
        if (isEmpty($data)) {
            return $required ? 'required' : false;
        }

        if (ps_strlen($data) > $maxLen) {
            return 'maxlength';
        }

        if (TexTools::hasTex($data)) {
            return 'notex';
        }
        return self::censure($data);
    }

    public static function validateLongText($data, $required = true) {
        if (isEmpty($data)) {
            return $required ? 'required' : false;
        }

        $error = TexTools::getTexError($data);
        if ($error) {
            return $error;
        }
        return self::censure($data);
    }

    public static function validateOldPass($oldPass) {
        if (isEmpty($oldPass)) {
            return 'required';
        }
        if (!PsUser::inst()->checkPassword($oldPass)) {
            return 'remote';
        }
        return false;
    }

}

?>