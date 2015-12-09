<?php

/**
 * Базовое исключение базы данных
 */
class DBException extends PException {

    const ERROR_NOT_CLASSIFIED = -100;
    const ERROR_DUPLICATE_ENTRY = -101;
    const ERROR_NO_DATA_FOUND = -102;
    const ERROR_TOO_MANY_ROWS = -103;

    /**
     * Выполненный запрос
     */
    private $query;

    /**
     * Параметры, с которыми был выполнен запрос
     */
    private $params;

    public function __construct($message, $code, $query, array $params, $previous = null) {
        $this->query = $query;
        $this->params = $params;

        $code = PsUtil::assertClassHasConstVithValue(__CLASS__, 'ERROR_', $code);
        if ($code == self::ERROR_NOT_CLASSIFIED) {
            /*
             * Если ошибка не классифицирована, то мы попробуем её классифицировать
             */
            if (starts_with($message, 'Duplicate entry ')) {
                $code = self::ERROR_DUPLICATE_ENTRY;
            }
        }

        $message = $message ? $message : 'Unknown db error';

        $message = "$message. Query: [$query]" . ($params ? ', Params: ' . array_to_string($params) . '.' : '');

        parent::__construct($message, $code, $previous);
    }

}

?>