<?php

use rdx\ps\Planet;
use rdx\ps\Unit;

class NotEnoughException extends Exception {}



function html( $text ) {
	return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8') ?: htmlspecialchars((string)$text, ENT_QUOTES, 'ISO-8859-1');
}

function get_url( $path, $query = array() ) {
	$query = $query ? '?' . http_build_query($query) : '';
	$path = $path ? $path . '.php' : basename($_SERVER['SCRIPT_NAME']);
	return $path . $query;
}

function do_json( $data ) {
	header('Content-type: text/json; charset=utf-8');
	echo json_encode($data);
	exit;
}

function do_redirect( $path = null, $query = array() ) {
	$url = get_url($path, $query);
	header('Location: ' . $url);
	exit;
}

function html_options( $options, $selected = null, $empty = '', $datalist = false ) {
	$selected = $selected ? (array) $selected : array();

	$html = '';
	$empty && $html .= '<option value="">' . $empty . '</option>';
	foreach ( $options AS $value => $label ) {
		$isSelected = in_array($value, $selected) ? ' selected' : '';
		$value = $datalist ? html($label) : html($value);
		$label = $datalist ? '' : html($label);
		$html .= '<option value="' . $value . '"' . $isSelected . '>' . $label . '</option>';
	}
	return $html;
}



function createToken( $name ) {
	if ( $_SESSION['unihash'] ) {
		return sha1($name . ':' . $_SESSION['unihash']);
	}

	return '';
}

function checkToken( $name, $token = null ) {
	$token === null and $token = (string) @$_REQUEST['_token'];
	return strlen($token) && $token === createToken($name);
}

function validTokenOrFail( $name, $token = null ) {
	if ( !checkToken($name, $token) ) {
		accessFail("token: $name");
	}
}

function accessFail( $message ) {
	echo "Access denied: $message\n";
	exit;
}



function scanForAsteroids( $scans, $size, $amps ) {
	$nr = $a = 0;
	if ( $a != $scans ) {
		while ( $a < $scans ) {
			$a++;
			$rnd = rand(0, $size+$nr)*0.5;
			if ( $rnd < 50 * (1 + sqrt($amps)) / max(1, $size + $nr) ) {
				$nr++;
			}
		}
	}
	return $nr;
}



function sessionMessage( $message, $type = 'notice' ) {
	$_SESSION['ps_msg'] = [$type, $message];
}

function sessionSuccess( $message ) {
	return sessionMessage($message, 'success');
}

function sessionWarning( $message ) {
	return sessionMessage($message, 'warning');
}

function sessionError( $message ) {
	return sessionMessage($message, 'error');
}



function rand_string( $f_iLength = 8 ) {
	$chars = array_merge(range('A','Z'), range(0, 9));

	$string = '';
	while ( strlen($string) < $f_iLength ) {
		$string .= $chars[ array_rand($chars) ];
	}

	return $string;
}



function nextRoidCosts( $f_iHave ) {
	return 110 * $f_iHave;
}



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
	global $g_user, $g_prefs;

	include 'inc.footer.php';
}

function _header() {
	global $g_user, $g_prefs;
	global $tickdif, $GAMEPREFS;

	include 'inc.header.php';
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
	global $g_user;

	if ( defined('PLANET_ID') ) {
		return true;
	}

	if ( isset($_SESSION['planet_id'], $_SESSION['unihash']) ) {
		if ( $objPlanet = Planet::find($_SESSION['planet_id']) ) {
			if ( /*$objPlanet->unihash == $_SESSION['unihash'] &&*/ !$objPlanet->closed ) {
				$g_user = $objPlanet;

				define( 'PLANET_ID', (int) $objPlanet->id);

				if ( $objPlanet->lastaction < time() - 60 ) {
					$objPlanet->update(['lastaction' => time()]);
				}

				return true;

			}
		}
	}

	unset($_SESSION['planet_id']);
	unset($_SESSION['unihash']);

	if ( $f_bAct ) {
		exit('<a href="login.php">Invalid session!</a>');
	}

	return false;
}


