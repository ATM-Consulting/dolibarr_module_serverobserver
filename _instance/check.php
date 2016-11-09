<?php

	if(is_file('../main.inc.php'))$dir = '../';
	else  if(is_file('../../../main.inc.php'))$dir = '../../../';
	else  if(is_file('../../../../main.inc.php'))$dir = '../../../../';
	else  if(is_file('../../../../../main.inc.php'))$dir = '../../../../../';
	else $dir = '../../';

	require $dir.'master.inc.php';

	$result = new stdClass;
	
	$result->dolibarr = new stdClass;
	$result->dolibarr->version = DOL_VERSION;
	$result->dolibarr->version1 = $conf->global->MAIN_VERSION_LAST_INSTALL;
	$result->dolibarr->theme = $conf->theme;
	
	$result->dolibarr->path=new stdClass;
	$result->dolibarr->path->http = dol_buildpath('/',2); 
	$result->dolibarr->path->relative = dol_buildpath('/',1);
	$result->dolibarr->path->absolute = dol_buildpath('/',0);
	
	$result->dolibarr->data = new stdClass;
	$result->dolibarr->data->path = DOL_DATA_ROOT;
	$result->dolibarr->data->size = _dir_size(DOL_DATA_ROOT);
	
	$result->db=new stdClass;
	$result->db->host = $dolibarr_main_db_host;
	$result->db->name = $dolibarr_main_db_name;
	$result->db->user = $dolibarr_main_db_user;
	$result->db->type = $dolibarr_main_db_type;
	
	$result->user=new stdClass;
	$result->user->all = _nb_user();
	$result->user->active = _nb_user(true);
	
	$result->module = new stdClass;
	
	$result->module = _module_active();
	
	//var_dump($result);
	
	echo json_encode($result);
	
function _module_active() {
	
	global $db;
	
	$sql="SELECT name FROM ".MAIN_DB_PREFIX."const WHERE name LIKE 'MAIN_MODULE_%' AND value=1 ORDER BY name";
	
	$res = $db->query($sql);
	$Tab=array();
	while($obj = $db->fetch_object($res)) {
		if(preg_match('/^MAIN_MODULE_([0-9A-Z]+)$/i',$obj->name,$reg)) {
			$name =ucfirst(strtolower($reg[1]));
			
			if(!in_array($name,$Tab)) $Tab[] = $name;
		}
	}
	
	return $Tab;
}	
	
function _dir_size($dir) {
	
	// taile en Mo
	
	$io = popen ( 'du -sm ' . $dir, 'r' );
    $size = fgets ( $io, 4096);
    $size = substr ( $size, 0, strpos ( $size, "\t" ) );
    pclose ( $io );
	
	return (int)$size;
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