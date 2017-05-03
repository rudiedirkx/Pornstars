<?php

require 'inc.bootstrap.php';

logincheck();

$iNumMovingRows = 3;
// $iAttackScoreLimit	= max(1, (int)$GAMEPREFS['military_scorelimit']); //  2
// $iMinActionTicks	= max(1, (int)$GAMEPREFS['military_min_period']);
// $iMaxActionTicks	= max($iMinActionTicks, (int)$GAMEPREFS['military_max_period']);

// MOVE SHIPS //
if ( isset($_POST['m']) ) {
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
else if ( isset($_POST['action'], $_POST['x'], $_POST['y'], $_POST['z'], $_POST['fleetname'], $_POST['period']) ) {
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
else if ( isset($_GET['selfdestruct_fleetname']) ) {
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
else if ( isset($_GET['recall_fleetname']) ) {
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
else if ( !empty($_GET['activate_fleets']) ) {
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
<h1>Military</h1>

<?= getFleetMatrix($g_user, true) ?>

<h2>Fleet Management</h2>

<form method="post" action autocomplete="off">
	<table>
		<tr class="b">
			<td>Unit</td>
			<td>Amount</td>
			<td>From</td>
			<td>To</td>
		</tr>
		<? for ( $i = 0; $i < $iNumMovingRows; $i++ ): ?>
			<tr>
				<td>
					<select>
						<option value="">--</option>
						<option value="all">-- All units</option>
						<?= html_options($g_user->ships) ?>
					</select>
				</td>
				<td>
					<input type="number" />
				</td>
				<td>
					<select>
						<option value="">--</option>
						<?= html_options($g_user->fleets) ?>
					</select>
				</td>
				<td>
					<select>
						<option value="">--</option>
						<?= html_options($g_user->fleets) ?>
					</select>
				</td>
			</tr>
		<? endfor ?>
		<tr>
			<td colspan="4">
				<button>Move Units</button>
			</td>
		</tr>
	</table>
</form>

<h2>Missions</h2>

<table>
	<? foreach ( $g_user->fleets as $fleet ): ?>
		<tr class="<?= html($fleet->action) ?>ing fleet">
			<th><?= html($fleet) ?></th>
			<td>
				<? if ( $fleet->action == 'return' ): ?>
					is returning from <?= $fleet->destination_planet ?> (ETA: <?= $fleet->eta ?>)
				<? elseif ( $fleet->action == 'attack' ): ?>
					is <?= $fleet->activated ? '' : 'NOT YET' ?> attacking <?= $fleet->destination_planet ?> (ETA: <?= $fleet->eta ?>)
				<? elseif ( $fleet->action == 'defend' ): ?>
					is <?= $fleet->activated ? '' : 'NOT YET' ?> defending <?= $fleet->destination_planet ?> (ETA: <?= $fleet->eta ?>)
				<? elseif ( $fleet->fleetname ): ?>
					is ready to be sent to
					<input class="coord" type="number" /> :
					<input class="coord" type="number" /> :
					<input class="coord" type="number" />
					for
					<input class="coord" type="number" />
					ticks
				<? else: ?>
					is idling at home...
				<? endif ?>
			</td>
			<td>
				<select>
					<option value="">--</option>
					<?= html_options($fleet->available_actions) ?>
				</select>
			</td>
		</tr>
	<? endforeach ?>
</table>

<?php

_footer();
