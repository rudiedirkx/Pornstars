<?php

require_once('inc.config.php');
logincheck();

if ( isset($_GET['train']) ) {
	if ( !($arrSkill = db_select('d_skills s, planet_skills p', 'p.skill_id = s.id AND s.id = '.(int)$_GET['train'].' AND p.planet_id = '.PLANET_ID)) ) {
		exit('Invalid skill!');
	}
	$arrSkill = $arrSkill[0];

	if ( 0 < db_count('skill_training', 'planet_id = '.PLANET_ID) ) {
		exit('Already training a skill!');
	}

	$iEta = pow(5+(int)$arrSkill['value'], 2);
	db_insert('skill_training', array('planet_id' => PLANET_ID, 'skill_id' => $arrSkill['id'], 'eta' => $iEta));

	header('Location: skills.php');
	exit;
}

_header();

$arrSkills = db_fetch('SELECT * FROM d_skills s LEFT JOIN planet_skills p ON p.skill_id = s.id AND p.planet_id = '.PLANET_ID.';');
//echo '<pre>';print_r($arrSkills);
$arrTraining = db_select_fields('skill_training', 'skill_id,eta', '0 <= eta AND planet_id = '.PLANET_ID);
echo db_error();
?>

<div class="header">Skills</div>

<br />

<table border="0" bordercolor="#dddddd" cellspacing="0" cellpadding="3" align="center" class="widecells">
<tr>
	<th width="10"></th>
	<th width="100%" class="left">Name</th>
	<th align="center">ETA</th>
	<th align="right">Progress</th>
	<th align="center">Action</th>
	<th nowrap="1" align="right">Current level</th>
</tr>
<?php

foreach ( $arrSkills AS $arrSkill ) {
	$iTrainingETA = isset($arrTraining[$arrSkill['id']]) ? (int)$arrTraining[$arrSkill['id']] : 0;
	echo '<tr valign="top" class="bt"'.( 0 < $iTrainingETA ? ' bgcolor="#221111"' : '' ).'>';
	echo '<td>'.$arrSkill['id'].'</td>';
	echo '<td width="100%"><i onclick="var d=this.parentNode.getElementsByTagName(\'div\')[0].style;d.display=\'none\'!=d.display?\'none\':\'\';" style="cursor:pointer;">'.$arrSkill['skill'].'</i><div style="display:none;">'.$arrSkill['explanation'].'</div></td>';
	$iTargetETA = pow(5+(int)$arrSkill['value'], 2);
	echo '<td align="center">'.( null === $arrSkill['value'] ? '-' : ( 0 < $iTrainingETA ? $iTrainingETA : $iTargetETA ) ).'</td>';
	echo '<td align="right">'.( 0 < $iTrainingETA ? floor(100*($iTargetETA-$iTrainingETA)/$iTargetETA).' %' : '-' ).'</td>';
	echo '<td align="center">'.( 0 < array_sum($arrTraining) || null === $arrSkill['value'] ? '-' : '<a href="?train='.$arrSkill['id'].'">train</a>' ).'</td>';
	echo '<td align="right">'.( null === $arrSkill['value'] ? '-' : (int)$arrSkill['value'] ).'</td>';
	echo '</tr>';
}

?>
</table>

<?php

_footer();

?>