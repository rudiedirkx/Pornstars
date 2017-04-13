<?php

require_once '../inc.bootstrap.php';

$types = ['ship', 'defence', 'roidscan', 'power', 'scan', 'amp', 'block'];

// Save existing
if ( isset($_POST['units']) ) {
	$db->begin();

	foreach ( $_POST['units'] as $id => $data ) {
		$costs = array_filter($data['costs']);
		$combats = (array) @$data['combat_stats'];
		unset($data['costs'], $data['combat_stats']);

		// properties //
		$data['is_stealth'] = !empty($data['is_stealth']);
		$data['is_mobile'] = !empty($data['is_mobile']);
		$data['is_offensive'] = !empty($data['is_offensive']);
		$data['steals'] or $data['steals'] = null;
		$db->update('d_all_units', $data, compact('id'));

		// costs //
		$db->delete('d_unit_costs', ['unit_id' => $id]);
		foreach ( $costs as $rid => $amount ) {
			$db->insert('d_unit_costs', [
				'unit_id' => $id,
				'resource_id' => $rid,
				'amount' => $amount,
			]);
		}

		// combat stats //
		$db->delete('d_combat_stats', ['shooting_unit_id' => $id]);
		foreach ( $combats as $otherId => $combat ) {
			if ( $combat['ratio'] ) {
				$db->insert('d_combat_stats', [
					'shooting_unit_id' => $id,
					'receiving_unit_id' => $otherId,
					'ratio' => 1 / $combat['ratio'],
					'target_priority' => $combat['target_priority'] ?: 1,
				]);
			}
		}

	}

	$db->commit();

	return do_redirect(null);
}

// @todo Create new

$arrRD = $db->select_fields('d_r_d_available', "id, CONCAT(UPPER(T), ': ', name)", '1 ORDER BY T, id');

$arrUnits = $db->select('d_all_units', '1 ORDER BY FIELD(T, ?), o', [$types]);

$arrOffensiveTypes = ['ship', 'defence'];
$arrOffensives = $db->select('d_all_units', 'T IN (?) ORDER BY T, o', [$arrOffensiveTypes])->all();

$arrCombatStats = $db->select('d_combat_stats', '1');
$g_arrCombatStats = array();
foreach ( $arrCombatStats AS $cs ) {
	$g_arrCombatStats[$cs->shooting_unit_id][$cs->receiving_unit_id] = [(float) $cs->ratio, (int) $cs->target_priority];
}

$arrCosts = $db->select('d_unit_costs', '1');
$g_arrCosts = array();
foreach ( $arrCosts as $c ) {
	$g_arrCosts[$c->unit_id][$c->resource_id] = $c->amount;
}

$arrResources = $db->select_fields('d_resources', 'id, resource', '1');

$arrSteals = ['asteroids','resources'];
$arrSteals = array_combine($arrSteals, $arrSteals);

?>
<title>Units</title>

<form method="post" action>
	<table border="1" cellpadding="4" cellspacing="1">
		<tr>
			<th></th>
			<th>SHIP DETAILS</th>
			<th></th>
		</tr>
		<?php
		foreach ( $arrUnits AS $n => $unit ) {
			echo '<tr valign="top">';
			echo '<th>' . $unit->id . '. ' . html($unit->unit) . '<br />[' . $unit->T . ']</th>';
			echo '<td>';
			echo '<table border="0">';

			// properties //

			echo '<tr>';
			echo '<th>Name</th>';
			echo '<td><input name="units['. $unit->id . '][unit]" value="' . html($unit->unit) . '" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Plural</th>';
			echo '<td><input name="units['. $unit->id . '][unit_plural]" value="' . html($unit->unit_plural) . '" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Explanation</th>';
			echo '<td><input name="units['. $unit->id . '][explanation]" value="' . html($unit->explanation) . '" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Build ETA</th>';
			echo '<td><input name="units['. $unit->id . '][build_eta]" value="' . html($unit->build_eta) . '" size="5" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Travel ETA</th>';
			echo '<td><input name="units['. $unit->id . '][move_eta]" value="' . html($unit->move_eta) . '" size="5" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Power</th>';
			echo '<td><input name="units['. $unit->id . '][power]" value="' . html($unit->power) . '" size="5" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Stealthy</th>';
			echo '<td><input type="checkbox" name="units['. $unit->id . '][is_stealth]" ' . ($unit->is_stealth ? 'checked' : '') . ' /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Mobile</th>';
			echo '<td><input type="checkbox" name="units['. $unit->id . '][is_mobile]" ' . ($unit->is_mobile ? 'checked' : '') . ' /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Offensive</th>';
			echo '<td><input type="checkbox" name="units['. $unit->id . '][is_offensive]" ' . ($unit->is_offensive ? 'checked' : '') . ' /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Steals</th>';
			echo '<td>';
			echo '<select name="units['. $unit->id . '][steals]">';
			echo html_options($arrSteals, $unit->steals, '--');
			echo '</select>';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>R&D required</th>';
			echo '<td>';
			echo '<select name="units['. $unit->id . '][r_d_required_id]">';
			echo html_options($arrRD, $unit->r_d_required_id);
			echo '</select>';
			echo '</td>';
			echo '</tr>';

			// costs //

			foreach ( $arrResources as $id => $name ) {
				echo '<tr>';
				echo '<th>' . html($name) . '</th>';
				echo '<td>';
				echo '<input name="units['. $unit->id . '][costs][' . $id . ']" size="5" value="' . @$g_arrCosts[$unit->id][$id] . '" />';
				echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
			echo '</td>';

			// combat stats //

			echo '<td align="center">';
			if ( in_array($unit->T, $arrOffensiveTypes) ) {
				echo '<strong><em>' . html($unit->unit_plural) . '</em> needed to destroy one:';
				echo '<table border="0">';
				foreach ( $arrOffensives AS $unit2 ) {
					$combat = @$g_arrCombatStats[$unit->id][$unit2->id];

					echo '<tr>';
					echo '<td nowrap>' . $unit2->unit . '</td>';
					echo '<td>';
					echo '<input name="units['. $unit->id . '][combat_stats][' . $unit2->id . '][ratio]" value="' . ( $combat ? round(1 / $combat[0], 2) : '' ) . '" size="5" />';
					echo '<input name="units['. $unit->id . '][combat_stats][' . $unit2->id . '][target_priority]" value="'.( $combat ? $combat[1] : '' ) . '" size="1" />';
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</td>';
			echo '</tr>';
		}

		?>
		<tr>
			<th colspan="3"><input type="submit" value="Opslaan" /></td>
		</tr>
	</table>
</form>

<form method="post" action>
	<fieldset>
		<select name="new_unit_T"><?= html_options($types) ?></select>
		<select name="required"><?php foreach ( $arrRD AS $id => $name ) { echo '<option value="'.$id.'">'.$name.'</option>'; } ?></select>
		<input type="submit" value="New" />
	</fieldset>
</form>
