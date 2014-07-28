<?php


function db_transaction_update( $f_arrUpdates, $f_szIfField, $f_szUpdateField ) {
	db_query("BEGIN;");
	$szIfClause = '__N__';
	$szIfClause0 = 'IF('.$f_szIfField.'=__X__,__Y__,__N__)';
	foreach ( $f_arrUpdates AS $x => $y ) {
		$szIfClause = str_replace('__N__', str_replace('__X__', $x, str_replace('__Y__', $y, $szIfClause0)), $szIfClause);
	}
	$szIfClause = str_replace('__N__', '0', $szIfClause);
	db_query('UPDATE planet_resources SET '.$f_szUpdateField.' = '.$f_szUpdateField.' - '.$szIfClause.' WHERE '.$f_szUpdateField.' >= '.$szIfClause.' AND planet_id = '.PLANET_ID.';');
	if ( count($f_arrUpdates) === (int)db_affected_rows() ) {
		db_query("COMMIT;");
		return true;
	}
	db_query("ROLLBACK;");
	return false;
}


function _footer() {
	global $g_arrUser, $st, $stF, $titlearray;
	require_once('inc.footer.php');
}
function _header() {
	global $g_arrUser, $tickdif, $GAMEPREFS, $titlearray;
	require_once('inc.header.php');
}

function rand_string( $f_iLength = 8 )
{
	$arrTokens = array_merge( range("a","z"), range("A","Z"), range("0","9") );

	$szRandString = "";
	for ( $i=0; $i<max(1, (int)$f_iLength); $i++ )
	{
		$szRandString .= $arrTokens[array_rand($arrTokens)];
	}

	return $szRandString;
}


function fullname($f_iPlanetId, $f_bIncGalaxy = true) {
	if ( $f_bIncGalaxy ) {
		$szFullname = db_select_one('planets p, galaxies g', 'concat(p.rulername,\' of \',p.planetname,\' (\',x,\':\',y,\':\',z,\')\')', 'g.id = p.galaxy_id AND p.id = '.(int)$f_iPlanetId);
	}
	else {
		$szFullname = db_select_one('planets p', 'concat(p.rulername,\' of \',p.planetname,\' (\',z,\')\')', 'id = '.(int)$f_iPlanetId);
	}
	return $szFullname;
}


function shipsInFleet( $f_iShipId, $f_szFleet, $f_iPlanetId = PLANET_ID ) {
	$iShipsInFleet = db_select_one('fleets f, ships_in_fleets s', 's.amount', 's.fleet_id = f.id AND f.fleetname = \''.$f_szFleet.'\' AND s.ship_id = '.(int)$f_iShipId.' AND f.owner_planet_id = '.$f_iPlanetId);
	if ( false === $iShipsInFleet ) {
		$iFleetId = db_select_one('fleets', 'id', 'owner_planet_id = '.$f_iPlanetId.' AND fleetname = \''.$f_szFleet.'\'');
		if ( false === $iFleetId ) {
			db_insert('fleets', array('owner_planet_id' => $f_iPlanetId, 'fleetname' => $f_szFleet));
			$iFleetId = db_insert_id();
		}
		db_insert('ships_in_fleets', array('fleet_id' => (int)$iFleetId, 'ship_id' => $f_iShipId));
		return 0;
	}
	return (int)$iShipsInFleet;
}

function deleteShipsFromFleet( $f_iShipId, $f_iAmount, $f_szFleet, $f_iPlanetId = PLANET_ID ) {
	$iShipsInFleet = shipsInFleet($f_iShipId, $f_szFleet);
	$iShipsToMove = true === $f_iAmount ? $iShipsInFleet : min($f_iAmount, $iShipsInFleet);
	db_update('ships_in_fleets', 'amount=amount-'.$iShipsToMove, 'fleet_id = (SELECT id FROM fleets WHERE owner_planet_id = '.$f_iPlanetId.' AND fleetname = \''.$f_szFleet.'\') AND ship_id = '.(int)$f_iShipId);
	return $iShipsToMove;
}

