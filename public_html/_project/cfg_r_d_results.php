<?php

require_once '../inc.bootstrap.php';

$types = ['travel_eta', 'r_d_eta', 'r_d_costs', 'fuel_use', 'offense', 'defence'];
$types = array_merge(array_values($db->select_fields('d_resources', "id, CONCAT('income:', id)", '1')), $types);

if ( isset($_POST['r']) ) {
	foreach ( $_POST['r'] AS $id => $data ) {
		$data['enabled'] = !empty($data['enabled']);
		$db->update('d_r_d_results', $data, compact('id'));
	}

	if ( isset($_POST['new']) ) {
		$new = array_filter($_POST['new']);
		if ( isset($new['type'], $new['done_r_d_id'], $new['change'], $new['unit'], $new['explanation']) ) {
			$new['enabled'] = true;
			$db->insert('d_r_d_results', $new);
		}
	}

	return do_redirect(null);
}

echo '<title>R&D results</title>';

$arrRDResults = $db->select('d_r_d_results', '1 ORDER BY o ASC, id DESC');
$RD = $db->select_fields('d_r_d_available', 'id, concat(UPPER(T),\' \',id,\'. \',name)', '1 ORDER BY T ASC, id ASC');

echo '<form method="post" action="" autocomplete="off"><table border="1" cellpadding="4" cellspacing="1">';
foreach ( $arrRDResults AS $k => $r ) {
	echo '<tr><th>[' . $r['id'] . '] Type</th><td><select name="r[' . $r['id'] . '][type]">';
	foreach ( $types AS $t ) {
		$selected = $t == $r['type'] ? 'selected' : '';
		echo '<option value="' . $t . '" ' . $selected . '>' . $t . '</option>';
	}
	echo '</select></td></tr>';
	echo '<tr><th>R&D</th><td><select name="r[' . $r['id'] . '][done_r_d_id]"><option value="">--</option>';
	foreach ( $RD AS $iRD => $szRD ) {
		$selected = $iRD == $r['done_r_d_id'] ? 'selected' : '';
		echo '<option value="' . $iRD . '" ' . $selected . '>' . $szRD . '</option>';
	}
	echo '</select></td></tr>';
	echo '<tr><th>Change</th><td><input type="text" size="8" name="r[' . $r['id'] . '][`change`]" value="' . $r['change'] . '" /></td></tr>';
	echo '<tr><th>Unit</th><td><select name="r[' . $r['id'] . '][unit]"><option value="">--</option><option'.( 'real' == $r['unit'] ? ' selected="1"' : '' ).' value="real">real</option><option'.( 'pct' == $r['unit'] ? ' selected="1"' : '' ).' value="pct">pct</option></select></td></tr>';
	echo '<tr><th>Explanation</th><td><input type="text" size="40" name="r[' . $r['id'] . '][explanation]" value="' . $r['explanation'] . '" /></td></tr>';
	echo '<tr><th>`Order`</th><td><input type="text" size="8" name="r[' . $r['id'] . '][o]" value="' . $r['o'] . '" /></td></tr>';
	echo '<tr><th>Enabled</th><td><input type="checkbox" name="r[' . $r['id'] . '][enabled]" value="1"'.( '1' === $r['enabled'] ? ' checked="1"' : '' ).' /></td></tr>';
	echo '<tr><th colspan="2" bgcolor="#666666"></td></tr>';
}

echo '<tr><th colspan="2" bgcolor="#999999">NEW:</td></tr>';
echo '<tr><th>Type</th><td><select name="new[type]"><option value="">--</option>';
foreach ( $types AS $t ) {
	echo '<option value="' . $t.'">' . $t.'</option>';
}
echo '</select></td></tr>';
echo '<tr><th>R&D</th><td><select name="new[done_r_d_id]"><option value="">--</option>';
foreach ( $RD AS $iRD => $szRD ) {
	echo '<option value="' . $iRD.'">' . $szRD.'</option>';
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
