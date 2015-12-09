<?php

/**
 * Базовый класс для всех классов, производящих аудит действий системы
 *
 * @author azazello
 */
abstract class BaseAudit extends AbstractSingleton {
    //Допустимые коды аудита

    const CODE_USERS = 1;
    const CODE_EMAILS = 2;

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** Код процесса */
    private $PROCESS_CODE;

    /** Название класса */
    private $CLASS;

    /** Допустимые коды действий */
    private $ACTIONS;

    /** Код сессии для хранения предыдущего кода аудита */
    private $SESSION;

    /** Счётчик вызово */
    private $NUM = 0;

    protected final function __construct() {
        $this->CLASS = get_called_class();
        PsUtil::assertClassHasDifferentConstValues($this->CLASS, 'CODE_');
        PsUtil::assertClassHasDifferentConstValues($this->CLASS, 'ACTION_');
        $this->ACTIONS = PsUtil::getClassConsts($this->CLASS, 'ACTION_');
        $this->LOGGER = PsLogger::inst($this->CLASS);
        $this->PROCESS_CODE = $this->getProcessCode();
        check_condition(in_array($this->PROCESS_CODE, PsUtil::getClassConsts(__CLASS__, 'CODE_')), "Класс [{$this->CLASS}] использует незарегистрированный код аудита [{$this->PROCESS_CODE}].");
        $this->SESSION = SESSION_AUDIT_ACTION . '-' . $this->CLASS . '-' . $this->PROCESS_CODE;
    }

    /**
     * Код процесса
     */
    public abstract function getProcessCode();

    /**
     * Описание процесса
     */
    public abstract function getDescription();

    /**
     * Класс-реализация аудита
     */
    public final function getClass() {
        return $this->CLASS;
    }

    /**
     * Список всех действий
     */
    public final function getActions() {
        return $this->ACTIONS;
    }

    /**
     * Валидация типа действия
     */
    private function validateAction($action, $canBeNull = false) {
        if ($canBeNull && $action === null) {
            return null;
        }
        check_condition(is_inumeric($action), "Не целочисленный код действия [$action] для $this");
        $action = 1 * $action;
        check_condition(in_array($action, $this->ACTIONS), "Код действия [$action] не зарегистрирован для $this");
        return $action;
    }

    public final function decodeAction($action, $canBeNull = true) {
        $action = $this->validateAction($action, $canBeNull);
        if ($action === null) {
            return 'null';
        }
        return PsUtil::getClassConstByValue($this->CLASS, 'ACTION_', $action) . ' (' . $action . ')';
    }

    private function sessionCode($action) {
        return $this->SESSION . '-' . $this->validateAction($action);
    }

    protected function doAudit($action, $userId = null, $data = null, $saveToSession = false, $parentAction = null, $auditIfNoParent = true, $clearParent = true) {
        try {
            $action = $this->validateAction($action);
            $parentAction = $this->validateAction($parentAction, true);

            $actionSessionKey = $this->sessionCode($action);

            $parentActionSessionKey = $parentAction ? $this->sessionCode($parentAction) : null;
            $parentId = $parentActionSessionKey ? SessionArrayHelper::getInt($parentActionSessionKey) : null;
            $hasParentIdInSession = is_integer($parentId);

            $userId = AuthManager::validateUserIdOrNull($userId);
            $userIdAuthed = AuthManager::getUserIdOrNull();

            if ($this->LOGGER->isEnabled()) {
                $this->LOGGER->info();
                $this->LOGGER->info("<Запись #{}>", ++$this->NUM);
                $this->LOGGER->info('Действие: {}', $this->decodeAction($action));
                $this->LOGGER->info('Пользователь: {}', is_inumeric($userId) ? $userId : 'НЕТ');
                $this->LOGGER->info('Авторизованный пользователь: {}', is_inumeric($userIdAuthed) ? $userIdAuthed : 'НЕТ');
                $this->LOGGER->info('Данные: {}', $data === null ? 'НЕТ' : print_r($data, true));
                $this->LOGGER->info('Сохранять в сессию: {}', $saveToSession ? 'ДА' : 'НЕТ');
                $this->LOGGER->info('Родительское действие: {}', $this->decodeAction($parentAction));
                if ($parentAction) {
                    $this->LOGGER->info('Родительское действие есть в сессии: {}', $hasParentIdInSession ? "ДА ($parentActionSessionKey=$parentId)" : 'НЕТ');
                    if ($hasParentIdInSession) {
                        $this->LOGGER->info('Очищать родительское действие в сессии: {}', $clearParent ? 'ДА' : 'НЕТ');
                    } else {
                        $this->LOGGER->info('Производить аудит при отсутствии родит. действия: {}', $auditIfNoParent ? 'ДА' : 'НЕТ');
                    }
                }
            }

            if (!$hasParentIdInSession && !$auditIfNoParent) {
                $this->LOGGER->info('АУДИТ НЕ ПРОИЗВЕДЁН!');
                return; //--- Нужен предок, но его нет
            }

            $encoded = 0;
            if (is_array($data)) {
                if (count($data) == 0) {
                    $data = null;
                } else {
                    $data = self::encodeData($data);
                    $encoded = 1;
                }
            }

            check_condition($data === null || is_string($data) || is_numeric($data), 'Illegal audit data type: ' . gettype($data) . ' for ' . $this);

            $recId = UtilsBean::inst()->saveAudit($parentId, $userId, $userIdAuthed, $this->PROCESS_CODE, $action, $data, $encoded);

            if ($this->LOGGER->isEnabled()) {
                if ($data !== null) {
                    $this->LOGGER->info('Данные кодированы: {}', $encoded ? "ДА ($data)" : 'НЕТ');
                }
                $this->LOGGER->info('Информация сохранена в базу, id={}', $recId);
            }

            if ($saveToSession) {
                SessionArrayHelper::setInt($actionSessionKey, $recId);
                $this->LOGGER->info("Данные о действии сохранены в сессию ($actionSessionKey=$recId)");
            }

            if ($hasParentIdInSession && $clearParent) {
                SessionArrayHelper::reset($parentActionSessionKey);
                $this->LOGGER->info('Данные о родительском действии удалены из сессии');
            }

            $this->LOGGER->info('АУДИТ ПРОИЗВЕДЁН.');
        } catch (Exception $ex) {
            //Не удалось записть аудит, но работа должна быть продолжена!
            ExceptionHandler::dumpError($ex);
        }
    }

    private static final function encodeData(array $data) {
        return serialize($data);
    }

    public static final function decodeData($data) {
        return $data ? @unserialize($data) : null;
    }

    /**
     * Метод возвращает экземпляры всех аудитов системы
     */
    private static $insts = null;

    public final static function getAll() {
        if (!is_array(self::$insts)) {
            //Только админ может загружать все аудиты
            AuthManager::checkAdminAccess();
            //Инициализируем коллекцию
            self::$insts = array();
            foreach (Classes::getDirClassNames(__DIR__, 'impl', __CLASS__) as $className) {
                $inst = $className::inst();
                self::$insts[$inst->getProcessCode()] = $inst;
            }
            ksort(self::$insts);
        }
        return self::$insts;
    }

    /**
     * Метод возвращает аудит по его коду
     * 
     * @param int $code - код аудита
     * @return BaseAudit
     */
    public final static function getByCode($code) {
        return check_condition(array_get_value($code, self::getAll()), "не зарегистрирован аудит с кодом [$code]");
    }

    public final function __toString() {
        return "{$this->CLASS} [{$this->PROCESS_CODE}]";
    }

}

?>