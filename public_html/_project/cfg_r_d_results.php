<?php

require_once('../inc.connect.php');

if ( isset($_POST['r']) ) {
	header('Location: '.basename($_SERVER['PHP_SELF']));
	echo '<pre>';
	foreach ( $_POST['r'] AS $iResult => $arrResult ) {
		unset($arrResult['id']);
		$arrResult['enabled'] = empty($arrResult['enabled']) ? '0' : '1';
		echo $iResult.': ';
		var_dump(db_update('d_r_d_results', $arrResult, 'id = '.(int)$iResult));
		echo db_error();
	}
//	print_r($_POST['r']);
	if ( isset($_POST['new'], $_POST['new']['o'], $_POST['new']['type'], $_POST['new']['done_r_d_id'], $_POST['new']['change'], $_POST['new']['unit'], $_POST['new']['explanation']) ) {
		if ( '0' !== $_POST['new']['o'] && '' !== $_POST['new']['type'] && '' !== $_POST['new']['done_r_d_id'] && '0' !== $_POST['new']['change'] && '' !== $_POST['new']['unit'] && '' !== $_POST['new']['explanation'] ) {
			echo "\nNEW: ";
			unset($_POST['new']['id']);
			$_POST['new']['enabled'] = '1';
			var_dump(db_insert('d_r_d_results', $_POST['new']));
			echo db_error();
			print_r($_POST['new']);
		}
	}
	exit;
}

$arrRDResults = db_select('d_r_d_results', '1 ORDER BY id DESC');
$RD = db_select_fields('d_r_d_available', 'id, concat(UPPER(T),\' \',id,\'. \',name)', '1 ORDER BY T ASC, id ASC');

echo '<form method="post" action="" autocomplete="off"><table border="1" cellpadding="4" cellspacing="1">';
foreach ( $arrRDResults AS $k => $r ) {
	echo '<tr><th>['.$r['id'].'] Type</th><td><input name="r['.$r['id'].'][type]" value="'.$r['type'].'" /></td></tr>';
	echo '<tr><th>R&D</th><td><select name="r['.$r['id'].'][done_r_d_id]"><option value="">--</option>';
	foreach ( $RD AS $iRD => $szRD ) {
		echo '<option'.( $iRD == $r['done_r_d_id'] ? ' selected="1"' : '' ).' value="'.$iRD.'">'.$szRD.'</option>';
	}
	echo '</select></td></tr>';
	echo '<tr><th>Change</th><td><input type="text" size="8" name="r['.$r['id'].'][`change`]" value="'.$r['change'].'" /></td></tr>';
	echo '<tr><th>Unit</th><td><select name="r['.$r['id'].'][unit]"><option value="">--</option><option'.( 'real' == $r['unit'] ? ' selected="1"' : '' ).' value="real">real</option><option'.( 'pct' == $r['unit'] ? ' selected="1"' : '' ).' value="pct">pct</option></select></td></tr>';
	echo '<tr><th>Explanation</th><td><input type="text" size="40" name="r['.$r['id'].'][explanation]" value="'.$r['explanation'].'" /></td></tr>';
	echo '<tr><th>`Order`</th><td><input type="text" size="8" name="r['.$r['id'].'][o]" value="'.$r['o'].'" /></td></tr>';
	echo '<tr><th>Enabled</th><td><input type="checkbox" name="r['.$r['id'].'][enabled]" value="1"'.( '1' === $r['enabled'] ? ' checked="1"' : '' ).' /></td></tr>';
	echo '<tr><th colspan="2" bgcolor="#666666"></td></tr>';
}
echo '<tr><th colspan="2" bgcolor="#999999">NEW:</td></tr>';
echo '<tr><th>Type</th><td><select name="new[type]"><option value="">--</option>';
foreach ( array('travel_eta','r_d_eta','metal_income','crystal_income','energy_income','r_d_costs','fuel_use','offense','defence') AS $t ) {
	echo '<option value="'.$t.'">'.$t.'</option>';
}
echo '</select></td></tr>';
echo '<tr><th>R&D</th><td><select name="new[done_r_d_id]"><option value="">--</option>';
foreach ( $RD AS $iRD => $szRD ) {
	echo '<option value="'.$iRD.'">'.$szRD.'</option>';
}
echo '</select></td></tr>';
echo '<tr><th>Change</th><td><input type="text" size="8" name="new[change]" value="0" /></td></tr>';
echo '<tr><th>Unit</th><td><select name="new[unit]"><option value="">--</option><option value="real">real</option><option value="pct">pct</option></select></td></tr>';
echo '<tr><th>Explanation</th><td><input type="text" size="40" name="new[explanation]" value="" /></td></tr>';
echo '<tr><th>`Order`</th><td><input type="text" size="8" name="new[o]" value="0" /></td></tr>';
echo '<tr><th colspan="2"><input type="submit" value="Opslaan" /></td></tr>';
echo '</table></form>';

?>
<script type="text/javascript">document.forms[0].reset();</script>