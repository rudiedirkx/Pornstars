<?php

require_once('../inc.connect.php');

if ( isset($_POST['requires']) || isset($_POST['excludes']) )
{
	header('Location: '.$_SERVER['HTTP_REFERER']);

echo '<pre>';
	$arrRequires = isset($_POST['requires']) ? $_POST['requires'] : array();
	$arrExcludes = isset($_POST['excludes']) ? $_POST['excludes'] : array();
	$arrRDPerRace = isset($_POST['per_race']) ? $_POST['per_race'] : array();

	foreach ( $_POST['skills'] AS $iRD => $arrSkills ) {
		foreach ( $arrSkills AS $k => $d ) {
			if ( '' === $d['skill_id'] || '' === $d['value'] ) {
				unset($_POST['skills'][$iRD][$k]);
			}
		}
		if ( empty($_POST['skills'][$iRD]) ) {
			unset($_POST['skills'][$iRD]);
		}
		unset($arrSkills);
	}

print_r($_POST['skills']);
	db_query('DELETE FROM d_skills_per_r_d;');
	foreach ( $_POST['skills'] AS $iRD => $arrSkills )
	{
		foreach ( $arrSkills AS $d )
		{
			db_replace_into('d_skills_per_r_d', array('r_d_id' => $iRD, 'skill_id' => (int)$d['skill_id'], 'value' => (int)$d['value']));
		}
	}

	echo "\n".'delete from d_skills_per_r_d: ';
	var_dump(db_delete('d_skills_per_r_d', '0 >= required_value'));

	echo "\n".'TRUNCATE d_r_d_requires: ';
	var_dump(db_query("DELETE FROM d_r_d_requires;"));
	echo db_error();
	$szInserts = '';
	foreach ( $arrRequires AS $iRDId => $arrRDs )
	{
		$szInserts .= ",(".$iRDId."," . implode("),(".$iRDId.",", $arrRDs) . ")";
	}
	echo $szSqlQuery = 'INSERT INTO d_r_d_requires (r_d_id, r_d_requires_id) VALUES '.substr($szInserts, 1).';'."\n";
	db_query($szSqlQuery) or die("\n\n".db_error());

	echo "\n".'TRUNCATE d_r_d_excludes: ';
	var_dump(db_query("DELETE FROM d_r_d_excludes;"));
	echo db_error();
	$szInserts = '';
	foreach ( $arrExcludes AS $iRDId => $arrRDs )
	{
		$szInserts .= ",(".$iRDId."," . implode("),(".$iRDId.",", $arrRDs) . ")";
	}
	echo $szSqlQuery = 'INSERT INTO d_r_d_excludes (r_d_id, r_d_excludes_id) VALUES '.substr($szInserts, 1).';'."\n";
	db_query($szSqlQuery) or die("\n\n".db_error());

	echo "\n".'TRUNCATE d_r_d_per_race: ';
	var_dump(db_query("DELETE FROM d_r_d_per_race;"));
	$szInserts = '';
	foreach ( $arrRDPerRace AS $iRDId => $arrRaces )
	{
		$szInserts .= ",(".$iRDId."," . implode("),(".$iRDId.",", $arrRaces) . ")";
	}
	echo $szSqlQuery = 'INSERT INTO d_r_d_per_race (r_d_id, race_id) VALUES '.substr($szInserts, 1).';'."\n";
	db_query($szSqlQuery) or die("\n\n".db_error());

	exit;
}

else if ( isset($_POST['name'], $_POST['T'], $_POST['eta'], $_POST['explanation'], $_POST['costs']) ) {
	var_dump(db_query("INSERT INTO d_r_d_available (`T`, `name`, `eta`, `explanation`) VALUES ('".$_POST['T']."', '".$_POST['name']."', '".$_POST['eta']."', '".$_POST['explanation']."')"));
	$iRdId = db_insert_id();
	foreach ( $_POST['costs'] AS $iResourceId => $iAmount ) {
		var_dump(db_query("INSERT INTO d_r_d_costs (r_d_id, resource_id, amount) VALUES (".$iRdId.", ".$iResourceId.", ".$iAmount.")"));
	}
	exit;
}

