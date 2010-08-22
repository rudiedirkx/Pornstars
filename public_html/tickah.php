<?php

$fUtcStartTime = microtime(true);


if ( !defined('TICKERMAKER') ) {
	require_once('inc.config.php');
}



$TICKERTIMEOVERRIDE	= (bool)(int)$GAMEPREFS['tickertime_override'];	// If TRUE, the engine will run as much as its reloaded, no matter what the tickertime is
$AUTOREFRESH		= (bool)(int)$GAMEPREFS['ticker_autorefresh'];	// 



if ( isset($_GET['SET_USER_IS_TICKER']) )
{
	if ( $_GET['SET_USER_IS_TICKER'] )
	{
		$_SESSION['ps_is_ticker'] = 1;
		header("Location: ?special=yes");
	}
	else
	{
		unset($_SESSION['ps_is_ticker']);
		header("Location: leeg.php");
	}
	return;
}

echo "<title>TICKER</title><pre><body style=\"border-bottom:solid 1px #444444;\" style='margin:0px;overflow:auto;' bgcolor=" . ( !empty($_GET['special']) ? "red" : "white" ) . " scroll=no>";

$refreshingtime = (string)(int)max(0, ($GAMEPREFS['tickertime'] - $tickdif + 1));
if ( '0' == $refreshingtime ) {
	$refreshingtime = (string)(int)$GAMEPREFS['tickertime'];
}
//$refreshingtime = '1';


if ( !$GAMEPREFS['ticker_on'] )
{
	echo '<META http-equiv="refresh" content="'.$refreshingtime.'"><center>';
	if ( !$GAMEPREFS['tickcount'] )
	{
		echo "Ticker not started yet<br>Sign up and join!";
		return;
	}
	else
	{
		echo "TICKER DISABLED!<br>But be prepared";
		return;
	}
}

if ( $GAMEPREFS['tickcount'] >= $GAMEPREFS['general_gamestoptick'] && $GAMEPREFS['general_gamestoptick'] )
{
	$a = db_select('galaxies g, planets p', 'p.galaxy_id = g.id ORDER BY p.score DESC LIMIT 1');
	$r = $a[0];
	echo "You wont believe it, but the game has STOPPED! The winner:<br><b>".$r['rulername']." of ".$r['planetname']." from (".$r['x'].":".$r['y'].":".$r['z'].") with ".nummertje($r['score'])." points.";
	return;
}


if ( !empty($GAMEPREFS['ticker_password']) && ( !isset($_GET['p']) || md5($_GET['p']) != md5($GAMEPREFS['ticker_password']) ) ) {
	echo '<META http-equiv="refresh" content="'.$refreshingtime.'">Password error!';
	return;
}


if ( $tickdif < $GAMEPREFS['tickertime']-1 && !$TICKERTIMEOVERRIDE ) {
	echo isset($_GET['special']) ? "<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% bgcolor=red><tr valign=middle><td><center><font color=white face=Verdana style='font-size:18px;'><b>YOU TICK</td></tr></table>" : 'Less than '.$GAMEPREFS['tickertime'].' seconds since last tick!';
	echo '<META http-equiv="refresh" content="'.$refreshingtime.'">';
	return;
}
















db_update('prefs', 'tickcount = tickcount+1, last_tick = UNIX_TIMESTAMP(NOW())');

















// MISC 1 // SECTION C
db_update('planets', 'newbie_ticks = newbie_ticks-1', 'newbie_ticks > 0 AND lastaction > 0');

// Klaar met returnen -> army weer klaar
db_update('fleets', 'action = null, starteta = 0, actiontime = 0, startactiontime = 0, destination_planet_id = null', 'activated = \'1\' AND eta = 0 AND action = \'return\' AND fleetname != \'0\'');
// Crawl forward
db_update('fleets', 'eta = eta-1', 'activated = \'1\' AND eta > 0 AND fleetname != \'0\'');
// Vloten met 0 schepen zijn dood, dus meteen weer thuis
db_update('fleets', 'eta = 0, action = null, starteta = 0, actiontime = 0, startactiontime = 0, destination_planet_id = null', 'activated = \'1\' AND fleetname != \'0\' AND action IS NOT NULL AND 0 >= IFNULL((SELECT SUM(amount) FROM ships_in_fleets WHERE fleet_id = fleets.id),0)');
// Return want klaar met combat (alles leeg behalve eta en targetopties)
db_update('fleets', 'action = \'return\', eta = starteta', 'activated = \'1\' AND actiontime = 0 AND eta = 0 AND fleetname != \'0\' AND action IS NOT NULL');



