<?php

class TestUtils {

    public static function testProductivity($callback, $count = 1000) {
        PsDefines::assertProductionOff(__CLASS__);
        check_condition(is_callable($callback), 'Передан некорректный callback для тестирования');
        $s = Secundomer::inst();
        for ($index = 0; $index < $count; $index++) {
            $s->start();
            call_user_func($callback);
            $s->stop();
        }
        return $s;
    }

}

?>