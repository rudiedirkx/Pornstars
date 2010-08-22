<?php

require_once('../inc.connect.php');

if ( isset($_POST['r']) ) {
	header('Location: '.basename($_SERVER['PHP_SELF']));
	echo '<pre>';
	foreach ( $_POST['r'] AS $iRace => $arrRace ) {
		unset($arrRace['id']);
		$arrRace['enabled'] = empty($arrRace['enabled']) ? '0' : '1';
		echo $iRace.': ';
		var_dump(db_update('d_races', $arrRace, 'id = '.(int)$iRace));
		echo db_error();
	}
	exit;
}

if ( isset($_GET['new']) ) {
	db_insert('d_races', array('race' => 'NEW'));
	header('Location: '.basename($_SERVER['PHP_SELF']));
	exit;
}

$arrRaces = db_select('d_races', '1 ORDER BY id DESC');

echo '<form method="post" action="" autocomplete="off"><table border="1" cellpadding="4" cellspacing="1">';
echo '<tr><th><input type="button" value="new" onclick="document.location=\'?new=1\';" /></th><th>Name</th><th>Plural</th><th>Enabled</th></tr>';
foreach ( $arrRaces AS $r ) {
	echo '<tr>';
	echo '<th>['.$r['id'].']</th>';
	echo '<td><input type="text" size="20" name="r['.$r['id'].'][race]" value="'.$r['race'].'" /></td>';
	echo '<td><input type="text" size="20" name="r['.$r['id'].'][race_plural]" value="'.$r['race_plural'].'" /></td>';
	echo '<th><input type="checkbox" name="r['.$r['id'].'][enabled]" value="1"'.( '1' === $r['enabled'] ? ' checked="1"' : '' ).' /></th>';
	echo '</tr>';
//	echo '<tr><th colspan="4" bgcolor="#666666"></td></tr>';
}
echo '<tr><th colspan="4"><input type="submit" value="Opslaan" /></td></tr>';
echo '</table></form>';

?>
<script type="text/javascript">document.forms[0].reset();</script>