$RD = db_select_by_field('d_r_d_available', 'id');

?>
<style type="text/css">
select { font-size : 9px; } .rb { border-right : solid 4px black; }
</style>
<script type="text/javascript" src="/general_1_2_7.js"></script>
<form method="post" action="" autocomplete="off">
<table width="100%" border="0" cellpadding="3" cellspacing="0"><tr bgcolor="#dddddd"><th onclick="showColumn('r');" class="rb">RESEARCHES</th><th onclick="showColumn('d');">DEVELOPMENTS</th></tr><tr valign="top"><td class="rb" align="center">
<?php

$arrRequires = db_select('d_r_d_requires');
$g_arrRequires = array();
foreach ( $arrRequires AS $rd ) {
	$g_arrRequires[$rd['r_d_id']][$rd['r_d_requires_id']] = true;
}
$arrExcludes = db_select('d_r_d_excludes');
$g_arrExcludes = array();
foreach ( $arrExcludes AS $rd ) {
	$g_arrExcludes[$rd['r_d_id']][$rd['r_d_excludes_id']] = true;
}
$arrForRaces = db_select('d_r_d_per_race');
$g_arrForRaces = array();
foreach ( $arrForRaces AS $rd ) {
	$g_arrForRaces[$rd['r_d_id']][$rd['race_id']] = true;
}

$arrRaces = db_select_fields('d_races', 'id,race', '1 ORDER BY id ASC');
$arrSkills = db_select_fields('d_skills', 'id,skill', '1 ORDER BY id ASC');

printRDSelect( $RD, 'r', $arrRaces, $arrSkills );
echo '</td><td align="center">';
printRDSelect( $RD, 'd', $arrRaces, $arrSkills );
echo '</td></tr><tr bgcolor="#dddddd"><th class="rb">RESEARCHES</th><th>DEVELOPMENTS</th></tr><tr><th colspan="2"><input type="submit" value="Opslaan" /></th></tr></table>';

?>
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
	<td colspan="4"><?php foreach ( db_select('d_resources', '1 ORDER BY id ASC') AS $k => $r ) { echo ( 0 < $k ? ' | ' : '' ).$r['resource'].': <input style="background-color:'.$r['color'].';color:white;" type="text" size="8" value="0" name="costs['.$r['id'].']" />'; } ?></td>
</tr>
<tr>
	<th colspan="4"><input type="submit" value="Toevoegen" /></th>
</tr>
</table>
</form>

<script type="text/javascript">
function showColumn(yes) {
	var no = 'r' === yes ? 'd' : 'r';
	doToColumns($('tbl_'+yes), '');
	doToColumns($('tbl_'+no), 'none');
}
function doToColumns(t, w) {
	[].forEach.call(t.rows, function(r) {
		for ( var i=1; i<r.cells.length; i++ ) {
			r.cells[i].style.display = w;
		}
	});
}
[].forEach.call(document.forms, function(f) {
	f.reset();
});
</script>
<?php

