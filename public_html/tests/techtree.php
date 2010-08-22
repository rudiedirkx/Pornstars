<?

include("../config.php");

echo "<title>TESTS - TechTree</title>";
echo "<pre>";

echo "<table border=1 cellpadding=5 cellspacing=0 width=100%>";
$q = mysql_query("SELECT * FROM $TABLE[r_d] WHERE nodig='';");
while ($a = mysql_fetch_assoc($q))
{
	echo "<tr><td>(0) ".$a['naam']."</td>";
	$q2 = mysql_query("SELECT * FROM $TABLE[r_d] WHERE nodig LIKE '%".$a['soort']."%' LIMIT 1;");
	while ($b = mysql_fetch_assoc($q2))
	{
		echo "<td>(1) ".$b['naam']."</td>";
		$q3 = mysql_query("SELECT * FROM $TABLE[r_d] WHERE nodig LIKE '%".$b['soort']."%' LIMIT 1;");
		while ($c = mysql_fetch_assoc($q3))
		{
			echo "<td>(2) ".$c['naam']."</td>";
		}
	}
	echo "</tr>";
}
echo "</table>";

echo "<br /><hr color=red /><br />";

echo "";

// First, let's get all constructions
$constructions = Array();
$c = PSQ("SELECT * FROM $TABLE[r_d] WHERE SUBSTRING(soort,1,2) = 'c_' ORDER BY id ASC;");
while ($i = mysql_fetch_assoc($c))
{
	$tmp = Array();
	// De array van deze r_d maken
	$constructions[$i['soort']] = & $tmp;

	// En de array vullen
	$tmp[] = $i['naam'];
	$tmp[] = $i['uitleg'];
	$tmp[] = $i['eta'];
	$tmp[] = $i['metal'];
	$tmp[] = $i['crystal'];
	$tmp[] = $i['energy'];
	$tmp[] = $i['nodig'];
	$tmp[] = $i['excludes'];

	unset($tmp);
}

// Next, all researches
$researches = Array();
$c = PSQ("SELECT * FROM $TABLE[r_d] WHERE SUBSTRING(soort,1,2) = 'r_' ORDER BY id ASC;");
while ($i = mysql_fetch_assoc($c))
{
	$tmp = Array();
	// De array van deze r_d maken
	$researches[$i['soort']] = & $tmp;

	// En de array vullen
	$tmp[] = $i['naam'];
	$tmp[] = $i['uitleg'];
	$tmp[] = $i['eta'];
	$tmp[] = $i['metal'];
	$tmp[] = $i['crystal'];
	$tmp[] = $i['energy'];
	$tmp[] = $i['nodig'];
	$tmp[] = $i['excludes'];

	unset($tmp);
}

print_r($researches);
print_r($constructions);

?>