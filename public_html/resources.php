<?php

require_once('inc.config.php');
logincheck();


$iCurrentAsteroids = 0;
foreach ( $g_arrResources AS $id => $r ) { $iCurrentAsteroids += $r['asteroids']; }


//print_r($_POST);exit;

/** DONATE - FROM GALAXY FUND **
if ( isset($_POST['donate_metal_from_fund'], $_POST['donate_crystal_from_fund'], $_POST['donate_energy_from_fund']) )
{
	// You can't donate from The Fund if you're not MoF
	if ( (int)$g_arrUser['mof_planet_id'] !== PLANET_ID ) {
		Go();
	}

	foreach ( array('metal', 'crystal', 'energy') AS $t ) {
		$x = (int)min(max(0, (int)str_replace(',','',$_POST['donate_'.$t.'_from_fund'])), $g_arrUser['fund_'.$t]);
		if ( 0 < $x && db_update('galaxies', 'fund_'.$t.'=fund_'.$t.'-'.floor($x), 'id = '.(int)$g_arrUser['galaxy_id'].' AND fund_'.$t.' >= '.$x) && 0 < db_affected_rows() ) {
			db_update('planets', $t.'='.$t.'+'.$x, 'id = '.PLANET_ID.' AND galaxy_id >= '.(int)$g_arrUser['galaxy_id']);
		}
	}

	Go();
}

/** DONATE - TO GALAXY FUND **/
if ( isset($_POST['to_'], $_POST['donate_to_fund']) && 'fund' == $_POST['to_'] && is_array($_POST['donate_to_fund']) )
{
	exit(print_r($_POST['donate_to_fund'], true));

	// You can't donate to The Fund when there's no MoF elected
	if ( !(int)$g_arrUser['mof_planet_id'] || !db_count('planets', 'id = '.(int)$g_arrUser['mof_planet_id'].' AND galaxy_id = '.(int)$g_arrUser['galaxy_id']) ) {
		exit(json_encode(array(
			array('msg', 'Your galaxy has no Minister of Finance!'),
		)));
	}

#	$arrDonations = array();
	foreach ( array('metal', 'crystal', 'energy') AS $t ) {
		$x = min(max(0, (int)str_replace(',','',$_POST['donate_'.$t.'_to_fund'])), $g_arrUser[$t]);
		if ( 0 < $x && db_update('planets', $t.'='.$t.'-'.$x, 'id = '.PLANET_ID.' AND '.$t.' >= '.$x) && 0 < db_affected_rows() ) {
			db_update('galaxies', 'fund_'.$t.'=fund_'.$t.'+'.floor(0.95*$x), 'id = '.(int)$g_arrUser['galaxy_id']);
#			$arrDonations[] = floor(0.95*$x) . ' ' . $t;
		}
	}

#	if ( $arrDonations ) {
#		AddGalaxyNews( NEWS_SUBJECT_GALAXY, $g_arrUser['rulername'].' of '.$g_arrUser['planetname'].' ('.$g_arrUser['x'].') donated to The Fund:<br />'.implode(', ', $arrDonations), (int)$g_arrUser['galaxy_id'] );
#	}

	exit(json_encode(array(
		array('html', 'res_amount_metal', nummertje(db_select_one('planets', 'metal', 'id = '.PLANET_ID))),
		array('html', 'res_amount_crystal', nummertje(db_select_one('planets', 'crystal', 'id = '.PLANET_ID))),
		array('html', 'res_amount_energy', nummertje(db_select_one('planets', 'energy', 'id = '.PLANET_ID))),
		array('eval', "$('f_donations').reset();"),
	)));
}

