<?php

/**
 * Различные утилитные методы
 */
final class PsUtil {

    /**
     * Запускает режим, не ограниченный по времени и который нельзя прервать.
     * Необходим для выполнения долгих операций.
     */
    private static $isUnlimitedMode = false;

    public static function startUnlimitedMode() {
        if (self::$isUnlimitedMode) {
            return; //---
        }
        self::$isUnlimitedMode = true;
        ignore_user_abort(true);
        set_time_limit(0);
    }

    /**
     * Функция возвращает уникальную метку времени, пригодную для имени файла:
     * 2014-09-15_14-13-55_pos (дата_время_уникальность в рамках секунды).
     */
    public static function fileUniqueTime($addUnique = true) {
        return date(DF_FILE) . ($addUnique ? '_' . PsRand::string(4, false, false) : '');
    }

    /**
     * Метод возвращает название класса
     */
    public static function getClassName($class) {
        return check_condition(is_object($class) ? get_class($class) : get_file_name($class), 'Illegal class name');
    }

    /**
     * Метод возвращает информацию о первом вызове класса в стеке.
     */
    public static function getClassFirstCall($class) {
        check_condition($class, 'Не передан класс для поиска в вызове');
        check_condition($class != __CLASS__, 'Класс ' . __CLASS__ . ' не может быть использован для поиска вызова');
        $found = null;
        foreach (debug_backtrace(0) as $item) {
            $curClass = array_get_value('class', $item);
            if ($curClass == __CLASS__) {
                if (is_array($found)) {
                    break; //--
                }
                continue; //---
            }

            if ($curClass == $class) {
                $found = $item;
                continue; //---
            }

            if (is_array($found)) {
                break; //--
            }
        }
        return check_condition($found, "Класс $class не вызывался");
    }

    /**
     * Получает константы класса. Есть возможность получить только те, что ограничены префиксами.
     */
    private static $CLASS_CONSTS = array();

    public static function getClassConsts($class, $prefix = null, &$fromCache = false) {
        $prefix = trim($prefix);
        $key = self::getClassName($class) . ($prefix ? ':P:' . $prefix : '');

        $fromCache = array_key_exists($key, self::$CLASS_CONSTS);
        if (!$fromCache) {
            self::$CLASS_CONSTS[$key] = array();
            $class = new ReflectionClass($class);
            foreach ($class->getConstants() as $name => $val) {
                if (!$prefix || starts_with($name, $prefix)) {
                    self::$CLASS_CONSTS[$key][$name] = $val;
                }
            }
        }

        return self::$CLASS_CONSTS[$key];
    }

    /**
     * Метод утверждает, что класс не содержит констант с повторяющимися значениями
     */
    public static function assertClassHasDifferentConstValues($class, $prefix = null) {
        $result = array();
        $conflicts = false;
        foreach (self::getClassConsts($class, $prefix) as $constName => $constVal) {
            $conflicts = $conflicts || array_key_exists($constVal, $result);
            $result[$constVal][] = $constName;
        }
        if (!$conflicts) {
            return; //---
        }
        foreach ($result as $constVal => $constNames) {
            if (count($constNames) <= 1) {
                unset($result[$constVal]);
                continue;
            }
        }
        raise_error('Класс ' . self::getClassName($class) . ' содержит константы с повторяющимися значениями: ' . array_to_string($result, true));
    }

    /**
     * Метод возвращает массив названий констант класса с заданным значением.
     */
    public static function getClassConstsByValue($class, $prefix, $value) {
        $result = array();
        foreach (self::getClassConsts($class, $prefix) as $constName => $constVal) {
            if ($constVal == $value) {
                $result[] = $constName;
            }
        }
        return $result;
    }

    /**
     * Метод возвращает название константы класса с заданным значением.
     * Если найдётся более одной такой константы, будет выброшена ошибка.
     * Поведение в случае не обнаружения такой константы зависит от $canNotExists.
     */
    public static function getClassConstByValue($class, $prefix, $value, $canNotExists = false) {
        $consts = self::getClassConstsByValue($class, $prefix, $value);
        $className = self::getClassName($class);
        $constsCnt = count($consts);
        switch ($constsCnt) {
            case 0:
                if ($canNotExists) {
                    return null;
                }
                raise_error("Класс $className не содержит константы со значением [" . var_export($value, true) . ']' . ($prefix ? ' и префиксом [' . $prefix . ']' : ''));
                break;
            case 1:
                return $consts[0];
            default:
                raise_error("Класс $className содержит $constsCnt констант с значением [" . var_export($value, true) . ']' . ($prefix ? ' и префиксом [' . $prefix . ']' : ''));
        }
    }

