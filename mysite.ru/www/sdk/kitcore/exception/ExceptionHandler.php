<?php

/**
 * Наш класс, занимающийся обработкой ошибок, выбрасываемых через trigger_error.
 */
final class ExceptionHandler {

    /**
     * Метод форматирует вывод Exception в html
     */
    public static function getHtml(Exception $exception) {
        //Вычитываем [exception.html] и производим замены
        try {
            return str_replace('{STACK}', ExceptionHelper::formatStackHtml($exception), file_get_contents(file_path(__DIR__, 'exception.html')));
        } catch (Exception $ex) {
            //Если в методе форматирования эксепшена ошибка - прекращаем выполнение.
            die("Exception [{$exception->getMessage()}] stack format error: [{$ex->getMessage()}]");
        }
    }

    public static function register4errors() {
        set_error_handler(array(__CLASS__, 'processError'), error_reporting());
    }

    public static function register() {
        restore_exception_handler();
        set_exception_handler(array(__CLASS__, 'processException'));
    }

    public static function registerPretty() {
        restore_exception_handler();
        set_exception_handler(array(__CLASS__, 'processExceptionPretty'));
    }

    /**
     * Функция, вызывающая обычную обработку неотловленного Exception
     */
    public static function processException(Exception $exception) {
        die($exception->getMessage());
    }

    /**
     * Функция, вызывающая красивую обработку неотловленного Exception
     */
    public static function processExceptionPretty(Exception $exception) {
        die(self::getHtml($exception));
    }

    /**
     * Функция, выполняющая обработку php ошибок, выбрасываемых через trigger_error
     */
    public static function processError($errorLevel, $message, $file, $line) {
        if (!error_reporting()) {
            //Вывод ошибок для данного метода - @отключен
            return; //---
        }

        if (ExternalPluginsManager::isExternalFile($file)) {
            //Данный файл относится к файлам внешних плагинов
            //Возвращаем управление встроенному обработчику
            return false; //---
        }

        throw new PsErrorException($message, $errorLevel, $file, $line);
    }

    /**
     * Метод сохраняет ошибку выполнения в файл
     * 
     * @param Exception $exception
     */
    public static function dumpError(Exception $exception, $additionalInfo = '') {
        $additionalInfo = trim("$additionalInfo");

        //Поставим защиту от двойного дампинга ошибки
        $SafePropName = 'ps_ex_dumped';
        if (property_exists($exception, $SafePropName)) {
            return; //---
        }
        $exception->$SafePropName = true;

        try {
            $INFO[] = 'SERVER: ' . (isset($_SERVER) ? print_r($_SERVER, true) : '');
            $INFO[] = 'REQUEST: ' . (isset($_REQUEST) ? print_r($_REQUEST, true) : '');
            $INFO[] = 'SESSION: ' . (isset($_SESSION) ? print_r($_SESSION, true) : '');
            $INFO[] = 'FILES: ' . (isset($_FILES) ? print_r($_FILES, true) : '');

            if ($additionalInfo) {
                $INFO[] = "ADDITIONAL:\n$additionalInfo\n";
            }

            $INFO[] = 'STACK:';
            $INFO[] = ExceptionHelper::formatStackFile($exception);

            $original = ExceptionHelper::extractOriginal($exception);
            $fname = get_file_name($original->getFile());
            $fline = $original->getLine();

            $DM = DirManager::autogen('exceptions');
            if ($DM->getDirContentCnt() >= EXCEPTION_MAX_FILES_COUNT) {
                $DM->clearDir();
            }
            $DM->getDirItem(null, PsUtil::fileUniqueTime() . " [$fname $fline]", 'err')->putToFile(implode("\n", $INFO));
        } catch (Exception $ex) {
            //Если в методе дампа эксепшена ошибка - прекращаем выполнение.
            die("Exception [{$exception->getMessage()}] dump error: [{$ex->getMessage()}]");
        }
    }

}

?>