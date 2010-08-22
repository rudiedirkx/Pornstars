<?php

require_once('inc.config.php');
logincheck();

if ( isset($_GET['delete'], $_GET['fromtime']) )
{
	db_update('mail', 'deleted = \'1\' WHERE to_planet_id = '.PLANET_ID.' AND utc_time < '.(int)$_GET['fromtime']);
	Go();
}
else if ( isset($_GET['delete'], $_GET['id']))
{
	db_update('mail', 'deleted = \'1\' WHERE to_planet_id = '.PLANET_ID.' AND id = '.(int)$_GET['id']);
	Go();
}

_header();

?>
<div class="header">Planetary Mail</div>

<br />

<?php

$szAdmin = in_array(PLANET_ID, $GAMEPREFS['admins'], true) ? ' OR to_planet_id IS NULL' : '';
$arrMail = db_select('galaxies g, planets p, mail m', 'g.id = p.galaxy_id AND p.id = m.from_planet_id AND (m.to_planet_id = '.PLANET_ID.$szAdmin.') AND (m.deleted = \'0\' OR m.seen = \'0\') ORDER BY m.id DESC');

if ( 0 < count($arrMail) ) {
	echo '<a href="?delete=1&fromtime='.time().'">Delete all mail!</a><br />';
	foreach ( $arrMail AS $arrMsg )
	{
		echo '<br /><table border="0" cellpadding="3" cellspacing="0" width="400" align="center">';
		echo '<tr valign="top" bgcolor="'.( (int)$arrMsg['seen'] ? ( (int)$arrMsg['is_help_msg'] ? 'green' : '#222222' ) : 'red' ).'">';
		$szDay = strtolower(date("d-M-Y", $arrMsg['utc_sent']));
		if ( $szDay === strtolower(date("d-M-Y")) ) {
			$szDay = 'Today';
		}
		else if ( $szDay === strtolower(date("d-M-Y", time()-24*3600)) ) {
			$szDay = 'Yesterday';
		}
		echo '	<td>'.$szDay.' at '.date('H:i', $arrMsg['utc_sent']).'<br />MyT: '.$arrMsg['myt_sent'].'</td>';
		echo '	<td align="right"><a title="From: ('.$arrMsg['x'].':'.$arrMsg['y'].':'.$arrMsg['z'].')" href="galaxy.php?x='.$arrMsg['x'].'&y='.$arrMsg['y'].'&z='.$arrMsg['z'].'">'.$arrMsg['rulername'].' of '.$arrMsg['planetname'].'</a></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '	<td colspan="2" style="border-bottom:solid 1px #222222;">'.nl2br(htmlspecialchars($arrMsg['message'])).'</td>';
		echo '</tr>';
		echo '<tr valign="bottom">';
		echo '	<td><a href="communication.php?x='.$arrMsg['x'].'&y='.$arrMsg['y'].'&z='.$arrMsg['z'].'">reply</a></td>';
		echo '	<td align="right"><a href="?delete=1&id='.$arrMsg['id'].'">delete</a></td>';
		echo '</tr>';
		echo '</table><br />';
	}
	echo '<a href="?delete=1&fromtime='.time().'">Delete all mail!</a>';
}
else {
	echo 'No mail!';
}

echo '<br />';


$szAdmin = in_array(PLANET_ID, $GAMEPREFS['admins'], true) ? ' OR to_planet_id IS NULL' : '';
db_update('mail', 'seen = \'1\' WHERE (to_planet_id = '.PLANET_ID.$szAdmin.') AND utc_time < '.time());


_footer();

?>