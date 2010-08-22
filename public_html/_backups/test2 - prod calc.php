<?

include("../config.php");

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

function Optimale_Productie($metal1,$crystal1,$metal2,$crystal2,$METAL,$CRYSTAL)
{
	global $N1,$N2;

	$N1 = abs(floor((($METAL*$crystal2)-($CRYSTAL*$metal2))/(($metal1*$crystal2)-($metal2*$crystal1))));
	$N2 = abs(floor((($METAL*$crystal1)-($CRYSTAL*$metal1))/(($metal2*$crystal1)-($metal1*$crystal2))));
}


Optimale_Productie($_GET[metal1],$_GET[crystal1],$_GET[metal2],$_GET[crystal2],$_GET[METAL],$_GET[CRYSTAL]);

echo "metal1 = $_GET[metal1]<br>";
echo "crystal1 = $_GET[crystal1]<br>";
echo "metal2 = $_GET[metal2]<br>";
echo "crystal2 = $_GET[crystal2]<br><br>";

echo "Bouw $N1 units van Product 1 en $N2 units van Product 2!<br>";
echo "Er is dan ".($METAL-($N1*$_GET[metal1])-($N2*$_GET[metal2]))." metal over.<br>";
echo "Er is dan ".($CRYSTAL-($N1*$_GET[crystal1])-($N2*$_GET[crystal2]))." crystal over.<br>";


?>
<html>

<head>
<title>Test 2</title>
<script language="JavaScript">
function optimale_productie(unit1,unit2)
{
	var metal = totalmetal;
	var crystal = totalcrystal;
	var metal1 = units[unit1][m];
	var crystal1 = units[unit1][c];
	var metal2 = units[unit2][m];
	var crystal2 = units[unit3][c];

	var boven1 = metal*crystal2-crystal*metal2;
	var onder1 = metal1*crystal2-metal2*crystal1;
	var num1 = boven1/onder1;

	alert('Je kan '+num1+' units van UNIT 1 bouwen!');
}

alert('bitch');

<?

echo "var totalmetal = '$metal';\n";
echo "var totalcrystal = '$crystal';\n\n";

foreach ($productionarray AS $soort => $inhoud)
{
	echo (strlen($soort)>1) ? "var units[".substr($soort,0,4)."][m] = '".$inhoud[3]."';\n" : "";
	echo (strlen($soort)>1) ? "var units[".substr($soort,0,4)."][c] = '".$inhoud[4]."';\n\n" : "";
}

?>

optimale_productie('dest','scor');
</script>
</head>