function addShipsToFleet( $f_iShipId, $f_iAmount, $f_szFleet, $f_iPlanetId = PLANET_ID ) {
	// Make sure this fleet exists
	shipsInFleet($f_iShipId, $f_szFleet, $f_iPlanetId);
	// And add the ships
	return db_update('ships_in_fleets', 'amount=amount+'.$f_iAmount, 'fleet_id = (SELECT id FROM fleets WHERE owner_planet_id = '.$f_iPlanetId.' AND fleetname = \''.$f_szFleet.'\') AND ship_id = '.(int)$f_iShipId);
}

function moveShipsFromFleetToFleet( $f_iShipId, $f_iAmount, $f_szFromFleet, $f_szToFleet, $f_iPlanetId = PLANET_ID ) {
	$iAmount = deleteShipsFromFleet( $f_iShipId, $f_iAmount, $f_szFromFleet, $f_iPlanetId );
	addShipsToFleet( $f_iShipId, $iAmount, $f_szToFleet, $f_iPlanetId );
}


function logincheck( $f_bAct = true ) {
	global $sessionname, $g_arrUser, $g_arrResources;
	if ( defined('PLANET_ID') ) {
		return true;
	}
	if ( !isset($_SESSION[$sessionname]['planet_id'], $_SESSION[$sessionname]['unihash']) || !count($arrUsers=db_select('d_races r, galaxies g, planets p', "g.id = p.galaxy_id AND r.id = p.race_id AND p.id = ".(int)$_SESSION[$sessionname]['planet_id']." AND p.unihash = '".addslashes($_SESSION[$sessionname]['unihash'])."' AND closed != '1' LIMIT 1")) ) {
		unset($_SESSION[$sessionname]);
		if ( $f_bAct ) {
			exit('<a href="./login.php">Invalid session!</a>');
		}
		return false;
	}
	$g_arrUser = $arrUsers[0];
	if ( !defined('PLANET_ID') ) {
		define( 'PLANET_ID', (int)$g_arrUser['id'] );
	}
	$g_arrResources = db_select_by_field('d_resources r, planet_resources p', 'id', 'p.planet_id = '.PLANET_ID.' AND p.resource_id = r.id ORDER BY r.id ASC');
echo db_error();
	db_update('planets', 'lastaction = '.time(), 'id = '.PLANET_ID);
	return true;
}


function nummertje($n) {
	return number_format((int)$n, 0, ".", "," );
}


function addProductions( $f_szType, $f_arrUnits ) {
	global $GAMEPREFS;
	$arrUnits = array();
	foreach ( $f_arrUnits AS $iUnit => $iAmount ) {
		if ( 0 < (int)trim($iAmount) && count($u=db_select('d_all_units u, planet_r_d p','u.id = '.$iUnit.' AND p.r_d_id = u.r_d_required_id AND p.eta = 0 AND p.planet_id = '.PLANET_ID.' AND u.T IN (\''.str_replace(',', "','", $f_szType)."')")) ) {
			$arrUnit = $u[0];
			$arrAmounts = array((int)$iAmount);
			$arrFunds = db_select_fields('planet_resources', 'resource_id,amount', 'planet_id = '.PLANET_ID);
			$arrUnitCosts = db_select_fields('d_unit_costs', 'resource_id,amount', 'unit_id = '.$iUnit.' AND 0 < amount');
			foreach ( $arrUnitCosts AS $iResourceId => $iCosts ) {
				$arrAmounts[] = floor($arrFunds[$iResourceId]/$iCosts);
			}
			$iAmount = min($arrAmounts);
			if ( 0 < $iAmount ) {
				$arrCosts = array();
				foreach ( $arrUnitCosts AS $iResourceId => $iCosts ) {
					$arrCosts[$iResourceId] = $iAmount*$iCosts;
				}
				if ( 0 === array_sum($arrCosts) || db_transaction_update($arrCosts, 'resource_id', 'amount') ) {
					$arrInsert = array(
						'planet_id'	=> PLANET_ID,
						'unit_id'	=> $iUnit,
						'eta'		=> ( (int)$GAMEPREFS['havoc_production'] ? 0 : (int)$arrUnit['build_eta'] ),
						'amount'	=> $iAmount,
					);
					db_insert('planet_production', $arrInsert);
				}
			}
		}
	}

} // END addProductions()


