<?php

include_once '../lib/serverobserver.lib.php';

$server = new stdClass();
$server->apiversion = '1.0';

$server->space = getSystemSize();
$server->php = getPHPInfos();
$server->mysql = getMySQLInfos();

print json_encode($server);
