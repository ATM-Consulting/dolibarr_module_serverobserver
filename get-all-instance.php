<?php

// Vérification paramètres
if(!isset($_GET['token'])) exit("Missing token");

// Inclusion fichier conf
$res = @include 'config.php';
if(!$res) exit("Missing config file");

// Vérification token
$tokenToCheck = $_GET['token'];
if($token != $tokenToCheck) exit('Invalid token');

// Récupération de la liste des instances
$content = scandir($path);

$instances = array();
foreach ($content as $dir) {
    if($dir != '.' && $dir != '..' && is_dir($path.$dir)) {
        $instances[] = $dir;
    }
}

print json_encode($instances, JSON_PRETTY_PRINT);