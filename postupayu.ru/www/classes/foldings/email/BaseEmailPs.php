<?php

/**
 * Базовый класс для отправки электронного письма
 *
 * @author azazello
 */
class BaseEmailPs extends FoldedClass {

    /** @var PsLoggerInterface */
    protected $LOGGER;

    public function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function _construct() {
        $this->LOGGER = PsLogger::inst(get_called_class());
    }

}

?>
