<?php

/**
 * Get size of a directory on the server, in bytes
 * @param $dir	Absolute path of the directory to scan
 * @return int	Size of the diectory or -1 if $dir is not a directory
 */
function getDirSize($dir) {
	if(is_dir($dir)) {
		$cmd = 'du -sb ' . $dir;
		$res = shell_exec($cmd);

		return (int)$res;
	}

	return -1;
}

/**
 * Get informations about disk space
 * @param $dir		Directory to scan
 * @return stdClass	Data about total space, used, left and percentages
 */
function getSystemSize($dir=__DIR__) {
	$res = new stdClass();
	$res->bytes_total = disk_total_space($dir);
	$res->bytes_left = disk_free_space($dir);
	$res->bytes_used = $res->bytes_total - $res->bytes_left;
	$res->percent_used = round($res->bytes_used * 100 / $res->bytes_total);
	$res->percent_left = 100 - $res->percent_used;

	return $res;
}

/**
 * Get PHP informations on the server
 * @return stdClass	Data about PHP on the server
 */
function getPHPInfos() {
	$cmd = 'php -v';
	$res = shell_exec($cmd);

	$php = new stdClass();
	$php->version = $res;

	return $php;
}

/**
 * Get MySQL informations on the server
 * @return stdClass	Data about MySQL on the server
 */
function getMySQLInfos() {
	$cmd = 'mysql --version';
	$res = shell_exec($cmd);

	$mysql = new stdClass();
	$mysql->version = $res;

	return $mysql;
}
