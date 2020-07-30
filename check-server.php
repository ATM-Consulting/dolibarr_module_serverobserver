<?php

// Action spécifique pour récupérer les instances
if(isset($_GET['action']) && $_GET['action'] == 'get-all-instance') {
    include 'get-all-instance.php';
    exit;
}

// Action spécifique pour récupérer les information d'une instance
if(isset($_GET['action']) && $_GET['action'] == 'get-info-instance') {
    include 'get-info-instance.php';
    exit;
}

include_once 'lib/serverobserver.lib.php';

$server = new stdClass();
$server->apiversion = '2.0';

$server->space = getSystemSize();
$server->php = getPHPInfos();
$server->mysql = getMySQLInfos();

print json_encode($server, JSON_PRETTY_PRINT);
