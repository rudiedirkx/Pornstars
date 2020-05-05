<?php

require_once '../inc.bootstrap.php';

// Save existing
if ( isset($_POST['rd']) ) {
	$db->begin();

	foreach ( $_POST['rd'] as $id => $data ) {
		// requires //
		$db->delete('d_r_d_requires', ['r_d_id' => $id]);
		foreach ( (array) @$data['requires'] as $rdId ) {
			$db->insert('d_r_d_requires', [
				'r_d_id' => $id,
				'r_d_requires_id' => $rdId,
			]);
		}

		// excludes //
		$db->delete('d_r_d_excludes', ['r_d_id' => $id]);
		foreach ( (array) @$data['excludes'] as $rdId ) {
			$db->insert('d_r_d_excludes', [
				'r_d_id' => $id,
				'r_d_excludes_id' => $rdId,
			]);
		}

		// races //
		$db->delete('d_r_d_per_race', ['r_d_id' => $id]);
		foreach ( (array) @$data['race'] as $raceId ) {
			$db->insert('d_r_d_per_race', [
				'r_d_id' => $id,
				'race_id' => $raceId,
			]);
		}

		// skills //
		$db->delete('d_skills_per_r_d', ['r_d_id' => $id]);
		foreach ( (array) @$data['skill'] as $skill ) {
			if ( $skill['id'] && $skill['level'] ) {
				$db->insert('d_skills_per_r_d', [
					'r_d_id' => $id,
					'skill_id' => $skill['id'],
					'required_value' => $skill['level'],
				]);
			}
		}

		// costs //
		$db->delete('d_r_d_costs', ['r_d_id' => $id]);
		foreach ( (array) @$data['costs'] as $rid => $amount ) {
			if ( (int) $amount > 0 ) {
				$db->insert('d_r_d_costs', [
					'r_d_id' => $id,
					'resource_id' => $rid,
					'amount' => (int) $amount,
				]);
			}
		}
	}

	$db->commit();

	return do_redirect(null);
}

// Create new
elseif ( isset($_POST['name'], $_POST['T'], $_POST['eta'], $_POST['explanation']) ) {
	$db->insert('d_r_d_available', $_POST);

	return do_redirect(null);
}

$RD = $db->select_by_field('d_r_d_available', 'id', '1')->all();

$multipler = function($table, $column) {
	global $db;
	return array_reduce($db->select($table, '1')->all(), function($set, $row) use ($column) {
		$set[$row->r_d_id][] = $row->$column;
		return $set;
	}, []);
};

$g_arrRequires = $multipler('d_r_d_requires', 'r_d_requires_id');
$g_arrExcludes = $multipler('d_r_d_excludes', 'r_d_excludes_id');
$g_arrForRaces = $multipler('d_r_d_per_race', 'race_id');

$g_arrAllowsUnits = $db->select_fields('d_all_units', 'r_d_required_id, COUNT(*)', '1 GROUP BY r_d_required_id');

$g_arrAllowsRDResults = $db->select_fields('d_r_d_results', 'done_r_d_id, COUNT(*)', '1 GROUP BY done_r_d_id');

$arrRaces = $db->select_fields('d_races', 'id, race', '1 ORDER BY id ASC');
$arrSkills = $db->select_fields('d_skills', 'id, skill', '1 ORDER BY id ASC');
$arrResources = $db->select_fields('d_resources', 'id, resource', '1 ORDER BY id ASC');

?>
<title>R&D</title>

<style>
input[size="1"] {
	width: 2em;
}
select {
	max-width: 15em;
}
body[data-rd]:not([data-rd="d"]) [data-rd="d"] .hideable,
body[data-rd]:not([data-rd="r"]) [data-rd="r"] .hideable {
	display: none;
}
.rb {
	border-right: solid 4px black;
}
</style>

<form method="post" action autocomplete="off">
	<table width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr bgcolor="#dddddd">
			<th data-rd="r" onclick="document.body.dataset.rd = 'r'" class="rb">RESEARCHES</th>
			<th data-rd="d" onclick="document.body.dataset.rd = 'd'">DEVELOPMENTS</th>
		</tr>
		<tr valign="top">
			<td data-rd="r" class="rb" align="center">
				<?= printRDSelect($RD, 'r', $arrRaces, $arrSkills, $arrResources) ?>
			</td>
			<td data-rd="d" align="center">
				<?= printRDSelect( $RD, 'd', $arrRaces, $arrSkills, $arrResources ) ?>
			</td>
		</tr>
		<tr bgcolor="#dddddd">
			<th data-rd="r" onclick="document.body.dataset.rd = 'r'" class="rb">RESEARCHES</th>
			<th data-rd="d" onclick="document.body.dataset.rd = 'd'">DEVELOPMENTS</th>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Opslaan" /></th>
		</tr>
	</table>
</form>

