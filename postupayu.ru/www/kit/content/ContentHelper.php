<?php

class ContentHelper {

    /**
     * Метод безопасно получает контент.
     * В случае возникновения ошибки возвращает её стек.
     */
    public static function getContent($objOrTpl, $method = 'buildContent') {
        $isCallable = is_callable($objOrTpl);
        $isTpl = $objOrTpl instanceof Smarty_Internal_Template;
        if (!$isCallable && !$isTpl) {
            check_condition(is_object($objOrTpl), 'Not object passed to ' . __FUNCTION__);
            PsUtil::assertMethodExists($objOrTpl, $method);
        }

        $returned = null;
        $flushed = null;

        ob_start();
        ob_implicit_flush(false);
        try {
            if ($isCallable) {
                $returned = call_user_func($objOrTpl);
            } else if ($isTpl) {
                $returned = $objOrTpl->fetch();
            } else {
                $returned = $objOrTpl->$method();
            }
        } catch (Exception $ex) {
            ob_end_clean();
            return ExceptionHandler::getHtml($ex);
        }
        $flushed = ob_get_contents();
        ob_end_clean();

        return isEmpty($returned) ? (isEmpty($flushed) ? null : $flushed) : $returned;
    }

}

?>