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
		// $db->delete('d_r_d_per_race', ['r_d_id' => $id]);
		// foreach ( (array) @$data['race'] as $raceId ) {
		// 	$db->insert('d_r_d_per_race', [
		// 		'r_d_id' => $id,
		// 		'race_id' => $raceId,
		// 	]);
		// }

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
$g_arrRequiredBy = [];
foreach ($g_arrRequires as $a => $bs) {
	foreach ($bs as $b) {
		$g_arrRequiredBy[$b][] = $a;
	}
}
$g_arrExcludes = $multipler('d_r_d_excludes', 'r_d_excludes_id');
// $g_arrForRaces = $multipler('d_r_d_per_race', 'race_id');
// dd($g_arrRequires, $g_arrRequiredBy);

$g_arrAllowsUnits = $db->select_fields('d_all_units', 'r_d_required_id, COUNT(*)', '1 GROUP BY r_d_required_id');

$g_arrAllowsRDResults = $db->select_fields('d_r_d_results', 'done_r_d_id, COUNT(*)', '1 GROUP BY done_r_d_id');

// $arrRaces = $db->select_fields('d_races', 'id, race', '1 ORDER BY id ASC');
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
			<th data-rd="r" x-onclick="document.body.dataset.rd = 'r'" class="rb">RESEARCHES</th>
			<th data-rd="d" x-onclick="document.body.dataset.rd = 'd'">DEVELOPMENTS</th>
		</tr>
		<tr valign="top">
			<td data-rd="r" class="rb" align="center">
				<?= printRDSelect($RD, 'r', /*$arrRaces,*/ $arrSkills, $arrResources) ?>
			</td>
			<td data-rd="d" align="center">
				<?= printRDSelect($RD, 'd', /*$arrRaces,*/ $arrSkills, $arrResources) ?>
			</td>
		</tr>
		<tr bgcolor="#dddddd">
			<th data-rd="r" x-onclick="document.body.dataset.rd = 'r'" class="rb">RESEARCHES</th>
			<th data-rd="d" x-onclick="document.body.dataset.rd = 'd'">DEVELOPMENTS</th>
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

<?php include 'tpl.js.php' ?>
<script>
function filterRD(a, cls) {
	$$('.r-d').prop('hidden', true);
	$$(`.${cls}`).prop('hidden', false);
	a.closest('.r-d').hidden = false;
}

$$('a[data-filter-class]').invoke('addEventListener', 'click', function(e) {
	e.preventDefault();
	filterRD(this, this.dataset.filterClass);
});
</script>

<?php

function printRDSelect($RD, $type, /*$races,*/ $skills, $resources) {
	global $db, $g_arrRequires, $g_arrRequiredBy, $g_arrExcludes, /*$g_arrForRaces,*/ $g_arrAllowsRDResults, $g_arrAllowsUnits;

	$rdOptions = array_map(function($rd) {
		return strtoupper($rd['T']) . ' ' . $rd['id'] . '. ' . $rd['name'];
	}, $RD);

	$szHtml = '';
	$szHtml .= '<table width="100%" id="tbl_' . $type . '" border="0">';
	$szHtml .= '<tr>';
	$szHtml .= '<td></td>';
	$szHtml .= '<th class="hideable">REQUIRES</th>';
	$szHtml .= '<th class="hideable">EXCLUDES</th>';
	// $szHtml .= '<th class="hideable">RACES</th>';
	$szHtml .= '<th class="hideable">SKILLS</th>';
	$szHtml .= '</tr>';

	$n2 = 0;
	foreach ( $RD AS $rd ) {
		if ( $rd->T !== $type ) {
			continue;
		}

		$classes = array_merge(array_map(function($id) {
			return "requires-$id";
		}, $g_arrRequires[$rd->id] ?? []), array_map(function($id) {
			return "required-by-$id";
		}, $g_arrRequiredBy[$rd->id] ?? []), array_map(function($id) {
			return "excludes-$id";
		}, $g_arrExcludes[$rd->id] ?? []));
		$szHtml .= '<tr valign="top" class="r-d ' . implode(' ', $classes) . '">';

		// info //
		$szHtml .= '<td>';
		$szHtml .= '<b title="' . html($rd->explanation) . '">' . $rd->id . '. ' . $rd->name . '</b><br /><br />';
		$szHtml .= '<a href data-filter-class="required-by-' . $rd->id . '">Requires</a>:&nbsp;' . count($g_arrRequires[$rd->id] ?? []) . '<br />';
		$szHtml .= '<a href data-filter-class="requires-' . $rd->id . '">Required&nbsp;by</a>:&nbsp;' . count($g_arrRequiredBy[$rd->id] ?? []) . '<br />';
		$szHtml .= '<a href data-filter-class="excludes-' . $rd->id . '">Excludes</a>:&nbsp;' . count($g_arrExcludes[$rd->id] ?? []) . '<br />';
		// $szHtml .= 'Races:&nbsp;' . count($g_arrForRaces[$rd->id] ?? []) . '<br />';
		$szHtml .= '<a href="cfg_r_d_results.php?filter_r_d=' . $rd->id . '">R&D&nbsp;results</a>:&nbsp;' . ($g_arrAllowsRDResults[$rd->id] ?? 0) . '<br />';
		$szHtml .= '<a href="cfg_unit_stats.php?filter_r_d=' . $rd->id . '">Units</a>:&nbsp;' . ($g_arrAllowsUnits[$rd->id] ?? 0) . '<br />';
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
		// $szHtml .= '<td class="hideable" align="center">';
		// $szHtml .= '<select size="' . count($races) . '" name="rd[' . $rd->id . '][race][]" multiple>';
		// $szHtml .= html_options($races, (array) @$g_arrForRaces[$rd->id]);
		// $szHtml .= '</select>';
		// $szHtml .= '</td>';

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
