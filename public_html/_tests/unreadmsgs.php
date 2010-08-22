<?

require("../connect.php");

$q = mysql_query("SELECT COUNT(id) AS num_all,COUNT(seen) AS num_seen FROM mail;") or die(mysql_error());
$num_all = mysql_result($q,0,'num_all');
$num_new = $num_all-mysql_result($q,0,'num_seen');
echo "Messages $num_all/$num_new";