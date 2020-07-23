<?php

// Vérification paramètres
if(!isset($_GET['token'])) exit('Missing token');
if(!isset($_GET['instance_name'])) exit('Missing instance name');

// Inclusion fichier conf
$res = @include 'config.php';
if(!$res) exit("Missing config file");

// Vérification token
$tokenToCheck = $_GET['token'];
if($token != $tokenToCheck) exit('Invalid token');

// Vérification chemin instance
$instance_name = $_GET['instance_name'];
$instance_path = $path . $instance_name . '/dolibarr/htdocs/';

if(!is_file($instance_path . 'master.inc.php')) exit('Wrong instance path : '.$instance_path);

// Inclusion du master.inc et récupération des informations
require $instance_path . 'master.inc.php';
include_once 'lib/serverobserver.lib.php';

$instance = new stdClass;

$instance->apiversion = '1.0';

$instance->dolibarr = new stdClass;
$instance->dolibarr->version = DOL_VERSION;
$instance->dolibarr->version1 = $conf->global->MAIN_VERSION_LAST_INSTALL;
$instance->dolibarr->theme = $conf->theme;

$instance->dolibarr->path=new stdClass;
$instance->dolibarr->path->http = dol_buildpath('/',2);

$instance->dolibarr->data = new stdClass;
$instance->dolibarr->data->path = DOL_DATA_ROOT;
$instance->dolibarr->data->size = getDirSize($instance->dolibarr->data->path);

$instance->dolibarr->htdocs=new stdClass;
$instance->dolibarr->htdocs->path = DOL_DOCUMENT_ROOT;
$instance->dolibarr->htdocs->size = getDirSize($instance->dolibarr->htdocs->path);

$instance->dolibarr->repertoire_client=new stdClass;
$instance->dolibarr->repertoire_client->path = dirname(dirname(DOL_DOCUMENT_ROOT));
$instance->dolibarr->repertoire_client->size = getDirSize($instance->dolibarr->repertoire_client->path);

$instance->db=new stdClass;
$instance->db->host = $dolibarr_main_db_host;
$instance->db->name = $dolibarr_main_db_name;
$instance->db->user = $dolibarr_main_db_user;
$instance->db->type = $dolibarr_main_db_type;

$instance->user=new stdClass;
$instance->user->all = _nb_user();
$instance->user->active = _nb_user(true);
$instance->user->date_last_login = _last_login() ;

$instance->module = new stdClass;

$instance->module = _module_active();

print json_encode($instance, JSON_PRETTY_PRINT);



/********************************
 * Specific functions to get informations about Dolibarr (Modules, Users, ...)
 ********************************/

function _module_active() {
    include_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';

    global $db, $conf;
    $modNameLoaded = array();
    $modulesdir = dolGetModulesDirs();

    foreach ($modulesdir as $dir)
    {
        $handle = @opendir($dir);
        if (is_resource($handle))
        {
            while (($file = readdir($handle)) !== false)
            {
                if (is_readable($dir.$file) && substr($file, 0, 3) == 'mod' && substr($file, dol_strlen($file) - 10) == '.class.php')
                {
                    $modName = substr($file, 0, dol_strlen($file) - 10);

                    if ($modName)
                    {
                        try
                        {
                            $res = include_once $dir.$file; // A class already exists in a different file will send a non catchable fatal error.
                            if (class_exists($modName))
                            {
                                try {
                                    $objMod = new $modName($db);
                                    $modNameLoaded[$modName] = new stdClass();
                                    $modNameLoaded[$modName]->dir = $dir;
                                    $modNameLoaded[$modName]->numero = $objMod->numero;
                                    $modNameLoaded[$modName]->version = $objMod->version;
                                    $modNameLoaded[$modName]->source = $objMod->isCoreOrExternalModule();
                                    $modNameLoaded[$modName]->gitinfos = _getModuleGitInfos($dir);
                                    $modNameLoaded[$modName]->editor_name = dol_escape_htmltag($objMod->getPublisher());
                                    $modNameLoaded[$modName]->editor_url = dol_escape_htmltag($objMod->getPublisherUrl());
                                    $modNameLoaded[$modName]->active = !empty($conf->global->{$objMod->const_name});
                                }
                                catch (Exception $e)
                                {
                                    dol_syslog("Failed to load ".$dir.$file." ".$e->getMessage(), LOG_ERR);
                                }
                            }
                            else
                            {
                                print "Warning bad descriptor file : ".$dir.$file." (Class ".$modName." not found into file)<br>";
                            }
                        }
                        catch (Exception $e)
                        {
                            dol_syslog("Failed to load ".$dir.$file." ".$e->getMessage(), LOG_ERR);
                        }
                    }
                }
            }
            closedir($handle);
        }
        else
        {
            dol_syslog("htdocs/admin/modules.php: Failed to open directory ".$dir.". See permission and open_basedir option.", LOG_WARNING);
        }
    }

    return $modNameLoaded;
}

function _getModuleGitInfos($dir) {
    global $donedir;
    if(isset($donedir[$dir])) return $donedir[$dir];

    $cmd = 'cd ' . $dir . ' && git status';
    $res = shell_exec($cmd);

    $branch = substr($res, strpos($res, 'On branch ')+10, strpos($res, "\n" )-10);
    $donedir[$dir] = new stdClass();
    $donedir[$dir]->branch = $branch;
    $donedir[$dir]->status = $res;

    return $donedir[$dir];
}

function _last_login() {
    global $db;

    $sql = "SELECT MAX(datelastlogin) as datelastlogin FROM ".MAIN_DB_PREFIX."user WHERE 1 ";
    $sql.=" AND statut=1 AND rowid>1"; // pas l'admin

    $res = $db->query($sql);

    $obj = $db->fetch_object($res);

    return $obj->datelastlogin;
}

function _nb_user($just_actif = false) {
    global $db;

    $sql = "SELECT count(*) as nb FROM ".MAIN_DB_PREFIX."user WHERE 1 ";

    if($just_actif) {
        $sql.=" AND statut=1 ";
    }

    $res = $db->query($sql);

    $obj = $db->fetch_object($res);

    return (int)$obj->nb;
}