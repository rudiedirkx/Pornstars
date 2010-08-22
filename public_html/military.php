<?php

require_once('inc.config.php');
logincheck();

$iNumMovingRows		= 3;
$iAttackScoreLimit	= max(1, (int)$GAMEPREFS['military_scorelimit']); //  2
$iMinActionTicks	= max(1, (int)$GAMEPREFS['military_min_period']);
$iMaxActionTicks	= max($iMinActionTicks, (int)$GAMEPREFS['military_max_period']);

// MOVE SHIPS //
if ( isset($_POST['m']) )
{
	$arrAllowedShips = db_fetch_fields('
	SELECT
		s.id,
		s.id
	FROM
		d_all_units u,
		d_ships s
	WHERE
		u.id = s.id AND
		u.r_d_required_id IN (
			SELECT
				r_d_id
			FROM
				planet_r_d
			WHERE
				planet_id = '.PLANET_ID.' AND
				eta = 0
		);
	');

	foreach ( (array)$_POST['m'] AS $arrMove ) {
		if ( !isset($arrMove['ship_id'], $arrMove['amount'], $arrMove['from_fleet'], $arrMove['to_fleet']) || $arrMove['from_fleet'] === $arrMove['to_fleet'] ) {
			continue;
		}
		$szFleetAction1 = (string)db_select_one('fleets', 'action', 'owner_planet_id = '.PLANET_ID.' AND fleetname = \''.$arrMove['to_fleet'].'\'');
		$szFleetAction2 = (string)db_select_one('fleets', 'action', 'owner_planet_id = '.PLANET_ID.' AND fleetname = \''.$arrMove['from_fleet'].'\'');
		if ( $szFleetAction1 || $szFleetAction2 ) {
			continue;
		}

		$arrMove['amount'] = max(0, (int)$arrMove['amount']);
		if ( 'all' === $arrMove['ship_id'] ) {
			if ( 0 === $arrMove['amount'] ) {
				$arrMove['amount'] = true;
			}
			foreach ( $arrAllowedShips AS $iShip ) {
				moveShipsFromFleetToFleet( $iShip, $arrMove['amount'], $arrMove['from_fleet'], $arrMove['to_fleet'] );
			}
		}

		else if ( isset($arrAllowedShips[(int)$arrMove['ship_id']]) ) {
			if ( 0 === $arrMove['amount'] ) {
				$arrMove['amount'] = true;
			}
			moveShipsFromFleetToFleet( $arrMove['ship_id'], $arrMove['amount'], $arrMove['from_fleet'], $arrMove['to_fleet'] );
		}
	}
	Go();
}

// SEND FLEET FOR A MISSION //
else if ( isset($_POST['action'], $_POST['x'], $_POST['y'], $_POST['z'], $_POST['fleetname'], $_POST['period']) )
{
	if ( 0 < (int)$g_arrUser['newbie_ticks'] ) {
		exit('You are still under protection!');
	}

	// Check coordinates
	$arrTarget = db_select('galaxies g, planets p', 'p.galaxy_id = g.id AND x = '.(int)$_POST['x'].' AND y = '.(int)$_POST['y'].' AND z = '.(int)$_POST['z'].' AND p.id != '.PLANET_ID);
	if ( !count($arrTarget) ) {
		exit('Invalid coordinates!');
	}
	$arrTarget = $arrTarget[0];

	// Check fleet
	if ( !db_count('fleets', '`action` IS NULL AND owner_planet_id = '.PLANET_ID.' AND fleetname = \''.(int)$_POST['fleetname'].'\' AND fleetname != \'0\'') ) {
		exit('Invalid fleet!');
	}

	// Target planet properties
	if ( 0 < (int)$arrTarget['newbie_ticks'] || (int)$arrTarget['closed'] ) {
		exit('This planet is (still) under protection!');
	}

	if ( 0 < (int)$arrTarget['sleep'] ) {
		exit('This planet is in sleep mode!');
	}

	// Check action
	if ( 'attack' !== $_POST['action'] && 'defend' !== $_POST['action'] ) {
		exit('Invalid action!');
	}
	$iActionTime = min($iMaxActionTicks, max($iMinActionTicks, (int)$_POST['period']));

	// Calculate fleet eta
	$iEta = (int)db_select_one('d_ships s, ships_in_fleets sif, fleets f, d_all_units u', 'max(u.move_eta)', 'u.id = s.id AND s.id = sif.ship_id AND sif.fleet_id = f.id AND f.fleetname = \''.(int)$_POST['fleetname'].'\' AND f.owner_planet_id = '.PLANET_ID.' AND sif.amount > 0');
	if ( !$iEta ) {
		exit('ETA Error -> Invalid amount of ships or ships are broken -> Contact Admin!');
	}
	$iEta += (int)($g_arrUser['galaxy_id'] !== $arrTarget['galaxy_id'])*(int)$GAMEPREFS['military_extra_eta_outside_galaxy']; // galaxy
	$iEta += (int)($g_arrUser['x'] !== $arrTarget['x'])*(int)$GAMEPREFS['military_extra_eta_outside_cluster']; // cluster

//exit((string)$iEta);

	// Calculate fuel / tick
	$iFuelUsePerTick = (int)db_select_one('d_ships s, ships_in_fleets sif, fleets f, d_all_units u', 'sum(u.fuel*sif.amount)', 'u.id = s.id AND s.id = sif.ship_id AND sif.fleet_id = f.id AND f.fleetname = \''.(int)$_POST['fleetname'].'\' AND f.owner_planet_id = '.PLANET_ID.' AND sif.amount > 0');
	if ( !$iFuelUsePerTick ) {
		exit('Fuel Error -> Invalid amount of ships or ships are broken -> Contact Admin!');
	}

#	if ( $arrTarget['score'] <= $g_arrUser['score']/$iAttackScoreLimit ) {
#		exit('The planet is out of range! You can\'t attack planets this small!');
#	}

	// Energy consumation
	if ( !db_update('planets', 'energy=energy-'.($iFuelUsePerTick*$iEta), 'id = '.PLANET_ID.' AND energy >= '.($iFuelUsePerTick*$iEta)) || 0 >= db_affected_rows() ) {
		exit('Your planet does not have enough Energy!');
	}

	// Fleet orders
	if ( db_update('fleets', 'activated = \'0\', destination_planet_id = '.(int)$arrTarget['id'].', eta = '.(int)$iEta.', starteta = '.(int)$iEta.', action = \''.$_POST['action'].'\', actiontime = '.(int)$iActionTime.', startactiontime = '.(int)$iActionTime, 'owner_planet_id = '.PLANET_ID.' AND fleetname = \''.(int)$_POST['fleetname'].'\'') && 0 < db_affected_rows() ) {
		exit('OK');
	}
	exit('Something went wrong...');
}

// SELF-DESTRUCT FLEET //
else if ( isset($_GET['selfdestruct_fleetname']) )
{
	if ( 0 < (int)$_GET['selfdestruct_fleetname'] ) {
		$iFleetId = (int)db_select_one('fleets', 'id', 'fleetname = \''.(int)$_GET['selfdestruct_fleetname'].'\' AND owner_planet_id = '.PLANET_ID.'');
		// Kill all ships
		db_update('ships_in_fleets', 'amount = 0', 'fleet_id = '.$iFleetId);
		// `Fleet` status is idle and back home
		db_update('fleets', 'eta = 0, starteta = 0, action = null, destination_planet_id = null', 'id = '.$iFleetId);
	}
	exit('OK');
}

// RECALL FLEET //
else if ( isset($_GET['recall_fleetname']) )
{
	if ( 0 < (int)$_GET['recall_fleetname'] ) {
		// Recall fleet
		// If not activated yet
		db_update( 'fleets', 'activated = \'0\', eta = 0, starteta = 0, action = null, destination_planet_id = null', 'activated = \'0\' AND (action = \'attack\' OR action = \'defend\') AND fleetname = \''.(int)$_GET['recall_fleetname'].'\' AND owner_planet_id = '.PLANET_ID );
		// If activated and on the move already
		db_update( 'fleets', 'activated = \'1\', eta = starteta-eta, starteta = 0, action = \'return\'', 'activated = \'1\' AND (action = \'attack\' OR action = \'defend\') AND fleetname = \''.(int)$_GET['recall_fleetname'].'\' AND owner_planet_id = '.PLANET_ID );
	}
	exit('OK');
}

// ACTIVATE FLEETS //
else if ( !empty($_GET['activate_fleets']) )
{
	$arrFleetsToActivate = db_select('galaxies g, planets p, fleets f', 'g.id = p.galaxy_id AND p.id = f.destination_planet_id AND f.owner_planet_id = '.PLANET_ID.' AND (action = \'attack\' OR action = \'defend\') AND activated != \'1\'');
	foreach ( $arrFleetsToActivate AS $arrFleet ) {
		if ( db_update('fleets', 'activated = \'1\'', 'id = '.(int)$arrFleet['id']) && 0 < db_affected_rows() ) {
			$szHosFri = 'attack' == $arrFleet['action'] ? 'HOSTILE' : 'FRIENDLY';
			AddNews( constant('NEWS_SUBJECT_'.$szHosFri.'_INCOMING'), '<b>'.$g_arrUser['rulername'].' of '.$g_arrUser['planetname'].'</b> ('.$g_arrUser['x'].':'.$g_arrUser['y'].':'.$g_arrUser['z'].') sent his/her to attack us. ETA: '.$arrFleet['eta'].' ticks!', (int)$arrFleet['destination_planet_id'] );
			AddNews( constant('NEWS_SUBJECT_'.$szHosFri.'_OUTGOING'), 'We sent our fleet to attack <b>'.$arrFleet['rulername'].' of '.$arrFleet['planetname'].'</b> ('.$arrFleet['x'].':'.$arrFleet['y'].':'.$arrFleet['z'].'); ETA: '.$arrFleet['eta'].' ticks!', PLANET_ID, true );
		}
	}
	exit('OK');
}

_header();

?>
<style type="text/css">
table.fleets th,
table.fleets td {
	padding : 3px 8px;
}
</style>

<div class="header">Military</div>

<br />

<?php echo getFleetMatrix(PLANET_ID, true); ?>

<br />
<br />

<div class="header">Fleet Management</div>

<br />

I'm guessing you know how it works. If you don't, check <a href="manual.php">the Manual</a><br />
<br />
<form method="post" action="" autocomplete="off">
<table border=0 cellpadding=3 cellspacing=2 width=500 bordercolor=#444444 style='border:none;'>
<tr class="b">
	<td>UNIT TYPE</td>
	<td>AMOUNT</td>
	<td>FROM</td>
	<td>TO</td>
</tr>
<?php

for ( $i=0; $i<$iNumMovingRows; $i++ )
{
	?>
<tr>
<td width=150>
<select name="m[<?php echo $i; ?>][ship_id]" style="width:150px;">
<option value="all">- ALL UNITS
<option value="">--
<?php

foreach ( $t_arrShipNames AS $iShip => $szShip )
{
	echo '<option value="'.$iShip.'">'.$szShip.'</option>';
}

?>
</select>
</td>
<td width="150">
<input type="text" name="m[<?php echo $i; ?>][amount]" class="right" style="width:150px;" />
</td>
<td width=150>
<select name="m[<?php echo $i; ?>][from_fleet]" style="width:150px;">
<?php

foreach ( $t_arrFleetNames AS $iFleet => $szFleet )
{
	echo '<option value="'.$iFleet.'">'.$szFleet.'</option>';
}

?>
</select>
</td>
<td width=150>
<select name="m[<?php echo $i; ?>][to_fleet]" style="width:150px;">
<?php foreach ( $t_arrFleetNames AS $iFleet => $szFleet ) {
	echo '<option value="'.$iFleet.'">'.$szFleet.'</option>';
} ?>
</select>
</td> 
</tr>
	<?php
}

?>
<tr>
<td colspan=4 style='border:none;'><input type=submit value="Move Units" style='width:100%;font-weight:bold;font-size:13px;color:lime;'></td>
</tr>
</table>
</form>

<br />

<div class="header">Missions</div>

<br />

<div class="c">Dispatching to outside your galaxy, costs <?php echo $GAMEPREFS['military_extra_eta_outside_galaxy']; ?> ticks extra!</div>
<div class="c">Dispatching to outside your cluster, costs another <?php echo $GAMEPREFS['military_extra_eta_outside_cluster']; ?> ticks extra!</div>

<br />

<table id="overview_fleetstatus" border="0" cellpadding="3" cellspacing="0">
<?php

$arrFleets = db_fetch('SELECT *, (1) AS num_units FROM fleets f WHERE f.owner_planet_id = '.PLANET_ID.' AND fleetname != \'0\';');
$bDispatchButton = false;
foreach ( $arrFleets AS $arrFleet ) {
	$szFleet = '<b>'.$FLEETNAMES[$arrFleet['fleetname']].'</b>';
	$szEta = ( 0 == (int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA '.$arrFleet['eta'].', AT '.$arrFleet['actiontime'];

	if ( $arrFleet['action'] && isset($showcolors[$arrFleet['action']]) ) {
		$szTxtColor = ' style="color:'. $showcolors[$arrFleet['action']] .';"';
	}
	else {
		$szTxtColor = '';
	}
	echo '<tr'.$szTxtColor.'><td align="right">Fleet ['.$szFleet.']</td><td>';

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
			echo 'is returning'.( !empty($szDestination) ? ' from '.$szDestination : '' ).' (ETA: '.$arrFleet['eta'].')...';
		break;

		case 'attack':
		case 'defend':
			echo 'is'.( '1' !== $arrFleet['activated'] ? ' <b>NOT YET</b>' : '' ).' '.$arrFleet['action'].'ing '.$szDestination.' ('.$szEta.')...';
			if ( !(int)$arrFleet['activated'] ) {
				$bDispatchButton = true;
			}
		break;
	} // switch
	echo '</td>';
	echo '<td>'.( $arrFleet['action'] && 'return' != $arrFleet['action'] ? '<a href="?recall_fleetname='.$arrFleet['fleetname'].'" onclick="new Ajax(this.href,{onComplete:function(a){alert(a.responseText);}});return false;">recall</a>' : '' ).'</td>';
	echo '<td>'.( $arrFleet['action'] ? '<a href="?selfdestruct_fleetname='.$arrFleet['fleetname'].'" onclick="new Ajax(this.href,{onComplete:function(a){alert(a.responseText);}});return false;">self-destruct</a>' : '' ).'</td>';
	echo '<th>'.( !(int)$arrFleet['activated'] && $arrFleet['action'] && 'return' != $arrFleet['action'] ? '<input type="checkbox" name="fleetnames[]" value="'.$arrFleet['fleetname'].'" />' : '' ).'</th>';
	echo '</tr>';
}

if ( $bDispatchButton ) {
	echo '<tr><td colspan="2"></td><td colspan="3" align="right"><input type="button" value="Dispatch fleet(s)" onclick="new Ajax(\'?activate_fleets=1\', {onComplete:function(a){alert(a.responseText);}});" /></td></tr>';
}

?>
</table>

<br />

<?php if ( 1 < count($t_arrFleetNames) ): ?>
<form action="military.php" method="post" onsubmit="return postForm(this, function(a){var t=a.responseText;if('OK'!=t){alert(t);}else{document.location.reload();}});" autocomplete="off">
<table border="0" cellpadding="3" cellspacing="0" class="widecells">
<tr>
	<th>Action</td>
	<th>Target</td>
	<th>Fleet</td>
	<th>Period</td>
</tr>
<tr>
	<td><select name="action"><option value="">--</option><option value="attack">Attack</option><option value="defend">Defend</option></select></td>
	<td><input type="text" name="x" size="3" class="c">:<input type="text" name="y" size="3" class="c">:<input type="text" name="z" size="3" class="c"></td>
	<td><select name="fleetname"><option value="">--</option>
<?php foreach ( $t_arrFleetNames AS $iFleet => $szFleet ) {
	if ( $iFleet ) { echo '<option value="'.$iFleet.'">'.$szFleet.'</option>'; }
} ?>
</select></td>
	<td><select name="period"><?php for ( $i=(int)$GAMEPREFS['military_min_period']; $i<=(int)$GAMEPREFS['military_max_period']; $i++ ) { echo '<option'.( 5 === $i ? ' selected="1"' : '' ).' value="'.$i.'">'.$i.' tick(s)</option>'; } ?></select></td>
</tr>
<tr>
	<td colspan="3"></td>
	<td class="right"><input type="submit" value="Order" /></td>
</tr>
</table>

<br />
<?php endif; ?>

<?php

_footer();

?>