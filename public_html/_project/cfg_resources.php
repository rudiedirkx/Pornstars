<?php

require_once '../inc.bootstrap.php';

// Save
if ( isset($_POST['r'], $_POST['is_power']) ) {
	foreach ( $_POST['r'] AS $id => $data ) {
		$data['is_power'] = $_POST['is_power'] === (string) $id;
		if ( $id ) {
			$db->update('d_resources', $data, compact('id'));
		}
		elseif ( $data['resource'] ) {
			$db->insert('d_resources', $data);
		}
	}

	return do_redirect(null);
}

$arrResources = $db->select('d_resources', '1')->all();
$arrResources[] = new db_generic_record(['id' => '0']);

?>
<title>Resources</title>

<form method="post" action autocomplete="off">
	<table border="1" cellpadding="4" cellspacing="1">
		<tr>
			<th></th>
			<th>Name</th>
			<th>Explanation</th>
			<th>Color</th>
			<th>Is fuel</th>
		</tr>
		<?php
		foreach ( $arrResources AS $resource ) {
			echo '<tr>';
			echo '<th>[' . $resource->id . ']</th>';
			echo '<td><input name="r[' . $resource->id . '][resource]" value="' . html($resource->resource) . '" /></td>';
			echo '<td><input name="r[' . $resource->id . '][explanation]" value="' . html($resource->explanation) . '" /></td>';
			echo '<td><input name="r[' . $resource->id . '][color]" value="' . html($resource->color) . '" /></td>';
			echo '<td><input type="radio" name="is_power" value="' . $resource->id . '" ' . ($resource->is_power ? 'checked' : '') . ' /></td>';
			echo '</tr>';
		}
		?>
		<tr>
			<th colspan="5"><input type="submit" value="Opslaan" /></td>
		</tr>
	</table>
</form>
