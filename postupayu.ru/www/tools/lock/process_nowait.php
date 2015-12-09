<?php

require_once '../ToolsResources.php';
$CALLED_FILE = __FILE__;

dolog('try to get lock');
$taken = PsLock::lock('mylock-nowait', false);
dolog('lock ' . ($taken ? 'taken' : 'not taken'));
if ($taken) {
    sleep(10);
    PsLock::unlock();
    dolog('lock released');
}
?>