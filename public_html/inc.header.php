<?php

global $stF, $st, $FLEETNAMES, $arrIncomingFleets, $showcolors, $g_arrResources;
$stF = str_replace('.php', '', basename($_SERVER['SCRIPT_NAME']));
$st = explode(".", $stF)[0];

if ( logincheck(false) && '1' === $g_arrUser['oldpwd'] && 'preferences' != $st && $GAMEPREFS['must_change_pwd'] ) {
	Save_Msg('<b>YOU MUST CHANGE YOUR PASSWORD BEFORE STARTING THE GAME!!', 'red');
	Go("preferences.php");
}

?>
<!doctype html>
<html>

<head>
<meta charset="utf-8" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title><?= $GAMEPREFS['gamename'] ?></title>
<link type="text/css" rel="stylesheet" href="css/styles.css" />
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

<body style="background-color:black;margin:0px;padding:0px;">
<?php

if ( !logincheck(false) ) {
	return;
}

$iHasNews = db_count( 'news', 'planet_id = '.PLANET_ID.' AND seen = \'0\'' );
$szNews = $iHasNews ? '<b style="color:red;" title="'.$iHasNews.' new!">News</b>' : 'News';

$szAdmin = in_array(PLANET_ID, $GAMEPREFS['admins'], true) ? ' OR to_planet_id IS NULL' : '';
$iHasMail = db_count( 'mail', '(to_planet_id = '.PLANET_ID.$szAdmin.') AND seen = \'0\'' );
$szMail = $iHasMail ? '<b style="color:red;">Mail</b>' : 'Mail';

$arrCurLeader = db_select( 'galaxies g, planets p', '1 ORDER BY score DESC LIMIT 1' );
$szCurLeader = '<a style="cursor:help;" title="'.$arrCurLeader[0]['rulername'].' of '.$arrCurLeader[0]['planetname'].'" href="galaxy.php?x='.$arrCurLeader[0]['x'].'&y='.$arrCurLeader[0]['y'].'">'.( (int)PLANET_ID === (int)$arrCurLeader[0]['id'] ? '<b>You</b>' : $arrCurLeader[0]['x'].':'.$arrCurLeader[0]['y'].':'.$arrCurLeader[0]['z'] ).'</a>';

$arrIncomingFleets = db_fetch('SELECT f.*, concat(p.rulername,\'</b> of <b>\',p.planetname,\'</b> (\',g.x,\':\',g.y,\':\',p.z,\')\') AS owner, (SELECT IFNULL(SUM(amount),0) FROM ships_in_fleets WHERE fleet_id = f.id) AS num_units FROM planets p, fleets f, galaxies g WHERE f.activated = \'1\' AND g.id = p.galaxy_id AND f.owner_planet_id = p.id AND destination_planet_id = '.PLANET_ID.' AND ( action = \'attack\' OR action = \'defend\' ) ORDER BY action ASC');

// Hier wordt achterhaald of en welke research & development er bezig is
global $research, $construction;
$research = db_fetch('SELECT a.id,a.name,p.eta,ROUND((a.eta-p.eta)/a.eta*100) AS pct FROM d_r_d_available a, planet_r_d p WHERE a.T = \'r\' AND p.r_d_id = a.id AND p.eta > 0 AND p.planet_id = '.PLANET_ID.';');
if ( $research ) {
	$research = $research[0];
}
$construction = db_fetch('SELECT a.id,a.name,p.eta,ROUND((a.eta-p.eta)/a.eta*100) AS pct FROM d_r_d_available a, planet_r_d p WHERE a.T = \'d\' AND p.r_d_id = a.id AND p.eta > 0 AND p.planet_id = '.PLANET_ID.';');
if ( $construction ) {
	$construction = $construction[0];
}

$iRanked = 1+db_count('planets', 'score > '.(int)$g_arrUser['score']);
$arrRanks = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
//$szRanked = nummertje($iRanked);
$szRanked = (string)$iRanked . $arrRanks[$iRanked%10];

?>
<!-- STARTS HEADER -->
<div style="width:828px;">

