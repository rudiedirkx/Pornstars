<?php

$iUtcStartTime = microtime(true);

define( 'PARENT_SCRIPT_NAME',	$_SERVER['SCRIPT_NAME'] );
define( 'BASEPAGE', basename($_SERVER['PHP_SELF']) );

define( 'PROJECT_RUNTIME',	dirname(dirname(__FILE__)).'/runtime' );
define( 'PROJECT_LOGS',		PROJECT_RUNTIME.'/logs' );


session_start();
error_reporting(2047);

$sessionname = 'PS_SESSION';
$prefix = '';

require_once __DIR__ . '/env.php';
require_once PROJECT_DB_LOCATION . '/db_mysql.php';

$db = db_mysql::open([
	'user' => PROJECT_DB_USER,
	'pass' => PROJECT_DB_PASS,
	'db' => PROJECT_DB_NAME,
]);

function prepSomeGameStuff() {
	global $db, $GAMEPREFS, $ticktime, $tickdif;

	// TABLE `prefs` maken
	$gp = $db->select('prefs', '1 ORDER BY id DESC LIMIT 1')->first();
	if ( !$gp ) {
		exit('No current game exists...');
	}
	$GAMEPREFS = (array) $gp;
	$GAMEPREFS['admins'] = array_map('intval', explode(',', $GAMEPREFS['admin_planet_ids']));

	$ticktime = $GAMEPREFS['last_tick'];
	$tickdif = time() - $ticktime;
}

prepSomeGameStuff();

require_once 'inc.config_gamevalues.php';
require_once 'inc.functions.php';
