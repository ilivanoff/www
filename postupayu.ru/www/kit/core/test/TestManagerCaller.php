<?php

/**
 * Класс, вызывающий методы TestManager при вызове через ajax.
 */
final class TestManagerCaller {

    /** Список методов, которые можно вызывать */
    private static $METHODS;

    /**
     * Список методов, доступных для вызова
     */
    public static function getMethodsList() {
        if (!AuthManager::isAuthorizedAsAdmin() || PsDefines::isProduction()) {
            return array();
        }
        if (!is_array(self::$METHODS)) {
            self::$METHODS = array();

            $methodNames = PsUtil::getClassMethods(TestManager::inst(), true, false, true, true);

            foreach ($methodNames as $name) {
                $method = new ReflectionMethod(TestManager::inst(), $name);

                $params['name'] = $name;
                $params['descr'] = implode("\n", StringUtils::parseMultiLineComments($method->getDocComment()));
                $params['params'] = array();

                /* @var $param ReflectionParameter */
                foreach ($method->getParameters() as $param) {
                    $params['params'][] = array(
                        'name' => $param->getName(),
                        'dflt' => $param->isDefaultValueAvailable() ? var_export($param->getDefaultValue(), true) : null
                    );
                }

                self::$METHODS[$name] = $params;
            }
        }
        return self::$METHODS;
    }

    /**
     * Вызов выполнения метода. Используется из ajax.
     */
    public static function execute($method, array $params) {
        check_condition(array_key_exists($method, self::getMethodsList()), "Method TestManager::$method cannot be called");

        PsUtil::startUnlimitedMode();

        PsLogger::inst('TestManager')->info("Method TestManager::$method called with params: " . array_to_string($params));

        $s = Secundomer::startedInst();
        call_user_func_array(array(TestManager::inst(), $method), $params);
        $s->stop();

        PsLogger::inst('TestManager')->info("Call done in {$s->getTotalTime()} seconds");
    }

}

?>