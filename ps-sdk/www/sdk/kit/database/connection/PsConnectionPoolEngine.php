<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PsConnectionPoolEngine
 *
 * @author azazello
 */
abstract class PsConnectionPoolEngine extends AbstractSingleton {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /**
     * Настройки текущего соединения.
     * По умолчанию сначала всегда смотрим на боевую базу.
     * Для тестов/скриптов это поведение можно переключить.
     * 
     * @var PsConnectionParams
     */
    private $CONNECTION_PARAMS = null;

    /**
     * Текущее соединение к БД
     * 
     * @var ADOConnection 
     */
    private $CONNECTION = null;

    /**
     * Все выданные соединения к БД
     * Мы можем настроить adodb на работу с любым соединением.
     * @var ADOConnection 
     */
    private $CONNECTIONS = array();

    /** @return PsConnectionPoolEngine */
    protected static final function inst() {
        return parent::inst();
    }

    /**
     * Конструктор
     */
    protected final function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        /*
         * Подкобчаться к БД будем в MainImport, чтобы не подключиться заранее
         */
        //Поумолчанию коннектимся на продуктив
        //$this->CONNECTION_PARAMS = PsConnectionParams::get(PsConnectionParams::CONN_PRODUCTION);
    }

    /**
     * Метод проверяет, сконфигурирован ли пул
     */
    protected final function isConfigured() {
        return is_object($this->CONNECTION_PARAMS);
    }

    /**
     * Метод возвращает параметры подключения к БД
     * 
     * @return PsConnectionParams
     */
    public function getConnectionParams() {
        return $this->CONNECTION_PARAMS;
    }

    /**
     * Метод перенаправляет ConnectionPool на другой коннект
     */
    protected final function configureImpl(PsConnectionParams $params = null) {
        if ($this->CONNECTION_PARAMS == null) {
            if ($params == null) {
                return; //---
            }

            $this->LOGGER->info();
            $this->LOGGER->info('>  Setting connection params: [{}]', $params);

            $this->CONNECTION_PARAMS = $params;
        } else {
            if ($this->CONNECTION_PARAMS->equals($params)) {
                return; //Сейчас работаем с теми-же настройками ---
            } else {
                $this->LOGGER->info();
                $this->LOGGER->info('<> Switching connection params from [{}] to [{}]', $this->CONNECTION_PARAMS, $params == null ? 'disconnected' : $params);

                $this->CONNECTION_PARAMS = $params;
            }
        }

        //Очистим кеши
        PsDbCahce::clearAll();

        //Сбросим текущий коннект
        $this->CONNECTION = null;
    }

    /**
     * Метод получения коннекта к БД
     * 
     * @return ADOConnection
     */
    protected final function getConnection() {
        if ($this->CONNECTION != null) {
            return $this->CONNECTION; //---
        }

        if (!$this->isConfigured()) {
            PsUtil::raise('Cannot get DB connection, {} is not configured.', get_called_class());
        }

        $this->LOGGER->info();
        $this->LOGGER->info('?  Connection for [{}] is requested', $this->CONNECTION_PARAMS);

        $URL = $this->CONNECTION_PARAMS->url();

        //Посмотрим, есть ли у нас нужный коннект
        if (array_key_exists($URL, $this->CONNECTIONS)) {
            $this->LOGGER->info('<  Fast returned from cache');
            return $this->CONNECTION = $this->CONNECTIONS[$URL];
        }

        //Отлогируем
        $this->LOGGER->info('+  Establishing connection {}', $this->CONNECTION_PARAMS);

        //Подключаем adodb
        ExternalPluginsSdk::AdoDb();

        //Подключаемся
        $this->CONNECTION = ADONewConnection($URL);

        if (!is_object($this->CONNECTION)) {
            PsUtil::raise("Unable to connect to [{}]", $this->CONNECTION_PARAMS);
        }

        //Зададим некоторые настройки
        $this->CONNECTION->debug = ADODB_DEBUG;
        $this->CONNECTION->SetFetchMode(ADODB_FETCH_ASSOC);
        $this->CONNECTION->query("SET NAMES 'utf8'");
        $this->CONNECTION->query("SET CHARACTER SET 'utf8'");

        //Положим соединение в пул
        if (array_key_exists($URL, $this->CONNECTIONS)) {
            raise_error('Double trying to register db connection');
        }

        $this->LOGGER->info('<  Established connection returned');

        return $this->CONNECTIONS[$URL] = $this->CONNECTION;
    }

    /**
     * Очистим все выданные ранее соединения
     */
    public final function __destruct() {
        /** @var $conn ADOConnection */
        foreach ($this->CONNECTIONS as $conn) {
            $conn->Close();
        }
        $this->CONNECTIONS = array();
        $this->CONNECTION = null;
    }

}

?>