<?php

/**
 * Пул коннектов
 *
 * @author azazello
 */
final class PsConnectionPool extends PsConnectionPoolEngine {

    /**
     * Метод возвращает непосредственный коннект к БД
     * 
     * @return ADOConnection
     */
    public static function conn() {
        return self::inst()->getConnection();
    }

    /**
     * Метод возвращает параметры подключения
     * 
     * @return PsConnectionParams
     */
    public static function params() {
        return self::inst()->getConnectionParams();
    }

    /** Метод проверяет, можно ли подконнектиться к БД */
    public static function isConnectied() {
        return self::inst()->isConfigured();
    }

    /** Метод утверждает, что пул может выдать коннект */
    public static function assertConnectied() {
        check_condition(self::isConnectied(), __CLASS__ . ' is not configured.');
    }

    /** Метод утверждает, что пул не сконфигурирован и не может выдать коннект */
    public static function assertDisconnectied() {
        check_condition(!self::isConnectied(), __CLASS__ . ' is configured.');
    }

    /** Метод утверждает, что пул сконфигурирован переданными настройками */
    public static function assertConnectiedTo(PsConnectionParams $params) {
        check_condition($params->equals(self::params()), 'Коннект должен быть установлен на ' . $params);
    }

    /** @return ADOConnection */
    public static function configure(PsConnectionParams $params) {
        return self::inst()->configureImpl($params);
    }

    /** Метод отключения от БД */
    public static function disconnect() {
        return self::inst()->configureImpl(null);
    }

}

?>