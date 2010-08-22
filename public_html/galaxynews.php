<?php

require_once('inc.config.php');
logincheck();

_header();

?>
<div class="header">Galactic News</div>

<br />

<b>Incoming:</b><br />
<br />
<table border="0" cellpadding="3" cellspacing="0">
<?php

$arrFleets1 = db_fetch('SELECT f.*, concat(p1.rulername,\'</b> of <b>\',p1.planetname,\'</b> (\',g1.x,\':\',g1.y,\':\',p1.z,\')\') AS owner, concat(p2.rulername,\'</b> of <b>\',p2.planetname,\'</b> (\',g2.x,\':\',g2.y,\':\',p2.z,\')\') AS destination, (SELECT IFNULL(SUM(amount),0) FROM ships_in_fleets WHERE fleet_id = f.id) AS num_units FROM fleets f, planets p1, planets p2, galaxies g1, galaxies g2 WHERE f.activated = \'1\' AND p1.id = f.owner_planet_id AND g1.id = p1.galaxy_id AND p2.id = f.destination_planet_id AND g2.id = p2.galaxy_id AND p2.galaxy_id = '.$g_arrUser['galaxy_id'].' AND f.action = \'attack\' AND f.fleetname != \'0\';');
foreach ( $arrFleets1 AS $arrFleet ) {
	$szEta = ( !(int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA: '.$arrFleet['eta'].' ticks';
	echo '<tr valign="top" style="color:red;"><!--<td align="right">['.$FLEETNAMES[$arrFleet['fleetname']].']</td>--><td><b>'.$arrFleet['owner'].' is '.$arrFleet['action'].'ing <b>'.$arrFleet['destination'].' with '.$arrFleet['num_units'].' ships ('.$szEta.')</td></tr>';
}

$arrFleets2 = db_fetch('SELECT f.*, concat(p1.rulername,\'</b> of <b>\',p1.planetname,\'</b> (\',g1.x,\':\',g1.y,\':\',p1.z,\')\') AS owner, concat(p2.rulername,\'</b> of <b>\',p2.planetname,\'</b> (\',g2.x,\':\',g2.y,\':\',p2.z,\')\') AS destination, (SELECT IFNULL(SUM(amount),0) FROM ships_in_fleets WHERE fleet_id = f.id) AS num_units FROM fleets f, planets p1, planets p2, galaxies g1, galaxies g2 WHERE f.activated = \'1\' AND p1.id = f.owner_planet_id AND g1.id = p1.galaxy_id AND p2.id = f.destination_planet_id AND g2.id = p2.galaxy_id AND p2.galaxy_id = '.$g_arrUser['galaxy_id'].' AND f.action = \'defend\' AND f.fleetname != \'0\';');
foreach ( $arrFleets2 AS $arrFleet ) {
	$szEta = ( !(int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA: '.$arrFleet['eta'].' ticks';
	echo '<tr valign="top" style="color:lime;"><!--<td align="right">['.$FLEETNAMES[$arrFleet['fleetname']].']</td>--><td><b>'.$arrFleet['owner'].' is '.$arrFleet['action'].'ing <b>'.$arrFleet['destination'].' with '.$arrFleet['num_units'].' ships ('.$szEta.')</td></tr>';
}

if ( !(count($arrFleets1)+count($arrFleets2)) ) {
	echo 'No incoming fleets to your galaxy!<br />';
}

?>
</table>

<br />
<br />

<b>Outgoing:</b><br />
<br />
<table border="0" cellpadding="3" cellspacing="0">
<?php

$arrFleets1 = db_fetch('SELECT f.*, concat(p1.rulername,\'</b> of <b>\',p1.planetname,\'</b> (\',g1.x,\':\',g1.y,\':\',p1.z,\')\') AS owner, concat(p2.rulername,\'</b> of <b>\',p2.planetname,\'</b> (\',g2.x,\':\',g2.y,\':\',p2.z,\')\') AS destination, (SELECT IFNULL(SUM(amount),0) FROM ships_in_fleets WHERE fleet_id = f.id) AS num_units FROM fleets f, planets p1, planets p2, galaxies g1, galaxies g2 WHERE f.activated = \'1\' AND p1.id = f.owner_planet_id AND g1.id = p1.galaxy_id AND p2.id = f.destination_planet_id AND g2.id = p2.galaxy_id AND p1.galaxy_id = '.$g_arrUser['galaxy_id'].' AND f.action = \'attack\' AND f.fleetname != \'0\';');
foreach ( $arrFleets1 AS $arrFleet ) {
	$szEta = ( !(int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA: '.$arrFleet['eta'].' ticks';
	echo '<tr valign="top" style="color:red;"><!--<td align="right">['.$FLEETNAMES[$arrFleet['fleetname']].']</td>--><td><b>'.$arrFleet['owner'].' is '.$arrFleet['action'].'ing <b>'.$arrFleet['destination'].' with '.$arrFleet['num_units'].' ships ('.$szEta.')</td></tr>';
}

$arrFleets2 = db_fetch('SELECT f.*, concat(p1.rulername,\'</b> of <b>\',p1.planetname,\'</b> (\',g1.x,\':\',g1.y,\':\',p1.z,\')\') AS owner, concat(p2.rulername,\'</b> of <b>\',p2.planetname,\'</b> (\',g2.x,\':\',g2.y,\':\',p2.z,\')\') AS destination, (SELECT IFNULL(SUM(amount),0) FROM ships_in_fleets WHERE fleet_id = f.id) AS num_units FROM fleets f, planets p1, planets p2, galaxies g1, galaxies g2 WHERE f.activated = \'1\' AND p1.id = f.owner_planet_id AND g1.id = p1.galaxy_id AND p2.id = f.destination_planet_id AND g2.id = p2.galaxy_id AND p1.galaxy_id = '.$g_arrUser['galaxy_id'].' AND f.action = \'defend\' AND f.fleetname != \'0\';');
foreach ( $arrFleets2 AS $arrFleet ) {
	$szEta = ( !(int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA: '.$arrFleet['eta'].' ticks';
	echo '<tr valign="top" style="color:lime;"><!--<td align="right">['.$FLEETNAMES[$arrFleet['fleetname']].']</td>--><td><b>'.$arrFleet['owner'].' is '.$arrFleet['action'].'ing <b>'.$arrFleet['destination'].' with '.$arrFleet['num_units'].' ships ('.$szEta.')</td></tr>';
}

if ( !(count($arrFleets1)+count($arrFleets2)) ) {
	echo 'No outgoing fleets from your galaxy to another!<br />';
}

echo '</table>';
echo '<br />';

_footer();

?>