function applyRDChange( $f_szType, $f_iInitialValue, $f_iPlanetId = PLANET_ID ) {
	$arrTFuncs = array(
		'travel_eta'		=> 'ceil',
		'r_d_eta'			=> 'ceil',
		'r_d_costs'			=> 'ceil',
		'fuel_use'			=> 'ceil',
	);
	if ( isset($arrTFuncs[$f_szType]) ) {
		$szTFunc = $arrTFuncs[$f_szType];
	}
	else if ( 'income_' === substr($f_szType, 0, 7) && isint(substr($f_szType, 7)) ) {
		$szTFunc = 'floor';
	}
	else {
		return $f_iInitialValue;
	}

	$szSqlQuery = '
	SELECT
		*
	FROM
		d_r_d_results
	WHERE
		type = \''.$f_szType.'\' AND
		done_r_d_id in (
			SELECT
				r_d_id
			FROM
				planet_r_d
			WHERE
				planet_id = '.$f_iPlanetId.' AND
				eta = 0
		) AND
		enabled = \'1\'
	ORDER BY
		o ASC;';
	$arrRDResults = db_fetch($szSqlQuery);
	foreach ( $arrRDResults AS $arrChange ) {
		if ( 'pct' == $arrChange['unit'] ) {
			$f_iInitialValue *= abs($arrChange['change']);
		}
		else {
			$f_iInitialValue += $arrChange['change'];
		}
	}
	return call_user_func($szTFunc, $f_iInitialValue);

} // END applyRDChange()