    /**
     * Метод утверждает, что в классе есть константа с заданным значением.
     */
    public static function assertClassHasConstVithValue($class, $prefix, $value) {
        PsUtil::getClassConstByValue($class, $prefix, $value);
        return $value;
    }

    /**
     * Базовый метод для безопасного преобразования чего угодно в строку
     */
    public static function toString($item) {
        $type = gettype($item);
        switch ($type) {
            case PsConst::PHP_TYPE_STRING:
                return $item;
            case PsConst::PHP_TYPE_INTEGER:
            case PsConst::PHP_TYPE_DOUBLE:
            case PsConst::PHP_TYPE_FLOAT:
                return "$item";
            case PsConst::PHP_TYPE_BOOLEAN:
            case PsConst::PHP_TYPE_NULL:
                return var_export($item, true);
            case PsConst::PHP_TYPE_ARRAY:
                return print_r($item, true);
            case PsConst::PHP_TYPE_OBJECT:
                return get_class($item);
            case PsConst::PHP_TYPE_RESOURCE:
            case PsConst::PHP_TYPE_UNKNOWN:
                return $type;
        }
        raise_error("Unknown item type: [$type]");
    }

    /**
     * Определяет константы класса в define
     */
    public static function defineClassConsts($class, $definePrefix = null, $classConstsPrefix = null) {
        $definePrefix = $definePrefix ? $definePrefix : self::getClassName($class);
        foreach (self::getClassConsts($class, $classConstsPrefix) as $constName => $constValue) {
            $constName = $definePrefix . '_' . $constName;
            if (!defined($constName)) {
                define($constName, $constValue);
            }
        }
    }

    /**
     * Выполняет редирект на указанный URL
     */
    public static function redirectTo($url) {
        header("Location: $url");
        die;
    }

    /**
     * Выполняет редирект на ткущий URL
     */
    public static function redirectToSelf() {
        self::redirectTo(PsUrl::current());
    }

    private static $CLASS_METHODS = array();

    /**
     * Получает список методов класса.
     * Параметры передаются по порядку: public static final
     */
    public static function getClassMethods($class, $public = true, $static = null, $final = null, $checkOwned = true, $skipClass = null) {
        $class = self::getClassName($class);

        $key = $class . '-' . var_export($public, true) . '-' . var_export($static, true) . '-' . var_export($final, true) . '-' . var_export($checkOwned, true) . '-' . var_export($skipClass, true);

        if (array_key_exists($key, self::$CLASS_METHODS)) {
            return self::$CLASS_METHODS[$key];
        }
        self::$CLASS_METHODS[$key] = array();

        $rc = new ReflectionClass($class);

        /* @var $method ReflectionMethod */
        foreach ($rc->getMethods() as $method) {
            if ($final !== null && $final !== !!$method->isFinal()) {
                continue;
            }

            if ($static !== null && $static !== !!$method->isStatic()) {
                continue;
            }

            if ($public !== null && $public !== !!$method->isPublic()) {
                continue;
            }

            if ($checkOwned && $method->getDeclaringClass()->getName() != $rc->getName()) {
                //Метод определён не в этом классе
                continue;
            }

            if ($skipClass !== null && $method->getDeclaringClass()->getName() == $skipClass) {
                //Метод определён в классе, который мы пропускаем
                continue;
            }

            self::$CLASS_METHODS[$key][] = $method->getName();
        }
        return self::$CLASS_METHODS[$key];
    }

    /**
     * Получает методы класса, которые выглядят как контастанты, то есть
     * public static final function METHOD.
     * Есть возможность получить только те, что ограничены префиксами.
     */
    private static $CLASS_CONST_LIKE_METHODS = array();

    public static function getClassConstLikeMethods($class, $prefix = null, $checkOwned = true) {
        $prefix = trim($prefix);
        $key = self::getClassName($class) . ($prefix ? ':P:' . $prefix : '');

        if (!array_key_exists($key, self::$CLASS_CONST_LIKE_METHODS)) {
            self::$CLASS_CONST_LIKE_METHODS[$key] = array();
            foreach (self::getClassMethods($class, true, true, true, $checkOwned) as $name) {
                if (ps_is_upper($name) && (!$prefix || starts_with($name, $prefix))) {
                    self::$CLASS_CONST_LIKE_METHODS[$key][] = $name;
                }
            }
        }

        return self::$CLASS_CONST_LIKE_METHODS[$key];
    }

    private static $CLASS_PROPERTIES = array();

