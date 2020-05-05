<?php

use rdx\ps\Prefs;

$iUtcStartTime = microtime(true);

define( 'PARENT_SCRIPT_NAME',	$_SERVER['SCRIPT_NAME'] );
define( 'BASEPAGE', 			basename($_SERVER['PHP_SELF']) );

define( 'PROJECT_RUNTIME',		dirname(dirname(__FILE__)).'/runtime' );
define( 'PROJECT_LOGS',			PROJECT_RUNTIME . '/logs' );


session_start();
error_reporting(2047);

$_adminPasswords = ['$2y$10$FTKrlmfnxEWzokWQaQvr5.K56d.qrwNSa.2/nei6esxseJbIQBDlu'];

function validateAdminPassword( $password ) {
	global $_adminPasswords;
	foreach ( $_adminPasswords as $valid) {
		if ( password_verify($password, $valid) ) {
			return true;
		}
	}
}

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../vendor/autoload.php';

$db = db_mysql::open([
	'user' => PROJECT_DB_USER,
	'pass' => PROJECT_DB_PASS,
	'db' => PROJECT_DB_NAME,
]);
$db->returnAffectedRows = true;

/** @var Prefs $g_prefs */
$g_prefs = Prefs::first('1 ORDER BY id DESC');
if ( !$g_prefs ) {
	exit('No current game exists...');
}

$ticktime = $g_prefs->last_tick;
$tickdif = time() - $ticktime;

require_once 'inc.config_gamevalues.php';
