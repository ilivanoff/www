<?php

/**
 * Класс для применения циклических подстановок
 *
 * PregReplaceCyclic::replace('/x/', 'a x b x c x a x b x c', array(1, 2)) = 'a 1 b 2 c 1 a 2 b 1 c'
 * PregReplaceCyclic::replace('/x/', 'a x b x c x a x b x c', array(1, 2, 3)) = 'a 1 b 2 c 3 a 1 b 2 c'
 * PregReplaceCyclic::replace('/x/', 'a x b x c x a x b x c', array(1, 2, 3, 4)) = 'a 1 b 2 c 3 a 4 b 1 c'
 * 
 * @author azazello
 */
final class PregReplaceCyclic {

    /**
     * Замены (индексированный массив)
     */
    private $tokens;

    /**
     * Текущий индекс
     */
    private $count = 0;

    /**
     * Текущий индекс
     */
    private $idx = 0;

    /**
     * @var PregReplaceCyclic Экземпляр
     */
    private static $inst;

    /**
     * Метод ищет в строке подстроки, удовлетворяющие шаблону и заменяет их по очереди на подстановки,
     * переданные в виде массива.  Поиск идёт по регулярному выражению!
     * 
     * @param string $pattern - шаблон
     * @param string $text - текст
     * @param array $tokens - массив подстановок
     * @return string
     */
    public static function replace($pattern, $text, array $tokens) {
        self::$inst = self::$inst ? self::$inst : new PregReplaceCyclic();
        self::$inst->tokens = check_condition($tokens, 'Не переданы элементы для замены');
        if (is_assoc_array($tokens)) {
            raise_error('Недопустим ассоциативный массив подстановок. Передан: ' . array_to_string($tokens, true));
        }
        self::$inst->idx = 0;
        self::$inst->count = count($tokens);
        return preg_replace_callback($pattern, array(self::$inst, '_replace'), $text);
    }

    /**
     * Внутренняя функция, вызываемая из preg_replace_callback
     */
    private function _replace() {
        $token = $this->tokens[$this->idx];
        if (++$this->idx >= $this->count) {
            $this->idx = 0;
        }
        return $token;
    }

}

?>