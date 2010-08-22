<?php

require_once('inc.config.php');

_header();

?>

<b>These values are not exact!! There is always a random-factor.</b><br>
As you can see the number of roidscans needed is 1/chance.<br>
<br>

<table border=1 cellpadding=5 cellspacing=0 width=100%>
<tr><td>0 WaveAmps</td><td>20 WaveAmps</td><td>50 WaveAmps</td><td>200 WaveAmps</td><td>1000 WaveAmps</td></tr>
<tr>
<td><center><pre>
<?

// 0 WaveAmps
for ($i=2;$i<300;$i+=5)
{
	echo "You have $i roids\n";
	echo "Chance for one more: ".substr(round(min(1,(50*(1+sqrt(0))/$i)/$i),2),0,5)."\n";
	echo "RoidScans needed: ".substr(round(1/min(1,(50*(1+sqrt(0))/$i)/$i),2),0,5)."\n\n";
}

?>
</td>
<td><center><pre>
<?

// 20 WaveAmps
for ($i=2;$i<300;$i+=5)
{
	echo "You have $i roids\n";
	echo "Chance for one more: ".substr(round(min(1,(50*(1+sqrt(20))/$i)/$i),2),0,5)."\n";
	echo "RoidScans needed: ".substr(round(1/min(1,(50*(1+sqrt(20))/$i)/$i),2),0,5)."\n\n";
}

?>
</td>
<td><center><pre>
<?

// 50 WaveAmps
for ($i=2;$i<300;$i+=5)
{
	echo "You have $i roids\n";
	echo "Chance for one more: ".substr(round(min(1,(50*(1+sqrt(50))/$i)/$i),2),0,5)."\n";
	echo "RoidScans needed: ".substr(round(1/min(1,(50*(1+sqrt(50))/$i)/$i),2),0,5)."\n\n";
}

?>
</td>
<td><center><pre>
<?

// 200 WaveAmps
for ($i=2;$i<300;$i+=5)
{
	echo "You have $i roids\n";
	echo "Chance for one more: ".substr(round(min(1,(50*(1+sqrt(200))/$i)/$i),2),0,5)."\n";
	echo "RoidScans needed: ".substr(round(1/min(1,(50*(1+sqrt(200))/$i)/$i),2),0,5)."\n\n";
}

?>
</td>
<td><center><pre>
<?

// 1000 WaveAmps
for ($i=2;$i<300;$i+=5)
{
	echo "You have $i roids\n";
	echo "Chance for one more: ".substr(round(min(1,(50*(1+sqrt(1000))/$i)/$i),2),0,5)."\n";
	echo "RoidScans needed: ".substr(round(1/min(1,(50*(1+sqrt(1000))/$i)/$i),2),0,5)."\n\n";
}

?>
</td>
</tr>
</table>

<?

if (isset($UID))
{
	echo "You have ".$USER['size']." roids\n";
	echo "Chance for one more: ".substr(round(min(1,(50*(1+sqrt($USER['waveamps']))/$USER['size'])/$USER['size']),2),0,5)."\n";
	echo "RoidScans needed: ".substr(round(1/min(1,(50*(1+sqrt($USER['waveamps']))/$USER['size'])/$USER['size']),2),0,5)."\n\n";
}

?>