<?

include("config.php");

function Optimale_Productie($metal1,$crystal1,$metal2,$crystal2,$METAL,$CRYSTAL)
{
	global $N1,$N2;

	$N1 = abs(floor((($METAL*$crystal2)-($CRYSTAL*$metal2))/(($metal1*$crystal2)-($metal2*$crystal1))));
	$N2 = abs(floor((($METAL*$crystal1)-($CRYSTAL*$metal1))/(($metal2*$crystal1)-($metal1*$crystal2))));
}

// productionarray: soort(naam,uitleg,eta,metal,crystal,rescon_nodig)
$productionarray =array('infinitys'=>array('Infinitys','Small weak ships who make up their little powers with their agility and speed.','10','0','300','c_pinf'),
			'wraiths'=>array('Wraiths','Slightly bigger ships with slower though stronger weapons. Also high agility and speed.','20','1000','0','c_pwra'),
			'warfrigs'=>array('Warfrigates','Warfrigates are real warships! Big ships loaded with lowspeed cannons and highspeed machineguns.','30','2500','2500','r_pwarast'),
			'astropods'=>array('Astropods','The Astropod is the only ship that can steal roids from others. Astropods are slow and weak and cant shoot. They die on success.','40','1250','1250','r_pwarast'),
			'cobras'=>array('Cobras','Very strong and armoured ships! Blocks but not kills Astropods. A musthave in the modern army!','60','0','3000','r_pcob'),
			'destroyers'=>array('Destroyers','The biggest of all heavy duty warships! Quite an investment, but very efficient in combat!','60','2000','3000','c_pdes'),
			'scorpions'=>array('Scorpion','Great ships, <u>very stealth</u> and quite manouvrable and fast. A Cobra\'s worst enemy!','100','6000','1000','c_psco'),
			'a',
			'rcannons'=>array('Reaper Cannons','Best PDU against Astropods. This unit is a musthave in your Planetary Defense!','20','1000','0','c_pdu1'),
			'avengers'=>array('Avengers','Avengers are of a new generation of PDU. They select their targets and are therefore very efficient!','30','800','800','c_pdu1'),
			'lstalkers'=>array('Lucius Stalkers','Very heavy Defense Unit, which can only be killed by Wraiths. Lucius Stalkers go for the big fish of the attacker!','60','3000','3000','c_pdu2'));

?>
<html>

<head>
<title>Calculator</title>
<style>
BODY,TABLE,SELECT,INPUT { font-family:Verdana;font-size:11px;color:white;background:black;overflow:auto; }
</style>
</head>

<body style='margin:0px;' bgcolor=black>
<table border=0 cellpadding=0 cellspacing=0>
<form name=calc method=post action=calc.php><input type=hidden name=check value=1>
<tr><td><font face=verdana size=2><center>Pick a unit...</td><td><font face=verdana size=2><center>...and another</td></tr>
<tr>
<td><select name=unit1 size=<?=(count($productionarray)+1)?> style='width:200px;'>
<option>------------SHIPS:
<?

foreach ($productionarray AS $soort => $inhoud)
{
	echo (strlen($soort)>1) ? "<option value='$soort'>$inhoud[0] ($inhoud[3]m,$inhoud[4]c)" : "<option>------------PDU:";
}

?>
</select></td>
<td><select name=unit2 size=<?=(count($productionarray)+1)?> style='width:200px;'>
<option>------------SHIPS:
<?

foreach ($productionarray AS $soort => $inhoud)
{
	echo (strlen($soort)>1) ? "<option value='$soort'>$inhoud[0] ($inhoud[3]m,$inhoud[4]c)" : "<option>------------PDU:";
}

?>
</select></td>
</tr>
<tr><td colspan=2><center><br><input type=submit value="Calculate!"></td></tr>
</form></table>
<br>
<br>
<br>
You have <?=nummertje($metal)?> Metal and <?=nummertje($crystal)?> Crystal.<br>
<br>
<?

if ($_POST[check]==1 && $_POST[unit1]!=$_POST[unit2])
{
	$metal1 = $productionarray[$_POST[unit1]][3];
	$crystal1 = $productionarray[$_POST[unit1]][4];
	$metal2 = $productionarray[$_POST[unit2]][3];
	$crystal2 = $productionarray[$_POST[unit2]][4];

	Optimale_Productie($metal1,$crystal1,$metal2,$crystal2,$metal,$crystal);

	?>
	You can build <?=$N1?> <?=$productionarray[$_POST[unit1]][0]?> and<br>
	you can build <?=$N2?> <?=$productionarray[$_POST[unit2]][0]?>.<br>
	<br>
	You will have <?=($metal-($metal1*$N1)-($metal2*$N2))?> Metal and<br>
	You will have <?=($crystal-($crystal1*$N1)-($crystal2*$N2))?> Crystal left.<br>
	<?

}

?>


