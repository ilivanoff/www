<?php

/**
 * Форма регистрации
 *
 */
final class RegFormData implements FormSuccess {

    /**
     * Поля данного класса должны иметь те-же названия, какие имеют столбцы в таблице users.
     */
    private $user_name;
    private $email;
    private $passwd;
    private $b_sex;
    private $about;
    private $about_src;
    private $contacts;
    private $contacts_src;
    private $msg;
    private $msg_src;

    public function setAboutSrc($aboutSrc) {
        $this->about_src = $aboutSrc;
    }

    public function setContactsSrc($contactsSrc) {
        $this->contacts_src = $contactsSrc;
    }

    public function setMsgSrc($msgSrc) {
        $this->msg_src = $msgSrc;
    }

    public function getAboutSrc() {
        return $this->about_src;
    }

    public function getContactsSrc() {
        return $this->contacts_src;
    }

    public function getMsgSrc() {
        return $this->msg_src;
    }

    public function setAbout($about) {
        $this->about = $about;
    }

    public function setContacts($contacts) {
        $this->contacts = $contacts;
    }

    public function setMsg($msg) {
        $this->msg = $msg;
    }

    public function getAbout() {
        return $this->about;
    }

    public function getContacts() {
        return $this->contacts;
    }

    public function getMsg() {
        return $this->msg;
    }

    public function getUserName() {
        return $this->user_name;
    }

    public function getUserMail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->passwd;
    }

    public function setUserName($userName) {
        $this->user_name = $userName;
    }

    public function setUserMail($userMail) {
        $this->email = $userMail;
    }

    public function setPassword($password) {
        $this->passwd = $password;
    }

    public function getSex() {
        return $this->b_sex;
    }

    public function setSex($sex) {
        $this->b_sex = $sex;
    }

    /**
     * Метод преобразует данный объект в ассоциативный массив
     */
    public function asAssocArray(array $allowed = null) {
        $class = __CLASS__;
        $result = array();
        foreach (PsUtil::getClassProperties($class, false, false) as $fieldName) {
            check_condition(UserBean::hasColumn($fieldName), "Поле $class::$fieldName не может быть свойством пользователя");
            if ($allowed === null || in_array($fieldName, $allowed)) {
                $result[$fieldName] = $this->$fieldName;
            }
        }
        return $result;
    }

}