function getFleetMatrix( $f_iPlanetId = PLANET_ID, $f_bPrintDetails = true ) {
	global $NUM_OUTGOING_FLEETS, $FLEETNAMES, $showcolors, $t_arrFleetNames, $t_arrShipNames;

	$arrShips = db_fetch('
SELECT
	u.*,
	s.id AS ship_id,
	IFNULL((SELECT
			SUM(amount)
		FROM
			fleets f,
			ships_in_fleets sf
		WHERE
			f.owner_planet_id = '.$f_iPlanetId.' AND
			f.id = sf.fleet_id AND
			sf.ship_id = s.id AND
			f.fleetname = \'0\'
	),0) AS num_units
FROM
	d_all_units u,
	d_ships s
WHERE
	s.id = u.id AND
	u.r_d_required_id IN (
		SELECT
			rdpp.r_d_id
		FROM
			planet_r_d rdpp
		WHERE
			rdpp.planet_id = '.$f_iPlanetId.' AND
			rdpp.eta = 0
	)
ORDER BY
	s.id ASC;
');

	$szHtml = '';
	$szHtml .= '<table class="fleets" border="0" cellpadding="2" cellspacing="0"'.( !$f_bPrintDetails ? ' align="center"' : '' ).'><tr class="bb"><td>&nbsp;</td>';

	$t_arrFleetNames = $t_arrShipNames /*= $arrFleetETAs = $arrFleetCostsPerTick*/ = array();
	for ( $iFleetName=0; $iFleetName<=$NUM_OUTGOING_FLEETS; $iFleetName++ )
	{
		$arrFleetETAs[$iFleetName] = $arrFleetCostsPerTick[$iFleetName] = 0;

		$arrFleet = db_select('fleets', 'owner_planet_id = '.$f_iPlanetId.' AND fleetname = \''.$iFleetName.'\'');
		$arrFleet = $arrFleet[0];
		$szFleetAction = $arrFleet['action'];
		if ( !$szFleetAction ) {
			$t_arrFleetNames[$iFleetName] = $FLEETNAMES[(int)$iFleetName];
			$szTxtColor = '';
		}
		else {
			$szTxtColor = ' style="color:' . $showcolors[$szFleetAction] . ';"';
		}
		$szHtml .= '<th'.$szTxtColor.' width="70" nowrap="nowrap" class="right" title="'.$arrFleet['id'].'" id="fleetmatrix_fleet_'.$iFleetName.'">'.$FLEETNAMES[(int)$iFleetName].'</th>';
	}
	$szHtml .= '</tr>';

	foreach ( $arrShips AS $k => $arrShip )
	{
		$t_arrShipNames[(int)$arrShip['ship_id']] = $arrShip['unit_plural'];
		$szHtml .= '<tr class="bt'.( $f_bPrintDetails && $k == count($arrShips)-1 ? ' bb' : '' ).'">';
		$szHtml .= '<td nowrap="nowrap" title="'.$arrShip['id'].': ETA = '.$arrShip['move_eta'].', Fuel use = '.$arrShip['fuel'].'">'.htmlspecialchars($arrShip['unit_plural']).'</td>';
		$szHtml .= '<td align="right">'.nummertje($arrShip['num_units']).'</td>';
		for ( $iFleetName=1; $iFleetName<=$NUM_OUTGOING_FLEETS; $iFleetName++ )
		{
			$iShips = shipsInFleet($arrShip['ship_id'], $iFleetName, $f_iPlanetId);
#			if ( 0 < $iShips && $arrShip['move_eta'] > $arrFleetETAs[$iFleetName] ) {
#				$arrFleetETAs[$iFleetName] = $arrShip['move_eta'];
#			}
#			$arrFleetCostsPerTick[$iFleetName] += $iShips * $arrShip['fuel'];
			$szHtml .= '<td align="right">'.nummertje($iShips).'</td>';
		}
		$szHtml .= '</tr>';
	}
	if ( $f_bPrintDetails ) {
		$szHtml .= '<tr class="bt">';
		$szHtml .= '<td colspan="2" class="right">Min. ETA:</td>';
//		$szHtml .= '<td>&nbsp;</td>'; // Home fleet
		$arrEtas = array();
		for ( $iFleetName=1; $iFleetName<=$NUM_OUTGOING_FLEETS; $iFleetName++ )
		{
			$iEta = db_select_one('d_ships s, ships_in_fleets sif, fleets f, d_all_units u', 'max(u.move_eta)', 'u.id = s.id AND s.id = sif.ship_id AND sif.fleet_id = f.id AND f.fleetname = \''.(int)$iFleetName.'\' AND f.owner_planet_id = '.$f_iPlanetId.' AND sif.amount > 0');
			$iEta = applyRDChange('travel_eta', $iEta, $f_iPlanetId);
			$szHtml .= '<td align="right">'.$iEta.'</td>';
			$arrEtas[$iFleetName] = $iEta;
		}
		$szHtml .= '</tr>';
		$szHtml .= '<tr class="bb">';
		$szHtml .= '<td colspan="2" class="right" nowrap="1" wrap="off">Min. Fuel use:</td>';
//		$szHtml .= '<td>&nbsp;</td>'; // Home fleet
		for ( $iFleetName=1; $iFleetName<=$NUM_OUTGOING_FLEETS; $iFleetName++ )
		{
			$iFuelUsePerTick = db_select_one('d_ships s, ships_in_fleets sif, fleets f, d_all_units u', 'sum(u.fuel*sif.amount)', 'u.id = s.id AND s.id = sif.ship_id AND sif.fleet_id = f.id AND f.fleetname = \''.(int)$iFleetName.'\' AND f.owner_planet_id = '.$f_iPlanetId.' AND sif.amount > 0');
			$szHtml .= '<td align="right" style="color:'.$showcolors['energy'].';">'.nummertje($arrEtas[$iFleetName]*$iFuelUsePerTick).'</td>';
		}
		$szHtml .= '</tr>';
	}
	$szHtml .= '</table>';

	return $szHtml;
}


function getProductionList( $f_szTypes, $f_iPlanetId = PLANET_ID ) {
	$szSqlQuery = '
	SELECT
		u.id,
		u.unit_plural,
		sum(p.amount) AS amount
	FROM
		d_all_units u,
		planet_production p
	WHERE
		u.id = p.unit_id AND
		p.planet_id = '.(int)$f_iPlanetId.' AND
		p.eta >= 0 AND
		u.T IN (\''.str_replace(',', "','", $f_szTypes).'\')
	GROUP BY
		unit_id
	ORDER BY
		u.T ASC,
		u.o ASC;
	';
	$arrUnits = db_fetch($szSqlQuery);

	if ( !count($arrUnits) ) {
		return '';
	}

	$szHtml = '<table border="0" cellpadding="2" cellspacing="0" class="widecells"><tr><td class="right"><i>ETAs:</i></td>';

	$arrETAs = array_values(db_select_fields('d_all_units u, planet_production p', 'p.eta,p.eta', 'u.id = p.unit_id AND u.T IN (\''.str_replace(',', "','", $f_szTypes).'\') AND p.planet_id = '.$f_iPlanetId.' AND p.eta >= 0 ORDER BY p.eta ASC'));
	$arrHorMatrix = array();
	foreach ( $arrETAs AS $k => $iETA ) {
		if ( $k && $iETA-1 > $arrETAs[$k-1] ) {
			$szHtml .= '<td>..</td>';
			$arrHorMatrix[] = '-';
		}
		$szHtml .= '<th class="right">'.$iETA.'</th>';
		$arrHorMatrix[] = (int)$iETA;
	}
	$szHtml .= '</tr';

	$t_arrProductions = db_fetch('SELECT unit_id, eta, SUM(amount) AS amount FROM planet_production WHERE planet_id = '.$f_iPlanetId.' GROUP BY unit_id, eta HAVING amount > 0 AND eta >= 0');
	$arrProductions = array();
	foreach ( $t_arrProductions AS $r ) {
		$arrProductions[(int)$r['unit_id']][(int)$r['eta']] = $r['amount'];
	}

	foreach ( $arrUnits AS $arrUnit ) {
		$szHtml .= '<tr class="bt">';
		$szHtml .= '<th class="right" nowrap="nowrap" wrap="off">'.$arrUnit['unit_plural'].'</th>';
		foreach ( $arrHorMatrix AS $iETA ) {
			$szHtml .= '<td class="right">'.( isset($arrProductions[(int)$arrUnit['id']][(int)$iETA]) ? nummertje($arrProductions[(int)$arrUnit['id']][(int)$iETA]) : '&nbsp;' ).'</td>';
		}
		$szHtml .= '</tr>';
	}
	$szHtml .= '</table>';

	return $szHtml;

} // END getProductionList()


function getProductionForm( $f_szTypes, $f_iPlanetId = PLANET_ID ) {
	$szSqlQuery = '
	SELECT
		u.*
	FROM
		d_all_units u,
		planet_r_d p
	WHERE
		u.r_d_required_id = p.r_d_id AND
		p.planet_id = '.(int)$f_iPlanetId.' AND
		p.eta = 0 AND
		u.T IN (\''.str_replace(',', "','", $f_szTypes).'\')
	ORDER BY
		u.T ASC,
		u.o ASC;
	';
	$arrUnits = db_fetch($szSqlQuery);

	global $showcolors, $GAMEPREFS, $g_arrResources;
	$szHtml = $szLastType = '';

	$szHtml .= '
<form id="f_order_units" method="post" action="" autocomplete="off" onsubmit="return postForm(this,H);">
<table border="0" cellpadding="3" cellspacing="0" width="90%" align="center" class="widecells">
<tr>
	<th>&nbsp;</th>
	<th class="left">Name</th>
	<th>ETA</th>
	<th class="right">Costs</th>
	<th class="right">In&nbsp;stock</th>
	<th>Order</th>
</tr>';

	foreach ( $arrUnits AS $k => $arrUnit ) {
		if ( $szLastType !== $arrUnit['T'] ) {
			if ( $k ) {
				$szHtml .= '<tr class="bt"><td colspan="6">&nbsp;</td></tr>';
			}
			switch ( $arrUnit['T'] )
			{
				case 'ship':
					$szSqlTable = 'ships_in_fleets s, fleets f';
					$szSqlWhere = 's.ship_id = __UNIT_ID__ AND f.id = s.fleet_id AND f.owner_planet_id = '.(int)$f_iPlanetId;
				break;
				case 'defence':
					$szSqlTable = 'defence_on_planets';
					$szSqlWhere = 'defence_id = __UNIT_ID__ AND planet_id = '.(int)$f_iPlanetId;
				break;
				case 'roidscan':
				case 'scan':
				case 'block':
				case 'amp':
					$szSqlTable = 'waves_on_planets';
					$szSqlWhere = 'wave_id = __UNIT_ID__ AND planet_id = '.(int)$f_iPlanetId;
				break;
				case 'power':
					$szSqlTable = 'power_on_planets';
					$szSqlWhere = 'power_id = __UNIT_ID__ AND planet_id = '.(int)$f_iPlanetId;
				break;
				default:
					return '';
				break;
			}
			$szLastType = $arrUnit['T'];
		}
		$szHtml .= '<tr valign="top" class="bt">';
		// ID
		$szHtml .= '<td width="10%" align="right">'.$arrUnit['id'].'</td>';
		// Name
		$szHtml .= '<td width="100%"><i onclick="TD(this.parentNode.getElementsByTagName(\'div\')[0]);" style="cursor:pointer;">'.$arrUnit['unit_plural'].'</i><div style="display:none;font-size:10px;">'.$arrUnit['explanation'].'</div></td>';
		// ETA
		$szHtml .= '<td width="10%" align="center">'.( (int)$GAMEPREFS['havoc_production'] ? '0' : $arrUnit['build_eta'] ).'</td>';
		$arrPreCosts = db_select_fields('d_unit_costs', 'resource_id,amount', '0 < amount AND unit_id = '.(int)$arrUnit['id'].' ORDER BY resource_id ASC');
		$arrCosts = array();
		foreach ( $arrPreCosts AS $iResourceId => $iAmount ) {
			$iAmount = (int)$iAmount;
			$arrCosts[] = '<span style="color:'.$g_arrResources[$iResourceId]['color'].';">'.nummertje($iAmount).'&nbsp;'.strtolower($g_arrResources[$iResourceId]['resource']).'</span>';
		}
		$szCosts = $arrCosts ? implode('<br />', $arrCosts) : '-';
		// Costs
		$szHtml .= '<td width="10%" class="right">'.$szCosts.'</td>';
		$iInStock = (int)db_select_one($szSqlTable, 'SUM(amount)', str_replace('__UNIT_ID__', (int)$arrUnit['id'], $szSqlWhere));
		// In stock
		$szHtml .= '<td width="10%" class="right" id="unit_amount_'.$arrUnit['id'].'">'.nummertje($iInStock).'</td>';
		// Order
		$szHtml .= '<td width="10%" class="c"><input autocomplete="off" type="text" name="order_units['.$arrUnit['id'].']" value="" style="width:45px;text-align:right;padding:2px;" maxlength="5" /></td>';
		$szHtml .= '</tr>';
	}
	$szHtml .= '
<tr>
	<td colspan="5">&nbsp;</td>
	<td class="c"><input type="submit" value="Order" /></td>
</tr>
</table>
</form>';
	return $szHtml;

} // END getProductionForm()


function Go( $to = PARENT_SCRIPT_NAME, $die = 0 )
{
	if ($die)
	{
		die("$to<br><a href=\"$to\">Go There</a>");
	}
	else
	{
		Header("Location: $to");
		exit();
	}
}


function Verschil_In_Tijd( $tijd ) {
	$dagen = $uren = $minuten = $seconden = 0;

	if ( $tijd >= 3600*24 )	{ $dagen = floor($tijd/3600/24); }
	$tijd -= 3600*24*$dagen;
	if ( $tijd >= 60*60 )	{ $uren = floor($tijd/3600); }
	$tijd -= 3600*$uren;
	if ( $tijd >= 60 )		{ $minuten = floor($tijd/60); }
	$seconden = $tijd-$minuten*60;

	return ( 0 < $dagen ? $dagen.'d ' : '' ) . str_pad((string)$uren, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)$minuten, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string)$seconden, 2, '0', STR_PAD_LEFT);
}

function Save_Msg( $msg, $color = 'red' )
{
	$_SESSION['ps_msg']['msg'] = $msg;
	$_SESSION['ps_msg']['color'] = $color;
}


function Goede_Gebruikersnaam( $str )
{
	if ((is_string($str) || is_numeric($str)) && strlen($str))
	{
		if (preg_match("/^[\-._a-z0-9]+$/i", $str) && preg_match("/^[a-z(]+$/i", $str{0}))
		{
			return TRUE;
		}
		return FALSE;
	}
	else
	{
		trigger_error("Wrong argument passed for ".__FUNCTION__.". String needed, ".gettype($str)." passed");
	}
}


function initRoidsCosts($f_iWanna, $f_iHave) {
	$iCosts = 0;
	for ( $i = 0; $i<$f_iWanna; $i++ ) {
		$iHave = $i+$f_iHave;
		$iCosts += nextRoidCosts($iHave);
	}
	return $iCosts;
}
function nextRoidCosts($f_iCurrentRoids) {
	return 110*$f_iCurrentRoids;
}

function res_per_type( $x ) {
#	return (150*$x);

	$r = (int)(125 * $x);
	return $r;
}

function Logbook( $f_szAction, $f_szDetails = '', $f_iPlanetId = null ) {
	global $MyT;
	if ( is_array($f_szDetails) ) {
		$szDetails = '';
		foreach ( $f_szDetails AS $k => $v ) {
			$szDetails .= '&'.$k.'='.$v;
		}
		$szDetails = substr($szDetails, 1);
	}
	else {
		$szDetails = $f_szDetails;
	}
	$arrInsert = array(
		'planet_id'	=> is_int($f_iPlanetId) && 0 < $f_iPlanetId ? $f_iPlanetId : ( defined('PLANET_ID') && is_int(PLANET_ID) && 0 < PLANET_ID ? PLANET_ID : null ),
		'action'	=> $f_szAction,
		'time'		=> time(),
		'myt'		=> $MyT,
		'details'	=> $szDetails,
		'ip'		=> $_SERVER['REMOTE_ADDR'],
	);
	return db_insert('logbook', $arrInsert);
}

function AddNews( $f_iSubject, $f_szMessage, $f_iPlanetId, $bSeen = false ) {
	global $GAMEPREFS;
	$arrInsert = array(
		'planet_id'			=> $f_iPlanetId,
		'utc_time'			=> time(),
		'myt'				=> $GAMEPREFS['tickcount'],
		'news_subject_id'	=> $f_iSubject,
		'message'			=> $f_szMessage,
		'seen'				=> ($bSeen ? '1' : '0'),
	);
	if ( !db_insert('news', $arrInsert) ) {
		$arrInsert['news_subject_id'] = 0;
		return db_insert('news', $arrInsert);
	}
	return true;
}

function Show_Alliance_Members( $tag, $leader_id )
{
	$tag = addslashes($tag);
	$members = db_query("SELECT id,rulername,planetname,tag,x,y FROM planets WHERE tag='$tag' ORDER BY x,y");
	echo "All members:<br>";
	while ($mi = mysql_fetch_assoc($members))
	{
		echo "<a href=\"galaxy.php?xcoord=".$mi['x']."\">(".$mi['x'].":".$mi['y'].")</a> <a href=\"communication.php?x=".$mi['x']."&y=".$mi['y']."\">".$mi['rulername']." of ".$mi['planetname']."</a>";
		echo ($mi['id'] == $leader_id) ? " (leader)" : "";
		echo "<br>\n";
	}
}

function goedmaken( $bericht )
{
	$bericht = str_replace(">", "&gt;", $bericht);
	$bericht = str_replace("<", "&lt;", $bericht);
	$bericht = str_replace("'", "&#39;", $bericht);
	$bericht = str_replace('"', "&#34;", $bericht);

	return $bericht;
}

function GameOver( )
{
	global $GAMEPREFS;

	if ($GAMEPREFS['general_gamestoptick'] && $GAMEPREFS['tickcount'] >= $GAMEPREFS['general_gamestoptick'])
	{
		return mysql_fetch_assoc(db_query("SELECT rulername,planetname,x,y,score FROM planets ORDER BY -score LIMIT 1;"));
	}
	return FALSE;
}

function DateDiff( $interval, $date1, $date2 )
{

	// get the number of seconds between the two dates
	$timedifference =  $date2 - $date1;

	switch ($interval)
	{
		case "w":
			$retval  = bcdiv($timedifference ,604800);
			break;
		case "d":
			$retval  = bcdiv($timedifference,86400);
			break;
		case "h":
			$retval = bcdiv($timedifference,3600);
			break;
		case "n":
			$retval  = bcdiv($timedifference,60);
			break;
		default:
		case "s":
			$retval  = $timedifference;
			break;
	}
	return $retval;
}

function Make_GC( $f_iGalaxyId )
{
	$arrGalaxy = db_select('galaxies', 'id = '.(int)$f_iGalaxyId);
	$arrGalaxy = $arrGalaxy[0];

	$iPlanetsInGalaxy = db_count('planets', 'galaxy_id = '.(int)$f_iGalaxyId);
	$iVotesNeeded = floor($iPlanetsInGalaxy/2)+1;

	$iNewGCPlanetId = (int)db_select_one('planets', 'voted_for_planet_id', 'galaxy_id = '.(int)$f_iGalaxyId.' group by voted_for_planet_id having count(1) >= '.$iVotesNeeded);

	if ( $iNewGCPlanetId && $iNewGCPlanetId !== (int)$arrGalaxy['gc_planet_id'] ) {
		// Enough votes and not the same as before and the planet is in the galaxy
		db_update('galaxies', 'gc_planet_id = '.(int)$iNewGCPlanetId.', moc_planet_id = NULL, mow_planet_id = NULL', 'id = '.(int)$f_iGalaxyId);
		logbook('election', 'galaxy_id='.$f_iGalaxyId.'&gc='.$iNewGCPlanetId.'&x='.$arrGalaxy['x'].'&y='.$arrGalaxy['y']);
	}
	else if ( !$iNewGCPlanetId ) {
		// No planets has enough votes
		db_update('galaxies', 'gc_planet_id = NULL, moc_planet_id = NULL, mow_planet_id = NULL', 'id = '.(int)$f_iGalaxyId);
	}
}

function isint($x) {
	return (string)(int)$x === (string)$x;
}

function flip2darray($f_arr) {
	$arr = array();
	foreach ( $f_arr AS $k1 => $v1 ) {
		foreach ( $v1 AS $k2 => $v2 ) {
			$arr[$k2][$k1] = $v2;
		}
	}
	return $arr;
}

?>
