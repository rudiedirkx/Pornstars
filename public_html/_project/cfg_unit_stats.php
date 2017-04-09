<?php

require_once('../inc.connect.php');

$arrUnitProps = array('unit', 'unit_plural', 'explanation', 'build_eta', 'COSTS', 'move_eta', 'fuel', 'is_stealth', 'is_mobile', 'is_offensive', 'steals', 'r_d_required_id');

// if ( !empty($_POST) ) {
// 	echo '<pre>';print_r($_POST);exit;
// }

if ( isset($_POST['unit_id'], $_POST['unit'], $_POST['unit_plural'], $_POST['explanation'], $_POST['build_eta'], $_POST['costs'], $_POST['move_eta'], $_POST['fuel'], $_POST['steals'], $_POST['r_d_required_id']) ) {
	$iUnitId = (int)$_POST['unit_id'];
	$arrCosts = $_POST['costs'];
	$arrCombatStats = @$_POST['combat_stats'];
	unset($_POST['unit_id'], $_POST['costs'], $_POST['combat_stats']);

	// Attributes
	$_POST['steals'] = '' === $_POST['steals'] ? null : $_POST['steals'];
	foreach ( $arrUnitProps AS $szProp ) {
		if ( 0 === strpos($szProp, 'is_') ) {
			$_POST[$szProp] = !empty($_POST[$szProp]) ? '1' : '0';
		}
	}
	var_dump(db_update('d_all_units', $_POST, 'id = '.$iUnitId));
	echo db_error();
	echo "--\n";

	// Combat stats
	if ( $arrCombatStats ) {
		db_delete('d_combat_stats', 'shooting_unit_id = '.$iUnitId);
		foreach ( $arrCombatStats AS $iToUnitId => $s ) {
			if ( !empty($s['ratio']) && !empty($s['target_priority']) ) {
				var_dump(db_insert('d_combat_stats', array('shooting_unit_id' => $iUnitId, 'receiving_unit_id' => $iToUnitId, 'ratio' => 1.0/(float)$s['ratio'], 'target_priority' => $s['target_priority'])));
			}
		}
		echo "--\n";
	}

	// Costs
	db_delete('d_unit_costs', 'unit_id = '.$iUnitId);
	foreach ( $arrCosts AS $iResourceId => $iAmount ) {
		var_dump(db_replace_into('d_unit_costs', array('unit_id' => $iUnitId, 'resource_id' => $iResourceId, 'amount' => $iAmount)));
	}
	db_delete('d_unit_costs', 'amount = 0');

	exit;
}

if ( isset($_GET['new_unit_T'], $_GET['required']) ) {
	var_dump(db_insert('d_all_units', array('T' => $_GET['new_unit_T'], 'unit' => 'NEW', 'r_d_required_id' => $_GET['required'])));
	echo db_error();
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
}

echo '<title>Units</title>';

$arrRD = db_select_fields('d_r_d_available', 'id, CONCAT(UPPER(T),\': \',name)', '1 ORDER BY T ASC, id ASC');

?>
<style type="text/css">
select, input { font-size : 9px; }
</style>

<div><form action="" method="get">
	<select name="new_unit_T"><option value="ship">ship</option><option value="defence">defence</option><option value="roidscan">roidscan</option><option value="power">power</option><option value="scan">scan</option><option value="amp">amp</option><option value="block">block</option></select>
	<select name="required"><?php foreach ( $arrRD AS $id => $name ) { echo '<option value="'.$id.'">'.$name.'</option>'; } ?></select>
	<input type="submit" value="New" />
</form></div>

<table border="0">
<tr>
	<th></th>
	<th>SHIP DETAILS</th>
	<th></th>
</tr>
<?php

$arrUnits = db_select('d_all_units', '1 ORDER BY T ASC, o ASC');
$arrOffensives = db_select('d_all_units', '(T = \'ship\' OR T = \'defence\') ORDER BY T ASC, o ASC');

