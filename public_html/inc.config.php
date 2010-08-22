<?php

$iUtcStartTime = microtime(true);

define( 'PARENT_SCRIPT_NAME',	$_SERVER['SCRIPT_NAME'] );
define( 'BASEPAGE', basename($_SERVER['PHP_SELF']) );


session_start();
error_reporting(2047);

$sessionname = 'PS_SESSION';
$prefix = '';

require_once('inc.connect.php');
require_once('inc.cls.json.php');


function prepSomeGameStuff() {
	global $GAMEPREFS, $ticktime, $tickdif;
	// TABLE `prefs` maken
	$gp = db_select('prefs', '1 ORDER BY id DESC LIMIT 1');
	if ( !$gp ) {
		exit('No current game exists...');
	}
	$GAMEPREFS = $gp[0];
	$GAMEPREFS['admins'] = array_map('intval', explode(',', $GAMEPREFS['admin_planet_ids']));

	$ticktime = $GAMEPREFS['last_tick'];
	$tickdif = time() - $ticktime;
}
prepSomeGameStuff();

require_once('inc.config_gamevalues.php');
require_once('inc.functions.php');

?>