/** DONATE - TO PLANET **/
if ( isset($_POST['to_'], $_POST['to_planet_id'], $_POST['donate_to_planet']) && 'planet' == $_POST['to_'] && is_array($_POST['donate_to_fund']) )
{
	exit(print_r($_POST['donate_to_planet'], true));

	// You can only donate to other planets in this galaxy AND you can only donate when you're out of newbie state
	if ( 0 < (int)$g_arrUser['newbie_ticks'] || !db_count('planets', 'id = '.(int)$_POST['to_planet_id'].' AND galaxy_id = '.(int)$g_arrUser['galaxy_id'].' AND id <> '.PLANET_ID) ) {
		exit(json_encode(array(
			array('msg', ( 0 < (int)$g_arrUser['newbie_ticks'] ? 'You can\'t donate whilst in newbie protection!' : 'Invalid planet ID!') ),
		)));
	}

	$arrDonations = array();
	foreach ( array('metal', 'crystal', 'energy') AS $t ) {
		$x = min(max(0, (int)str_replace(',','',$_POST['donate_'.$t.'_to_planet'])), $g_arrUser[$t]);
		if ( 0 < $x && db_update('planets', $t.'='.$t.'-'.$x, 'id = '.PLANET_ID.' AND '.$t.' >= '.$x) && 0 < db_affected_rows() ) {
			db_update('planets', $t.'='.$t.'+'.floor(0.9*$x), 'id = '.(int)$_POST['to_planet_id']);
			$arrDonations[] = '<span style="color:'.$showcolors[$t].';">' . nummertje(floor(0.9*$x)) . ' ' . $t . '</span>';
		}
	}

	if ( $arrDonations ) {
		$msg = 'You donated: ' . implode(', ', $arrDonations) . ' to ' . fullname($_POST['to_planet_id'], false);
//		Save_Msg($msg, 'lime');
		AddNews( NEWS_SUBJECT_GALAXY, $msg, PLANET_ID, true );
		AddNews( NEWS_SUBJECT_GALAXY, $g_arrUser['rulername'].' of '.$g_arrUser['planetname'].' ('.$g_arrUser['x'].') donated to you:<br />'.implode(', ', $arrDonations), (int)$_POST['to_planet_id'] );
	}

	exit(json_encode(array(
		array('html', 'res_amount_metal', nummertje(db_select_one('planets', 'metal', 'id = '.PLANET_ID))),
		array('html', 'res_amount_crystal', nummertje(db_select_one('planets', 'crystal', 'id = '.PLANET_ID))),
		array('html', 'res_amount_energy', nummertje(db_select_one('planets', 'energy', 'id = '.PLANET_ID))),
		array('eval', "$('f_donations').reset();"),
	)));
}

/** INITIATE ASTEROIDS **/
if ( isset($_POST['init_roids']) && is_array($_POST['init_roids']) )
{
	$arrInitRoids = array_map('intval', $_POST['init_roids']);

exit(print_r($arrInitRoids, true));

	$arrResetFormAjaxUpdate = array('eval', "$('f_asteroids').reset();");
	if ( 0 > min($arrInitRoids) || 0 >= (int)$g_arrUser['inactive_asteroids'] ) {
		exit(json_encode(array(
			$arrResetFormAjaxUpdate,
		)));
	}

	$iTotalInitiated = $iTotalCosts = 0;
	foreach ( $arrInitRoids AS $iResourceId => $iAsteroids ) {
		$iAsteroids = min($g_arrUser['inactive_asteroids'], (int)$iAsteroids);
		if ( 0 < $iAsteroids ) {
			$iCosts = initRoidsCosts($iAsteroids, $iCurrentAsteroids);
			if ( db_update('planet_resources', 'amount = amount-'.$iCosts, 'resource_id = '.$iActivationResource.' AND planet_id = '.PLANET_ID.' AND amount >= '.$iCosts) && 0 < db_affected_rows() ) {
				db_update('planet_resources', 'asteroids = asteroids+'.$iAsteroids, 'asteroids = '.$g_arrResources[$iResourceId]['asteroids'].' AND resource_id = '.$iResourceId.' AND planet_id = '.PLANET_ID);
				db_update('planets', 'inactive_asteroids = inactive_asteroids-'.$iAsteroids, 'id = '.PLANET_ID);
				$g_arrUser['inactive_asteroids'] -= $iAsteroids;
				$iCurrentAsteroids += $iAsteroids;
				$iTotalInitiated += $iAsteroids;
				$iTotalCosts += $iCosts;
			}
		}
	}

	if ( 0 == $iTotalInitiated ) {
		exit(json_encode(array(
			$arrResetFormAjaxUpdate,
		)));
	}

	$arrJson = array(
		$arrResetFormAjaxUpdate,
		array('html', 'roids_amount_inactive', nummertje(db_select_one('planets', 'inactive_asteroids', 'id = '.PLANET_ID))),
		array('html', 'resources_for_x_roids_x', '?'),
		array('msg', 'You initiated '.nummertje($iTotalInitiated).' asteroids for '.strtoupper(substr($g_arrResources[$iActivationResource]['resource'], 0, 1)).' '.nummertje($iTotalCosts).'!'),
	);
	$iAsteroids = 0;
	foreach ( db_select('planet_resources', 'planet_id = '.PLANET_ID) AS $r ) {
		$iAsteroids += (int)$r['asteroids'];
		$arrJson[] = array('html', 'roids_amount_'.$r['resource_id'], nummertje($r['asteroids']));
		$arrJson[] = array('html', 'res_amount_'.$r['resource_id'], nummertje($r['amount']));
	}
	$arrJson[] = array('html', 'next_roid_init_costs', nummertje(nextRoidCosts($iAsteroids)));
	exit(json_encode($arrJson));

}


_header();

$szDisabled = 0 < $g_arrUser['inactive_asteroids'] ? '' : ' disabled="1"';

?>

<div class="header">Resources</div>

