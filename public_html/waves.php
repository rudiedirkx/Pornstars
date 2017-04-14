<?php

use rdx\ps\Unit;

require 'inc.bootstrap.php';

logincheck();

// ORDER UNITS //
if ( isset($_POST['order_units'], $_POST['_token']) ) {

	validTokenOrFail('production');

	addProductions($_POST['order_units'], 'wave');

	return do_redirect();
}

// SCAN FOR ASTEROIDS //
else if ( isset($_POST['asteroid_scans']) ) {
	if ( !isint($_POST['asteroid_scans']) || $_POST['asteroid_scans'] < $g_user->asteroid_scans ) {
		return do_json([
			['msg', 'Invalid amount!'],
		]);
	}

	$amps = $g_user->wave_amps;
	$roids = $g_user->inactive_asteroids + array_sum(array_column($g_user->resources, 'asteroids'));

	$found = scanForAsteroids($_POST['asteroid_scans'], $roids, $amps);

	// @todo Update available asteroid scans

	$g_user->update('inactive_asteroids = inactive_asteroids + ' . (int) $found);

	return do_json([
		['msg', 'Found ' . $found . ' asteroids.'],
	]);
}

// INTELLIGENCE SCAN A PLANET //
// @todo
else if ( isset($_GET['intel_scan_id'], $_GET['x'], $_GET['y'], $_GET['z']) ) {
	$arrScan = db_fetch('SELECT *, u.id AS scan_id FROM d_all_units u WHERE id = '.(int)$_GET['intel_scan_id'].' AND u.T = \'scan\' AND u.r_d_required_id IN (SELECT r_d_id FROM planet_r_d WHERE planet_id = '.PLANET_ID.' AND eta = 0) AND 0 < (SELECT amount FROM waves_on_planets WHERE planet_id = '.PLANET_ID.' AND wave_id = scan_id)');
	if ( !$arrScan ) {
		exit(json_encode(array(
			array('msg', 'Invalid scan!'),
		)));
	}
	$arrScan = $arrScan[0];

	$arrTarget = db_select('galaxies g, planets p', 'g.id = p.galaxy_id AND p.z = '.(int)$_GET['z'].' AND g.y = '.(int)$_GET['y'].' AND g.x = '.(int)$_GET['x']);
	if ( !$arrTarget ) {
		exit(json_encode(array(
			array('msg', 'Invalid coordinates!'),
		)));
	}
	$arrTarget = $arrTarget[0];

	$iMyWaveAmps = (int)db_select_one( 'd_all_units u, d_waves w, waves_on_planets p', 'IFNULL(SUM(amount),0)', 'u.id = w.id AND w.id = p.wave_id AND p.planet_id = '.PLANET_ID.' AND u.T = \'amp\' AND u.r_d_required_id IN (SELECT r_d_id FROM planet_r_d WHERE planet_id = '.PLANET_ID.' AND eta = 0)' );
	$iMyAsteroids = $g_arrUser['inactive_asteroids'];
	foreach ( $g_arrResources AS $r ) { $iMyAsteroids += $r['asteroids']; }

	$iTargetWaveBlockers = (int)db_select_one( 'd_all_units u, d_waves w, waves_on_planets p', 'IFNULL(SUM(amount),0)', 'u.id = w.id AND w.id = p.wave_id AND p.planet_id = '.(int)$arrTarget['id'].' AND u.T = \'block\' AND u.r_d_required_id IN (SELECT r_d_id FROM planet_r_d WHERE planet_id = '.(int)$arrTarget['id'].' AND eta = 0)' );
	$iTargetAsteroids = $arrTarget['inactive_asteroids'];
	foreach ( db_select('planet_resources', 'planet_id = '.$arrTarget['id']) AS $r ) { $iTargetAsteroids += $r['asteroids']; }

	// kansen berekenen
	$chance = 30 * (1 + $iMyWaveAmps/max(1, $iMyAsteroids) - $iTargetWaveBlockers/max(1,$iTargetAsteroids));
	$rval = rand(0, 100);
	$scan_fact = $arrScan['fuel'];
	$blocked = $noticed = false;
	if ( $rval < $chance ) {
		if ($rval+5*$scan_fact > $chance ) {
			$noticed = true;
		}
	}
	else {
		$blocked = true;
		$noticed = true;
	}


	db_update('waves_on_planets', 'amount=amount-1', 'planet_id = '.PLANET_ID.' AND wave_id = '.(int)$arrScan['scan_id'].' AND amount > 0');
	if ( 0 >= db_affected_rows() ) {
		exit(json_encode(array(
			array('msg', 'Invalid scan!'),
		)));
	}
	$iScansLefs = db_select_one('waves_on_planets', 'amount', 'planet_id = '.PLANET_ID.' AND wave_id = '.(int)$arrScan['scan_id']);
	$arrScansLeftAjaxUpdate = array('html', 'unit_amount_'.(int)$_GET['intel_scan_id'], nummertje($iScansLefs));


	$szHTML = '';
	if ( !$blocked ) {
		if ( $noticed) {
			if ( (int)$arrTarget['id'] !== PLANET_ID ) {
				AddNews( NEWS_SUBJECT_WAVES, '<b>'.$g_arrUser['rulername'].' of '.$g_arrUser['planetname'].'</b> ('.$g_arrUser['x'].':'.$g_arrUser['y'].':'.$g_arrUser['z'].') tried to <b>'.$arrScan['unit'].'</b> you and succeeded!!', $arrTarget['id']);
			}
		}
		switch ( (int)$arrScan['scan_id'] )
		{
			case 15: // sector
				$iDefence = (int)db_select_one( 'defence_on_planets', 'sum(amount)', 'planet_id = '.(int)$arrTarget['id'] );
				$iShips = (int)db_select_one( 'fleets f, ships_in_fleets s', 'sum(s.amount)', 's.fleet_id = f.id AND f.owner_planet_id = '.(int)$arrTarget['id'] );

				$szHTML .= "<table border=0 cellpadding=2 cellspacing=0 width=100% class=\"widecells\">\n";
				$szHTML .= '<tr><td colspan="2"><center><b>BASIC Infiltration Report on '.$arrTarget['rulername'].' of '.$arrTarget['planetname'].' ('.$arrTarget['x'].':'.$arrTarget['y'].':'.$arrTarget['z'].')</b></td></tr>';
				$szHTML .= "<tr class=\"bt\"><td align=right width=\"50%\">Score</td><td>".nummertje($arrTarget['score'])."</td></tr>\n";
				$szAsteroids = '';
				foreach ( db_select('d_resources r, planet_resources pr', 'r.id = pr.resource_id AND pr.planet_id = '.$arrTarget['id']) AS $r ) {
					$szHTML .= "<tr class=\"bt\"><td align=right>".$r['resource']."</td><td>".nummertje($r['amount'])."</td></tr>\n";
					$szAsteroids .= "<tr class=\"bt\"><td align=right>".$r['resource']." asteroids</td><td>".nummertje($r['asteroids'])."</td></tr>\n";
				}
				$szHTML .= $szAsteroids;
				$szHTML .= "<tr class=\"bt\"><td align=right>Inactive Asteroids</td><td>".nummertje($arrTarget['inactive_asteroids'])."</td></tr>\n";
				$szHTML .= "<tr class=\"bt\"><td align=right># Ships</td><td>".nummertje($iShips)."</td></tr>\n";
				$szHTML .= "<tr class=\"bt\"><td align=right># Defence</td><td>".nummertje($iDefence)."</td></tr>\n";
				$szHTML .= "</table>\n";
			break;

			case 16: // unit
				$arrShips = db_fetch_fields('
SELECT
	u.name,
	IFNULL((
		SELECT
			SUM(amount)
		FROM
			fleets f,
			ships_in_fleets sf
		WHERE
			f.owner_planet_id = '.(int)$arrTarget['id'].' AND
			f.id = sf.fleet_id AND
			sf.ship_id = s.id
	),0) AS num_units
FROM
	d_all_units u,
	d_ships s
WHERE
	s.id = u.id AND
	u.is_stealth = \'0\' AND
	u.r_d_required_id IN (
		SELECT
			rdpp.r_d_id
		FROM
			planet_r_d rdpp
		WHERE
			rdpp.planet_id = '.(int)$arrTarget['id'].' AND
			rdpp.eta = 0
	)
ORDER BY
	s.id ASC;
');
				$iTotalShips = 0;
				$szHTML .= "<table border=0 cellpadding=2 cellspacing=0 width=100% class=\"widecells\">\n";
				$szHTML .= "<tr><td colspan=\"2\"><center><b>UNIT Infiltration Report on ".$arrTarget['rulername']." of ".$arrTarget['planetname']." (".$arrTarget['x'].":".$arrTarget['y'].":".$arrTarget['z'].")</b></td></tr>";
				foreach ( $arrShips AS $szShip => $iAmount ) {
					$szHTML .= '<tr class="bt"><td align="right">'.$szShip.'</td><td>'.$iAmount.'</td></tr>';
					$iTotalShips += (int)$iAmount;
				}
				$szHTML .= '<tr class="bt"><th align="right" width="50%">Total</th><th align="left">'.nummertje($iTotalShips).'</th></tr>';
				$szHTML .= "</table>\n";
			break;

			case 17: // defence
				$arrDefences = db_fetch_fields('
SELECT
	u.name,
	IFNULL((
		SELECT
			SUM(amount)
		FROM
			defence_on_planets
		WHERE
			planet_id = '.(int)$arrTarget['id'].' AND
			defence_id = d.id
	),0) AS num_units
FROM
	d_all_units u,
	d_defence d
WHERE
	d.id = u.id AND
	u.r_d_required_id IN (
		SELECT
			rdpp.r_d_id
		FROM
			planet_r_d rdpp
		WHERE
			rdpp.planet_id = '.(int)$arrTarget['id'].' AND
			rdpp.eta = 0
	)
ORDER BY
	d.id ASC;
');
				$iTotalDefence = 0;
				$szHTML .= "<table border=0 cellpadding=2 cellspacing=0 width=100% class=\"widecells\">\n";
				$szHTML .= "<tr><td colspan=\"2\"><center><b>DEFENCE Infiltration Report on ".$arrTarget['rulername']." of ".$arrTarget['planetname']." (".$arrTarget['x'].":".$arrTarget['y'].":".$arrTarget['z'].")</b></td></tr>";
				foreach ( $arrDefences AS $szDefence => $iAmount ) {
					$szHTML .= '<tr class="bt"><td align="right">'.$szDefence.'</td><td>'.$iAmount.'</td></tr>';
					$iTotalDefence += (int)$iAmount;
				}
				$szHTML .= '<tr class="bt"><th align="right" width="50%">Total</th><th align="left">'.nummertje($iTotalDefence).'</th></tr>';
				$szHTML .= "</table>\n";
			break;

			case 18: // fleet
				$szHTML .= '<div class="c b" style="padding:4px;">FLEET Infiltration Report on '.$arrTarget['rulername'].' of '.$arrTarget['planetname'].' ('.$arrTarget['x'].':'.$arrTarget['y'].':'.$arrTarget['z'].')</div>' . getFleetMatrix($arrTarget['id'], false) . '';
			break;

			case 19: // news
				$arrNewsItems = db_select('d_news_subjects s, news n', 'n.news_subject_id = s.id AND n.planet_id = '.(int)$arrTarget['id'].' ORDER BY n.id DESC');
				$szHTML .= '<div align="center" class="c">';
				if ( 0 < count($arrNewsItems) )
				{
					foreach ( $arrNewsItems AS $arrItem )
					{
						$szHTML .= '<br /><table border="0" cellpadding="3" cellspacing="0" width="450" align="center" style="border:solid 1px #222;border-width:0 1px 1px 0;">';
						$szHTML .= '<tr>';
						$szHTML .= '	<td style="padding:0;"><img title="'.$arrItem['name'].'" alt="'.$arrItem['name'].'" src="images/'.$arrItem['image'].'" height="55" width="55" /></td>';
						$szHTML .= '	<td bgcolor="#222222">'.date('Y-m-d H:i:s', $arrItem['utc_time']).', <b>MyT: '.$arrItem['myt'].'</b></td>';
						$szHTML .= '</tr>';
						$szHTML .= '<tr>';
						$szHTML .= '	<td></td>';
						$szHTML .= '	<td colspan=2>'.$arrItem['message'].'</td>';
						$szHTML .= '</tr>';
						$szHTML .= '</table><br />';
					}
				}
				else {
					$szHTML .= 'No news items!';
				}
				$szHTML .= '</div>';
			break;

			case 20: // production
				$szHTML .= '<div align="center" class="c">' . ( ($P=getProductionList('ship,defence', (int)$arrTarget['id'])) ? $P : 'No units in production!' ) . '</div>';
			break;

			case 23: // political <> should display the Galaxy Forum
				$arrTopics = db_fetch('SELECT p.id, p.title, p.utc_time, c.rulername, c.planetname FROM politics p, planets c WHERE c.id = p.creator_planet_id AND p.galaxy_id = '.$arrTarget['galaxy_id'].' AND parent_thread_id IS NULL AND p.is_deleted = \'0\' ORDER BY p.id DESC');
				$szHTML .= '<table border="0" cellpadding="5" cellspacing="0" width="600" align="center">';
				$szHTML .= '<tr class="bb">';
				$szHTML .= '<th class="left">Title</th>';
				$szHTML .= '<th>Poster</th>';
				$szHTML .= '<th class="right">Date & Time</th>';
				$szHTML .= '</tr>';
				foreach ( $arrTopics AS $arrTopic ) {
					$szHTML .= '<tr class="bt">';
					$szHTML .= '<td><b>'.( trim($arrTopic['title']) ? htmlspecialchars($arrTopic['title']) : '---' ).'</b></td>';
					$szHTML .= '<td align="center"><b>'.$arrTopic['rulername'].'</b> of <b>'.$arrTopic['planetname'].'</b></td>';
					$szHTML .= '<td class="right">'.date("Y-m-d H:i:s", $arrTopic['utc_time']).'</td>';
					$szHTML .= '</tr>';
					$szHTML .= '<tr><td colspan="3">';
					$arrReplies = db_fetch('SELECT p.id, c.rulername, c.planetname, p.utc_time, p.title, p.message FROM politics p, planets c WHERE c.id = p.creator_planet_id AND p.galaxy_id = '.(int)$arrTarget['galaxy_id'].' AND (p.id = '.(int)$arrTopic['id'].' OR p.parent_thread_id = '.(int)$arrTopic['id'].') ORDER BY id ASC');
					foreach ( $arrReplies AS $arrReply ) {
						$szHTML .= '<br />';
						$szHTML .= '<table border="0" cellpadding="3" cellspacing="0" width="600" style="background-color:#111;" align="center">';
						$szHTML .= '<tr>';
						$szHTML .= '<td><b>'.$arrReply['rulername'].'</b> of <b>'.$arrReply['planetname'].'</b></td>';
						$szHTML .= '<td align="right">'.date("Y-m-d H:i:s", $arrReply['utc_time']).'</td>';
						$szHTML .= '</tr>';
						$szHTML .= '<tr class="bt" valign="top">';
						$szHTML .= '<td height="60" colspan="2">'.( trim($arrReply['title']) ? '<b>'.htmlspecialchars(trim($arrReply['title'])).'</b><hr />' : '' ).nl2br(htmlspecialchars(trim($arrReply['message']))).'<br></td>';
						$szHTML .= '</tr>';
						$szHTML .= '</table>';
						$szHTML .= '<br />';
					}
					$szHTML .= '</td></tr>';
				}
				$szHTML .= '</table>';
			break;

			default:
				exit(json_encode(array(
					array('msg', 'Invalid scan ['.(int)$arrScan['scan_id'].']!'),
					$arrScansLeftAjaxUpdate,
				)));
			break;
		}

		// PRINT SUCCESS
		exit(json_encode(array(
			$arrScansLeftAjaxUpdate,
			array('html', 'div_scanresults', $szHTML),
			array('eval', "$('div_scanresults').style.border='solid 1px red';$('div_scanresults').style.marginBottom='15px;';"),
		)));
	}

	// YOU FAILED
	if ( (int)$arrTarget['id'] !== PLANET_ID ) {
		AddNews( NEWS_SUBJECT_WAVES, '<b>'.$g_arrUser['rulername'].' of '.$g_arrUser['planetname'].'</b> ('.$g_arrUser['x'].':'.$g_arrUser['y'].':'.$g_arrUser['z'].') tried to <b>'.$arrScan['unit'].'</b> you, but he failed!', $arrTarget['id']);
	}
	exit(json_encode(array(
		$arrScansLeftAjaxUpdate,
		array('msg', 'You failed scanning the target!'),
	)));

}

_header();

$intelScans = Unit::_options(array_filter($g_user->waves, Unit::typeFilter('scan')));

?>
<h1>Waves</h1>

<!-- <h2>Intelligence</h2> -->
<form method="post" action autocomplete="off">
	<table>
		<tr>
			<th>Type</th>
			<th>Target</th>
			<th>Number</th>
			<th></th>
		</tr>
		<tr>
			<td><select name="intel_scan_id"><?= html_options($intelScans) ?></select></td>
			<td>
				<input class="coord" type="number" name="x" placeholder="x" />
				<input class="coord" type="number" name="y" placeholder="y" />
				<input class="coord" type="number" name="z" placeholder="z" />
			</td>
			<td><input type="number" name="amount" /></td>
			<td><button>Search</button></td>
		</tr>
	</table>
</form>
<br />

<h2>Asteroids</h2>
<form method="post" action autocomplete="off">
	<table>
		<tr>
			<th>Inactive asteroids</th>
			<td><?= nummertje($g_user->inactive_asteroids) ?></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>Wave amps</th>
			<td><?= nummertje($g_user->wave_amps) ?></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<th>Asteroid scans</th>
			<td><?= nummertje($g_user->asteroid_scans) ?></td>
			<td><input type="number" name="asteroid_scans" max="<?= $g_user->asteroid_scans ?>" /></td>
			<td><button <?= !$g_user->asteroid_scans ? 'disabled' : '' ?>>Scan</button></td>
		</tr>
	</table>
</form>
<br />

<h2>Order new</h2>
<div id="div_productionform">
	<?= getProductionForm('wave') ?>
</div>
<br />

<h2>Production progress</h2>
<div id="div_productionlist">
	<?= getProductionList('wave') ?>
</div>
<br />

<?php

_footer();
