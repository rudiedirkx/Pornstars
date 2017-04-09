<?php

require_once '../inc.bootstrap.php';

// Save
if ( isset($_POST['r']) ) {
	foreach ( $_POST['r'] AS $id => $data ) {
		$data['enabled'] = !empty($data['enabled']);
		if ( $id ) {
			$db->update('d_races', $data, compact('id'));
		}
		elseif ( $data['race'] ) {
			$db->insert('d_races', $data);
		}
	}

	return do_redirect(null);
}

$arrRaces = $db->select('d_races', '1')->all();
$arrRaces[] = new db_generic_record(['id' => '0']);

?>
<title>Races</title>

<form method="post" action autocomplete="off">
	<table border="1" cellpadding="4" cellspacing="1">
		<tr>
			<th></th>
			<th>Name</th>
			<th>Plural</th>
			<th>Enabled</th>
		</tr>
		<?php
		foreach ( $arrRaces AS $race ) {
			echo '<tr>';
			echo '<th>[' . $race->id . ']</th>';
			echo '<td><input name="r[' . $race->id . '][race]" value="' . html($race->race) . '" /></td>';
			echo '<td><input name="r[' . $race->id . '][race_plural]" value="' . html($race->race_plural) . '" /></td>';
			echo '<th><input type="checkbox" name="r[' . $race->id . '][enabled]" ' . ( $race->enabled ? 'checked' : '' ) . ' /></th>';
			echo '</tr>';
		}
		?>
		<tr>
			<th colspan="4"><input type="submit" value="Opslaan" /></td>
		</tr>
	</table>
</form>
