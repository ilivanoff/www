<?php

function smarty_modifier_short_tr_info(PostContentProvider $postCP) {
    $fetchData = $postCP->getPostParams();

    /*
     * Обработаем данные плагинов
     */
    $PLUGIN_TESTINGS = array();

    $pluginsData = $fetchData->getPluginsData();
    foreach ($pluginsData as $pldata) {
        $ident = $pldata[0];
        $data = $pldata[1];
        //#1
        switch ($ident) {
            case PluginsManager::PLUGIN_TESTING:
                $testingId = $data;
                $PLUGIN_TESTINGS[] = array(
                    TestingBean::inst()->getTestingById($testingId),
                    TestingManager::getInstance()->getTestingResults($testingId));
                break;
        }
        //#1
    }

    PSSmarty::template('tr/short_info.tpl', array(
        'data' => $fetchData,
        'testings' => $PLUGIN_TESTINGS)
    )->display();
}

?>
