<?php

require_once('inc.config.php');

set_time_limit(0);
while ( true ) {
	prepSomeGameStuff();
	include(dirname(__FILE__).'/tickah.php');
	sleep((int)$GAMEPREFS['tickertime']);
}

?>