<table border="0" cellpadding="5" cellspacing="0" width="100%" style="background-color:#111;">
<tr valign="middle" height="30">
	<td colspan="4" align="center" style="font-size:30px;padding:0;color:#3388dd;letter-spacing:10px;" class="b" onclick="window.location.reload();"><?php echo strtoupper(str_replace('_', ' ', isset($titlearray[$st]) ? $titlearray[$st] : $st)); ?></td>
	<td colspan="2" align="right"<?php echo $tickdif>$GAMEPREFS['tickertime'] ? ' style="color:red;"' : ''; ?>><?php echo $szTickDiff = Verschil_In_Tijd($tickdif); ?> since last tick (<?php echo $GAMEPREFS['tickertime']; ?>s)</td>
</tr>
<tr height="30">
	<td colspan="4"><b>&nbsp;&nbsp;<?php echo $g_arrUser['rulername']." of ".$g_arrUser['planetname'].' ('.$g_arrUser['x'].':'.$g_arrUser['y'].':'.$g_arrUser['z']; ?>)</b></td>
	<td align="center"><a title="<?php echo $iHasMail; ?> new" href="messages.php" style="color:#ddd;"><?php echo $szMail; ?></a> &nbsp;/&nbsp; <a title="<?php echo $iHasNews; ?> new" href="news.php" style="color:#ddd;"><?php echo $szNews; ?></a></td>
	<td align="center"><b>&nbsp;&nbsp;&nbsp;MyT = <?php echo $GAMEPREFS['tickcount']; ?></b></td>
</tr>
<tr height="20"><td colspan="6" style="padding:0;"><table height="100%" width="100%" cellpadding="5" cellspacing="0"><tr>
<?php $iWidth=round(100/count($g_arrResources)); foreach ( $g_arrResources AS $r ) { echo '<td width="'.$iWidth.'%" class="c" colspan="2" style="cursor:help;background-color:'.$r['color'].';" title="resource: '.$r['resource'].'"><span id="res_amount_'.$r['id'].'">'.nummertje($r['amount']).'</span></td>'; } ?>
</tr></table></td></tr>
<tr height="20">
	<td width="276" colspan="2" class="c"><div><a href="research.php">research</a></div><?php if ( $research ) { ?><div title="<?php echo $research['name'].': '.$research['pct']; ?>%" style="cursor:help;text-align:left;background-color:#a00;"><div style="width:<?php echo $research['pct']; ?>%;height:8px;background-color:green;"></div></div><?php } ?></td>
	<td width="276" colspan="2" class="c"><div><a href="construction.php">construction</a></div><?php if ( $construction ) { ?><div title="<?php echo $construction['name'].': '.$construction['pct']; ?>%" style="cursor:help;text-align:left;background-color:#a00;"><div style="width:<?php echo $construction['pct']; ?>%;height:8px;background-color:green;"></div></div><?php } ?></td>
	<td width="138" align="center">Leader:<br /><?php echo $szCurLeader; ?></td>
	<td width="138" align="center">You:<br /><?php echo $szRanked; ?> / <?php echo nummertje($g_arrUser['score']); ?></td>
</tr>
</table>
<?php

if ( isset($_SESSION['ps_msg']) ) {
	echo '<div class="b right" style="color:'.$_SESSION['ps_msg']['color'].';">'.$_SESSION['ps_msg']['msg'].'</div>';
}

if ( 0 < count($arrIncomingFleets) ) {
	echo '<table border="0" cellpadding="3" cellspacing="0">';
	foreach ( $arrIncomingFleets AS $arrFleet ) {
		$szEta = ( 0 == (int)$arrFleet['eta'] && $arrFleet['actiontime'] <= $arrFleet['startactiontime'] ) ? $arrFleet['actiontime'].' more ticks' : 'ETA: '.$arrFleet['eta'].' ticks';
		echo '<tr style="color:'.( $arrFleet['action'] == 'defend' ? 'lime' : 'red' ).';">'./*'<td align="right">['.$FLEETNAMES[$arrFleet['fleetname']].']</td>'.*/'<td><b>'.$arrFleet['owner'].' is '.$arrFleet['action'].'ing you with '.nummertje($arrFleet['num_units']).' ships ('.$szEta.')</td></tr>';
	}
	echo '</table>';
}
else if ( !isset($_SESSION['ps_msg']) ) {
	echo '<br />';
}

?>
<!-- ENDS HEADER -->
<!-- STARTS PAGE CONTENT -->
