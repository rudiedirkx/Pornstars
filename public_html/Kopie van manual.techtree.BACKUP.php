<?php

require_once('inc.config.php');


/**
 * @brief Print_RD prints all info on one item (research or contruction) by inputting it's code
 * 
 * @param	string		$soort		the code/name of the type of research/construction
 *
 * @method	RD_Array	SOORT => ARRAY( NAAM, UITLEG, ETA, METAL, CRYSTAL, ENERGY, NODIG, EXCLUDES )
 * 
 **/
function Print_RD( $soort, $colspan = 1 )
{
	global $constructionarray, $researcharray;
	global $_N, $_S, $_D;
	global $_R, $_C;

	$error_array = array("<b style='background:red;color:white;font-size:12px;'><i>&nbsp;Not Found: $soort&nbsp;</i></b>",'');
	$s2 = substr($soort,2);
	$bExcludes = FALSE;

	// Checken welke R_D het is (R of D)
	if (substr($soort,0,2)=="c_")
	{
		$szType = "c";
		// Checken of deze construction bestaat
		if (isset($constructionarray[$s2]))
		{
			// ARRAY definieren
			$array = $constructionarray[$s2];
		}
		else
		{
			// Construction bestaat niet dus ERROR_ARRAY definieren als ARRAY
			$array = $error_array;
		}
	}
	else
	{
		$szType = "r";
		// Checken of deze research bestaat
		if (isset($researcharray[$s2]))
		{
			// ARRAY definieren
			$array = $researcharray[$s2];
			// Checken of er een EXCLUDES in zit
			if (strlen(trim($array[7])))
			{
				$bExcludes = TRUE;
			}
		}
		else
		{
			// Research bestaat niet dus ERROR_ARRAY definieren als ARRAY
			$array = $error_array;
		}
	}

	// Start de TD
	echo '<td class="tt" width="150" style="cursor:pointer;" onclick="Show_Menu(\'div'.$soort.'\');" colspan="'.$colspan.'"'.(('c' == $szType) ? $_C : $_R).'>';

	// Print Naam <br> Uitleg
	echo "<b>".((TRUE == $bExcludes) ? "<u>" : '').$array[0].((TRUE == $bExcludes) ? "</u>" : '')."</b><br>\n<div style=\"display:".((TRUE == $_D) ? '' : 'none').";\" id=\"div".$soort."\">".$array[1];

	// Print EXCLUDES
	if (TRUE == $bExcludes)
	{
		$a = explode(";",$array[7]);
		for ($i=0;$i<count($a);$i++)
		{
			if (strlen(trim($a[$i]))>1)
			{
				$b = substr(trim($a[$i]),2);
				if (substr(trim($a[$i]),0,2)=="c_")	$excludes[] = '"'.$constructionarray[$b][0].'"';
				else								$excludes[] = '"'.$researcharray[$b][0].'"';
			}
		}
		echo "<br>(<font color=\"red\"><b><i>excludes ".stripslashes(implode(" and ",$excludes))."</i></b></font>)";
	}

	// Print REQUIRES
	if (isset($array[6]) && strlen(trim($array[6])) && (TRUE == $_N || strstr($array[6],';')))
	{
		$a = explode(";",$array[6]);
		for ($i=0;$i<count($a);$i++)
		{
			if (strlen(trim($a[$i]))>1)
			{
				$b = substr(trim($a[$i]),2);
				if (substr(trim($a[$i]),0,2)=="c_")	$requires[] = '"'.$constructionarray[$b][0].'"';
				else								$requires[] = '"'.$researcharray[$b][0].'"';
			}
		}
		echo "<br>(<i>requires ".stripslashes(implode(" and ",$requires))."</i>)";
	}

	// Print soort as in Array
	if (TRUE == $_S)
	{
		echo "<br><b>[$soort]</b>";
	}

	// End DIV and TD
	echo "</div></td>";

} // END function Print_RD( )



if ( PLANET_ID )
{
	_header();
	echo "</table>";
}
else
{
	echo "<title>Manual > TechTree</title>\n\n<link rel=stylesheet href=\"css/styles.css\">\n<script type=\"text/javascript\" src=\"scripts.js\"></script>\n\n<body bgcolor=black>\n\n";
}
echo "<style>\nTD.tt { text-align:left;border:solid 1px black;padding:4px;height:40px; }\n</style>\n\n";

$onderscheid_tussen_kolommen	= 4;
$kleur_research					= "#FFB0B0";
$kleur_construction				= "lightgreen";

$_C = ' bgcolor="'.$kleur_construction.'"';
$_R = ' bgcolor="'.$kleur_research.'"';

/** Volgende variabelen zijn TRUE of FALSE en beiden zijn niet perse TRUE nodig **/
$_S = 0;	// Als TRUE wordt de variabele geprint zoals ie in de arrays staat
$_N = 0;	// Als TRUE wordt geprint wat er voor Nodig is voor deze R/C
$_D = 0;	// Als TRUE worden beschrijving etc ook geprint, anders alleen naam

error_reporting(0);

?>
<center>

<br>
There are <?php echo count($constructionarray); ?> developments.<br>
There are <?php echo count($researcharray); ?> researches.<br>
-- You cannot do all of them, because some exclude others --<br>
<br>

