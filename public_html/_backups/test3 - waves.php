<table border=1 cellpadding=5 cellspacing=0 width=100%>
<tr>
<td><center><pre>
<?

// 10 WaveAmps
for ($i=2;$i<50;$i++)
{
	echo "You have $i roids\n";
	echo "Kans op deze roid: ".round(min(1,(50*(1+sqrt(10))/$i)/$i),2)."\n";
	echo "RoidScans nodig: ".round(1/min(1,(50*(1+sqrt(10))/$i)/$i),2)."\n\n";
}

?>
</td>
<td><center><pre>
<?

// 50 WaveAmps
for ($i=2;$i<50;$i++)
{
	echo "You have $i roids\n";
	echo "Kans op deze roid: ".round(min(1,(50*(1+sqrt(50))/$i)/$i),2)."\n";
	echo "RoidScans nodig: ".round(1/min(1,(50*(1+sqrt(50))/$i)/$i),2)."\n\n";
}

?>
</td>
<td><center><pre>
<?

// 200 WaveAmps
for ($i=2;$i<50;$i++)
{
	echo "You have $i roids\n";
	echo "Kans op deze roid: ".round(min(1,(50*(1+sqrt(200))/$i)/$i),2)."\n";
	echo "RoidScans nodig: ".round(1/min(1,(50*(1+sqrt(200))/$i)/$i),2)."\n\n";
}

?>
</td>
<td><center><pre>
<?

// 1000 WaveAmps
for ($i=2;$i<50;$i++)
{
	echo "You have $i roids\n";
	echo "Kans op deze roid: ".round(min(1,(50*(1+sqrt(1000))/$i)/$i),2)."\n";
	echo "RoidScans nodig: ".round(1/min(1,(50*(1+sqrt(1000))/$i)/$i),2)."\n\n";
}

?>
</td>
</tr>
</table>