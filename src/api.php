<?php
	//ini_set('display_errors', TRUE);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: X-Requested-With, content-type');
	include_once '../config/config.php';
	include_once 'libs/DbConnection.php';
	include_once 'libs/Installer.php';
	include_once 'libs/manager.php';
	global $db;
	global $config;
	$db = new DbConnection();
	if(!$db->exists()){
		Installer::install();	
	}
	$manager = new Manager();
	$manager->__execute();
	echo $manager->output();
?>