// R & D //
if ( '1' !== $GAMEPREFS['news_for_done_rd'] ) {
	db_update( 'planet_r_d', 'eta = eta-1', 'eta > 0' );
}

// SKILL TRAINING //
db_update('skill_training', 'eta = eta-1', 'eta > 0');
db_update('planet_skills spp', 'value = value+1', '1 <= (SELECT COUNT(1) FROM skill_training WHERE skill_id = spp.skill_id AND planet_id = spp.planet_id AND eta = 0)');
db_delete('skill_training', 'eta <= 0');



db_update('planet_production', 'eta = eta-1 WHERE eta > 0');

## SHIPS ##
$arrProductions = db_fetch('SELECT p.planet_id, p.unit_id, SUM(p.amount) AS amount FROM planet_production p, d_all_units u WHERE u.id = p.unit_id AND u.T = \'ship\' GROUP BY planet_id, unit_id, eta HAVING eta = 0');
foreach ( $arrProductions AS $arrProd ) {
	$iUnitId = (int)$arrProd['unit_id'];
	addShipsToFleet($iUnitId, (int)$arrProd['amount'], '0', (int)$arrProd['planet_id']);
	db_delete('planet_production', 'unit_id = '.(int)$arrProd['unit_id'].' AND planet_id = '.(int)$arrProd['planet_id'].' AND eta = 0');
}

## DEFENCES ##
$arrProductions = db_fetch('SELECT p.planet_id, p.unit_id, SUM(p.amount) AS amount FROM planet_production p, d_all_units u WHERE u.id = p.unit_id AND u.T = \'defence\' GROUP BY planet_id, unit_id, eta HAVING eta = 0');
foreach ( $arrProductions AS $arrProd ) {
	$iUnitId = (int)$arrProd['unit_id'];
	if ( !db_count('defence_on_planets', 'planet_id = '.(int)$arrProd['planet_id'].' AND defence_id = '.$iUnitId) ) {
		db_insert('defence_on_planets', array('planet_id' => (int)$arrProd['planet_id'], 'defence_id' => $iUnitId, 'amount' => (int)$arrProd['amount']));
	}
	else {
		db_update( 'defence_on_planets', 'amount=amount+'.(int)$arrProd['amount'], 'planet_id = '.(int)$arrProd['planet_id'].' AND defence_id = '.$iUnitId.'');
	}
	db_delete('planet_production', 'unit_id = '.(int)$arrProd['unit_id'].' AND planet_id = '.(int)$arrProd['planet_id'].' AND eta = 0');
}

## WAVES / SCANS ##
$arrProductions = db_fetch('SELECT p.planet_id, p.unit_id, SUM(p.amount) AS amount FROM planet_production p, d_all_units u WHERE u.id = p.unit_id AND ( u.T = \'scan\' OR u.T = \'roidscan\' OR u.T = \'amp\' OR u.T = \'block\' ) GROUP BY planet_id, unit_id, eta HAVING eta = 0');
foreach ( $arrProductions AS $arrProd ) {
	$iUnitId = (int)$arrProd['unit_id'];
	if ( !db_count('waves_on_planets', 'planet_id = '.(int)$arrProd['planet_id'].' AND wave_id = '.$iUnitId) ) {
		db_insert('waves_on_planets', array('planet_id' => (int)$arrProd['planet_id'], 'wave_id' => $iUnitId, 'amount' => (int)$arrProd['amount']));
	}
	else {
		db_update( 'waves_on_planets', 'amount=amount+'.(int)$arrProd['amount'], 'planet_id = '.(int)$arrProd['planet_id'].' AND wave_id = '.$iUnitId.'');
	}
	db_delete('planet_production', 'unit_id = '.(int)$arrProd['unit_id'].' AND planet_id = '.(int)$arrProd['planet_id'].' AND eta = 0');
}




$g_arrShips = db_select_fields('d_all_units', 'id,0', '(T = \'ship\') ORDER BY id ASC');
$g_iRoidSnatcher = (int)db_select_one('d_all_units', 'id', '(T = \'ship\') AND steals = \'asteroids\' ORDER BY id ASC');
$g_arrDefences = db_select_fields('d_all_units', 'id,0', '(T = \'defence\') ORDER BY id ASC');
$g_arrUnits = db_select_fields('d_all_units', 'id,unit_plural', '(T = \'ship\' OR T = \'defence\') ORDER BY id ASC');
foreach ( $g_arrUnits AS $iUnitId => &$arrUnit ) {
	$arrUnit = array('name' => $arrUnit, 'id' => $iUnitId);
	$arrUnit['targets'] = db_select_fields('d_combat_stats', 'target_priority,concat(receiving_unit_id,\':\',ratio)', 'shooting_unit_id = '.(int)$iUnitId.' ORDER BY target_priority ASC LIMIT 3');
	foreach ( $arrUnit['targets'] AS &$t ) {
		$t = explode(':', $t);
	}
}
//print_r($g_arrUnits);




