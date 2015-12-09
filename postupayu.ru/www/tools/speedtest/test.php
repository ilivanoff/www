<?php

require_once dirname(__DIR__) . '/ToolsResources.php';
$CALLED_FILE = __FILE__;

$DATE = date('Y-m-d H.i.s');

$sm = Secundomer::startedInst();

$HOST = getCmdParam(1); //postupayu.ru
$USE_SCENARIOS = getCmdParam(2) == 1; //true/false
$REQUESTS_CNT = 5; //Кол-во запросов

$DM = DirManager::inst(__DIR__);

dolog("Started, DATE: $DATE, HOST: $HOST, USE_SCENARIOS=$USE_SCENARIOS, REQUESTS_CNT=$REQUESTS_CNT.");

/*
 * Загружаем ссылки
 */
$HREFS = array();
foreach (NavigationManager::inst()->getRealHrefs() as $a) {
    $data = simplexml_load_string($a);
    $href = '' . $data['href'];
    $content = '' . $data[0];
    if ($href && $content) {
        $HREFS[$href] = $content;
    }
}

dolog('Hrefs list: ' . print_r($HREFS, true));

if (empty($HREFS)) {
    exit;
}

$TOTAL = 0;

function doTest() {
    global $HOST;
    global $TOTAL;
    global $HREFS;
    global $REQUESTS_CNT;

    $RESULTS = array();
    $j = 0;
    foreach ($HREFS as $href => $name) {
        ++$j;
        $href = ensure_starts_with($href, '/');
        $path = "http://$HOST$href";

        $sec = Secundomer::inst();
        for ($index = 0; $index <= $REQUESTS_CNT; $index++) {
            if ($index == 0) {
                //Пропустим первый вызов, на случай кеширования
                file_get_contents($path);
                continue;
            }
            $sec->start();
            file_get_contents($path);
            $sec->stop();
            ++$TOTAL;
        }
        dolog(pad_zero_left($j, 2) . '/' . count($HREFS) . " [$path] - " . $sec->getAverage() . ' (' . $sec->getTotalTime() . '/' . $sec->getCount() . ')');
        $RESULTS[$path] = str_replace('.', ',', round($sec->getAverage(), 2));
    }
    asort($RESULTS, SORT_DESC);
    $RESULTS = array_reverse($RESULTS, true);
    return $RESULTS;
}

/*
 * Начинаем работу
 */
$SC_RESULTS = array();

if ($USE_SCENARIOS) {
    $scenarios = $DM->getDirContent('scenarios', PsConst::EXT_TXT);
    dolog('Testing scenarios count: ' . count($scenarios));
    if (empty($scenarios)) {
        exit;
    }

    $PS_GLOBALS = PsGlobals::inst()->getPropsKeyValue();

    $i = 0;
    /** @var DirItem */
    foreach ($scenarios as $sc) {
        ++$i;
        $scName = $sc->getNameNoExt();
        dolog('');
        dolog('STARTED SCENARIO: ' . $scName);

        $props = $sc->getFileAsProps();
        $newGlobals = array_merge($PS_GLOBALS, $props);

        dolog('Set scenario global props: ' . print_r($props, true));
        dolog('Full global props: ' . print_r($newGlobals, true));

        PsGlobals::inst()->updateProps($props);

        dolog($i . ' scenario of ' . count($scenarios));

        $RESULTS = doTest();

        dolog('Test finished, restoring globals.');
        PsGlobals::inst()->updateProps($PS_GLOBALS);

        $SC_RESULTS[$scName] = array('res' => $RESULTS, 'props' => $newGlobals);
    }
} else {
    $RESULTS = doTest();
    $SC_RESULTS = array('res' => $RESULTS);
}


$sm->stop();

dolog('RESULTS: ' . print_r($SC_RESULTS, true));

$params['time'] = str_replace('.', ',', round($sm->getTotalTime(), 2));
$params['total'] = $TOTAL;
$params['host'] = $HOST;
$params['rqcnt'] = $REQUESTS_CNT;
$params['result'] = $SC_RESULTS;
$params['usesc'] = $USE_SCENARIOS;


/*
 * Сохраняем в файл
 */
saveResult2Html('speedtest.tpl', $params, __DIR__, $HOST . '_' . $DATE);
?>