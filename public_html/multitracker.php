<?php

require 'inc.bootstrap.php';

if ( !validateAdminPassword($_SESSION['ADMIN']) ) {
	return do_redirect('comp');
}

if ( isset($_GET['gethostbyaddr']) ) {
	exit(gethostbyaddr($_GET['gethostbyaddr']));
}

?>
<html>

<head>
<title><?php echo $GAMENAME; ?></title>
<style>
body, table {
	margin : 0;
	cursor:default;
	overflow:auto;
	font-family:verdana;
	font-size:11px;
	color:#000000;
}
input, select {
	font-family:verdana;
	font-size:11px;
	color:#000000;
}
a {
	color : black;
	text-decoration : underline;
}
a:hover {
	text-decoration : none;
}
tr.hd th, tr.hd td {
	background-color : #bbb;
}
</style>
<script type="text/javascript" src="general_1_2_7.js"></script>
<script type="text/javascript" src="ajax_1_3_1.js"></script>
<script type="text/javascript">
<!--//
function T(id) {
	$('td_content_logs').firstChild.style.display = 'none';
	$('td_content_iptracker').firstChild.style.display = 'none';
	$('td_content_planettracker').firstChild.style.display = 'none';
	$('td_content_'+id).firstChild.style.display = '';
	return false;
}
function OneIpInIpTracker(ip) {
	var r = $('td_content_iptracker').firstChild.getElementsByTagName('tbody')[0].rows, i = r.length;
	while ( i-- ) {
		if ( r[i].getAttribute('ip') != ip ) {
			r[i].style.display = 'none';
		}
	}
	return T('iptracker');
}
function OnePlanetInPlanetTracker(planet) {
	var r = $('td_content_planettracker').firstChild.getElementsByTagName('tbody')[0].rows, i = r.length;
	while ( i-- ) {
		if ( r[i].getAttribute('planet') != planet ) {
			r[i].style.display = 'none';
		}
	}
	return T('planettracker');
}
//-->
</script>
</head>

<body>
<table border="0" cellpadding="5" cellspacing="1" height="100%" width="100%">
<tr valign="middle" height="40">
	<th bgcolor="#eeeeee" style="cursor:pointer;" onclick="T('logs');">LOGS</th>
	<th bgcolor="#eeeeee" style="cursor:pointer;" onclick="T('iptracker');">IP-Tracker</th>
	<th bgcolor="#eeeeee" style="cursor:pointer;" onclick="T('planettracker');">Planet-Tracker</th>
</tr>
<tr valign="top">
	<td align="center" id="td_content_logs"><table align="center" border="0" cellpadding="4" cellspacing="1">
<tr class="hd">
	<th>Time</th>
	<th>MyT</th>
	<th>Action</th>
	<th>User</th>
	<th>Details</th>
	<th>IP</th>
</tr>
<?php

$kleurtjes = array('#eeeeee', '#dddddd');

$arrLogs = db_fetch('SELECT p.*, l.* FROM logbook l LEFT JOIN planets p ON (p.id = l.planet_id) ORDER BY l.time DESC');
$n = 0;
foreach ( $arrLogs AS $arrLog ) {
	$szBg = $kleurtjes[$n++%count($kleurtjes)];
	echo '<tr bgcolor="'.$szBg.'">';
	echo '<td nowrap="1" wrap="off">'.date('d-M-Y H:i:s', $arrLog['time']).'</td>';
	echo '<td>'.$arrLog['myt'].'</td>';
	echo '<td>'.$arrLog['action'].'</td>';
	echo '<td>'.( null !== $arrLog['planet_id'] ? $arrLog['rulername'].' of '.$arrLog['planetname'] : '?' ).'</td>';
	echo '<td nowrap="1" wrap="off">'.str_replace('=', ' = ', str_replace('&', '<br />', $arrLog['details'])).'</td>';
	echo '<td>'.$arrLog['ip'].'</td>';
	echo '</tr>'."\n";
}

?>
	</table><br /></td>
	<td align="center" id="td_content_iptracker"><table align="center" border="0" cellpadding="4" cellspacing="1">
<thead>
<tr class="hd">
	<td colspan="5" align="right"><a href="#" onclick="var r=$('td_content_iptracker').firstChild.getElementsByTagName('tbody')[0].rows,i=r.length;while(i--){r[i].style.display='';}return false;">show all</a></td>
</tr>
<tr class="hd">
	<th>IP</td>
	<th>Planet</td>
	<th>Min date</td>
	<th>Max date</td>
	<th># logins</td>
</tr>
</thead>
<tbody>
<?php

$kleurtjes = array('red','green','blue','yellow','pink');

