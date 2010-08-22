<?php

require_once('inc.db_mysql.php');

define('MYSQL_HOST',		'localhost');
define('MYSQL_USER',		'usager');
define('MYSQL_PASS',		'usager');
define('MYSQL_DATABASE',	'pornstars');

db_set(db_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS,MYSQL_DATABASE));


