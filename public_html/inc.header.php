<?php

use rdx\ps\Planet;

$stF = str_replace('.php', '', basename($_SERVER['SCRIPT_NAME']));
$st = explode(".", $stF)[0];

if ( logincheck(false) && $g_user->oldpwd && 'preferences' != $st && $g_prefs->must_change_pwd ) {
	Save_Msg('<b>YOU MUST CHANGE YOUR PASSWORD BEFORE STARTING THE GAME!!', 'red');

	return do_redirect('preferences');
}

?>
<!doctype html>
<html>

<head>
<? include 'tpl.head.php' ?>
<title><?= html($g_prefs->gamename) ?></title>
<script src="general_1_2_6.js"></script>
<script src="ajax_1_3_1.js"></script>
<script>
function TD(o) {
	var d = $(o).style;
	d.display = d.display != 'none' ? 'none' : '';
}
function R(o) {
	new Ajax(o.href, {
		method		: 'GET',
		onComplete	: H
	});
	return false;
}
function H(a) {
	var t = a.responseText;
	try {
		var r = eval('(' + t + ')');
	} catch ( e ) { alert(t); }
	for ( var i=0; i<r.length; i++ ) {
		switch ( r[i][0] ) {
			case 'msg':
				alert(r[i][1]);
			break;
			case 'eval':
				try { eval(r[i][1]); } catch(e){}
			break;
			case 'html':
				setInnerHTML($(r[i][1]), r[i][2]);
			break;
		}
	}
}
</script>
</head>

<? if ( !logincheck(false) ): ?>
	<body>
	<? return;
endif ?>

<body>
<div class="flex">
<div id="left">
	<? include 'inc.menu.php' ?>
</div>
<div id="right">
<?php

$iHasNews = count($g_user->new_news);
$szNews = $iHasNews ? '<b style="color:red;" title="' . $iHasNews . ' new!">News</b>' : 'News';

$iHasMail = count($g_user->new_mail);
$szMail = $iHasMail ? '<b style="color:red;">Mail</b>' : 'Mail';

$objLeaderPlanet = Planet::first('1 ORDER BY score DESC');
$szCurLeader = '<a style="cursor:help;" title="' . html($objLeaderPlanet) . '" href="galaxy.php?x=' . $objLeaderPlanet->x . '&y=' . $objLeaderPlanet->y . '">' . ( PLANET_ID == $objLeaderPlanet->id ? '<b>You</b>' : implode(':', $objLeaderPlanet->coordinates) ) . '</a>';

// @todo Incoming fleets
// $arrIncomingFleets = db_fetch('SELECT f.*, concat(p.rulername,\'</b> of <b>\',p.planetname,\'</b> (\',g.x,\':\',g.y,\':\',p.z,\')\') AS owner, (SELECT IFNULL(SUM(amount),0) FROM ships_in_fleets WHERE fleet_id = f.id) AS num_units FROM planets p, fleets f, galaxies g WHERE f.activated = \'1\' AND g.id = p.galaxy_id AND f.owner_planet_id = p.id AND destination_planet_id = '.PLANET_ID.' AND ( action = \'attack\' OR action = \'defend\' ) ORDER BY action ASC');

?>
<table border="1">
	<tr>
		<td colspan="4" onclick="location.reload()">
			<?= html(strtoupper($st)) ?>
		</td>
		<td colspan="2">
			<a
				<?= $tickdif > $g_prefs->tickertime ? 'style="color: red"' : '' ?>
				href="tick.php"
				target="_blank"
				onclick="return (x => { x=new XMLHttpRequest; x.open('get',this.href); x.onload=e=>location.reload(); x.send(); })(), false"
			>
				<?= Verschil_In_Tijd($tickdif) ?> since last tick (<?= $g_prefs->tickertime ?>s)
			</a>
		</td>
	</tr>
	<tr>
		<td colspan="4"><?= $g_user ?> (<?= implode(':', $g_user->coordinates) ?>)</td>
		<td>
			<a title="<?= $iHasMail ?> new" href="messages.php"><?= $szMail ?></a>
			&nbsp;/&nbsp;
			<a title="<?= $iHasNews ?> new" href="news.php"><?= $szNews ?></a>
		</td>
		<td>MyT = <?= $g_prefs->tickcount ?></td>
	</tr>
	<tr>
		<td colspan="6">
			<table>
				<tr>
					<?php
					foreach ( $g_user->resources AS $resource ) {
						echo '<td style="background: ' . html($resource->color)  . '" title="' . html($resource->resource) . '">';
						echo html($resource->resource) . ': ' . nummertje($resource->amount);
						echo '</td>';
					}
					?>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<a href="research.php">research</a>
			<?php if ( $g_user->researching ) { ?>
				<div style="background-color: #444" title="<?= html($g_user->researching->name) . ': ' . $g_user->researching->planet_eta ?> left">
					<div style="width: <?= $g_user->researching->pct_done ?>%; height: 8px; background-color: #a00"></div>
				</div>
			<?php } ?>
		</td>
		<td colspan="2">
			<a href="construction.php">construction</a>
			<?php if ( $g_user->constructing ) { ?>
				<div style="background-color: #444" title="<?= html($g_user->constructing->name) . ': ' . $g_user->constructing->planet_eta ?> left">
					<div style="width: <?= $g_user->constructing->pct_done ?>%; height: 8px; background-color: green"></div>
				</div>
			<?php } ?>
		</td>
		<td>Leader:<br /><?= $szCurLeader ?></td>
		<td>You:<br /><?= $g_user->ranked ?> / <?= nummertje($g_user->score) ?></td>
	</tr>
</table>
<?php

include 'inc.message.php';

// if ( 0 < count($arrIncomingFleets) ) {
// 	echo '<table border="0" cellpadding="3" cellspacing="0">';
// 	foreach ( $arrIncomingFleets AS $arrFleet ) {
// 		$szEta = ( 0 == (int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA: '.$arrFleet['eta'].' ticks';
// 		echo '<tr style="color:'.( $arrFleet['action'] == 'defend' ? 'lime' : 'red' ).';">'./*'<td align="right">['.$FLEETNAMES[$arrFleet['fleetname']].']</td>'.*/'<td><b>'.$arrFleet['owner'].' is '.$arrFleet['action'].'ing you with '.nummertje($arrFleet['num_units']).' ships ('.$szEta.')</td></tr>';
// 	}
// 	echo '</table>';
// }