$arrLoginsByIp = db_fetch('SELECT ip, COUNT(DISTINCT planet_id) AS planets FROM logbook WHERE action = \'login\' AND planet_id IS NOT NULL GROUP BY ip HAVING 1 < planets ORDER BY ip ASC;');
$n = 0;
foreach ( $arrLoginsByIp AS $arrLogin ) {
	$szBg = $kleurtjes[$n++%count($kleurtjes)];
	echo '<tr ip="'.$arrLogin['ip'].'" bgcolor="'.$szBg.'">';
	echo '<td rowspan="'.$arrLogin['planets'].'" ondblclick="new Ajax(\'?gethostbyaddr='.addslashes($arrLogin['ip']).'\',{onComplete:function(t){alert(t.responseText);}});">'.$arrLogin['ip'].'</td>';
	$arrPlanets = db_fetch('SELECT MIN(l.time) AS min_time, MAX(l.time) AS max_time, COUNT(1) AS logins, p.* FROM planets p, logbook l WHERE l.planet_id = p.id AND l.ip = \''.$arrLogin['ip'].'\' GROUP BY l.planet_id ORDER BY l.planet_id ASC;');
	foreach ( $arrPlanets AS $k => $arrPlanet ) {
		if ( 0 < $k ) { echo '<tr ip="'.$arrLogin['ip'].'" bgcolor="'.$szBg.'">'; }
		echo '<td nowrap="1" wrap="off"><a href="#'.$arrPlanet['id'].'" onclick="return OnePlanetInPlanetTracker(\''.$arrPlanet['id'].'\');">'.$arrPlanet['rulername'].' of '.$arrPlanet['planetname'].'</a></td>';
		echo '<td nowrap="1" wrap="off">'.date('d-M-Y H:i:s', $arrPlanet['min_time']).'</td>';
		echo '<td nowrap="1" wrap="off">'.date('d-M-Y H:i:s', $arrPlanet['max_time']).'</td>';
		echo '<td align="right">'.$arrPlanet['logins'].'</td>';
		if ( 0 < $k ) { echo '</tr>'."\n"; }
	}
}

?>
</tbody>
	</table><br /></td>
	<td align="center" id="td_content_planettracker"><table align="center" border="0" cellpadding="4" cellspacing="1">
<thead>
<tr class="hd">
	<td colspan="5" align="right"><a href="#" onclick="var r=$('td_content_planettracker').firstChild.getElementsByTagName('tbody')[0].rows,i=r.length;while(i--){r[i].style.display='';}return false;">show all</a></td>
</tr>
<tr class="hd">
	<th>Planet</td>
	<th>IP</td>
	<th>Min date</td>
	<th>Max date</td>
	<th># logins</td>
</tr>
</thead>
<tbody>
<?php

$kleurtjes = array('red','green','blue','yellow','pink');

$arrLoginsByPlanet = db_fetch('SELECT COUNT(DISTINCT l.ip) AS ips, p.* FROM logbook l, planets p WHERE l.action = \'login\' AND p.id = l.planet_id GROUP BY l.planet_id HAVING 1 < ips ORDER BY l.planet_id ASC;');
$n = 0;
foreach ( $arrLoginsByPlanet AS $arrLogin ) {
	$szBg = $kleurtjes[$n++%count($kleurtjes)];
	echo '<tr planet="'.$arrLogin['id'].'" bgcolor="'.$szBg.'">';
	echo '<td rowspan="'.$arrLogin['ips'].'">'.$arrLogin['rulername'].' of '.$arrLogin['planetname'].'</td>';
	$arrIPs = db_fetch('SELECT MIN(time) AS min_time, MAX(time) AS max_time, ip, COUNT(1) AS logins FROM logbook WHERE planet_id = \''.$arrLogin['id'].'\' GROUP BY ip ORDER BY ip ASC;');
	foreach ( $arrIPs AS $k => $arrIP ) {
		if ( 0 < $k ) { echo '<tr planet="'.$arrLogin['id'].'" bgcolor="'.$szBg.'">'; }
		echo '<td nowrap="1" wrap="off"><a href="#'.$arrIP['ip'].'" onclick="return OneIpInIpTracker(\''.addslashes($arrIP['ip']).'\');">'.$arrIP['ip'].'</a></td>';
		echo '<td nowrap="1" wrap="off">'.date('d-M-Y H:i:s', $arrIP['min_time']).'</td>';
		echo '<td nowrap="1" wrap="off">'.date('d-M-Y H:i:s', $arrIP['max_time']).'</td>';
		echo '<td align="right">'.$arrIP['logins'].'</td>';
		if ( 0 < $k ) { echo '</tr>'."\n"; }
	}
	echo '</tr>'."\n";
}

?>
</tbody>
	</table><br /></td>
</tr>
</table>

<script type="text/javascript">T('iptracker');</script>

</body>

</html>
