<?php

/**
 * Утилитные методы для работы со строками
 * @author azazello
 */
final class PsStrings {

    /**
     * Метод ищет в строке подстроки, удовлетворяющие шаблону и заменяет их по очереди на подстановки,
     * переданные в виде массива. Поиск идёт по регулярному выражению!
     * 
     * @param string $pattern - шаблон
     * @param string $text - текст
     * @param array $tokens - массив подстановок
     * @return string
     */
    public static function pregReplaceCyclic($pattern, $text, array $tokens) {
        return PregReplaceCyclic::replace($pattern, $text, $tokens);
    }

    /**
     * Метод заменяет подстроку $delimiter в строке $text на элементы из массива 
     * подстановок $params.
     * 
     * @param string $delimiter - элемент для поиска
     * @param string $text - текст, в котором производится поиск
     * @param array $params - элементы для замены
     * @param bool $checkCount - признак, проверять ли совпадение кол-ва разделителей в строке и элементов для замены
     * @return string
     */
    public static function replaceWithParams($delimiter, $text, array $params, $checkCount = false) {
        $paramsCount = count($params);
        if (!$paramsCount && !$checkCount) {
            //Выходим, если параметры не переданы и нам не нужно проверять совпадение кол-ва параметров с кол-вом разделителей
            return $text;
        }
        //Разделим текст на кол-во элеметнов, плюс один
        $tokens = explode($delimiter, $text, $paramsCount + 2);
        $tokensCount = count($tokens);
        if ($checkCount) {
            check_condition($paramsCount == ($tokensCount - 1), "Не совпадает кол-во элементов для замены. Разделитель: `$delimiter`. Строка: `$text`. Передано подстановок: $paramsCount.");
        }

        if ($tokensCount == 0 || $tokensCount == 1) {
            //Была передана пустая строка? Вернём её.
            return $text;
        }

        $idx = 0;
        $result[] = $tokens[$idx];
        foreach ($params as $param) {
            if (++$idx >= $tokensCount) {
                break;
            }
            $result[] = $param;
            $result[] = $tokens[$idx];
        }
        while (++$idx < $tokensCount) {
            $result[] = $delimiter;
            $result[] = $tokens[$idx];
        }

        return implode('', $result);
    }

    /**
     * Заменяет последовательно {} на аргументы вызова функции
     * @param type $msg - сообщение
     * @param type $param1 - параметр 1
     * @param type $param2 - параметр 2
     * @param type $param3 - параметр 3
     * @return type
     */
    public static function replaceWithBraced($msg = '', $param1 = null, $param2 = null, $param3 = null) {
        $params = func_get_args();
        if (count($params) > 1) {
            unset($params[0]);
            $msg = PsStrings::replaceWithParams('{}', $msg, $params);
        }
        return $msg;
    }

    /**
     * Заменяет в строке ключи, переданные в ассоциативном массиве
     */
    public static function replaceMap($string, $params, $prefix = '', $suffix = '') {
        foreach ($params as $search => $replace) {
            $string = str_replace($prefix . $search . $suffix, $replace, $string);
        }
        return $string;
    }

    /**
     * Заменяет вхождения ключей, обёрнутых в фигурные скобки
     */
    public static function replaceMapBracedKeys($string, array $params) {
        return self::replaceMap($string, $params, '{', '}');
    }

}

?>