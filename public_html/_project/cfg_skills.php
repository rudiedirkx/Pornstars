<?php

require_once '../inc.bootstrap.php';

// Save
if ( isset($_POST['r']) ) {
	foreach ( $_POST['r'] AS $id => $data ) {
		if ( $id ) {
			$db->update('d_skills', $data, compact('id'));
		}
		elseif ( $data['skill'] ) {
			$db->insert('d_skills', $data);
		}
	}

	return do_redirect(null);
}

$arrSkills = $db->select('d_skills', '1')->all();
$arrSkills[] = new db_generic_record(['id' => '0']);

?>
<title>Skills</title>

<form method="post" action autocomplete="off">
	<table border="1" cellpadding="4" cellspacing="1">
		<tr>
			<th></th>
			<th>Name</th>
			<th>Explanation</th>
		</tr>
		<?php
		foreach ( $arrSkills AS $skill ) {
			echo '<tr>';
			echo '<th>[' . $skill->id . ']</th>';
			echo '<td><input name="r[' . $skill->id . '][skill]" value="' . html($skill->skill) . '" /></td>';
			echo '<td><input name="r[' . $skill->id . '][explanation]" value="' . html($skill->explanation) . '" /></td>';
			echo '</tr>';
		}
		?>
		<tr>
			<th colspan="3"><input type="submit" value="Opslaan" /></td>
		</tr>
	</table>
</form>
