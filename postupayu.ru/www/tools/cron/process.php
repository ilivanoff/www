<?php

require_once '../ToolsResources.php';
$CALLED_FILE = __FILE__;

dolog(__FILE__ . ' called in ' . time());
file_append_contents(file_path(__DIR__, 'called.log'), time() . "\n");
?>