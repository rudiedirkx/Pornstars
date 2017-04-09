<?php

require_once '../inc.bootstrap.php';

// Save
if ( isset($_POST['r']) ) {
	foreach ( $_POST['r'] AS $id => $data ) {
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
		</tr>
		<?php
		foreach ( $arrResources AS $resource ) {
			echo '<tr>';
			echo '<th>[' . $resource->id . ']</th>';
			echo '<td><input name="r[' . $resource->id . '][resource]" value="' . html($resource->resource) . '" /></td>';
			echo '<td><input name="r[' . $resource->id . '][explanation]" value="' . html($resource->explanation) . '" /></td>';
			echo '<td><input name="r[' . $resource->id . '][color]" value="' . html($resource->color) . '" /></td>';
			echo '</tr>';
		}
		?>
		<tr>
			<th colspan="4"><input type="submit" value="Opslaan" /></td>
		</tr>
	</table>
</form>
