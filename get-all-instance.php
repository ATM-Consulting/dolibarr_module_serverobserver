<?php

// Vérification paramètres
if(!isset($_GET['hash'])) exit('Missing parameter');
if(!isset($_GET['time'])) exit('Missing parameter');

// Inclusion fichier conf
$res = @include 'config.php';
if(!$res) exit("Missing config file");

// Vérification token
$hashToCheck = $_GET['hash'];
$tokenTime = $_GET['time'];
$hash = md5($token . $tokenTime);
if($hash != $hashToCheck) exit('Invalid hash');

// Récupération de la liste des instances
$content = scandir($path);

$instances = array();
foreach ($content as $dir) {
    if($dir != '.' && $dir != '..' && is_dir($path.$dir)) {
        $instances[] = $dir;
    }
}

print json_encode($instances, JSON_PRETTY_PRINT);