function printRDSelect($RD, $T, $R, $S)
{
	global $g_arrRequires, $g_arrExcludes, $g_arrForRaces;
	$szHtml = '';
	$szHtml .= '<table width="100%" id="tbl_'.$T.'" border="0">';
	$szHtml .= '<tr><td><br /></td><th>REQUIRED R&D</th><th>EXCLUDED R&D</th><th>ONLY FOR RACES</th><th>REQUIRED SKILLS</th></tr>';
	$n2=0;
	foreach ( $RD AS $rd )
	{
		if ( $rd['T'] !== $T ) {
			continue;
		}
		$szHtml .= '<tr'.( $n2++%2==0 ? ' bgcolor="#eeeeee"' : '' ).' valign="top">';

		$szHtml .= '<td><b>'.$rd['id'].'. '.$rd['name'].'</b><br /><br />';
		$szHtml .= 'Requires '.( !empty($g_arrRequires[$rd['id']]) ? count($g_arrRequires[$rd['id']]) : '0' ).'<br />';
		$szHtml .= 'Excludes '.( !empty($g_arrExcludes[$rd['id']]) ? count($g_arrExcludes[$rd['id']]) : '0' ).'<br />';
		$szHtml .= 'Available to '.( !empty($g_arrForRaces[$rd['id']]) ? count($g_arrForRaces[$rd['id']]) : '0' );
		$szHtml .= '</td>';

		// requires //
		$szHtml .= '<td align="center"><select size="10" id="requires_'.$rd['id'].'" name="requires['.$rd['id'].'][]" multiple="1">';
		$szOptions = '';
		foreach ( $RD AS $_rd ) {
			if ( $_rd['id'] !== $rd['id'] ) {
				$szOptions .= '<option'.( !empty($g_arrRequires[$rd['id']][$_rd['id']]) ? ' selected="selected"' : '' ).' value="'.$_rd['id'].'">'.strtoupper($_rd['T']).' '.$_rd['id'].'. '.$_rd['name'].'</option>';
			}
		}
		$szHtml .= $szOptions;
		$szHtml .= '</select></td>';

		// excludes //
		$szHtml .= '<td align="center"><select size="10" id="excludes_'.$rd['id'].'" name="excludes['.$rd['id'].'][]" multiple="1">';
		$szOptions = '';
		foreach ( $RD AS $_rd ) {
			if ( $_rd['id'] !== $rd['id'] ) {
				$szOptions .= '<option'.( !empty($g_arrExcludes[$rd['id']][$_rd['id']]) ? ' selected="selected"' : '' ).' value="'.$_rd['id'].'">'.strtoupper($_rd['T']).' '.$_rd['id'].'. '.$_rd['name'].'</option>';
			}
		}
		$szHtml .= $szOptions;
		$szHtml .= '</select></td>';

		// for races //
		$szHtml .= '<td align="center"><select size="'.count($R).'" id="for_race_'.$rd['id'].'" name="per_race['.$rd['id'].'][]" multiple="1">';
		$szOptions = '';
		foreach ( $R AS $iRace => $szRace ) {
			$szOptions .= '<option'.( !empty($g_arrForRaces[$rd['id']][$iRace]) ? ' selected="selected"' : '' ).' value="'.$iRace.'">'.$szRace.'</option>';
		}
		$szHtml .= $szOptions;
		$szHtml .= '</select></td>';

		// skills //
		$arrRequiredSkills = db_select('d_skills_per_r_d', 'r_d_id = '.(int)$rd['id']);
		$szHtml .= '<td align="center" nowrap="1" wrap="off">';
		$n = 0;
		foreach ( $arrRequiredSkills AS $rs ) {
			$szHtml .= '<div><select name="skills['.$rd['id'].']['.($n).'][skill_id]"><option value="">--</option>';
			foreach ( $S AS $iSkill => $szSkill ) { $szHtml .= '<option'.( $rs['skill_id'] == $iSkill ? ' selected="1"' : '' ).' value="'.$iSkill.'">'.$szSkill.'</option>'; }
			$szHtml .= '</select><input type="text" name="skills['.$rd['id'].']['.($n++).'][value]" size="1" style="text-align:center;" value="'.$rs['required_value'].'" /></div>';
		}
		for ( $i=0; $i<2; $i++ ) {
			$szHtml .= '<div><select name="skills['.$rd['id'].']['.($n).'][skill_id]"><option value="">--</option>';
			foreach ( $S AS $iSkill => $szSkill ) { $szHtml .= '<option value="'.$iSkill.'">'.$szSkill.'</option>'; }
			$szHtml .= '</select><input type="text" name="skills['.$rd['id'].']['.($n++).'][value]" size="1" style="text-align:center;" value="" /></div>';
		}
		$szHtml .= '</td>';

		$szHtml .= '</tr>';
	}
	$szHtml .= '</table>';
	echo $szHtml;
//	return $szHtml;
}

?>
