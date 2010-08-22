<?php

require_once('inc.config.php');
logincheck();

$szGCMessage = trim(db_select_one('galaxies', 'gc_message', 'id = '.(int)$g_arrUser['galaxy_id']));

$szGC = db_select_one( 'galaxies g, planets p', 'CONCAT(rulername,\' of \',planetname)', 'p.galaxy_id = g.id AND g.id = '.(int)$g_arrUser['galaxy_id'].' AND p.id = g.gc_planet_id' );
if ( !$szGC ) {
	$szGC = '-';
}

_header();

?>
<div onclick="TD('overview_mftc');" class="header">.: Message from The Creator :.</div>
<table id="overview_mftc" style="color:#ff66ff;display:none;" width="450" align="center"><tr><td align="center">
<?php echo nl2br($GAMEPREFS['general_adminmsg']); ?>
<?php echo ($r = GameOver()) ? "<br /><font style='font-size:12px;'><b>THE GAME IS OVER!! ".$GAMEPREFS['general_gamestoptick']." ticks HAVE BEEN PLAYED!!<br />The Winner: ".$r['rulername']." of ".$r['planetname']." from (".$r['x'].":".$r['y'].") with ".nummertje($r['score'])." points.</b>" : (($GAMEPREFS['general_gamestoptick']) ? "<br />Game stops after <b>tick ".nummertje($GAMEPREFS['general_gamestoptick'])."</b>!" : ""); ?>
</td></tr>
</table>

<br />


<?php if ( 0 < $g_arrUser['newbie_ticks'] ) { ?>
<div onclick="TD('overview_msgsnews');" class="header">.: Messages / News :.</div>
<table id="overview_msgsnews" align="center"><tr><td align="center">
<?php echo 0 < $g_arrUser['newbie_ticks'] ? '<b><font color="#aaff00">You are under newbie protection for '.$g_arrUser['newbie_ticks'].' more ticks</font></b><br />' : ''; ?>
</td></tr>
</table>

<br />
<?php } ?>


<?php if ( strlen($szGCMessage) || $szGC ) { ?>
<div onclick="TD('overview_galmsg');" class="header">.: Galactic Message :.</div>
<table id="overview_galmsg" style="color:#ff66ff;display:none;" align="center"><tr><td align="center">
<b style="color:lime;">Your GC (<?php echo $szGC; ?>) says:</b><br />
<?php echo nl2br($szGCMessage); ?>
</td></tr>
</table>

<br />
<?php } ?>


<?php if ( false ) { ?><div onclick="TD('overview_randd');" class="header">.: Research &amp; Development :.</div>
<div id="overview_randd"<?php if ( !$research && !$construction ) { ?> style="display:none;"<?php } ?> align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="400" align="center">
		<tr><td></td><td>&nbsp;<u>Name</u>&nbsp;</td><td align="center">&nbsp;<u>ETA</u>&nbsp;</td><td align="center">&nbsp;<u>Progress</u>&nbsp;</td></tr>
		<tr><td><a href="research.php">Researching</a>&nbsp;</td><td>&nbsp;<?php echo $research ? $research['name'] : '-'; ?>&nbsp;</td><td align="center">&nbsp;<?php echo $research ? $research['eta'] : '-'; ?>&nbsp;</td><td align="center">&nbsp;<?php echo $research ? round($research['pct']).' %' : '-'; ?>&nbsp;</td></tr>
		<tr><td><a href="construction.php">Developing</a>&nbsp;</td><td>&nbsp;<?php echo $construction ? $construction['name'] : '-'; ?>&nbsp;</td><td align="center">&nbsp;<?php echo $construction ? $construction['eta'] : '-'; ?>&nbsp;</td><td align="center">&nbsp;<?php echo $construction ? round($construction['pct']).' %' : '-'; ?>&nbsp;</td></tr>
	</table>
</div>

<br /><?php } ?>

<?php

$arrSkills = db_fetch('SELECT *, IFNULL((SELECT value FROM planet_skills WHERE skill_id = s.id AND planet_id = '.PLANET_ID.'),0) AS value FROM d_skills s ORDER BY s.id ASC;');

?>
<div onclick="TD('overview_skills');" class="header">.: Skills :.</div>
<div id="overview_skills" align="center" style="display:none;">
<table border="0" cellpadding="3" cellspacing="0" align="center" class="widecells">
<?php