$allusers = db_select('galaxies g, planets p', 'p.galaxy_id = g.id ORDER BY x ASC, y ASC, z ASC');
foreach ( $allusers AS $arrUser )
{
	$id = (int)$arrUser['id'];

	// R & D NEWS
	if ( '1' === $GAMEPREFS['news_for_done_rd'] ) {
		$arrNames = array('d' => 'constructing', 'r' => 'researching');
		$arrRD = db_select( 'd_r_d_available a, planet_r_d p', 'a.id = p.r_d_id AND p.eta = 1 AND p.planet_id = '.$id );
		foreach ( $arrRD AS $rd ) {
			AddNews( NEWS_SUBJECT_R_D, 'Your technicians have finished researching <b>'.$rd['name'].'</b>.', $id );
		}
		db_update( 'planet_r_d', 'eta = eta-1', 'eta > 0 AND planet_id = '.$id );
	}


	// RESOURCES
	foreach ( db_select('planet_resources', 'planet_id = '.$id) AS $arrResource ) {
		$iGain = applyRDChange('income_'.$arrResource['resource_id'], res_per_type($arrResource['asteroids']), $id);
		if ( 0 < $iGain ) {
			db_update('planet_resources', 'amount = amount + '.$iGain, 'planet_id = '.$id.' AND resource_id = '.$arrResource['resource_id']);
		}
	}



#continue;



































// COMBAT // SECTION F

	// De info van de personen die $id AANVALLEN | $id = battlefield
	$szAWhere = 'destination_planet_id = '.$id.' AND eta = 0 AND action = \'attack\' AND actiontime > 0 AND activated = \'1\'';
	$arrAttackingFleets = db_select_by_field('fleets', 'id', $szAWhere);
	if ( 0 < count($arrAttackingFleets) )
	{
		$iAttackingOwners = db_select_one('fleets', 'COUNT(DISTINCT owner_planet_id)', $szAWhere);

		$szDWhere = '(destination_planet_id = '.$id.' AND eta = 0 AND action = \'defend\' AND actiontime > 0 AND activated = \'1\') OR (owner_planet_id = '.$id.' AND ((destination_planet_id IS NULL AND eta = 0) OR activated != \'1\'))';
		$arrDefendingFleets = db_select_by_field('fleets', 'id', $szDWhere);
		$iDefendingOwners = db_select_one('fleets', 'COUNT(DISTINCT owner_planet_id)', $szDWhere);

		if ( empty($_GET['special']) ) {
			echo '<br />&battlefield = $id = `'.$id.'`.<br />';
			echo 'Attacking: '.count($arrAttackingFleets).' fleets<br />';
			echo 'Defending: '.count($arrDefendingFleets).' fleets<br /><br />';
		}

		// ATTACKING FLEETS //
		$arrATotalShips = $arrATotalShipsLost = $arrABlockedShips = $g_arrShips;
		$arrRoidSnatchersPerFleet = array();
		// Numbers: per fleet & total
		foreach ( $arrAttackingFleets AS &$arrFleet )
		{
			$arrAShipsInFleet[$arrFleet['id']] = db_select_fields( 'ships_in_fleets', 'ship_id,amount', 'fleet_id = '.(int)$arrFleet['id'].' AND 0 < amount' ) + $g_arrShips;
			foreach ( $arrAShipsInFleet[$arrFleet['id']] AS $iShip => $iAmount ) {
				$arrATotalShips[$iShip] += $iAmount;
				if ( $g_iRoidSnatcher === (int)$iShip ) {
					if ( isset($arrRoidSnatchersPerFleet[$arrFleet['id']]) ) { $arrRoidSnatchersPerFleet[$arrFleet['id']] += $iAmount; }
					else { $arrRoidSnatchersPerFleet[$arrFleet['id']] = $iAmount; }
				}
			}
			unset($arrFleet);
		}
		// Percentages of total per fleet
		foreach ( $arrAShipsInFleet AS $iFleet => &$arrFleet ) {
			foreach ( $arrFleet AS $iShip => &$iAmount ) {
				$iAmount = array( $iAmount, ( 0 < $arrATotalShips[$iShip] ? $iAmount/$arrATotalShips[$iShip] : 0 ) );
				unset($iAmount);
			}
			unset($arrFleet);
		}
		$arrAInitialTotalShips = $arrATotalShips;


		// DEFENDING FLEETS //
		$arrDTotalShips = $arrDTotalShipsLost = $g_arrShips;
		// Numbers: per fleet & total
		foreach ( $arrDefendingFleets AS &$arrFleet )
		{
			$arrDShipsInFleet[$arrFleet['id']] = db_select_fields( 'ships_in_fleets', 'ship_id,amount', 'fleet_id = '.(int)$arrFleet['id'].' AND 0 < amount' ) + $g_arrShips;
			foreach ( $arrDShipsInFleet[$arrFleet['id']] AS $iShip => $iAmount ) {
				$arrDTotalShips[$iShip] += $iAmount;
			}
			unset($arrFleet);
		}
		// Percentages of total per fleet
		foreach ( $arrDShipsInFleet AS $iFleet => &$arrFleet ) {
			foreach ( $arrFleet AS $iShip => &$iAmount ) {
				$iAmount = array( $iAmount, ( 0 < $arrDTotalShips[$iShip] ? $iAmount/$arrDTotalShips[$iShip] : 0 ) );
				unset($iAmount);
			}
			unset($arrFleet);
		}
		$arrPlanetaryDefences = db_select_fields('defence_on_planets', 'defence_id,amount', '0 < amount AND planet_id = '.$id) + $g_arrDefences;
		$arrDBlockedShips = $g_arrShips + $g_arrDefences;
		$arrDTotalShips = $arrDTotalShips + $arrPlanetaryDefences;
		$arrDInitialTotalShips = $arrDTotalShips;


#echo 'ATTACKING FORCES PRE-COMBAT: ';
#print_r($arrAShipsInFleet);
#print_r($arrATotalShips);
#echo '<br />';

#echo 'DEFENDING FORCES PRE-COMBAT: ';
#print_r($arrDShipsInFleet);
#print_r($arrDTotalShips);
#echo '<br />';


		// AGRESSOR ATTACKS //
		foreach ( $arrATotalShips AS $iUnit => $iAmountOfUnits )
		{
			$iShootersLeft = ceil($iAmountOfUnits);
#echo 'Unit # '.$iUnit.' has '.$iShootersLeft.' shooters<br />';
			foreach ( $g_arrUnits[$iUnit]['targets'] AS $arrTarget ) {
				$iTarget = $arrTarget[0];
				if ( 0 < $iShootersLeft && isset($arrDTotalShips[$iTarget]) && 0 < ($arrDTotalShips[$iTarget]-$arrDBlockedShips[$iTarget]) ) {
					$fRatio = abs($arrTarget[1]);
#echo 'Unit # '.$iUnit.' shoots at units # '.$iTarget.' with '.$iShootersLeft.' shooters (ratio = '.$fRatio.')<br />';
					$fKills = min($arrDTotalShips[$iTarget]-$arrDBlockedShips[$iTarget], $fRatio * $iShootersLeft);
					$iShootersUsed = ceil( $fKills / $fRatio );
					if ( 0 > $arrTarget[1] ) {
						// EMP
						$arrDBlockedShips[$iTarget] += $fKills;
					}
					else {
						// KILL
						$arrDTotalShips[$iTarget] -= $fKills;
					}
$fTargetsLeft = $arrDTotalShips[$iTarget]-$arrDBlockedShips[$iTarget];
if ( empty($_GET['special']) ) {
	echo 'A->D: '.$iShootersLeft.' units # '.$iUnit.' shoot '.floor($fKills).' units # '.$iTarget.( 0 > $arrTarget[1] ? ' (EMP)' : '' ).' -> '.ceil($fTargetsLeft).' left<br />';
}
					$iShootersLeft -= $iShootersUsed;
				}
			}
#print_r($arrDTotalShips);
		}


#echo 'DEFENDING FORCE: ';
#print_r($arrDTotalShips);
#echo '<br />';


		// DEFENDER ATTACKS //
		foreach ( $arrDTotalShips AS $iUnit => $iAmountOfUnits )
		{
			$iShootersLeft = ceil($iAmountOfUnits);
#echo 'Unit # '.$iUnit.' has '.$iShootersLeft.' shooters<br />';
			foreach ( $g_arrUnits[$iUnit]['targets'] AS $arrTarget ) {
				$iTarget = $arrTarget[0];
				if ( 0 < $iShootersLeft && isset($arrATotalShips[$iTarget]) && 0 < ($arrATotalShips[$iTarget]-$arrABlockedShips[$iTarget]) ) {
					$fRatio = abs($arrTarget[1]);
#echo 'Unit # '.$iUnit.' shoots at units # '.$iTarget.' with '.$iShootersLeft.' shooters (ratio = '.$fRatio.')<br />';
					$fKills = min($arrATotalShips[$iTarget]-$arrABlockedShips[$iTarget], $fRatio * $iShootersLeft);
					$iShootersUsed = ceil( $fKills / $fRatio );
					if ( 0 > $arrTarget[1] ) {
						$arrABlockedShips[$iTarget] += $fKills;
					}
					else {
						$arrATotalShips[$iTarget] -= $fKills;
					}
$fTargetsLeft = $arrATotalShips[$iTarget]-$arrABlockedShips[$iTarget];
if ( empty($_GET['special']) ) {
	echo 'D->A: '.$iShootersLeft.' units # '.$iUnit.' shoot '.floor($fKills).' units # '.$iTarget.( 0 > $arrTarget[1] ? ' (EMP)' : '' ).' -> '.ceil($fTargetsLeft).' left<br />';
}
					$iShootersLeft -= $iShootersUsed;
				}
			}
#print_r($arrATotalShips);
		}


#echo 'ATTACKING FORCE: ';
#print_r($arrATotalShips);
#echo '<br />';


		foreach ( $arrATotalShips AS $iUnitId => &$iAmountLeft ) {
			$iInitialAmount = $arrAInitialTotalShips[$iUnitId];
			$iAmountLeft = max(0, floor($iAmountLeft));
			$arrATotalShipsLost[$iUnitId] = $iInitialAmount - $iAmountLeft;
			unset($iAmountLeft);
		}

		foreach ( $arrDTotalShips AS $iUnitId => &$iAmountLeft ) {
			$iInitialAmount = $arrDInitialTotalShips[$iUnitId];
			$iAmountLeft = max(0, floor($iAmountLeft));
			$arrDTotalShipsLost[$iUnitId] = $iInitialAmount - $iAmountLeft;
			unset($iAmountLeft);
		}

#echo 'ATTACKING FORCE LOST UNITS: ';
#print_r($arrATotalShipsLost);
#echo '<br />';

#echo 'DEFENDING FORCE LOST UNITS: ';
#print_r($arrDTotalShipsLost);
#echo '<br />';


		foreach ( $arrAShipsInFleet AS $iFleet => &$arrFleet ) {
			foreach ( $arrFleet AS $iShip => &$arrAmount ) {
				$arrAmount[0] = floor($arrAmount[1] * max(0, floor($arrATotalShips[$iShip])));
			}
			unset($arrFleet);
		}

		foreach ( $arrDShipsInFleet AS $iFleet => &$arrFleet ) {
			foreach ( $arrFleet AS $iShip => &$arrAmount ) {
				$arrAmount[0] = floor($arrAmount[1] * max(0, floor($arrDTotalShips[$iShip])));
			}
			unset($arrFleet);
		}

#echo 'ATTACKING FORCES POST-COMBAT: ';
#print_r($arrATotalShips);
#print_r($arrAShipsInFleet);
#echo '<br />';

#echo 'DEFENDING FORCES POST-COMBAT: ';
#print_r($arrDTotalShips);
#print_r($arrDShipsInFleet);
#echo '<br />';


		// todo:
		// - asteroid capturing -> divide asteroids among fleets and kill used units
		// - save ships + DEFENCE to database


		// - asteroid capturing - //
		$iRoidSnatchers = $arrATotalShips[$g_iRoidSnatcher] - $arrABlockedShips[$g_iRoidSnatcher];
		$iAllAsteroids = $arrUser['inactive_asteroids'] + array_sum($arrPlanetRoids=db_select_fields('planet_resources', 'resource_id,asteroids', 'planet_id = '.(int)$arrUser['id']));
		$iSnatchedAsteroids = min( $iRoidSnatchers, $iAllAsteroids / 10 );
		$iSnatchedAsteroidsPerFleet = floor($iSnatchedAsteroids / count($arrAShipsInFleet));
		// two default arrays
		$arrSnatchedRoidsPerResource = db_select_fields('d_resources', 'id,0');
		$arrSnatchedAsteroidsPerFleetPerResource = array();
		if ( 0 < $iSnatchedAsteroidsPerFleet ) {
			$arrSnatchedAsteroidsPerFleet = array();
			foreach ( $arrAShipsInFleet AS $iFleet => $arrFleet ) {
				$iFleetRoidSnatchers = $arrFleet[$g_iRoidSnatcher][0] - $arrABlockedShips[$g_iRoidSnatcher] * $arrFleet[$g_iRoidSnatcher][1];
				$arrSnatchedAsteroidsPerFleet = max(0, min($iSnatchedAsteroidsPerFleet, $iFleetRoidSnatchers));
			}
			if ( 0 < array_sum($arrSnatchedAsteroidsPerFleet) ) {
				$arrSnatchedAsteroidsPerFleetPerResource = array();
				$fPct = $arrUser['inactive_asteroids'] / $iAllAsteroids;
				foreach ( $arrSnatchedAsteroidsPerFleet AS $iFleet => $iAsteroids ) {
					$arrSnatchedAsteroidsPerFleetPerResource[$iFleet][0] = floor($iAsteroids*$fPct);
				}
				foreach ( $arrPlanetRoids AS $iResourceId => $iResourceAsteroids ) {
					$fPct = $iResourceAsteroids / $iAllAsteroids;
					foreach ( $arrSnatchedAsteroidsPerFleet AS $iFleet => $iAsteroids ) {
						$arrSnatchedAsteroidsPerFleetPerResource[$iFleet][$iResourceId] = floor($iAsteroids*$fPct);
					}
				}
				$arrSnatchedRoidsPerResource = array();
				foreach ( $arrSnatchedAsteroidsPerFleetPerResource AS $iFleet => $arrResources ) {
					foreach ( $arrResources AS $iResource => $iSnatchedRoids ) {
						if ( isset($arrSnatchedRoidsPerResource[$iResource]) ) { $arrSnatchedRoidsPerResource[$iResource] += $iSnatchedRoids; }
						else { $arrSnatchedRoidsPerResource[$iResource] = $iSnatchedRoids; }
					}
				}
				$iSnatchedAsteroids = array_sum($arrSnatchedRoidsPerResource);
				// Two very important variables known now:
				// For the defending planet: $arrSnatchedRoidsPerResource
				// For the attacking fleets: $arrSnatchedAsteroidsPerFleetPerResource
				// Update asteroids snatcher losses
				foreach ( $arrAShipsInFleet AS $iFleet => &$arrFleet ) {
					$iExtraLoss = min($arrSnatchedAsteroidsPerFleetPerResource[$iFleet], $arrFleet[$g_iRoidSnatcher][0]);
					$arrFleet[$g_iRoidSnatcher][0] -= $iExtraLoss;
					$arrATotalShips -= $iExtraLoss;
					unset($arrFleet);
				}
			}
		}
		// - asteroid capturing - //


		// - save new numbers into database - //
		// units //
		foreach ( $arrAShipsInFleet AS $iFleet => $arrFleet ) {
			foreach ( $arrFleet AS $iUnit => $arrAmount ) {
				db_update('ships_in_fleets', 'amount = '.max(0, $arrAmount[0]), 'fleet_id = '.$iFleet.' AND ship_id = '.$iUnit);
			}
		}
		foreach ( $arrDShipsInFleet AS $iFleet => $arrFleet ) {
			foreach ( $arrFleet AS $iUnit => $arrAmount ) {
				if ( isset($g_arrDefences[$iUnit]) ) {
					db_update('defence_on_planets', 'amount = '.max(0, $arrAmount[0]), 'planet_id = '.$id.' AND defence_id = '.$iUnit);
				}
				else {
					db_update('ships_in_fleets', 'amount = '.max(0, $arrAmount[0]), 'fleet_id = '.$iFleet.' AND ship_id = '.$iUnit);
				}
			}
		}
		// asteroids //
		if ( 0 < $iSnatchedAsteroids ) {
			# home planet
			foreach ( $arrSnatchedRoidsPerResource AS $iResource => $iLost ) {
				db_update('planet_resources', 'asteroids = MAX(0, asteroids-'.$iLost.')', 'planet_id = '.$id.' AND resource_id = '.$iResource);
			}
			# attacking fleets
			foreach ( $arrAttackingFleets AS $iFleet => $objFleet ) {
				foreach ( $arrSnatchedAsteroidsPerFleetPerResource[$iFleet] AS $iResource => $iSnatched ) {
					db_update('planet_resources', 'asteroids = asteroids+'.$iSnatched, 'planet_id = '.$objFleet['planet_id'].' AND resource_id = '.$iResource);
				}
			}
		}
		// - save new numbers into database - //


		// - save combat report - //
		$szCombatReport = '<table border="1" width="100%">';
		$szCombatReport .= '<tr><th colspan="4">BATTLE AT '.$arrUser['rulername'].' of '.$arrUser['planetname'].' ('.$arrUser['x'].':'.$arrUser['y'].':'.$arrUser['z'].')</th></tr>';
		$szCombatReport .= '<tr><td align="center" colspan="4">Attacking forces ('.count($arrAttackingFleets).' fleets, '.$iAttackingOwners.' owners):</td></tr>';
		$szCombatReport .= '<tr>';
		$szCombatReport .= '<td><br /></td>';
		$szCombatReport .= '<th align="right">Initial</th>';
//		$szCombatReport .= '<td align="right">Post-combat</td>';
		$szCombatReport .= '<th align="right">Lost</th>';
		$szCombatReport .= '<th align="right">Frozen</th>';
		$szCombatReport .= '</tr>';
		foreach ( $arrAInitialTotalShips AS $iUnitId => $iAmount ) {
			$szCombatReport .= '<tr>';
			$szCombatReport .= '<th align="right">'.$g_arrUnits[$iUnitId]['name'].' ['.$iUnitId.']</th>';
			$szCombatReport .= '<td align="right">'.nummertje($iAmount).'</td>';
//			$szCombatReport .= '<td align="right">'.nummertje($arrATotalShips[$iUnitId]).'</td>';
			$szCombatReport .= '<td align="right">'.nummertje($iAmount-$arrATotalShips[$iUnitId]).'</td>';
			$szCombatReport .= '<td align="right">'.nummertje($arrABlockedShips[$iUnitId]).'</td>';
			$szCombatReport .= '</tr>';
		}
		$szCombatReport .= '<tr><td align="center" colspan="4">Defending forces ('.count($arrDefendingFleets).' fleets, '.$iDefendingOwners.' owners):</td></tr>';
		$szCombatReport .= '<tr>';
		$szCombatReport .= '<td><br /></td>';
		$szCombatReport .= '<th align="right">Initial</th>';
//		$szCombatReport .= '<td align="right">Post-combat</td>';
		$szCombatReport .= '<th align="right">Lost</th>';
		$szCombatReport .= '<th align="right">Frozen</th>';
		$szCombatReport .= '</tr>';
		foreach ( $arrDInitialTotalShips AS $iUnitId => $iAmount ) {
			$szCombatReport .= '<tr>';
			$szCombatReport .= '<th align="right">'.$g_arrUnits[$iUnitId]['name'].' ['.$iUnitId.']</th>';
			$szCombatReport .= '<td align="right">'.nummertje($iAmount).'</td>';
//			$szCombatReport .= '<td align="right">'.nummertje($arrDTotalShips[$iUnitId]).'</td>';
			$szCombatReport .= '<td align="right">'.nummertje($iAmount-$arrDTotalShips[$iUnitId]).'</td>';
			$szCombatReport .= '<td align="right">'.nummertje($arrDBlockedShips[$iUnitId]).'</td>';
			$szCombatReport .= '</tr>';
		}
		$szCombatReport .= '<tr><td align="center" colspan="4">Asteroids lost: '.nummertje($iSnatchedAsteroids).'</td></tr>';
		foreach ( $arrSnatchedRoidsPerResource AS $iResource => $iAsteroidsLost ) {
			$szCombatReport .= '<tr><th colspan="3" align="right">Resource '.$iResource.'</th><td align="right">'.nummertje($iAsteroidsLost).'</td></tr>';
		}
		$szCombatReport .= '</table>';
		// - save combat report - //


		$arrPlanetIds = db_select_fields('fleets', 'owner_planet_id,1', 'id IN ('.implode(',', array_keys($arrDShipsInFleet)).') OR id IN ('.implode(',', array_keys($arrDShipsInFleet)).')');
		$arrPlanetIds[$id] = '1';
		$arrPlanetIds = array_keys($arrPlanetIds);
		foreach ( $arrPlanetIds AS $iPlanetId ) {
			AddNews(NEWS_SUBJECT_COMBAT, $szCombatReport, $iPlanetId);
		}

	} // 0 < count($arrAttackingFleets)

} // foreach $allusers

