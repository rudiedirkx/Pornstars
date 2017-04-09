<?php

require_once('inc.config.php');
logincheck();

// ORDER UNITS //
if ( isset($_POST['order_units']) && is_array($_POST['order_units']) && 0 < count($_POST['order_units']) )
{
	addProductions('roidscan,scan,amp,block', $_POST['order_units']);
	$arrJson = array(
		array('eval', "$('f_order_units').reset();"),
		array('html', 'div_productionlist', getProductionList('roidscan,scan,amp,block')),
		array('msg', 'Productions added!'),
	);
	foreach ( db_select_fields('planet_resources', 'resource_id,amount', 'planet_id = '.PLANET_ID) AS $iResourceId => $iAmount ) {
		$arrJson[] = array('html', 'res_amount_'.$iResourceId, nummertje($iAmount));
	}
	exit(json_encode($arrJson));
}

// SCAN FOR ASTEROIDS //
else if ( isset($_POST['number_of_asteroid_scans'], $_POST['roid_scan_id']) )
{
	$iTotalAsteroidScans = (int)db_select_one( 'd_all_units u, d_waves w, waves_on_planets p', 'IFNULL(SUM(amount),0)', 'u.id = '.(int)$_POST['roid_scan_id'].' AND u.id = w.id AND w.id = p.wave_id AND p.planet_id = '.PLANET_ID.' AND u.T = \'roidscan\' AND u.r_d_required_id IN (SELECT r_d_id FROM planet_r_d WHERE planet_id = '.PLANET_ID.' AND eta = 0)' );
	$a = (int)min( $_POST['number_of_asteroid_scans'], $iTotalAsteroidScans );

	if ( 0 >= $a ) {
		exit(json_encode(array(
			array('msg', 'Invalid amount!'),
		)));
	}

	$iTotalWaveAmps = (int)db_select_one( 'd_all_units u, d_waves w, waves_on_planets p', 'IFNULL(SUM(amount),0)', 'u.id = w.id AND w.id = p.wave_id AND p.planet_id = '.PLANET_ID.' AND u.T = \'amp\' AND u.r_d_required_id IN (SELECT r_d_id FROM planet_r_d WHERE planet_id = '.PLANET_ID.' AND eta = 0)' );

	$iAsteroids = $g_arrUser['inactive_asteroids'];
	foreach ( $g_arrResources AS $r ) { $iAsteroids += $r['asteroids']; }
	$iAsteroidsFound = calcres( $a, $iAsteroids, $iTotalWaveAmps );

	if ( db_update('waves_on_planets', 'amount = amount-'.$a, 'planet_id = '.PLANET_ID.' AND wave_id = '.(int)$_POST['roid_scan_id'].'') && 0 < db_affected_rows() ) {
		db_update( 'planets', 'inactive_asteroids = inactive_asteroids+'.$iAsteroidsFound, 'id = '.PLANET_ID );
	}
	$iScansLefs = db_select_one('waves_on_planets', 'amount', 'planet_id = '.PLANET_ID.' AND wave_id = '.(int)$_POST['roid_scan_id'].'');

	exit(json_encode(array(
		array('html', 'unit_amount_'.(int)$_POST['roid_scan_id'], nummertje($iScansLefs)),
		array('msg', 'Your '.nummertje($a).' scans found '.nummertje($iAsteroidsFound).' Asteroids!'),
	)));
}

// INTELLIGENCE SCAN A PLANET //
else if ( isset($_GET['intel_scan_id'], $_GET['x'], $_GET['y'], $_GET['z']) )
{
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

} // END if ( isset($_GET['intel_scan_id'], $_GET['x'], $_GET['y'], $_GET['z']) )

_header();

?>
<div id="div_scanresults"></div>

<div class="header">Scans</div>

<br />

<table border="0" cellpadding="4" cellspacing="0" width="600" align="center">
<tr class="bb">
	<th>Type</th>
	<th>Number&nbsp;/&nbsp;Target</th>
	<td>&nbsp;</td>
