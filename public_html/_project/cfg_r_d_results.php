<?php

require_once '../inc.bootstrap.php';

$types = ['travel_eta', 'r_d_eta', 'r_d_costs', 'fuel_use', 'offense', 'defence'];
$types = $db->select_fields('d_resources', "CONCAT('income:', id), CONCAT(resource, ' income')", '1') + array_combine($types, $types);

$units = ['real', 'pct'];
$units = array_combine($units, $units);

// Save
if ( isset($_POST['r']) ) {
	foreach ( $_POST['r'] AS $id => $data ) {
		$data['enabled'] = !empty($data['enabled']);
		if ( $id ) {
			$db->update('d_r_d_results', $data, compact('id'));
		}
		elseif ( $data['type'] && $data['done_r_d_id'] && $data['explanation'] ) {
			$data['o'] or $data['o'] = $db->max('d_r_d_results', 'o') + 1;
			$db->insert('d_r_d_results', $data);
		}
	}

	return do_redirect(null);
}

$RD = $db->select_fields('d_r_d_available', "id, concat(UPPER(T), ' ', id, '. ', name)", '1 ORDER BY T, id');

$arrRDResults = $db->select('d_r_d_results', '1 ORDER BY o ASC, id DESC')->all();
$arrRDResults[] = new db_generic_record(['id' => '0']);

?>
<title>R&D results</title>

<form method="post" action autocomplete="off">
	<table border="1" cellpadding="4" cellspacing="1">
		<?php
		foreach ( $arrRDResults AS $n => $r ) {
			$dummy = $r->id ? null : '--';

			if ( $n ) {
				echo '<tr>';
				echo '<th colspan="2" bgcolor="#666666"></td>';
				echo '</tr>';
			}

			echo '<tr>';
			echo '<th>[' . $r['id'] . '] Type</th>';
			echo '<td><select name="r[' . $r['id'] . '][type]">' . html_options($types, $r['type'], $dummy) . '</select></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>R&D</th>';
			echo '<td><select name="r[' . $r['id'] . '][done_r_d_id]">' . html_options($RD, $r['done_r_d_id'], $dummy) . '</select></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Change</th>';
			echo '<td><input type="text" size="8" name="r[' . $r['id'] . '][`change`]" value="' . $r['change'] . '" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Unit</th>';
			echo '<td><select name="r[' . $r['id'] . '][unit]">' . html_options($units, $r['unit'], $dummy) . '</select></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Explanation</th>';
			echo '<td><input type="text" size="40" name="r[' . $r['id'] . '][explanation]" value="' . $r['explanation'] . '" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>`Order`</th>';
			echo '<td><input type="text" size="8" name="r[' . $r['id'] . '][o]" value="' . $r['o'] . '" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Enabled</th>';
			echo '<td><input type="checkbox" name="r[' . $r['id'] . '][enabled]" value="1"'.( '1' === $r['enabled'] ? ' checked="1"' : '' ).' /></td>';
			echo '</tr>';
		}
		?>
		<tr>
			<th colspan="2"><input type="submit" value="Opslaan" /></td>
		</tr>
	</table>
</form>
