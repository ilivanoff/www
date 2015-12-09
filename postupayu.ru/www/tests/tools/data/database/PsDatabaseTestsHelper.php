<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PsDatabaseTestsHelper
 *
 * @author azazello
 */
class PsDatabaseTestsHelper {

    private static function assertPrepeared($tableExists = null) {
        PsConnectionPool::assertConnectiedTo(PsConnectionParams::sdkTest());
        foreach (to_array($tableExists) as $table) {
            check_condition(PsTable::exists($table), "Таблица $table не существует");
        }
    }

    public static function ps_test_data_load_Fill($minKey = 1, $maxKey = 4) {
        $table = 'ps_test_data_load';
        self::assertPrepeared($table);
        PSDB::update("delete from $table");
        for ($i = $minKey; $i <= $maxKey; $i++) {
            PSDB::update("insert into $table (v_key, v_value) values (?, ?)", array("key$i", "val$i"));
        }
    }

}

?>