<br />

<form id="f_asteroids" method="post" action="resources.php" autocomplete="off" onsubmit="return postForm(this,H);">
<table border="0" cellpadding="3" cellspacing="0" class="widecells" width="600" align="center">
<tr>
	<th colspan="3" class="br">Asteroids</th>
	<th colspan="4" class="bl">Income per tick</th>
</tr>
<tr class="bt bb">
	<th class="right">Type</th>
	<th>Amount</th>
	<th class="br" bgcolor="#111111">Initiate</th>
	<th class="bl">Planet-</th>
	<th>Asteroid-</th>
	<th>Bonus-</th>
	<th>Total</th>
</tr>
<?php foreach ( $g_arrResources AS $r ) { ?>
<tr class="bt">
	<th class="right" style="color:<?php echo $r['color']; ?>;"><?php echo ucfirst($r['resource']); ?></th>
	<td class="right" id="roids_amount_<?php echo $r['id']; ?>"><?php echo nummertje($r['asteroids']); ?></td>
	<td class="c br" bgcolor="#111111"><input<?php echo $szDisabled; ?> type="text" name="init_roids[<?php echo $r['id']; ?>]" value="" class="right" size="4" maxlength="3" style="padding:1px 2px;" /></td>
	<td class="right bl"><?php echo nummertje($p=applyRDChange('income_'.$r['id'], 0)); ?></td>
	<td class="right"><?php echo nummertje($a=res_per_type($r['asteroids'])); ?></td>
	<td class="right"><?php echo nummertje( (($t=applyRDChange('income_'.$r['id'], res_per_type($r['asteroids']))) - $a - $p) ); ?></td>
	<th class="right"><?php echo nummertje($t); ?></th>
</tr>
<?php } ?>
<tr class="bt">
	<th class="right">Inactive</th>
	<td class="right" id="roids_amount_inactive"><?php echo nummertje($g_arrUser['inactive_asteroids']); ?></td>
	<td class="c br" bgcolor="#111111"><input<?php echo $szDisabled; ?> type="submit" value="Initiate" /></td>
	<td class="c" colspan="4"><?php echo 0 < $g_arrUser['inactive_asteroids'] ? 'Activating the next asteroid will cost <span id="next_roid_init_costs">'.nummertje(nextRoidCosts($iCurrentAsteroids)).'</span>' : '<br />'; ?></th>
</tr>
</table>
</form>

<br />
<br />

<form id="f_donations" method="post" action="resources.php" autocomplete="off" onsubmit="return ('' != this.elements['to_'].value ? postForm(this,H) : false);">
<input type="hidden" name="to_" value="" />
<table border="0" cellpadding="3" cellspacing="0" class="widecells" align="center">
<tr>
	<th colspan="2"<?php if ( (int)$g_arrUser['mof_planet_id'] === PLANET_ID ) { echo ' style="color:'.$showcolors['mof'].';";'; } ?>>The Galaxy Fund</th>
	<th class="br bl"><br /></th>
	<th nowrap="1" wrap="off" style="cursor:help;" title="Donating to a Planet costs 10%">Planet donation</th>
</tr>
<tr class="bt">
	<th class="right">Fund</th>
	<th style="cursor:help;" title="Donating to The Fund costs 5%">Donate</th>
	<th class="br bl">Type</th>
	<td align="center"><select name="to_planet_id"><option value="">-- Recipient Planet</option><?php $p=db_select_fields('planets','id,concat(z,\'. \',rulername,\' of \',planetname)','id <> '.PLANET_ID.' AND galaxy_id = '.(int)$g_arrUser['galaxy_id'].' ORDER BY z ASC'); foreach ( $p AS $iP => $szP ) { echo '<option value="'.$iP.'">'.$szP.'</option>'; } ?></select></td>
</tr>
<?php foreach ( $g_arrResources AS $r ) { ?>
<tr class="bt" style="color:<?php echo $r['color']; ?>;">
	<td class="right">????</td>
	<td class="c"><input type="text" name="donate_to_fund[<?php echo $r['id']; ?>]" value="" class="right" style="width:80px;" /></td>
	<th class="br bl"><?php echo $r['resource']; ?></th>
	<td class="c"><input type="text" name="donate_to_planet[<?php echo $r['id']; ?>]" value="" class="right" style="width:80px;" /></td>
</tr>
<?php } ?>
<tr>
	<td>&nbsp;</td>
	<td class="c"><input type="submit" onclick="this.form.elements['to_'].value='fund';" value="Donate" /></td>
	<td class="c br bl"><input type="reset" value="reset" /></td>
	<td class="c"><input type="submit" onclick="this.form.elements['to_'].value='planet';" value="Donate" /></td>
</tr>
</table>
</form>

<br />

<?php

_footer();

?>