// decrease actiontime
db_update('fleets', 'actiontime=actiontime-1', 'actiontime > 0 AND eta = 0 AND activated = \'1\'');




/** CHECK NUM FLEETS **
$a = PSQ("SELECT owner_id,owner_x,owner_y,COUNT(*) AS num FROM $TABLE[fleets] GROUP BY owner_id ASC");
while (list($owner_id,$owner_x,$owner_y,$num_fleets) = mysql_fetch_row($a))
{
	$num_fleets-=1;
	if ($num_fleets < $NUM_OUTGOING_FLEETS)
	{
		// Add one or more fleets for this $owner_id
		for ($i=$num_fleets+1;$i<=$NUM_OUTGOING_FLEETS;$i++)
		{
			PSQ("INSERT INTO $TABLE[fleets] (owner_id,owner_x,owner_y,fleetname) VALUES ('$owner_id','$owner_x','$owner_y','$i');");
		}
	}
	else if ($num_fleets > $NUM_OUTGOING_FLEETS)
	{
		// Remove one or more fleets of this $owner_id and transfer all ships to fleet 'base' (ONLY FLEETS NOT ACTIVE, so purpose is empty: '')
		$b = PSQ("SELECT id,infinitys,wraiths,warfrigs,astropods,cobras,destroyers,scorpions,antennas,(infinitys+wraiths+warfrigs+astropods+cobras+destroyers+scorpions+antennas) AS sum_ships FROM fleets WHERE owner_id='$owner_id' AND purpose='' ORDER BY fleetname DESC LIMIT ".($num_fleets-$NUM_OUTGOING_FLEETS).";");
		while (list($fleet_id,$infinitys,$wraiths,$warfrigs,$astropods,$cobras,$destroyers,$scorpions,$antennas,$sum_ships) = mysql_fetch_row($b))
		{
			if ($sum_ships)
			{
				// $infinitys,$wraiths,$warfrigs,$astropods,$cobras,$destroyers,$scorpions,$antennas toevoegen aan fleet Base voor Owner $owner_id
				PSQ("UPDATE $TABLE[fleets] SET infinitys=infinitys+$infinitys,wraiths=wraiths+$wraiths,warfrigs=warfrigs+$warfrigs,astropods=astropods+$astropods,cobras=cobras+$cobras,destroyers=destroyers+$destroyers,scorpions=scorpions+$scorpions,antennas=antennas+$antennas WHERE owner_id='$owner_id' AND fleetname='0';");
			}
			// En de vloot die teveel is weggooien
			PSQ("DELETE FROM $TABLE[fleets] WHERE id='$fleet_id';");
		}
	}
}
/**/