$arrCombatStats = db_select('d_combat_stats');
$g_arrCombatStats = array();
foreach ( $arrCombatStats AS $cs ) {
	$g_arrCombatStats[(int)$cs['shooting_unit_id']][(int)$cs['receiving_unit_id']] = array((float)$cs['ratio'], $cs['target_priority']);
}

$n=0;
foreach ( $arrUnits AS $arrUnit ) {
	echo '<form method="post" action="?submit"><input type="hidden" name="unit_id" value="'.$arrUnit['id'].'" /><tr'.( $n++%2==0 ? ' bgcolor="#eeeeee"' : '' ).' unit_id="'.$arrUnit['id'].'" valign="top">';
	echo '<th>'.$arrUnit['unit'].'<br />['.$arrUnit['T'].']<br /><br /><input type="submit" value="Save" /></th>';
	echo '<td><table border="0">';
	foreach ( $arrUnitProps AS $k => $szProp ) {
		if ( 'r_d_required_id' != $szProp && 'steals' != $szProp ) {
			if ( 'COSTS' === $szProp ) {
				$costs = db_fetch('SELECT *'.( 'NEW' !== $arrUnit['id'] ? ' FROM d_resources r LEFT JOIN d_unit_costs c ON c.resource_id = r.id AND c.unit_id = '.$arrUnit['id'] : ', 0 amount FROM d_resources r' ).' ORDER BY r.id ASC');
echo db_error();
				foreach ( $costs AS $c ) {
					echo '<tr><td align="right">'.$c['resource'].'</td><td>:</td><td><input type="text" name="costs['.$c['id'].']" value="'.(int)$c['amount'].'" size="5" /></td></tr>';
				}
			}
			else {
				echo '<tr><td>'.$szProp.'</td><td>:</td><td>'.( 0 === strpos($szProp, 'is_') ? '<input type="checkbox" name="'.$szProp.'" value="1"'.( (int)$arrUnit[$szProp] ? ' checked="1"' : '' ).' />' : '<input type="text" name="'.$szProp.'" value="'.$arrUnit[$szProp].'" size="'.( 2 >= $k ? 40 : 5 ).'" />' ).'</td></tr>';
			}
		}
	}
	echo '<tr><td>Steals</td><td>:</td><td><select name="steals">';
	foreach ( array(null,'asteroids','resources') AS $s ) {
		echo '<option'.( $s === $arrUnit['steals'] ? ' selected="1"' : '' ).' value="'.$s.'">'.$s.'</option>';
	}
	echo '</select></td></tr>';
	echo '<tr><td>R&D required</td><td>:</td><td><select name="r_d_required_id">';
	foreach ( $arrRD AS $iRD => $szRD ) { echo '<option value="'.$iRD.'"'.( (string)$arrUnit['r_d_required_id'] === (string)$iRD ? ' selected="1"' : '' ).'>'.$szRD.'</option>'; }
	echo '</select></td></tr>';
	echo '</table></td>';
	if ( 'NEW' != $arrUnit['id'] && ( 'ship' == $arrUnit['T'] || 'defence' == $arrUnit['T'] ) ) {
		echo '<td align="center">Units needed to destroy one:<table border="0">';
		foreach ( $arrOffensives AS $u ) {
			if ( 'NEW' !== $u['id'] ) {
				echo '<tr><td nowrap="1" wrap="off">'.$u['unit'].'</td><td>:</td><td><input type="text" name="combat_stats['.$u['id'].'][ratio]" value="'.( isset($g_arrCombatStats[(int)$arrUnit['id']][(int)$u['id']]) ? round(1/$g_arrCombatStats[(int)$arrUnit['id']][(int)$u['id']][0], 2) : '' ).'" size="8" /><input type="text" name="combat_stats['.$u['id'].'][target_priority]" style="text-align:center;" value="'.( isset($g_arrCombatStats[(int)$arrUnit['id']][(int)$u['id']]) ? $g_arrCombatStats[(int)$arrUnit['id']][(int)$u['id']][1] : '' ).'" size="1" /></td></tr>';
			}
		}
		echo '</table></td>';
	}
	echo '</tr></form>';
}

?>
</table>