<table border="0" cellpadding="2" cellspacing="0" bordercolor="red" style="color:black;border-collapse:collapse;">
<tr>
<td class="tt" bgcolor="midnightblue"><center><font color=white><b>Crystal Mining</td>
<td class="tt" bgcolor="midnightblue" colspan=2><center><font color=white><b>Waves</td>
<td class="tt" bgcolor="midnightblue"><center><font color=white><b>Metal Mining</td>
<td class="tt" bgcolor="midnightblue" colspan=4><center><font color=white><b>Warships & PDU</td>
<td class="tt" bgcolor="midnightblue"><center><font color=white><b>Energy</td>
</tr>

<tr valign="top">
<!-- Crystal Refinery			--><? Print_RD('c_crystal10'); ?>
<!-- About Scanning				--><? Print_RD('r_scan1',2); ?>
<!-- Metal Refinery				--><? Print_RD('c_metal10',1); ?>
<!--							--><td class="tt" colspan="4" valign="bottom"><img src="images/resconpijltje_rechts.gif">&nbsp;</td>
<!-- About Energy				--><? Print_RD('r_energy'); ?>
</tr>

<tr valign="top">
<!-- More Crystal				--><? Print_RD('r_crystal15'); ?>
<!-- Sector Scanning			--><? Print_RD('c_bscan',2); ?>
<!-- More Metal					--><? Print_RD('r_metal15'); ?>
<!-- First Airport				--><? Print_RD('c_pinf',4); ?>
<!-- Making Energy				--><? Print_RD('c_energy'); ?>
</tr>

<tr valign="top">
<!-- Get More Crystal			--><? Print_RD('c_crystal20'); ?>
<!-- Unit Patterns				--><? Print_RD('r_scan2',2); ?>
<!-- Get More Metal				--><? Print_RD('c_metal20'); ?>
<!-- Robot Factory				--><? Print_RD('c_pwra',4); ?>
<!-- Robot Efficiency			--><? Print_RD('r_enef'); ?>
</tr>

<tr valign="top">
<!-- Blind Diggers For Crystal	--><? Print_RD('r_crystal25'); ?>
<!-- Unit Scanning				--><? Print_RD('c_uscan',2); ?>
<!-- Blind Diggers For Metal	--><? Print_RD('r_metal25'); ?>
<!-- Tank Building				--><? Print_RD('r_pwarast',4); ?>
<!-- Creons In Boxes			--><? Print_RD('c_enef'); ?>
</tr>

<tr valign="top">
<!-- Hardcore Crystal			--><? Print_RD('c_crystal30'); ?>
<!-- PDU Patterns				--><? Print_RD('r_scan3',2); ?>
<!-- Hardcore Metal				--><? Print_RD('c_metal30'); ?>
<!-- EMP Studies				--><? Print_RD('r_pcob',3); ?>
<!-- Destroyer Factory			--><? Print_RD('c_pdes'); ?>
<!-- 							--><td class="tt" rowspan="6"></td>
</tr>

<tr valign="top">
<!-- Time Travel				--><? Print_RD('r_timt'); ?>
<!-- PDU Scanning				--><? Print_RD('c_pscan',2); ?>
<!-- Resource Boost				--><? Print_RD('r_rebo'); ?>
<!-- PDU Machines				--><? Print_RD('c_pdu1',3); ?>
<!-- Scorpion Factory			--><? Print_RD('c_psco'); ?>
</tr>

<tr valign="top">
<!-- Time Travel (II)			--><? Print_RD('r_timt2'); ?>
<!-- The New Spies				--><? Print_RD('r_scan5'); ?>
<!-- Fleet Signatures			--><? Print_RD('r_scan4'); ?>
<!-- Work, Bitch, Work!			--><? Print_RD('c_rebo'); ?>
<!-- Antenna Bot				--><? Print_RD('r_abot'); ?>
<!-- Super PDU					--><? Print_RD('c_pdu2'); ?>
<!-- Wave Blockers				--><? Print_RD('r_wblockers'); ?>
<!-- 							--><td class="tt" rowspan="4"></td>
</tr>

<tr valign="top">
<!-- Hot 'n' Thick				--><? Print_RD('c_timt'); ?>
<!-- High-speed-plasma Spies	--><? Print_RD('c_nscan'); ?>
<!-- Fleet Scanners				--><? Print_RD('c_mscan'); ?>
<!-- Building Space Warehouses	--><? Print_RD('c_rebo2'); ?>
<!-- New Hangars				--><? Print_RD('r_abot2'); ?>
<!-- 							--><td class="tt" rowspan="3"></td>
<!-- Wave Blockers				--><? Print_RD('c_wblockers'); ?>
</tr>

<tr valign="top">
<!-- 							--><td class="tt" rowspan="2" colspan="4"></td>
<!-- Super Cruisers				--><? Print_RD('c_abot'); ?>
<!-- Production Spies			--><? Print_RD('r_prsp'); ?>
</tr>

<tr valign="top">
<!-- 							--><td class="tt" width="150"></td>
<!-- Production Spy Suits		--><? Print_RD('c_prsp') ?>
</tr>
</table>

<br/>

<table border="0" cellpadding="5" cellspacing="2" width="100%" style="color:black;">
<tr>
<td<?php echo $_C; ?> width="50%" align="center" style="text-align:center;"><b style="font-size:13px;">DEVELOPMENTS</b></td>
<td<?php echo $_R; ?> width="50%" align="center" style="text-align:center;"><b style="font-size:13px;">RESEARCHES</b><br><u>Research</u> -> you have to give up something for this -><br>eg.: "(<b><i><font color=red>excludes Fleet Signatures</font></i></b>)"</td>
</tr>
</table>
<br />
</center>