# # #   S C O R E S   A N D   E N E R G Y   F O R   P L A N E T S   # # #
db_query('
UPDATE
	planets
SET
	score = 0.01 * (			/*SHIPS IN FLEETS*/
			SELECT
				IFNULL(SUM(
					s.amount *
					(
						SELECT
							(SELECT SUM(amount) FROM d_unit_costs WHERE unit_id = u.id)
						FROM
							d_all_units u
						WHERE
							s.ship_id = u.id
					)
				),0)
			FROM
				ships_in_fleets s,
				fleets f
			WHERE
				s.fleet_id = f.id AND
				f.owner_planet_id = planets.id
		) +
		0.01 * (				/*DEFENCE ON PLANETS*/
			SELECT
				IFNULL(SUM(
					dop.amount *
					(
						SELECT
							(SELECT SUM(amount) FROM d_unit_costs WHERE unit_id = u.id)
						FROM
							d_all_units u
						WHERE
							dop.defence_id = u.id
					)
				),0)
			FROM
				defence_on_planets dop
			WHERE
				dop.planet_id = planets.id
		) +
		0.01 * (				/*WAVES ON PLANETS*/
			SELECT
				IFNULL(SUM(
					wop.amount *
					(
						SELECT
							(SELECT SUM(amount) FROM d_unit_costs WHERE unit_id = u.id)
						FROM
							d_all_units u
						WHERE
							wop.wave_id = u.id
					)
				),0)
			FROM
				waves_on_planets wop
			WHERE
				wop.planet_id = planets.id
		) +
		150 * (SELECT SUM(asteroids) FROM planet_resources WHERE planet_id = planets.id) +
		ROUND( 0.002 * (SELECT SUM(amount) FROM planet_resources WHERE planet_id = planets.id) );
');
echo db_error();


$fExecutionTime = microtime(true) - $fUtcStartTime;
db_insert('ticks', array('exec_time' => $fExecutionTime));
$load_time = number_format($fExecutionTime, 4);


if ( !empty($_GET['special']) )
{
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% bgcolor=red><tr valign=middle><td><center><font color=white face=Verdana style='font-size:18px;'><b>YOU TICK</td></tr></table>";
	echo ($AUTOREFRESH)?"<META http-equiv=refresh content=$refreshingtime>":"";
}
else
{
	?>
<br />
<br />Tick done! (<?php echo ($MyT+1); ?>) (<?php echo $GAMEPREFS['tickertime']; ?>s.)<br />
Game stops <?php echo ($GAMEPREFS['general_gamestoptick']) ? "at ".$GAMEPREFS['general_gamestoptick'] : "never!"; ?><br />
Ticker loaded in <?php echo $load_time; ?> sec
<?php

	echo $AUTOREFRESH ? '<meta http-equiv="refresh" content="'.$refreshingtime.'">' : '<b>!-Not Auto-!</b>';

	if ($GAMEPREFS['debug_mode'] == 1)
	{
		echo "\n\nUsed ".$g_iQueries." queries...";
		print_r($g_arrQueries);
	}
}

?>