    /**
     * Получает список свойств класса.
     * Параметры передаются по порядку: public static
     */
    public static function getClassProperties($class, $public = true, $static = null, $checkOwned = true) {
        $class = self::getClassName($class);

        $key = $class . '-' . var_export($public, true) . '-' . var_export($static, true) . '-' . var_export($checkOwned, true);

        if (array_key_exists($key, self::$CLASS_PROPERTIES)) {
            return self::$CLASS_PROPERTIES[$key];
        }
        self::$CLASS_PROPERTIES[$key] = array();

        $rc = new ReflectionClass($class);

        /* @var $property ReflectionProperty */
        foreach ($rc->getProperties() as $property) {
            if ($static !== null && $static !== !!$property->isStatic()) {
                continue;
            }

            if ($public !== null && $public !== !!$property->isPublic()) {
                continue;
            }

            if ($checkOwned && $property->getDeclaringClass()->getName() != $rc->getName()) {
                //Метод определён не в этом классе
                continue;
            }

            self::$CLASS_PROPERTIES[$key][] = $property->getName();
        }
        return self::$CLASS_PROPERTIES[$key];
    }

    /**
     * Метод утверждает, что переданный класс существует
     */
    public static function assertClassExists($class_name) {
        if (!class_exists($class_name)) {
            raise_error("Класс $class_name не существует");
        }
    }

    /**
     * Метод утверждает, что переданный интерфейс существует
     */
    public static function assertInterfaceExists($interface_name) {
        if (!interface_exists($interface_name)) {
            raise_error("Интерфейс $interface_name не существует");
        }
    }

    /**
     * Метод утверждает, что переданный класс содержит указанный метод
     */
    public static function assertMethodExists($object, $method) {
        if (!method_exists($object, $method)) {
            raise_error('Метод ' . self::getClassName($object) . "::$method не существует");
        }
    }

    /**
     * Метод утверждает, что переданный класс $class имплементирует/наследует $parentClassOrInterface
     */
    public static function assertInstanceOf($class, $parentClassOrInterface) {
        if (!self::isInstanceOf($class, $parentClassOrInterface)) {
            raise_error('Класс ' . self::getClassName($class) . ' не является наследником ' . self::getClassName($parentClassOrInterface));
        }
        return $class;
    }

    /**
     * @return ReflectionClass
     */
    public static function newReflectionClass($class, $mustExists = true) {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException $ex) {
            if ($mustExists) {
                throw $ex;
            }
            return null;
        }
    }

    /**
     * @return ReflectionMethod
     */
    public static function newReflectionMethod($class, $method, $mustExists = true) {
        try {
            return new ReflectionMethod($class, $method);
        } catch (ReflectionException $ex) {
            if ($mustExists) {
                throw $ex;
            }
            return null;
        }
    }

    /**
     * Проверяет, является ли $class наследником $parent
     */
    public static function isInstanceOf($class, $parent) {
        $class = self::getClassName($class);
        $parent = self::getClassName($parent);
        return $class == $parent || self::newReflectionClass($class)->isSubclassOf($parent);
    }

    /**
     * Функция для мёрджа файлов ini с обработкой сложной логики
     * мёрджа свойств.
     */
    public static function mergeIniFiles(array $ini1 = null, array $ini2 = null, array $ini3 = null) {
        $files = func_get_args();

        $result = null;

        #foreach files
        foreach ($files as $ini) {
            if ($ini === null) {
                continue; //---
            }

            check_condition(is_array($ini), 'Arrays expected in ' . __FUNCTION__);

            if ($result === null) {
                $result = $ini;
                continue; //---
            }

            #foreach ini file
            foreach ($ini as $section => $properties) {
                /*
                 * Если секция ранее не регистрировалась - регистрируем
                 */
                if (!array_key_exists($section, $result)) {
                    $result[$section] = $properties;
                    continue; //---
                }
                /*
                 * Мы вынуждены будем пробежаться по ключам и сложным образом смёрджить их
                 */
                foreach ($properties as $key => $value) {
                    if (!array_key_exists($key, $result[$section])) {
                        //Такого ключа ранее небыло, добавляем
                        $result[$section][$key] = $value;
                        continue; //---
                    }
                    if (!is_array($value) || !is_array($result[$section][$key])) {
                        //Примитивные ключи добавляем сразу
                        $result[$section][$key] = $value;
                        continue; //---
                    }
                    //В исходном и целевом ini файле - массивы, нужно их смёрджить
                    $result[$section][$key] = array_merge($result[$section][$key], $value);
                }
            }
            #foreach ini file
        }
        #foreach files

        return $result; //---
    }

    /**
     * Метод выбрасывает ошибку, заменяя {} последовательно на параметры
     */
    public static function raise($msg, $param1 = null, $param2 = null) {
        $params = func_get_args();
        if (count($params) > 1) {
            unset($params[0]);
            $msg = PsStrings::replaceWithParams('{}', $msg, $params);
        }
        raise_error($msg);
    }

}

?>