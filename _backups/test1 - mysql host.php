<?

include("../config.php");

$user_n_host		= mysql_result(mysql_query("SELECT VERSION() as version, USER() as user"), 0, 'user');
echo			$server	= substr($user_n_host, strpos($user_n_host, '@')+1, strlen($user_n_host));


