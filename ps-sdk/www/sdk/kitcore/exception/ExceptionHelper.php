<?php

/**
 * Вспомогательные утилиты для форматирования эксепшенов
 *
 * @author azazello
 */
final class ExceptionHelper {

    /**
     * Стек ошибки начинается со второго шага возникновения ошибки. Положим в начало самый первый шаг.
     */
    private static function extendTrace(Exception $ex) {
        $trace = $ex->getTrace();
        array_unshift($trace, array('file' => $ex->getFile(), 'line' => $ex->getLine()));
        return $trace;
    }

    /**
     * Метод возвращает оригинальный эксепшн
     */
    public static function extractOriginal(Exception $ex) {
        return $ex->getPrevious() instanceof Exception ? self::extractOriginal($ex->getPrevious()) : $ex;
    }

    /**
     * Метод каждое сообщение из стека оборачивает в див с отступом, соответствующим номеру сообщения.
     * 0# C:\www\postupayu.ru\www\kit\content\ContentHelper.php (39)
     *  1# C:\www\postupayu.ru\www\resources\folded\pagebuilders\basic\basic.php (53)
     *   2# C:\www\postupayu.ru\www\kit\folded\pagebuilders\AbstractPageBuilder.php (55)
     */
    private static function formatTraceMessagesHtml(Exception $ex) {
        $stack = '';
        foreach (self::extendTrace($ex) as $num => $stackItem) {
            $params['style']['padding-left'] = ($num * 5) . 'px';
            $file = value_Array(array('file', 'class'), $stackItem);
            $line = value_Array('line', $stackItem);
            $stack .= PsHtml::div($params, "$num# $file ($line)");
        }
        return $stack;
    }

    /**
     * Метод форматирует скек, добовляя к нему предыдущий эксепшн, если есть.
     */
    public static function formatStackHtml(Exception $ex) {
        $prev = $ex->getPrevious() instanceof Exception ? $ex->getPrevious() : null;
        return
                PsHtml::div(array('class' => 'message'), get_class($ex) . ': ' . $ex->getMessage()) .
                PsHtml::div(array('class' => 'stack'), self::formatTraceMessagesHtml($ex)) .
                ($prev ? PsHtml::div(array('class' => 'prevoius'), self::formatStackHtml($prev)) : '');
    }

    /**
     * Метод каждое сообщение из стека оборачивает в див с отступом, соответствующим номеру сообщения.
     * 0# C:\www\postupayu.ru\www\kit\content\ContentHelper.php (39)
     *  1# C:\www\postupayu.ru\www\resources\folded\pagebuilders\basic\basic.php (53)
     *   2# C:\www\postupayu.ru\www\kit\folded\pagebuilders\AbstractPageBuilder.php (55)
     */
    private static function formatTraceMessagesFile(Exception $ex) {
        $stack = '';
        foreach (self::extendTrace($ex) as $num => $stackItem) {
            $file = value_Array(array('file', 'class'), $stackItem);
            $line = value_Array('line', $stackItem);
            $stack .= pad_left('', $num * 2, ' ') . "$num# $file ($line)";
            $stack .= "\n";
        }
        return $stack;
    }

    /**
     * Метод форматирует скек, добовляя к нему предыдущий эксепшн, если есть.
     */
    public static function formatStackFile(Exception $ex, $prev = false) {
        $STACK[] = '+' . ($prev ? '' : '') . get_class($ex) . ': ' . $ex->getMessage();
        $STACK[] = self::formatTraceMessagesFile($ex);
        if ($ex->getPrevious() instanceof Exception) {
            $STACK[] = self::formatStackFile($ex->getPrevious(), true);
        }
        return implode("\n", $STACK);
    }

}

?>