for ( $i=0; $i<count($arrSkills); $i+=2 ) {
	echo '<tr>';
	echo '	<td width="150" align="right"><span style="cursor:help;" title="'.$arrSkills[$i]['id'].'. '.$arrSkills[$i]['explanation'].'">'.$arrSkills[$i]['skill'].'</span></td>';
	echo '	<td width="100" align="left">'.nummertje($arrSkills[$i]['value']).'</td>';
	if ( isset($arrSkills[$i+1]) ) {
		echo '	<td width="150" align="right"><span title="'.$arrSkills[$i+1]['id'].'. '.$arrSkills[$i+1]['explanation'].'">'.$arrSkills[$i+1]['skill'].'</span></td>';
		echo '	<td width="100" align="left">'.nummertje($arrSkills[$i+1]['value']).'</td>';
	}
	echo '</tr>'."\n";
}

?>
</table>
</div>

<br />


<?php

$arrShips = db_fetch('
SELECT
	u.*,
	IFNULL((
		SELECT
			SUM(amount)
		FROM
			fleets f,
			ships_in_fleets sf
		WHERE
			f.owner_planet_id = '.PLANET_ID.' AND
			f.id = sf.fleet_id AND
			sf.ship_id = u.id
	),0) AS num_units
FROM
	d_all_units u
WHERE
	u.id IN (SELECT id FROM d_ships) AND
	u.r_d_required_id IN (
		SELECT
			rdpp.r_d_id
		FROM
			planet_r_d rdpp
		WHERE
			rdpp.planet_id = '.PLANET_ID.' AND
			rdpp.eta = 0
	)
ORDER BY
	u.id ASC;
');
echo db_error();

$arrRShips = flip2darray($arrShips);
$iNumShips = count($arrShips) ? array_sum($arrRShips['num_units']) : 0;

?>
<div onclick="TD('overview_units');" class="header">.: Mobile units (<?php echo nummertje($iNumShips); ?>) :.</div>
<div id="overview_units"<?php echo false && !count($arrShips) ? ' style="display:none;"' : ''; ?> align="center">
<table border="0" cellpadding="3" cellspacing="0" align="center" class="widecells">
<?php

for ( $i=0; $i<count($arrShips); $i+=2 ) {
	echo '<tr>';
	echo '	<td width="150" align="right"><span title="'.$arrShips[$i]['id'].'. '.$arrShips[$i]['explanation'].'">'.$arrShips[$i]['unit_plural'].'</span></td>';
	echo '	<td width="100" align="left">'.nummertje($arrShips[$i]['num_units']).'</td>';
	if ( isset($arrShips[$i+1]) ) {
		echo '	<td width="150" align="right"><span title="'.$arrShips[$i+1]['id'].'. '.$arrShips[$i+1]['explanation'].'">'.$arrShips[$i+1]['unit_plural'].'</span></td>';
		echo '	<td width="100" align="left">'.nummertje($arrShips[$i+1]['num_units']).'</td>';
	}
	echo '</tr>'."\n";
}
if ( !count($arrShips) ) {
	echo '<tr><td><i>No mobile units available</i></td></tr>';
}

?>
</table>
</div>

<br />


<?php

$arrDefences = db_fetch('
SELECT
	u.*,
	IFNULL((
		SELECT
			SUM(amount)
		FROM
			defence_on_planets
		WHERE
			planet_id = '.PLANET_ID.' AND
			defence_id = u.id
	),0) AS num_units
FROM
	d_all_units u
WHERE
	u.id IN (SELECT id FROM d_defence) AND
	u.r_d_required_id IN (
		SELECT
			rdpp.r_d_id
		FROM
			planet_r_d rdpp
		WHERE
			rdpp.planet_id = '.PLANET_ID.' AND
			rdpp.eta = 0
	)
ORDER BY
	u.id ASC;
');

$arrRDefences = flip2darray($arrDefences);
$iNumDefences = count($arrDefences) ? array_sum($arrRDefences['num_units']) : 0;

?>
<div onclick="TD('overview_pdu');" class="header">.: Static units (<?php echo nummertje($iNumDefences); ?>) :.</div>
<div id="overview_pdu"<?php echo false && !count($arrDefences) ? ' style="display:none;"' : ''; ?> align="center">
<table border="0" cellpadding="3" cellspacing="0" align="center" class="widecells">
<?php

for ( $i=0; $i<count($arrDefences); $i+=2 ) {
	echo '<tr>';
	echo '	<td width="150" align="right"><span title="'.$arrDefences[$i]['id'].'. '.$arrDefences[$i]['explanation'].'">'.$arrDefences[$i]['unit_plural'].'</span></td>';
	echo '	<td width="100" align="left">'.nummertje($arrDefences[$i]['num_units']).'</td>';
	if ( isset($arrDefences[$i+1]) ) {
		echo '	<td width="150" align="right"><span title="'.$arrDefences[$i+1]['id'].'. '.$arrDefences[$i+1]['explanation'].'">'.$arrDefences[$i+1]['unit_plural'].'</span></td>';
		echo '	<td width="100" align="left">'.nummertje($arrDefences[$i+1]['num_units']).'</td>';
	}
	echo '</tr>'."\n";
}
if ( !count($arrDefences) ) {
	echo '<tr><td><i>No defensive units available</i></td></tr>';
}

?>
</table>
</div>

<br />


<div onclick="TD('overview_asteroids');" class="header">.: Asteroids (<?php $iRoids = $g_arrUser['inactive_asteroids']; foreach ( $g_arrResources AS $r ) { $iRoids += $r['asteroids']; } echo nummertje($iRoids); ?>) :.</div>
<div id="overview_asteroids" align="center">
<table border="0" cellpadding="3" cellspacing="0" align="center" class="widecells">
<?php foreach ( $g_arrResources AS $r ) { ?>
<tr>
	<td width="250" align="right"><?php echo $r['resource']; ?></td>
	<td width="250"><?php echo nummertje($r['asteroids']); ?></td>
</tr>
<?php } ?>
<tr>
	<td width="250" align="right">Inactive asteroids</td>
	<td width="250"><?php echo nummertje($g_arrUser['inactive_asteroids']); ?></td>
</tr>
</table>
</div>

<br />


<div onclick="TD('overview_fleetstatus');" class="header">.: Fleet Status :.</div>
<table id="overview_fleetstatus" border="0" cellpadding="3" cellspacing="0" align="center">
<?php

$arrFleets = db_fetch('SELECT *, (SELECT SUM(amount) FROM ships_in_fleets WHERE fleet_id = f.id) AS num_units FROM fleets f WHERE f.owner_planet_id = '.PLANET_ID.' AND fleetname != \'0\';');
foreach ( $arrFleets AS $arrFleet ) {
	$szFleet = '<b>'.$FLEETNAMES[$arrFleet['fleetname']].'</b>';
	$szEta = ( 0 == (int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA '.$arrFleet['eta'].', AT '.$arrFleet['actiontime'];

	if ( $arrFleet['action'] ) {
		$szTxtColor = ' style="color:'. $showcolors[$arrFleet['action']] .';"';
	}
	else {
		$szTxtColor = '';
	}
	echo '<tr'.$szTxtColor.'><td align="right">Fleet ['.$szFleet.']</td><td align="right">('.nummertje($arrFleet['num_units']).' units)</td><td>';

	if ( (int)$arrFleet['destination_planet_id'] && $arrFleet['action'] ) {
		$szDestination = '<b>'.db_select_one('galaxies g, planets p', 'concat(p.rulername,\'</b> of <b>\',p.planetname,\'</b> (\',g.x,\':\',g.y,\':\',p.z,\')\')', 'p.galaxy_id = g.id AND p.id = '.(int)$arrFleet['destination_planet_id']);
	}

	switch ( $arrFleet['action'] )
	{
		case null:
		default:
			echo 'is idling at home...';
		break;

		case 'return':
			echo 'is returning from '.$szDestination.' (ETA: '.$arrFleet['eta'].')...';
		break;

		case 'attack':
		case 'defend':
			echo 'is'.( '1' !== $arrFleet['activated'] ? ' <b>NOT YET</b>' : '' ).' '.$arrFleet['action'].'ing '.$szDestination.' ('.$szEta.')...';
		break;
	} // switch
	echo '</td></tr>';
}

?>
</table>

<br />

<?php

_footer();

?>