<?php

require_once('../inc.connect.php');

if ( isset($_POST['r']) ) {
	header('Location: '.basename($_SERVER['PHP_SELF']));
	echo '<pre>';
	foreach ( $_POST['r'] AS $iSkill => $arrSkill ) {
		if ( !empty($arrSkill['delete']) ) {
			echo $iSkill.' (delete): ';
			var_dump(db_delete('d_resources', 'id = '.(int)$iSkill));
			echo db_error();
		}
		else {
			unset($arrSkill['id']);
			echo $iSkill.': ';
			var_dump(db_update('d_resources', $arrSkill, 'id = '.(int)$iSkill));
			echo db_error();
		}
	}
	exit;
}

if ( isset($_GET['new']) ) {
	db_insert('d_resources', array('resource' => 'NEW'));
	header('Location: '.basename($_SERVER['PHP_SELF']));
	exit;
}

echo '<title>Resources</title>';

$arrResources = db_select('d_resources', '1 ORDER BY id DESC');

echo '<form method="post" action="" autocomplete="off"><table border="1" cellpadding="4" cellspacing="1">';
echo '<tr><th><input type="button" value="new" onclick="document.location=\'?new=1\';" /></th><th>Name</th><th>Explanation</th><th>Color</th><th title="DELETE">X</th></tr>';
foreach ( $arrResources AS $r ) {
	echo '<tr>';
	echo '<th>['.$r['id'].']</th>';
	echo '<td><input type="text" size="20" name="r['.$r['id'].'][resource]" value="'.$r['resource'].'" /></td>';
	echo '<td><input type="text" size="40" name="r['.$r['id'].'][explanation]" value="'.$r['explanation'].'" /></td>';
	echo '<td><input type="text" size="20" name="r['.$r['id'].'][color]" value="'.$r['color'].'" /></td>';
	echo '<th><input type="checkbox" name="r['.$r['id'].'][delete]" value="1" /></th>';
	echo '</tr>';
//	echo '<tr><th colspan="4" bgcolor="#666666"></td></tr>';
}
echo '<tr><th colspan="5"><input type="submit" value="Opslaan" /></td></tr>';
echo '</table></form>';


