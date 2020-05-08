<?php

use rdx\ps\Unit;

require_once '../inc.bootstrap.php';

$types = array_keys(Unit::$types);
$subtypes = Unit::$subtypes;

// Save existing
if ( isset($_POST['units']) ) {
	$db->begin();

	foreach ( $_POST['units'] as $id => $data ) {
		$variants = array_values(array_filter($data['costs'], function($cost) {
			return array_filter($cost);
		}));
		$combats = (array) @$data['combat_stats'];
		unset($data['costs'], $data['combat_stats']);

		// properties //
		$data['is_stealth'] = !empty($data['is_stealth']);
		$data['steals'] or $data['steals'] = null;
		$data['subtype'] or $data['subtype'] = null;
		$db->update('d_all_units', $data, compact('id'));

		// costs //
		$db->delete('d_unit_costs', ['unit_id' => $id]);
		foreach ( $variants as $variant => $costs ) {
			foreach ( array_filter($costs) as $rid => $amount ) {
				$db->insert('d_unit_costs', [
					'unit_id' => $id,
					'variant' => $variant,
					'resource_id' => $rid,
					'amount' => $amount,
				]);
			}
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

if ( isset($_POST['new_unit_type'], $_POST['r_d_required']) ) {
	$db->begin();

	$id = Unit::insert([
		'T' => $_POST['new_unit_type'],
		'r_d_required_id' => $_POST['r_d_required'],
		'o' => 99,
	]);
	$db->insert(Unit::$tables[$_POST['new_unit_type']], ['id' => $id]);

	$db->commit();

	return do_redirect();
}

$arrRD = $db->select_fields('d_r_d_available', "id, CONCAT(UPPER(T), ': ', name)", '1 ORDER BY T, id');
$arrUsedRD = array_intersect_key($arrRD, $db->select_fields('d_all_units', 'r_d_required_id', '1'));

$arrUnits = $db->select('d_all_units', '1 ORDER BY FIELD(T, ?), o', [$types]);

$arrOffensiveTypes = ['ship', 'defence'];
$arrOffensives = $db->select('d_all_units', 'T IN (?) ORDER BY FIELD(T, ?), o', [$arrOffensiveTypes, $types])->all();

$arrCombatStats = $db->select('d_combat_stats', '1');
$g_arrCombatStats = array();
foreach ( $arrCombatStats AS $cs ) {
	$g_arrCombatStats[$cs->shooting_unit_id][$cs->receiving_unit_id] = [(float) $cs->ratio, (int) $cs->target_priority];
}

$arrCosts = $db->select('d_unit_costs', '1');
$g_arrCosts = array();
foreach ( $arrCosts as $c ) {
	$g_arrCosts[$c->unit_id][$c->variant][$c->resource_id] = $c->amount;
}

$arrResources = $db->select_fields('d_resources', 'id, resource', '1');

$arrSteals = ['asteroids','resources'];
$arrSteals = array_combine($arrSteals, $arrSteals);

?>
<title>Units</title>

<p>
	Filter:
	<select data-filter='span[data-type="?"]'><?= html_options(array_combine($types, $types), $_GET['filter_type'] ?? null, '-- All Types') ?></select>
	<select data-filter='select[data-r_d="?"]'><?= html_options($arrUsedRD, $_GET['filter_r_d'] ?? null, '-- All R&D') ?></select>
</p>

<form method="post" action>
	<table border="1" cellpadding="4" cellspacing="1">
		<thead>
			<tr>
				<th></th>
				<th>SHIP DETAILS</th>
				<th></th>
			</tr>
		</thead>
		<?php
		foreach ( $arrUnits AS $n => $unit ) {
			echo '<tbody class="filterable">';

			echo '<tr valign="top">';
			echo '<th>' . $unit->id . '. ' . html($unit->unit) . '<br /><span data-type="' . $unit->T . '">[' . $unit->T . ']</span></th>';
			echo '<td>';
			echo '<table>';

			// properties //

			echo '<tr>';
			echo '<th>Name</th>';
			echo '<td colspan="9"><input name="units[' . $unit->id . '][unit]" value="' . html($unit->unit) . '" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Plural</th>';
			echo '<td colspan="9"><input name="units[' . $unit->id . '][unit_plural]" value="' . html($unit->unit_plural) . '" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Explanation</th>';
			echo '<td colspan="9"><input name="units[' . $unit->id . '][explanation]" value="' . html($unit->explanation) . '" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Build ETA</th>';
			echo '<td colspan="9"><input name="units[' . $unit->id . '][build_eta]" value="' . html($unit->build_eta) . '" size="5" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Travel ETA</th>';
			echo '<td colspan="9"><input name="units[' . $unit->id . '][move_eta]" value="' . html($unit->move_eta) . '" size="5" /></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<th>Power</th>';
			echo '<td colspan="9"><input name="units[' . $unit->id . '][power]" value="' . html($unit->power) . '" size="5" /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Stealthy</th>';
			echo '<td colspan="9"><input type="checkbox" name="units[' . $unit->id . '][is_stealth]" ' . ($unit->is_stealth ? 'checked' : '') . ' /></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Steals</th>';
			echo '<td colspan="9">';
			echo '<select name="units[' . $unit->id . '][steals]">';
			echo html_options($arrSteals, $unit->steals, '--');
			echo '</select>';
			echo '</td>';
			echo '</tr>';

			$_subtypes = array_keys(array_filter($subtypes, function($type, $subtype) use ($unit) {
				return $type == $unit->T;
			}, ARRAY_FILTER_USE_BOTH));
			echo '<tr>';
			echo '<th>Sub type</th>';
			echo '<td colspan="9">';
			echo '<select name="units[' . $unit->id . '][subtype]">';
			echo html_options(array_combine($_subtypes, $_subtypes), $unit->subtype, '--');
			echo '</select>';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>R&D required</th>';
			echo '<td colspan="9">';
			echo '<select data-r_d="' . $unit->r_d_required_id . '" name="units[' . $unit->id . '][r_d_required_id]">';
			echo html_options($arrRD, $unit->r_d_required_id);
			echo '</select>';
			echo '</td>';
			echo '</tr>';

			// costs //

			$variants = count((array) @$g_arrCosts[$unit->id]) + 1;
			foreach ( $arrResources as $rid => $name ) {
				echo '<tr>';
				echo '<th>' . html($name) . '</th>';
				for ($variant = 0; $variant < $variants; $variant++) {
					echo '<td>';
					echo '<input name="units[' . $unit->id . '][costs][' . $variant . '][' . $rid . ']" size="5" value="' . @$g_arrCosts[$unit->id][$variant][$rid] . '" />';
					echo '</td>';
				}
				echo '</tr>';
			}

			echo '<tr>';
			echo '<th>Order</th>';
			echo '<td colspan="9"><input type="number" min="0" name="units[' . $unit->id . '][o]" value="' . $unit->o . '" size="5" /></td>';
			echo '</tr>';

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
					echo '<input name="units[' . $unit->id . '][combat_stats][' . $unit2->id . '][ratio]" value="' . ( $combat ? round(1 / $combat[0], 2) : '' ) . '" size="5" />';
					echo '<input name="units[' . $unit->id . '][combat_stats][' . $unit2->id . '][target_priority]" value="'.( $combat ? $combat[1] : '' ) . '" size="1" />';
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			echo '</td>';
			echo '</tr>';

			echo '</tbody>';
		}

		?>
		<tfoot>
			<tr>
				<th colspan="3"><input type="submit" value="Opslaan" /></td>
			</tr>
		</tfoot>
	</table>
</form>

<form method="post" action>
	<fieldset>
		<select name="new_unit_type"><?= html_options(array_combine($types, $types)) ?></select>
		<select name="r_d_required"><?= html_options($arrRD) ?></select>
		<input type="submit" value="New" />
	</fieldset>
</form>

<?php include 'tpl.js.php' ?>
