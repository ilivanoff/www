<?php

/**
 * Description of PsPropMessages
 *
 * @author azazello
 */
final class PsMsgs {

    private static $MESSAGES = array();

    public static function format($__CLASS__, $__FUNCTION__, array $arguments) {
        if (!array_key_exists($__CLASS__, self::$MESSAGES)) {
            /*
             * Защита от зацикливания
             */
            self::$MESSAGES[$__CLASS__] = null;
            /*
             * Определим, где расположен класс с сообщениями
             */
            $classPath = Autoload::inst()->getClassPath($__CLASS__);
            if (!$classPath) {
                return PsUtil::raise('Группа сообщений {} не зарегистрирована', $__CLASS__);
            }
            /*
             * Получим DirItem сообщений для этого класса
             */
            $messagesDi = DirItem::inst(dirname($classPath), $__CLASS__, PsConst::EXT_MSGS);
            if (!$messagesDi->isFile()) {
                return PsUtil::raise('Файл с сообщениями {} не существует', $messagesDi->getName());
            }
            /*
             * Распарсим сообщения из файла
             */
            self::$MESSAGES[$__CLASS__] = $messagesDi->getFileAsProps();
        }

        /*
         * Проверим на зацикливание
         */
        if (self::$MESSAGES[$__CLASS__] === null) {
            PsUtil::raise('Зацикливание при попытке получить сообещние {}::{}', $__CLASS__, $__FUNCTION__);
        }

        /*
         * Проверим на существование самого сообщения
         */
        if (!array_key_exists($__FUNCTION__, self::$MESSAGES[$__CLASS__])) {
            return PsUtil::raise('Сообщение {}::{} не существует', $__CLASS__, $__FUNCTION__);
        }

        /*
         * Заменим макросы {} и вернём сообщение
         */
        return PsStrings::replaceMapBracedKeys(self::$MESSAGES[$__CLASS__][$__FUNCTION__], $arguments);
    }

    /**
     * Возвращает название текущего класса для автогенерации
     */
    public static function getClass() {
        return __CLASS__;
    }

}

?>