function nummertje($n) {
	return number_format((int)$n, 0, ".", "," );
}


function addProductions( $units, ...$bases ) {
	global $g_user;

	$types = Unit::basesToTypes(...$bases);

	$allUnits = Unit::all();

	foreach ( $units as $id => $variants ) {
		if ( isset($allUnits[$id]) && in_array($allUnits[$id]->T, $types) ) {
			$unit = $allUnits[$id];
			foreach ( $variants as $variant => $amount ) {
				if ( isint($amount) && $amount > 0 ) {
					$costs = $unit->costs[$variant];
					$costs = array_column($costs, 'amount', 'id');

					$maxAmount = $g_user->maxResourcesFor($costs);
					$amount = min($maxAmount, $amount);

					if ( $amount > 0 ) {
						$g_user->takeTransaction(function($g_user) use ($unit, $costs, $amount) {
							$g_user->takeResources($costs, $amount);

							$unit->produce($g_user, $amount);
						}, false);
					}
				}
			}
		}
	}
}


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


function getFleetMatrix( Planet $objPlanet, $withDetails = true ) {
	global $db;
	// global $NUM_OUTGOING_FLEETS, $FLEETNAMES, $showcolors, $t_arrFleetNames, $t_arrShipNames;

	$fleets = $objPlanet->fleets;
	$ships = $objPlanet->ships;

	$html = '';
	$html .= '<table>';
	$html .= '<tr>';
	$html .= '<td></td>';
	foreach ( $fleets as $fleetId => $fleet ) {
		$html .= '<th>' . html($fleet) . '</th>';
	}
	$html .= '</tr>';
	foreach ( $ships as $shipId => $ship ) {
		$html .= '<tr>';
		$html .= '<th>' . html($ship->unit_plural) . '</th>';
		foreach ( $fleets as $fleetId => $fleet ) {
			$html .= '<td>' . nummertje($fleet->ships[$shipId]->planet_amount) . '</td>';
		}
		$html .= '</tr>';
	}

	if ( $withDetails ) {
		$html .= '<tr>';
		$html .= '<th>ETA</th>';
		foreach ( $fleets as $fleetId => $fleet ) {
			$html .= '<td>' . ( $fleet->fleetname ? $fleet->ships_eta : '' ) . '</td>';
		}
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<th>Fuel</th>';
		foreach ( $fleets as $fleetId => $fleet ) {
			$html .= '<td>' . ( $fleet->fleetname ? nummertje($fleet->ships_power) : '' ) . '</td>';
		}
		$html .= '</tr>';
	}

	$html .= '</table>';

	return $html;
}


function getProductionList( ...$bases ) {
	global $g_user, $db;

	$types = Unit::basesToTypes(...$bases);

	$units = $db->fetch('
		SELECT u.*, p.eta AS planet_eta, SUM(p.amount) AS planet_building
		FROM d_all_units u
		JOIN planet_production p ON p.unit_id = u.id AND p.planet_id = ?
		WHERE u.T IN (?)
		GROUP BY u.id, p.eta
		ORDER BY u.o ASC, p.eta ASC
	', [
		'params' => [$g_user->id, $types],
		'class' => Unit::class,
	])->all();

	if ( !$units ) {
		return '';
	}

	$etas = array_column($units, 'planet_eta');
	$maxEta = max($etas);
	$minEta = min(1, min($etas));

	$matrix = [];
	foreach ( $units as $unit ) {
		if ( !isset($matrix[$unit->id]) ) {
			$matrix[$unit->id] = [
				'unit' => $unit,
				'etas' => [],
			];
		}
		$matrix[$unit->id]['etas'][$unit->planet_eta] = $unit->planet_building;
	}

	$matrixEtas = [];
	for ( $eta = $minEta; $eta <= $maxEta; $eta++ ) {
		if ( $eta <= 1 || in_array($eta, $etas) || in_array($eta-1, $etas) || in_array($eta+1, $etas) ) {
			$matrixEtas[] = $eta;
		}
		elseif ( end($matrixEtas) !== null ) {
			$matrixEtas[] = null;
		}
	}
	if ( count($matrixEtas) > 1 && $matrixEtas[0] === 1 && $matrixEtas[1] === null && !in_array(1, $etas) ) {
		array_shift($matrixEtas);
	}

	$szHtml  = '<table>';
	$szHtml .= '<tr>';
	$szHtml .= '<td></td>';
	foreach ( $matrixEtas as $eta ) {
		$szHtml .= '<td>' . ( $eta ?? '...' ) . '</td>';
	}
	$szHtml .= '</tr>';
	foreach ( $matrix as $line ) {
		$szHtml .= '<tr>';
		$szHtml .= '<th>' . html($line['unit']->unit_plural) . '</th>';
		foreach ( $matrixEtas as $eta ) {
			$szHtml .= '<td>' . @$line['etas'][$eta] . '</td>';
		}
		$szHtml .= '</tr>';
	}
	$szHtml .= '</table>';

	return $szHtml;

} // END getProductionList()


