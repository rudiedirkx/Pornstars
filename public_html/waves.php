<?php

use rdx\ps\Planet;
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

	validTokenOrFail('scan');

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
else if ( isset($_POST['intel_scan_id'], $_POST['x'], $_POST['y'], $_POST['z']) ) {

	validTokenOrFail('scan');

	$intelScans = array_filter($g_user->waves, Unit::typeFilter('scan'));
	if ( !isset($intelScans[ $_POST['intel_scan_id'] ]) ) {
		return accessFail('scan');
	}

	$scan = $intelScans[ $_POST['intel_scan_id'] ];

	$planet = Planet::fromCoordinates($_POST['x'], $_POST['y'], $_POST['z']);
	if ( !$planet ) {
		return accessFail('planet');
	}

	$amps = $g_user->wave_amps;
	$roids = $g_user->total_asteroids;

	$targetBlockers = $planet->wave_blockers;
	$targetRoids = $planet->total_asteroids;

	// @todo Incorporate $scan->fuel into $chance

	$chance = 10 * (1 + $amps / max(1, $roids) - $targetBlockers / max(1, $targetRoids));
	$luck = rand(0, 100);
	$blocked = $luck < $chance;
	$noticed = $luck * 3 < $chance && intval($chance) % 10 == 1;

	// @todo Update available scans

	if ( $noticed ) {
		// @todo Planet news
		// $planet->createNews('waves', "{$g_user} " . ( $blocked ? " tried to {$scan} you, but failed." : "scanned you ({$scan})." ));
	}

	// @todo Prettify and save results

	$_SESSION['last_scanned'] = $planet->coordinates;

	if ( $blocked ) {
		sessionWarning("You failed to scan {$planet}.");
	}
	else {
		$report = $planet->createScanReport($scan, $g_user);
		sessionSuccess(html("{$scan} for {$planet}") . ':<br /><pre>' . print_r($report, 1) . '</pre>');
	}

	return do_redirect();

	$szHTML = '';
	if ( !$blocked ) {
		switch ( (int)$arrScan['scan_id'] )
		{

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
		}
	}

	exit;
}

_header();

$intelScans = Unit::_options(array_filter($g_user->waves, Unit::typeFilter('scan')));

?>
<h1>Waves</h1>

<!-- <h2>Intelligence</h2> -->
<form method="post" action autocomplete="off">
	<input type="hidden" name="_token" value="<?= createToken('scan') ?>" />
	<table>
		<tr>
			<th>Type</th>
			<th>Target</th>
			<!-- <th>Number</th> -->
			<th></th>
		</tr>
		<tr>
			<td><select name="intel_scan_id"><?= html_options($intelScans) ?></select></td>
			<td>
				<input class="coord" type="number" name="x" placeholder="x" value="<?= @$_SESSION['last_scanned'][0] ?>" />
				<input class="coord" type="number" name="y" placeholder="y" value="<?= @$_SESSION['last_scanned'][1] ?>" />
				<input class="coord" type="number" name="z" placeholder="z" value="<?= @$_SESSION['last_scanned'][2] ?>" />
			</td>
			<!-- <td><input type="number" name="amount" /></td> -->
			<td><button>Search</button></td>
		</tr>
	</table>
</form>
<br />

<h2>Asteroids</h2>
<form method="post" action autocomplete="off">
	<input type="hidden" name="_token" value="<?= createToken('scan') ?>" />
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
