<?php

/**
 * Description of BasePsTest
 *
 * @author azazello
 */
abstract class BasePsTest extends PHPUnit_Framework_TestCase {

    const NOT_ALLOWED_STR = '_NOT_ALLOWED_';
    const NOT_ALLOWED_INT = -1;

    protected function setUp() {
        
    }

    public static function setUpBeforeClass() {
        
    }

    protected function tearDown() {
        
    }

    public static function tearDownAfterClass() {
        
    }

    protected static final function log($msg, $arg1 = '', $arg2 = '') {
        $num = func_num_args();
        if ($num > 1) {
            $params = func_get_args();
            unset($params[0]);
            $msg = PsStrings::replaceWithParams('{}', $msg, $params);
        }

        $class = get_called_class();
        $trace = PsUtil::getClassFirstCall($class);
        $name = $trace['function'];

        echo "\n$class::$name: " . trim($msg) . "\n";
    }

    /**
     * Метод прерывает выполнение, если небыло выброшено исключение
     */
    protected final function brakeNoException() {
        $this->fail('Exception is expected');
    }

    protected final function assertClassHasDifferentConstValues($class, $prefix = '') {
        PsUtil::assertClassHasDifferentConstValues($class, $prefix);
    }

}

?>