<form method="post" action="">
	<table border="1" cellpadding="4" cellspacing="1">
		<tr>
			<th>Name</th>
			<td><input type="text" size="40" value="" name="name" /></td>
			<th>Type</th>
			<td><select name="T"><option value="r">r</option><option value="d">d</option></select></td>
		</tr>
		<tr>
			<th>Explanation</th>
			<td><input type="text" size="40" value="" name="explanation" /></td>
			<th>ETA</th>
			<td><input type="text" size="8" value="0" name="eta" /></td>
		</tr>
		<tr>
			<th colspan="4"><input type="submit" value="Toevoegen" /></th>
		</tr>
	</table>
</form>

<?php

function printRDSelect($RD, $type, $races, $skills, $resources) {
	global $db, $g_arrRequires, $g_arrExcludes, $g_arrForRaces, $g_arrAllowsRDResults, $g_arrAllowsUnits;

	$rdOptions = array_map(function($rd) {
		return strtoupper($rd['T']) . ' ' . $rd['id'] . '. ' . $rd['name'];
	}, $RD);

	$szHtml = '';
	$szHtml .= '<table width="100%" id="tbl_' . $type . '" border="0">';
	$szHtml .= '<tr>';
	$szHtml .= '<td></td>';
	$szHtml .= '<th class="hideable">REQUIRE</th>';
	$szHtml .= '<th class="hideable">EXCLUDE</th>';
	$szHtml .= '<th class="hideable">RACES</th>';
	$szHtml .= '<th class="hideable">SKILLS</th>';
	$szHtml .= '</tr>';

	$n2 = 0;
	foreach ( $RD AS $rd ) {
		if ( $rd->T !== $type ) {
			continue;
		}

		$szHtml .= '<tr'.( $n2++%2==0 ? ' bgcolor="#eeeeee"' : '' ).' valign="top">';

		// info //
		$szHtml .= '<td>';
		$szHtml .= '<b>' . $rd->id . '. ' . $rd->name . '</b><br /><br />';
		$szHtml .= 'Requires:&nbsp;' . count((array) @$g_arrRequires[ $rd->id ]) . '<br />';
		$szHtml .= 'Excludes:&nbsp;' . count((array) @$g_arrExcludes[ $rd->id ]) . '<br />';
		$szHtml .= 'Races:&nbsp;' . count((array) @$g_arrForRaces[ $rd->id ]) . '<br />';
		$szHtml .= 'R&D&nbsp;results:&nbsp;' . (int) @$g_arrAllowsRDResults[ $rd->id ] . '<br />';
		$szHtml .= 'Units:&nbsp;' . (int) @$g_arrAllowsUnits[ $rd->id ] . '<br />';
		$szHtml .= '</td>';

		// requires //
		$szHtml .= '<td class="hideable" align="center">';
		$szHtml .= '<select size="10" name="rd[' . $rd->id . '][requires][]" multiple>';
		$szHtml .= html_options($rdOptions, (array) @$g_arrRequires[$rd->id]);
		$szHtml .= '</select>';
		$szHtml .= '</td>';

		// excludes //
		$szHtml .= '<td class="hideable" align="center">';
		$szHtml .= '<select size="10" name="rd[' . $rd->id . '][excludes][]" multiple>';
		$szHtml .= html_options($rdOptions, (array) @$g_arrExcludes[$rd->id]);
		$szHtml .= '</select>';
		$szHtml .= '</td>';

		// races //
		$szHtml .= '<td class="hideable" align="center">';
		$szHtml .= '<select size="' . count($races) . '" name="rd[' . $rd->id . '][race][]" multiple>';
		$szHtml .= html_options($races, (array) @$g_arrForRaces[$rd->id]);
		$szHtml .= '</select>';
		$szHtml .= '</td>';

		$arrRequiredSkills = $db->select('d_skills_per_r_d', ['r_d_id' => $rd->id])->all();
		$arrRequiredSkills[] = ['skill_id' => '', 'required_value' => ''];

		$arrCosts = $db->select_fields('d_r_d_costs', 'resource_id, amount', ['r_d_id' => $rd->id]);

		$szHtml .= '<td class="hideable" nowrap>';
		// skills //
		foreach ( $arrRequiredSkills AS $n => $rs ) {
			$szHtml .= '<div>';
			$szHtml .= '<select name="rd[' . $rd->id . '][skill][' . $n . '][id]">';
			$szHtml .= html_options($skills, $rs['skill_id'], '--');
			$szHtml .= '</select>';
			$szHtml .= '<input name="rd[' . $rd->id . '][skill][' . $n . '][level]" size="1" value="' . $rs['required_value'] . '" />';
			$szHtml .= '</div>';
		}
		$szHtml .= '<br />';
		// costs //
		foreach ( $resources as $id => $name ) {
			$szHtml .= '<div>';
			$szHtml .= $name . '<br />';
			$szHtml .= '<input name="rd[' . $rd->id . '][costs][' . $id . ']" size="5" value="' . @$arrCosts[$id] . '" />';
			$szHtml .= '</div>';
		}
		$szHtml .= '</td>';

		$szHtml .= '</tr>';
	}

	$szHtml .= '</table>';

	return $szHtml;
}
