<?php

require_once('inc.config.php');

require_once('inc.shipstats.php');

_header();

$t_arrCombatStats = db_select('d_combat_stats', 'ratio != 0.0');
$arrCombatStats = array();
foreach ( $t_arrCombatStats AS $arrStats ) {
	$arrCombatStats[$arrStats['shooting_unit_id']][$arrStats['receiving_unit_id']] = array((float)$arrStats['ratio'], $arrStats['target_priority']);
}

$arrAttackers = db_select('d_all_units', 'id IN (SELECT DISTINCT shooting_unit_id FROM d_combat_stats) ORDER BY id ASC');
$arrDefenders = db_select('d_all_units', 'id IN (SELECT DISTINCT receiving_unit_id FROM d_combat_stats) ORDER BY id ASC');

$iFreezers = 0;

$arrTargetPriorities = array(
	'1' => 'red',
	'2' => 'blue',
	'3' => 'yellow',
);

?>

<table border="0" cellpadding="4" cellspacing="1" class="widecells">
<tr height="25">
	<td><br /></td>
<?php foreach ( $arrAttackers AS $arrAttacker ) {
	echo '	<th title="'.$arrAttacker['id'].'" class="c" style="color:#000;background-color:'.$showcolors['attack'].';" nowrap="1" wrap="off">'.$arrAttacker['unit'].'</th>'."\n";
} ?>
</tr>
<?php foreach ( $arrDefenders AS $arrDefender ) {
	$iDefender = $arrDefender['id'];
	$szDefender = $arrDefender['unit'];
	echo '<tr height="25">'."\n";
	echo '	<th title="'.$arrDefender['id'].'" class="right" style="color:#000;background-color:'.$showcolors['defend'].';" nowrap="1" wrap="off">'.$szDefender.'</th>'."\n";
	foreach ( $arrAttackers AS $arrAttacker ) {
		$iAttacker = $arrAttacker['id'];
		$szAttacker = $arrAttacker['unit'];
		$arrRatio = isset($arrCombatStats[$iAttacker][$iDefender]) ? $arrCombatStats[$iAttacker][$iDefender] : false;
		$iRatio = $arrRatio ? $arrRatio[0] : false;
		$iFreezers += (int)(0 > $iRatio);
		echo '	<td'.( $arrRatio && isset($arrTargetPriorities[$arrRatio[1]]) ? ' bgcolor="'.$arrTargetPriorities[$arrRatio[1]].'"' : '' ).' class="c"'.( $iRatio ? ' title="'.round(1/$iRatio,2).'"' : '' ).'>'.( $iRatio ? number_format($iRatio, 2, '.', ',') : '-' ).'</td>'."\n";
	}
	echo '</tr>'."\n";
} ?>
</table>

<table border="0"><tr height="40">
<th>TARGET PRIORITIES :&nbsp;</th>
<?php foreach ( $arrTargetPriorities AS $iPri => $szColor ) {
	echo '<th bgcolor="'.$szColor.'" width="40">'.$iPri.'</th>';
} ?>
</tr></table>

<p><b>NOTE:</b> Negative numbers indicate freezing/blocking, instead of killing/destructing (<?php echo $iFreezers; ?> instances)!<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Freezing happens after the hostile units kill/destruct.</p>