</tr>
<form method="post" action="" autocomplete="off" onsubmit="return postForm(this,H);">
<tr class="bb">
	<td class="c"><select name="roid_scan_id"><option value="">--</option>
<?php
$arrScans = db_fetch_fields('
SELECT
	u.id,
	CONCAT(u.id,\'. \',u.unit_plural)
FROM
	d_all_units u,
	planet_r_d p
WHERE
	u.r_d_required_id = p.r_d_id AND
	p.planet_id = '.PLANET_ID.' AND
	p.eta = 0 AND
	u.T = \'roidscan\' AND
	0 < IFNULL((SELECT SUM(amount) FROM waves_on_planets WHERE planet_id = '.PLANET_ID.' AND wave_id = u.id),0)
ORDER BY
	u.o ASC;
');
foreach ( $arrScans AS $iScan => $szScan ) {
	echo '<option value="'.$iScan.'">'.$szScan.'</option>';
}
?>
	</select></td>
	<td><input type="text" name="number_of_asteroid_scans" style="width:100%;text-align:center;" value="" /></td>
	<td><input type="submit" value="Search" style="width:100%;" /></td>
</tr>
</form>
<form method="get" action="" autocomplete="off" onsubmit="return postForm(this,H);">
<tr>
	<td width="40%" class="c"><select name="intel_scan_id"><option value="">--</option>
<?php
$arrScans = db_fetch_fields('
SELECT
	u.id,
	CONCAT(u.id,\'. \',u.unit_plural)
FROM
	d_all_units u,
	planet_r_d p
WHERE
	u.r_d_required_id = p.r_d_id AND
	p.planet_id = '.PLANET_ID.' AND
	p.eta = 0 AND
	u.T = \'scan\' AND
	u.is_mobile = \'1\' AND
	0 < IFNULL((SELECT SUM(amount) FROM waves_on_planets WHERE planet_id = '.PLANET_ID.' AND wave_id = u.id),0)
ORDER BY
	u.o ASC;
');
foreach ( $arrScans AS $iScan => $szScan ) {
	echo '<option'.( isset($_GET['intel_scan_id']) && $_GET['intel_scan_id'] == $iScan ? ' selected="1"' : '' ).' value="'.$iScan.'">'.$szScan.'</option>';
}
?>
	</select></td>
	<td width="40%" class="c">
		<input type="text" name="x" value="<?php echo isset($_GET['x']) ? (int)$_GET['x'] : 'X'; ?>" onfocus="this.select();" class="c" size="3" />
		<input type="text" name="y" value="<?php echo isset($_GET['y']) ? (int)$_GET['y'] : 'Y'; ?>" onfocus="this.select();" class="c" size="3" />
		<input type="text" name="z" value="<?php echo isset($_GET['z']) ? (int)$_GET['z'] : 'Z'; ?>" onfocus="this.select();" class="c" size="3" />
	</td>
	<td width="20%"><input type="submit" value="Scan" style="width:100%;" /></td>
</tr>
</form>
</table>

<br />
<br />

<div class="header">Order waves<?php if ( (int)$GAMEPREFS['havoc_production'] ) { echo ' (<b style="color:red;">HAVOC!</b>)'; } ?></div>

<br />

<?php echo getProductionForm('roidscan,scan,amp,block'); ?>

<br />

<div id="div_productionlist">
<?php

echo getProductionList('roidscan,scan,amp,block');

echo '</div><br />';

_footer();


function calcres( $f_iScansUsed, $f_iPlanetSize, $f_iWaveAmps ) {
	$nr = $a = 0;
	if ( $a != $f_iScansUsed ) {
		while ( $a < $f_iScansUsed ) {
			$a++;
			$rnd = rand(0, $f_iPlanetSize+$nr)*0.5;
			if ( $rnd < 50*(1+sqrt($f_iWaveAmps))/max(1,$f_iPlanetSize+$nr) ) {
				$nr++;
			}
		}
	}
	return $nr;
}

?>