function getProductionForm( ...$bases ) {
	global $g_user, $db;

	$types = Unit::basesToTypes(...$bases);

	$units = $db->fetch('
		SELECT u.*, rd.planet_id
		FROM d_all_units u
		JOIN planet_r_d rd ON rd.r_d_id = u.r_d_required_id AND rd.planet_id = ? AND eta = 0
		WHERE u.T IN (?)
		ORDER BY u.o ASC
	', [
		'params' => [$g_user->id, $types],
		'class' => Unit::class,
	])->all();

	$szHtml = '
<form method="post" action autocomplete="off">
<input type="hidden" name="_token" value="' . createToken('production') . '" />
<table>
<tr>
	<th></th>
	<th>Name</th>
	<th>ETA</th>
	<th>In stock</th>
	<th>Costs</th>
	<th>Order</th>
</tr>';

	foreach ( $units AS $unit ) {
		$rowspan = count($unit->costs);

		$szHtml .= '<tr>';
		$szHtml .= '<td rowspan="' . $rowspan . '">' . $unit->id . '</td>';
		$szHtml .= '<td rowspan="' . $rowspan . '">' . html($unit->unit_plural) . '<br />' . html($unit->explanation) . '</td>';
		$szHtml .= '<td rowspan="' . $rowspan . '">' . $unit->build_eta . '</td>';
		$szHtml .= '<td rowspan="' . $rowspan . '">' . nummertje($unit->number_owned) . '</td>';

		// Costs & order
		$szHtml .= '<td nowrap>' . renderCostsVariant($unit->costs[0]) . '</td>';
		$szHtml .= '<td><input class="costs" name="order_units[' . $unit->id . '][0]" /></td>';

		$szHtml .= '</tr>';

		// More cost variants
		foreach ( array_slice($unit->costs, 1) as $variant => $costs ) {
			$szHtml .= '<tr>';
			$szHtml .= '<td nowrap>' . renderCostsVariant($costs) . '</td>';
			$szHtml .= '<td><input class="costs" name="order_units[' . $unit->id . '][' . ($variant+1) . ']" /></td>';
			$szHtml .= '</tr>';
		}
	}
	$szHtml .= '
<tr>
	<td colspan="5">&nbsp;</td>
	<td><button>Order</button></td>
</tr>
</table>
</form>';
	return $szHtml;

} // END getProductionForm()


function renderCostsVariant( array $costs ) {
	$labels = array_map(function($cost) {
		return '<span class="resource" style="background: ' . $cost->color . '">' . $cost->amount . '</span>';
	}, $costs);
	return implode(' + ', $labels);
}


function Go( $to = PARENT_SCRIPT_NAME, $die = 0 ) {
	// @todo Replace by do_redirect()
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

function isint( $x ) {
	return (string) (int) $x === (string) $x;
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
